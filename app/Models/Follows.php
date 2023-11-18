<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Follows extends Authenticatable
{
    protected $table = 'Follows'; // Specify your table name
    protected $primaryKey = 'UserID'; // Specify your primary key column name
    public $timestamps = false; // Disable timestamps
}
