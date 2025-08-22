<?php

namespace App\Http\Controllers\Auth;

use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index()
    {
        return view('pages.auth.login.index');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|min:3|max:50',
            'password' => 'required|min:6|max:50',
        ]);

        if ($validator->fails()) {
            return Message::validator("Validasi gagal.", $validator->errors());
        }

        DB::beginTransaction();

        try {
            $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

            $user = User::where($loginField, $request->login)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return Message::unauthorize("Incorrect email address/username or password.");
            }

            if ($user->email_verified_at === null) {
                return Message::unauthorize("Your account has not been verified. Please contact the admin..");
            }

            if (Auth::attempt([$loginField => $request->login, 'password' => $request->password])) {
                $request->session()->regenerate();

                $user->last_login_at = now();
                $user->last_login_ip = $request->ip();
                $user->save();

                DB::commit();

                return response()->json([
                    'code' => 200,
                    'message' => 'Login berhasil.',
                    'redirect' => url('/~admin/dashboard'),
                ], 200);
            }

            DB::rollBack();
            return Message::unauthorize("Incorrect email/username or password.");
        } catch (\Exception $e) {
            DB::rollBack();
            return Message::error("An error has occurred. " . $e->getMessage());
        }
    }
}
