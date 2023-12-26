<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use League\CommonMark\Extension\Mention\Mention;

class Tweets extends Model
{
    protected $table = 'Tweet'; // Specify your table name
    protected $primaryKey = 'TweetID'; // Specify your primary key column name
    public $timestamps = true; // Disable timestamps
    protected $fillable = ['UserID', 'TweetContent', 'LikesCount', 'RetweetsCount', 'RepliesCount'];
    public function user()
    {
        return $this->belongsTo(Users::class, 'UserID');
    }

    public function likes()
    {
        return $this->hasMany(Likes::class, 'TweetID');
    }

    public function hashtags()
    {
        return $this->hasMany(Hashtags::class, 'TweetID');
    }

    public function mentions()
    {
        return $this->hasMany(Mention::class, 'TweetID');
    }
    public function retweets()
    {
        return $this->hasMany(Retweets::class, 'TweetID');
    }
}
