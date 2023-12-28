<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Follows extends Model
{
    protected $table = 'Follows'; // Specify your table name
    protected $primaryKey = 'FollowID'; // Specify your primary key column name
    public $timestamps = false; // Disable timestamps
    public function follower()
    {
        return $this->belongsTo(Users::class, 'FollowerID');
    }

    public function following()
    {
        return $this->belongsTo(Users::class, 'FollowingID');
    }
}
