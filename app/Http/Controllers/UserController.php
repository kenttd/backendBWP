<?php

namespace App\Http\Controllers;

use App\Models\Bookmarks;
use App\Models\DirectMessages;
use App\Models\Follows;
use App\Models\Likes;
use App\Models\Replies;
use App\Models\Retweets;
use App\Models\Tweets;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $posts = Tweets::whereHas('user.followers', function ($query) use ($id) {
            $query->where('FollowerID', $id);
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

    public function getPost($id, $requester)
    {
        // Fetch the tweets that the user has posted
        $userPosts = Tweets::where('UserID', $id)
            ->with(['user', 'likes' => function ($query) use ($requester) {
                $query->where('UserID', $requester);
            }])
            ->get();

        // Fetch the tweets that the user has retweeted
        $retweets = Users::find($id)->retweets
            ->load(['tweet' => function ($query) use ($requester) {
                $query->with(['user', 'likes' => function ($query) use ($requester) {
                    $query->where('UserID', $requester);
                }]);
            }]);

        // Merge the two collections
        $posts = $userPosts->concat($retweets->map(function ($retweet) {
            $retweet->tweet->isRetweed = true;
            return $retweet->tweet;
        }));

        $posts->each(function ($post) use ($requester) {
            $post->liked = $post->likes->where('UserID', $requester)->isNotEmpty();
            $post->likeid = $post->liked ? $post->likes->first()->LikeID : null;
            unset($post->likes);
        });

        // Check if $requester is following $id
        $isFollowing = Follows::where('FollowerID', $requester)->where('FollowingID', $id)->exists();

        // Check if $id is following $requester
        $isFollowed = Follows::where('FollowingID', $requester)->where('FollowerID', $id)->exists();

        return response()->json(['posts' => $posts, 'isFollowing' => $isFollowing, 'isFollowed' => $isFollowed]);
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
            $tweet->LikesCount -= 1;
            $tweet->save();
            $like = Likes::find($request->LikeID);
            $like->delete();
            if ($like->trashed()) {
                return response()->json(["message" => "success"]);
            } else return response()->json(["message" => "failed"]);
        } else return response()->json(["message" => "failed"]);
    }

    public function doRetweet(Request $request)
    {
        $tweet = Tweets::find($request->TweetID);
        if ($tweet) {
            $tweet->RetweetsCount += 1;
            $tweet->save();
            if ($request->update == false) {
                $retweet = Retweets::where("RetweetID", $request->RetweetID)->restore();
            } else {
                $newRetweet = Retweets::create([
                    "UserID" => $request->UserID,
                    "TweetID" => $request->TweetID
                ]);
            }
            return response()->json(["RetweetID" => $newRetweet->RetweetID ?? $request->RetweetID, "update" => $request->update]);
        } else return response()->json(["message" => "failed"]);
    }

    public function doUnRetweet(Request $request)
    {
        $tweet = Tweets::find($request->TweetID);
        if ($tweet) {
            $tweet->RetweetsCount -= 1;
            $tweet->save();
            $retweet = Retweets::find($request->RetweetID);
            $retweet->delete();
            if ($retweet->trashed()) {
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

    public function doFollow(Request $request)
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
    public function getMessages(Request $request)
    {
        $latestMessages = DirectMessages::getLatestMessages($request->RequesterID);
        return response()->json(["latestMessages" => $latestMessages]);
    }

    public function doVerify(Request $request)
    {
        $user = Users::where("UserID", $request->UserID)->update(["isVerified" => true]);
        if ($user) {
            return response()->json(["message" => "success"]);
        }
        return response()->json(["message" => "failed"]);
    }

    public function doBan(Request $request)
    {
        $user = Users::where("UserID", $request->UserID)->update(["isBanned" => true]);
        if ($user) {
            return response()->json(["message" => "success"]);
        }
        return response()->json(["message" => "failed"]);
    }

    public function doUnverify(Request $request)
    {
        $user = Users::where("UserID", $request->UserID)->update(["isVerified" => false]);
        if ($user) {
            return response()->json(["message" => "success"]);
        }
        return response()->json(["message" => "failed"]);
    }

    public function doUnban(Request $request)
    {
        $user = Users::where("UserID", $request->UserID)->update(["isBanned" => false]);
        if ($user) {
            return response()->json(["message" => "success"]);
        }
        return response()->json(["message" => "failed"]);
    }

    public function doStaff(Request $request)
    {
        $user = Users::where("UserID", $request->UserID)->update(["isStaff" => true]);
        if ($user) {
            return response()->json(["message" => "success"]);
        }
        return response()->json(["message" => "failed"]);
    }

    public function doUnstaff(Request $request)
    {
        $user = Users::where("UserID", $request->UserID)->update(["isStaff" => false]);
        if ($user) {
            return response()->json(["message" => "success"]);
        }
        return response()->json(["message" => "failed"]);
    }

    public function getVerifiedPost()
    {
        $users = Users::where('isVerified', true)->with('tweets')->get();
        return response()->json(["users" => $users]);
    }

    public function getMessagesSpecific(Request $request)
    {
        $messages = DirectMessages::where(function ($query) use ($request) {
            $query->where('SenderID', $request->RequesterId)
                ->where('ReceiverID', $request->otherPersonId);
        })
            ->orWhere(function ($query) use ($request) {
                $query->where('SenderID', $request->otherPersonId)
                    ->where('ReceiverID', $request->RequesterId);
            })
            ->get();

        foreach ($messages as $message) {
            $message->sentByRequester = $message->SenderID == $request->RequesterId;
        }

        return response()->json(['messages' => $messages]);
    }

    public function sendMessage(Request $request)
    {
        $message = DirectMessages::create([
            'SenderID' => $request->SenderID,
            'ReceiverID' => $request->ReceiverID,
            'MessageContent' => $request->message,
            'timestamp' => now(),
            'isRead' => false
        ]);
        if ($message) {
            return response()->json(['message' => $message]);
        }
        return response()->json(['message' => 'failed']);
    }

    public function deleteMessage(Request $request)
    {
        $message = DirectMessages::find($request->MessageID);
        if ($message) {
            $message->delete();
            return response()->json(['message' => 'success']);
        }
        return response()->json(['message' => 'failed']);
    }

    public function editMessage(Request $request)
    {
        $message = DirectMessages::find($request->MessageID);
        if ($message) {
            $message->MessageContent = $request->MessageContent;
            $message->save();
            return response()->json(['message' => 'success']);
        }
        return response()->json(['message' => 'failed']);
    }

    public function getTweetDetail($TweetID)
    {

        if (Tweets::find($TweetID) == null) abort(404);
        $tweet = Tweets::where('TweetID', $TweetID)->with(['user', 'likes' => function ($query) use ($TweetID) {
            $query->where('TweetID', $TweetID);
        }, 'retweets' => function ($query) use ($TweetID) {
            $query->where('TweetID', $TweetID);
        }])->first();

        $tweet->liked = $tweet->likes->isNotEmpty();
        $tweet->likeid = $tweet->liked ? $tweet->likes->first()->LikeID : null;
        unset($tweet->likes);
        $tweet->retweeted = $tweet->retweets->isNotEmpty();
        $tweet->retweetid = $tweet->retweeted ? $tweet->retweets->first()->RetweetID : null;
        unset($tweet->retweets);
        $replies = Replies::where('TweetID', $TweetID)->with('user')->get();
        return response()->json(['tweet' => $tweet, 'replies' => $replies]);
    }

    public function tweetExist($TweetID)
    {
        if (Tweets::find($TweetID) == null) abort(404);
        return response()->json(['tweet' => Tweets::find($TweetID)]);
    }

    public function listFollowing($Username)
    {
        $user = Users::where('Username', $Username)->first();
        if (!$user) abort(404);
        $following = $user->following()->with('following')->get();
        return response()->json(['following' => $following]);
    }

    public function listFollower($Username)
    {
        $user = Users::where('Username', $Username)->first();
        if (!$user) abort(404);
        $follower = $user->followers()->with('follower')->get();
        return response()->json(['follower' => $follower]);
    }

    public function listVerifiedFollower($Username)
    {
        $user = Users::where('Username', $Username)->first();
        if (!$user) abort(404);
        $follower = $user->followers()->with(['follower' => function ($query) {
            $query->where('isVerified', true);
        }])->get();
        return response()->json(['follower' => $follower]);
    }

    public function listLikes($id, $requester)
    {
        // Fetch the tweets that the user has liked
        $likedPosts = Likes::where('UserID', $id)
            ->with(['tweet' => function ($query) use ($requester) {
                $query->with(['user', 'likes' => function ($query) use ($requester) {
                    $query->where('UserID', $requester);
                }, 'retweets' => function ($query) use ($requester) {
                    $query->where('UserID', $requester);
                }]);
            }])
            ->get();

        // Transform the collection
        $posts = $likedPosts->map(function ($like) use ($requester) {
            $post = $like->tweet;
            $post->liked = $post->likes->where('UserID', $requester)->isNotEmpty();
            $post->likeid = $post->liked ? $post->likes->first()->LikeID : null;
            $post->isRetweed = $post->retweets->where('UserID', $requester)->isNotEmpty();
            $post->retweetid = $post->isRetweed ? $post->retweets->first()->RetweetID : null;
            unset($post->likes, $post->retweets);
            return $post;
        });
        // Check if $requester is following $id
        $isFollowing = Follows::where('FollowerID', $requester)->where('FollowingID', $id)->exists();

        // Check if $id is following $requester
        $isFollowed = Follows::where('FollowingID', $requester)->where('FollowerID', $id)->exists();

        return response()->json(['posts' => $posts, 'isFollowing' => $isFollowing, 'isFollowed' => $isFollowed]);
    }

    public function userExist($username)
    {
        return (Users::where('Username', $username)->first() == null) ? abort(404) : response()->json(['user' => Users::where('Username', $username)->first()]);
    }
}
