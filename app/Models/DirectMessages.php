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
        $sub = self::select('SenderID', 'ReceiverID', DB::raw('MAX(Timestamp) as MaxTimestamp'))
            ->groupBy('SenderID', 'ReceiverID');

        $latestMessages = self::joinSub($sub, 'sub', function ($join) {
            $join->on('DirectMessages.SenderID', '=', 'sub.SenderID')
                ->on('DirectMessages.ReceiverID', '=', 'sub.ReceiverID')
                ->on('DirectMessages.Timestamp', '=', 'sub.MaxTimestamp');
        })
            ->join('Users as Sender', 'DirectMessages.SenderID', '=', 'Sender.UserID') // Join with Users table for sender
            ->join('Users as Receiver', 'DirectMessages.ReceiverID', '=', 'Receiver.UserID') // Join with Users table for receiver
            ->where(function ($query) use ($requesterId) {
                $query->where('DirectMessages.SenderID', $requesterId)
                    ->orWhere('DirectMessages.ReceiverID', $requesterId);
            })
            ->select('DirectMessages.*', 'Sender.Username as senderName', 'Receiver.Username as receiverName') // Select sender's and receiver's name
            ->orderby("Timestamp", "desc")->first();

        return $latestMessages;
    }
}
