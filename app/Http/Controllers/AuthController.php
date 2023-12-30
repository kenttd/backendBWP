<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required|min:8',
            'email' => 'required|email'
        ]);
        if (!$validator->fails()) {
            $user = Users::create([
                'Username' => $request->input('username'),
                "Password" => bcrypt($request->input('password')),
                // 'Password' => Hash::make($request->input('password')),
                'Email' => $request->input('email'),
                // Add other user fields as needed
                'ProfilePicture' => 'picture.jpg', // You can set a default profile picture
                'Bio' => "this user doesn't have a bio yet.", // You can set a default bio
                "Followers" => 0,
                "Following" => 0,
                "isVerified" => 0,
                "isStaff" => 0,
                "isBanned" => 0,
                "code" => Str::random(5),
                "link" => "quacker.online"
            ]);
            if ($user) {
                return response()->json(['message' => 'Registration successful']);
            } else {
                return response()->json(['message' => 'Registration failed'], 401);
            }
        }
        return response()->json(['error' => $validator->errors()], 401);
    }

    public function login(Request $request)
    {
        $credential = [
            "Username" => $request->username,
            "password" => $request->password
        ];
        if (Auth::attempt($credential)) {
            if (Auth::user()->isBanned) {
                return response()->json(['message' => 'You are banned'], 401);
            }
            return response()->json(['message' => 'success', 'UserID' => Auth::user()->UserID, 'Username' => Auth::user()->Username, "isVerified" => Auth::user()->isVerified, "isStaff" => Auth::user()->isStaff]);
        } else {
            return response()->json(['message' => 'Login failed, wrong password/username.'], 401);
        }
    }


    public function logout()
    {
        Auth::logout();

        return response()->json(['message' => 'Logout successful']);
    }

    public function getuser()
    {
        return response()->json(['user' => Auth::user()]);
    }
}
