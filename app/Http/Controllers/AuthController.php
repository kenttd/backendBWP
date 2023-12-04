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
        if ($user) {
            return response()->json(['message' => 'Registration successful']);
        } else {
            return response()->json(['message' => 'Registration failed'], 401);
        }
    }

    public function login(Request $request)
    {
        $credential = [
            "Username" => $request->username,
            "password" => $request->password
        ];
        if (Auth::attempt($credential)) {
            return response()->json(['message' => 'Login successful', 'UserID' => Auth::user()->UserID]);
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
