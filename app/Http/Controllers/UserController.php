<?php

namespace App\Http\Controllers;

use App\Models\Bookmarks;
use App\Models\Follows;
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
        $posts = Tweets::whereHas('user.following', function ($query) use ($id) {
            $query->where('FollowingID', $id);
        })
            ->with(['user', 'likes' => function ($query) use ($id) {
                $query->where('UserID', $id);
            }, 'retweets' => function ($query) use ($id) {
                $query->where('UserID', $id);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        $posts->each(function ($post) {
            $post->liked = $post->likes->isNotEmpty();
            $post->likeid = $post->liked ? $post->likes->first()->LikeID : null;
            unset($post->likes);
            $post->retweeted = $post->retweets->isNotEmpty();
            $post->retweetid = $post->retweeted ? $post->retweets->first()->RetweetID : null;
            unset($post->retweets);
        });

        return response()->json(['posts' => $posts]);
    }


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

    public function searchTweet(Request $request)
    {
        $q = $request->q;
        $list = Users::where("TweetContent", "LIKE", "%$q%")->get() ?? [];
        return response()->json(["list" => $list]);
    }

    public function getPost(Request $request)
    {
        $user = Users::find($request->id);
        $requester = Users::find($request->requester);

        $userIsFollowingRequester = $user->following->contains('FollowingID', $request->requester);
        $requesterIsFollowingUser = $user->followers->contains('FollowerID', $request->requester);

        $tweets = $user->tweets()->with('user')->get();

        return response()->json([
            "tweets" => $tweets,
            "isFollowed" => $userIsFollowingRequester,
            "isFollowing" => $requesterIsFollowingUser
        ]);
        return response()->json(["tweets" => $tweets]);
    }

    public function getBookmark($id)
    {
        $user = Users::find($id);
        $bookmarks = $user->bookmarks()->with(['Tweet' => function ($query) use ($id) {
            $query->with(['user', 'likes' => function ($query) use ($id) {
                $query->where('UserID', $id);
            }, 'retweets' => function ($query) use ($id) {
                $query->where('UserID', $id);
            }]);
        }])->get();

        $bookmarks->each(function ($bookmark) {
            $bookmark->Tweet->liked = $bookmark->Tweet->likes->isNotEmpty();
            $bookmark->Tweet->likeid = $bookmark->Tweet->liked ? $bookmark->Tweet->likes->first()->LikeID : null;
            unset($bookmark->Tweet->likes);
            $bookmark->Tweet->retweeted = $bookmark->Tweet->retweets->isNotEmpty();
            $bookmark->Tweet->retweetid = $bookmark->Tweet->retweeted ? $bookmark->Tweet->retweets->first()->RetweetID : null;
            unset($bookmark->Tweet->retweets);
        });
        return response()->json(['posts' => $bookmarks]);
    }

    public function doLike(Request $request)
    {
        $tweet = Tweets::find($request->TweetID);
        if ($tweet) {
            $tweet->LikesCount += 1;
            $tweet->save();
            if ($request->update == false) {
                $like = Likes::where("LikeID", $request->LikeID)->restore();
            } else {
                $newLike = Likes::create([
                    "UserID" => $request->UserID,
                    "TweetID" => $request->TweetID
                ]);
            }
            return response()->json(["LikeID" => $newLike->LikeID ?? $request->LikeID, "update" => $request->update]);
        } else return response()->json(["message" => "failed"]);
    }

    public function doUnLike(Request $request)
    {
        $tweet = Tweets::find($request->TweetID);
        if ($tweet) {
            $tweet->LikesCount = 0;
            $tweet->save();
            $like = Likes::find($request->LikeID);
            $like->delete();
            if ($like->trashed()) {
                return response()->json(["message" => "success"]);
            } else return response()->json(["message" => "failed"]);
        } else return response()->json(["message" => "failed"]);
    }

    public function doBookmark(Request $request)
    {
        $user = Users::find($request->UserID);
        $bookmark = $user->bookmarks()->where("TweetID", $request->TweetID);

        if (!$bookmark->exists()) {
            $newBookmark = Bookmarks::create([
                "UserID" => $request->UserID,
                "TweetID" => $request->TweetID
            ]);
            return response()->json(["BookmarkID" => $newBookmark->BookmarkID]);
        } else return response()->json(["message" => "failed"]);
    }

    public function doUnBookmark(Request $request)
    {
        $bookmark = Bookmarks::find($request->BookmarkID);
        if ($bookmark) {
            $bookmark->forceDelete();
            return response()->json(["message" => "success"]);
        }
        return response()->json(["message" => "failed"]);
    }

    public function getLike(Request $request)
    {
        $user = Users::find($request->id);
        $likes = $user->likes()->with('Tweet')->get();
        return response()->json(["likes" => $likes]);
    }

    public function doFolllow(Request $request)
    {
        //following orang yang di follow
        $follow = Follows::where("FollowerID", $request->FollowerID)->where("FollowingID", $request->FollowingID);
        if (!$follow->exists()) {
            $following = Users::find($request->FollowingID);
            $following->Followers += 1;
            $following->save();
            $follower = Users::find($request->FollowerID);
            $follower->Following += 1;
            $follower->save();
            $follow = Follows::create([
                "FollowerID" => $request->FollowerID,
                "FollowingID" => $request->FollowingID
            ]);
            if ($follow) {
                return response()->json(["message" => "success"]);
            } else return response()->json(["message" => "failed"]);
        } else return response()->json(["message" => "failed"]);
    }

    public function doUnfollow(Request $request)
    {
        $follow = Follows::where("FollowerID", $request->FollowerID)->where("FollowingID", $request->FollowingID);
        if ($follow) {
            $follow->delete();
            $following = Users::find($request->FollowingID);
            $following->Followers -= 1;
            $following->save();
            $follower = Users::find($request->FollowerID);
            $follower->Following -= 1;
            $follower->save();
            return response()->json(["message" => "success"]);
        } else return response()->json(["message" => "failed"]);
    }
}
