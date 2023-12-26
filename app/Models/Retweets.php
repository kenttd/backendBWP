<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Retweets extends Model
{
    protected $table = 'Retweet'; // Specify your table name
    protected $primaryKey = 'RetweetID'; // Specify your primary key column name
    public $timestamps = true; // Disable timestamps
}
