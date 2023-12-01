<?php

namespace App\Http\Controllers;

use App\Models\Tweets;
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
            abort(404);
        }
    }
    public function getPost($id)
    {
        $posts = Tweets::whereHas('user.follows', function ($query) use ($id) {
            $query->where('FollowingID', $id);
        })
            ->with('user') // Load the user relationship to get user details in the posts
            ->orderBy('created_at', 'desc') // Assuming Timestamp is the column for post timestamp
            ->get();
        return json_encode(['posts' => $posts]);
    }
}
