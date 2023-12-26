<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Users extends Authenticatable
{
    use Notifiable, HasApiTokens;
    protected $table = 'Users'; // Specify your table name
    protected $primaryKey = 'UserID'; // Specify your primary key column name

    public $timestamps = false; // Disable timestamps

    protected $fillable = ['Username', 'Password', 'Email', 'ProfilePicture', 'Bio', 'created_at', 'Followers', 'Following', 'access_key'];
    protected $hidden = ['Password'];

    protected $attributes = [
        'Username' => 'username',
        'Password' => 'password',
        'Email' => 'email',
        'ProfilePicture' => 'profile_picture',
        'Bio' => 'bio',
        'created_at' => 'created_at',
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

    public function follows()
    {
        return $this->hasMany(Follows::class, 'FollowerID');
    }
    public function follower()
    {
        return $this->belongsTo(Users::class, 'FollowerID');
    }

    public function following()
    {
        return $this->belongsTo(Users::class, 'FollowingID');
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmarks::class, 'UserID');
    }
    public function likes()
    {
        return $this->hasMany(Likes::class, 'UserID');
    }
}
