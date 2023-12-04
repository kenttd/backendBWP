<?php

namespace App\Http\Controllers;

use App\Models\Tweets;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function quack(Request $request)
    {
        $TweetContent = $request->TweetContent;
        $uid = $request->uid;
        $newTweet = Tweets::create([
            "UserID" => $uid,
            "TweetContent" => $TweetContent,
            "LikesCount" => 0,
            "RetweetsCount" => 0
        ]);
        if ($newTweet) {
            return response()->json(['message' => 'successful']);
        } else {
            return response()->json(['message' => 'failed'], 401);
        }
    }

    public function search(Request $request)
    {
        $username = $request->username;
        $list = Users::where("Username", "LIKE", "%$username%")->get() ?? [];
        return response()->json(["list" => $list]);
    }
}
