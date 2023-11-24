<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUserByUsername($username)
    {
        $user = Users::where('Username', $username)->first();
        if ($user) {
            return json_encode(["user" => $user]);
        } else {
            // header("HTTP/1.1 404 Not Found");
            abort(404);
            return json_encode(['message' => 'Not found', "test" => "abc"]);
        }
    }
}
