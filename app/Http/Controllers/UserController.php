<?php

namespace App\Http\Controllers;

use App\Models\Likes;
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
    public function Post($id)
    {
        $posts = Tweets::whereHas('user.follows', function ($query) use ($id) {
            $query->where('FollowingID', $id);
        })
            ->with(['user', 'likes' => function ($query) use ($id) {
                $query->where('userid', $id);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        // Add a property 'liked' to each post to indicate whether the specified user has liked the post
        $posts->each(function ($post) {
            $post->liked = $post->likes->isNotEmpty();
            unset($post->likes); // You may remove this line if you want to include 'likes' information in the response
        });

        return response()->json(['posts' => $posts]);
    }

    // public function Post($id)
    // {
    //     $posts = Tweets::whereHas('user.follows', function ($query) use ($id) {
    //         $query->where('FollowingID', $id);
    //     })
    //         ->with('user') // Load the user relationship to get user details in the posts
    //         ->orderBy('created_at', 'desc') // Assuming Timestamp is the column for post timestamp
    //         ->get();
    //     return response()->json(['posts' => $posts]);
    // }

    public function quack(Request $request)
    {
        $TweetContent = $request->TweetContent;
        $uid = $request->uid;
        $newTweet = Tweets::create([
            "UserID" => $uid,
            "TweetContent" => $TweetContent,
            "LikesCount" => 0,
            "RetweetsCount" => 0,
            "RepliesCount" => 0
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

    public function getPost(Request $request)
    {
        $user = Users::find($request->id);
        $tweets = $user->tweets()->with('user')->get();
        // $tweets->transform(function ($tweet) {
        //     return [
        //         'tweet' => $tweet,
        //         'user' => $tweet->user
        //     ];
        // });
        return response()->json(["tweets" => $tweets]);
    }

    public function getBookmark(Request $request)
    {
        $user = Users::find($request->id);
        $bookmarks = $user->bookmarks;
        return response()->json(["bookmarks" => $bookmarks]);
    }

    public function doLike(Request $request)
    {
        $tweet = Tweets::find($request->TweetID);
        if ($tweet) {
            $tweet->LikesCount += 1;
            $tweet->save();
            if ($request->update) {
                $like = Likes::where("LikeID", $request->LikeID)->restore();
            } else {
                $newLike = Likes::create([
                    "UserID" => $request->UserID,
                    "TweetID" => $request->TweetID
                ]);
            }
            return response()->json(["LikeID" => $newLike->LikeID]);
        } else return response()->json(["message" => "failed"]);
    }

    public function doUnLike(Request $request)
    {
        $tweet = Tweets::find($request->TweetID);
        if ($tweet) {
            $tweet->LikesCount -= 1;
            $tweet->save();
            $like = Likes::where("LikeID", $request->LikeID)->delete();
            return response()->json(["message" => "success"]);
        } else return response()->json(["message" => "failed"]);
    }
}
