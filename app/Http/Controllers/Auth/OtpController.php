<?php

namespace App\Http\Controllers\Auth;

use App\Facades\Message;
use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\OtpService;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class OtpController extends Controller
{
    protected $otpService;

    public function __construct()
    {
        $this->otpService = new OtpService();
    }
    public function index()
    {
        $data = User::find(Auth::user()->id);
        $data['maskedPhone'] = Helpers::maskMiddle($data->phone);
        $data['maskedEmail'] = Helpers::maskEmail($data->email);

        return view('pages.auth.otp.send-otp', $data);
    }

    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'method' => 'required|in:phone,email',
        ]);

        if ($validator->fails()) {
            return Message::validator("Validation failed.", $validator->errors());
        }

        $user = User::find(Auth::id());

        $identifier = $request->method === 'phone' ? $user->phone : $user->email;
        $purpose = $request->method === 'phone' ? 'phone_verification' : 'email_verification';

        $otp = $this->otpService->generateOtp(
            $user->id,
            $identifier,
            $request->method,
            $purpose,
            $request->ip(),
            $request->userAgent()
        );

        if ($request->method === 'phone') {

            $phone = $user->phone;

            $sent = WhatsappService::sendMessage(
                $phone,
                "Koudede 0TP3 Anda adalah: {$otp->code}"
            );

            if (!$sent) {
                return Message::error("Failed to send OTP to WhatsApp.");
            }

            return response()->json([
                'code' => 201,
                'message' => 'OTP successfully sent to WhatsApp.',
                'redirect' => route('verify-otp', ['type' => $request->method, 'purpose' => 'phone_verification']),
            ], 201);
        } elseif ($request->method === 'email') {

            $email = $user->email;

            try {
                Mail::to($email)->send(new OtpMail($otp->code));
            } catch (\Exception $e) {
                return Message::error("Failed to send OTP to email. " . $e->getMessage());
            }

            return response()->json([
                'code' => 201,
                'message' => 'OTP successfully sent to email.',
                'redirect' => route('verify-otp', ['type' => $request->method, 'purpose' => 'email_verification']),
            ], 201);
        }
    }

    public function verifyOtp(Request $request)
    {
        $data = User::find(Auth::user()->id);
        $data['maskedPhone'] = Helpers::maskMiddle($data->phone);
        $datap['maskeEmail'] = Helpers::maskEmail($data->email);

        $otp = OtpCode::where('user_id', Auth::id())->first();

        return view('pages.auth.otp.verify-otp', compact('data', 'otp'));
    }

    public function validateOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6',
            'type' => 'required|in:email,phone',
            'purpose' => 'required|in:phone_verification,email_verification,login,password_reset,account_recovery',
        ]);

        if ($validator->fails()) {
            return Message::validator("Validation failed.", $validator->errors()->first());
        }

        try {
            $user = User::find(Auth::id());

            $result = $this->otpService->validateOtp(
                $user->id,
                $request->otp,
                $request->type,
                $request->purpose
            );

            if (!$result['success']) {
                return Message::error($result['message']);
            }

            if ($request->purpose === 'phone_verification' && $request->type === 'phone') {
                $user->update([
                    'phone_verified_at' => now()
                ]);
            } elseif ($request->purpose === 'email_verification' && $request->type === 'email') {
                $user->update([
                    'email_verified_at' => now()
                ]);
            }

            $redirectTo = $user->hasRole('Super Admin') ? route('admin.dashboard') : route('user.dashboard');

            return response()->json([
                'code' => 200,
                'message' => $result['message'],
                'redirect' => $redirectTo,
            ], 200);
        } catch (\Throwable $e) {
            return Message::error("An error occurred while verifying the OTP. Please try again.");
        }
    }

    public function resentOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'    => 'required|in:phone,email',
            'purpose' => 'required|in:phone_verification,email_verification,login,password_reset,account_recovery',
        ]);

        if ($validator->fails()) {
            return Message::validator("Validation failed.", $validator->errors());
        }

        $user = User::find(Auth::id());

        if (!$user) {
            return Message::error("User not found.");
        }

        $identifier = $request->type === 'phone' ? $user->phone : $user->email;

        $otp = $this->otpService->resentOtp(
            $user->id,
            $identifier,
            $request->type,
            $request->purpose,
            $request->ip(),
            $request->userAgent()
        );

        if ($request->type === 'phone') {
            // Kirim via WhatsApp
            $sent = WhatsappService::sendMessage(
                $user->phone,
                "Kode OTP baru Anda adalah: {$otp->code}"
            );

            if (!$sent) {
                return Message::error("Failed to send OTP to WhatsApp.");
            }

            return response()->json([
                'code' => 200,
                'message' => 'New OTP successfully sent to WhatsApp.',
            ]);
        }

        if ($request->type === 'email') {
            try {
                Mail::to($user->email)->send(new OtpMail($otp->code));
            } catch (\Throwable $e) {
                return Message::error("Failed to send OTP to email.");
            }

            return response()->json([
                'code' => 200,
                'message' => 'New OTP successfully sent to email.',
            ]);
        }

        return Message::error("Invalid OTP method.");
    }
}
