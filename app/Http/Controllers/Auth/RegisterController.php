<?php

namespace App\Http\Controllers\Auth;

use App\Facades\Message;
use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function index()
    {
        return view('pages.auth.register.index');
    }

    public function register(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|max:50',
            'email' => 'required|email|unique:users,email',
            'phone' => [
                'required',
                'regex:/^[0-9+]+$/',
                'min:9',
                'max:15',
                'unique:users,phone',
            ],
            'password' => 'required|min:6|max:50',
        ]);

        if ($validator->fails()) {
            return Message::validator("", $validator->errors());
        }

        try {
            $phone = Helpers::formatPhoneInternational($request->phone);

            $checkNumber = WhatsappService::checkNumber($phone);

            if ($checkNumber->is_success == false) {
                return Message::error("WhatsApp number is invalid or not registered");
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $phone,
                'username' => Str::slug($request->name,),
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('user');

            Auth::login($user);

            $request->session()->regenerate();

            $user->last_login_at = now();
            $user->last_login_ip = $request->ip();
            $user->save();

            DB::commit();

            return response()->json([
                'code' => 201,
                'message' => 'Registration successful. Please log in..',
                'redirect' => route('send-otp'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return Message::error("An error has occurred. " . $e->getMessage());
        }
    }
}
