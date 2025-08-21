<?php

namespace App\Http\Controllers\Auth;

use App\Facades\Message;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
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
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-zA-Z0-9._]+$/',
                'unique:users,username',
            ],
            'password' => 'required|min:6|max:50',
        ]);

        if ($validator->fails()) {
            return Message::validator("", $validator->errors());
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => Str::slug($request->username,),
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'code' => 201,
                'message' => 'Registrasi berhasil. Silakan login.',
                'redirect' => route('login'),
            ], 201);
        } catch (\Exception $e) {
            return Message::error("Terjadi kesalahan. " . $e->getMessage());
        }
    }
}
