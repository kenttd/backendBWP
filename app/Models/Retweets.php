<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Retweets extends Model
{
    use SoftDeletes;
    protected $table = 'Retweet'; // Specify your table name
    protected $primaryKey = 'RetweetID'; // Specify your primary key column name
    protected $fillable = ['UserID', 'TweetID', 'TimeStamp'];
}
