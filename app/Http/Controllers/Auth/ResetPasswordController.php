<?php

namespace App\Http\Controllers\Auth;

use App\Facades\Message;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token)
    {
        return view('pages.auth.reset-password.index', [
            'token' => $token,
            'email' => $request->email,
            'phone' => $request->phone
        ]);
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|same:password_confirmation',
            'password_confirmation' => 'required|min:8|same:password',
        ]);

        if ($validator->fails()) {
            return Message::validator("", $validator->errors());
        }

        DB::beginTransaction();

        try {
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60),
                    ])->save();
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                DB::commit();
                return response()->json([
                    'code' => 200,
                    'message' => 'Password successfully reset.',
                    'redirect' => route('login'),
                ], 200);
            }

            DB::rollBack();
            return back()->withErrors(['email' => [__($status)]]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat reset password. Silakan coba lagi.');
        }
    }
}
