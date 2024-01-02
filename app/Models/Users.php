<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Users extends Authenticatable
{
    protected $table = 'Users'; // Specify your table name
    protected $primaryKey = 'UserID'; // Specify your primary key column name

    public $timestamps = true;

    protected $fillable = ['Username', 'Password', 'Email', 'ProfilePicture', 'Bio', 'Followers', 'Following', "isVerified", "isStaff", "isBanned", "code", "link"];
    protected $hidden = ['Password'];

    protected $attributes = [
        'Username' => 'username',
        'Password' => 'password',
        'Email' => 'email',
        'ProfilePicture' => 'profile_picture',
        'Bio' => 'bio',
    ];
    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->Password;
    }
    public function tweets()
    {
        return $this->hasMany(Tweets::class, 'UserID');
    }
    public function following()
    {
        return $this->hasMany(Follows::class, 'FollowerID');
    }

    public function followers()
    {
        return $this->hasMany(Follows::class, 'FollowingID');
    }
    // public function follower()
    // {
    //     return $this->belongsTo(Users::class, 'FollowerID');
    // }

    // public function following()
    // {
    //     return $this->belongsTo(Users::class, 'FollowingID');
    // }

    public function bookmarks()
    {
        return $this->hasMany(Bookmarks::class, 'UserID');
    }
    public function likes()
    {
        return $this->hasMany(Likes::class, 'UserID');
    }
    public function retweets()
    {
        return $this->hasMany(Retweets::class, 'UserID');
    }
}
