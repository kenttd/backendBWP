<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Likes extends Model
{
    protected $table = 'Likes'; // Specify your table name
    protected $primaryKey = 'UserID'; // Specify your primary key column name
    public $timestamps = false; // Disable timestamps
    public function user()
    {
        return $this->belongsTo(Users::class, 'UserID');
    }

    public function tweet()
    {
        return $this->belongsTo(Tweets::class, 'TweetID');
    }
}
