<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Likes extends Model
{
    use SoftDeletes;
    protected $table = 'Likes'; // Specify your table name
    protected $primaryKey = 'LikeID'; // Specify your primary key column name
    public $timestamps = true;
    protected $fillable = ['UserID', 'TweetID', 'TimeStamp'];

    public function user()
    {
        return $this->belongsTo(Users::class, 'UserID');
    }

    public function tweet()
    {
        return $this->belongsTo(Tweets::class, 'TweetID');
    }
}
