<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class DirectMessages extends Model
{
    protected $table = 'DirectMessages'; // Specify your table name
    protected $primaryKey = 'MessageID'; // Specify your primary key column name
    public $timestamps = false; // Disable timestamps

    public static function getLatestMessages($requesterId)
    {
        $sub = self::select(DB::raw('LEAST(SenderID, ReceiverID) as User1'), DB::raw('GREATEST(SenderID, ReceiverID) as User2'), DB::raw('MAX(Timestamp) as MaxTimestamp'))
            ->where(function ($query) use ($requesterId) {
                $query->where('SenderID', $requesterId)
                    ->orWhere('ReceiverID', $requesterId);
            })
            ->groupBy('User1', 'User2');

        $latestMessages = self::joinSub($sub, 'sub', function ($join) {
            $join->on('DirectMessages.SenderID', '=', DB::raw('LEAST(sub.User1, sub.User2)'))
                ->on('DirectMessages.ReceiverID', '=', DB::raw('GREATEST(sub.User1, sub.User2)'))
                ->on('DirectMessages.Timestamp', '=', 'sub.MaxTimestamp');
        })
            ->join('Users as Sender', 'DirectMessages.SenderID', '=', 'Sender.UserID') // Join with Users table for sender
            ->join('Users as Receiver', 'DirectMessages.ReceiverID', '=', 'Receiver.UserID') // Join with Users table for receiver
            ->select('DirectMessages.MessageContent', 'sub.MaxTimestamp', 'Sender.Username as senderName', 'Receiver.Username as receiverName') // Select only the columns that are part of the GROUP BY clause or are used with an aggregate function
            ->orderby("MaxTimestamp", "desc")
            ->get(); // Use get() instead of first()

        return $latestMessages;
    }
}
