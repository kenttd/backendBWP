<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmarks extends Model
{
    use HasFactory;
    protected $table = 'Bookmarks'; // Specify your table name
    protected $primaryKey = 'BookmarkID'; // Specify your primary key column name
    public $timestamps = true; // Disable timestamps
    protected $fillable = ['UserID', 'TweetID'];
    public function Users()
    {
        return $this->belongsTo(User::class, 'UserID');
    }

    public function Tweet()
    {
        return $this->belongsTo(Tweets::class, 'TweetID');
    }
}
