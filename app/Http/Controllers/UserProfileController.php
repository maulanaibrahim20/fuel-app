<?php

namespace App\Http\Controllers;

use App\Facades\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{
    public function index()
    {
        $data['user'] = User::where('id', Auth::id())->first();
        return view('pages.user-profile.index', $data);
    }

    public function update(Request $request)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'name'              => 'required|string|max:255',
            'username'          => 'required|string|max:255|unique:users,username,' . Auth::id(),
            'email'             => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'phone'             => 'nullable|string|max:15',
            'address'           => 'nullable|string|max:255',
            'profile_image'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'current_password'  => 'nullable|string|min:8',
            'password'          => 'nullable|string|min:8|confirmed',
            'avatar'            => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return Message::validator("Validation failed.", $validator->errors());
        }

        $user = User::find(Auth::id());


        try {

            if ($request->hasFile('avatar')) {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $avatarPath = $request->file('avatar')->store('user/avatars', 'public');
                $user->avatar = $avatarPath;
            }

            $user->update([
                'name'              => $request->name,
                'username'          => $request->username,
                'email'             => $request->email,
                'phone'             => $request->phone,
                'address'           => $request->address,
            ]);

            DB::commit();

            return Message::success("Profile successfully updated.");
        } catch (\Exception $e) {
            DB::rollBack();
            return Message::error("An error occurred while updating your profile. " . $e->getMessage());
        }
    }
}
