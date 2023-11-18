<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Users extends Authenticatable
{
    use Notifiable;
    protected $table = 'Users'; // Specify your table name
    protected $primaryKey = 'UserID'; // Specify your primary key column name

    public $timestamps = false; // Disable timestamps

    protected $fillable = ['Username', 'Password', 'Email', 'ProfilePicture', 'Bio', 'JoinDate'];
    protected $hidden = ['password'];
    protected $attributes = [
        'Username' => 'username',
        'Password' => 'password',
        'Email' => 'email',
        'ProfilePicture' => 'profile_picture',
        'Bio' => 'bio',
        'JoinDate' => 'join_date',
    ];
}
