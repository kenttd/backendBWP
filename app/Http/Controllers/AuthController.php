<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $user = Users::create([
            'Username' => $request->input('username'),
            "Password" => bcrypt($request->input('password')),
            // 'Password' => Hash::make($request->input('password')),
            'Email' => $request->input('email'),
            // Add other user fields as needed
            'ProfilePicture' => 'default.jpg', // You can set a default profile picture
            'Bio' => '', // You can set a default bio
            'created_at' => now(), // This assumes your database supports the 'now()' function
            "Followers" => 0,
            "Following" => 0,
            "access_key" => Str::random(64)
        ]);
        return response()->json(['message' => 'Registration successful']);
    }

    public function login(Request $request)
    {
        $user = Users::where('Username', $request->input('username'))->first();

        if ($user && Hash::check($request->input('password'), $user->Password)) {
            // Password benar
            if (Hash::needsRehash($user->password)) {
                $user->password = Hash::make($request->input('password'));
                $user->save();
            }
            return response()->json(['message' => 'Login successful', 'UserID' => $user->UserID]);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }

    public function logout()
    {
        Auth::logout();

        return response()->json(['message' => 'Logout successful']);
    }
}
