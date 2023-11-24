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

    protected $fillable = ['Username', 'Password', 'Email', 'ProfilePicture', 'Bio', 'JoinDate', 'Followers', 'Following'];
    protected $hidden = ['Password'];

    protected $attributes = [
        'Username' => 'username',
        'Password' => 'password',
        'Email' => 'email',
        'ProfilePicture' => 'profile_picture',
        'Bio' => 'bio',
        'JoinDate' => 'join_date',
    ];
}
