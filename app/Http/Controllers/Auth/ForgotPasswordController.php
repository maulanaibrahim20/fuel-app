<?php

namespace App\Http\Controllers\Auth;

use App\Facades\Message;
use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function index()
    {
        return view('pages.auth.forgot-password.index');
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'forgot-password' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        return true;
                    }

                    if (preg_match('/^(?:\+62|62|0)8[1-9][0-9]{6,11}$/', $value)) {
                        return true;
                    }

                    $fail("The $attribute must be a valid email address or WhatsApp phone number.");
                }
            ]
        ]);

        if ($validator->fails()) {
            return Message::validator("Validation failed.", $validator->errors());
        }

        $input = $request->input('forgot-password');

        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {

            $user = User::where('email', $input)->first();

            if (!$user) {
                return message::notFound('User not found.');
            }

            $status = Password::sendResetLink(['email' => $input]);

            if ($status === Password::RESET_LINK_SENT) {

                return Message::success('Reset link sent to your email address!');
            }

            return Message::error('Failed to send reset link.');
        } else {
            $phoneFormat = Helpers::formatPhoneInternational($input);
            $user = User::where('phone', $phoneFormat)->first();

            if (!$user) {
                return Message::notFound('User not found.');
            }

            $token = Password::createToken($user);

            $resetUrl = url(route('password.reset', [
                'token' => $token,
                'phone' => $user->phone,
            ], false));

            $sent = WhatsappService::sendMessage(
                $phoneFormat,
                "R3SETTT Password Link: {$resetUrl}"
            );

            if (!$sent) {
                return Message::error("Failed to send OTP to WhatsApp.");
            }

            return Message::success('OTP sent to your WhatsApp.');
        }
    }
}
