<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Replies extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'Replies'; // Specify your table name
    protected $primaryKey = 'ReplyID'; // Specify your primary key column name

    protected $fillable = ['TweetID', 'ParentReplyID', 'ReplyContent', 'LikesCount', 'RetweetsCount', 'RepliesCount', 'UserID'];

    public function user()
    {
        return $this->belongsTo(Users::class, 'UserID');
    }
}
