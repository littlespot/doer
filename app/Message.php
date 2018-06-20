<?php

namespace Zoomov;
use Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Message extends Model
{
    public $timestamps = false;
    // protected $appends = ['sender'];
    protected $fillable = ['subject', 'body', 'sender_id'];

    public function outbox()
    {
        return $this->hasMany('Zoomov\MessagePlaceholder')
            ->where('user_id', Auth::user()->id)
            ->where('placeholder_id', config('constants.messageplaceholder.outbox'))
            ->select('id', 'message_id', 'parent_id');
    }

    public function senders(){
        return $this->belongsTo('Zoomov\User', 'sender_id');
    }

    public function replies(){
        return $this->hasMany('Zoomov\MessagePlaceholder', 'parent_id')
            ->where('user_id', Auth::user()->id)
            ->where('message_id', '<>', $this->id)
            ->join('messages', 'messages.id', '=', 'message_id')
            ->join('users', 'messages.sender_id', '=', 'users.id')
            ->selectRaw("message_id, parent_id, body, message_placeholders.created_at, sender_id = '".Auth::user()->id."' as sender, user_id, username")
            ->orderBy('message_placeholders.created_at');
    }

    public function receivers(){
        return $this->hasMany('Zoomov\MessagePlaceholder')
            ->where('placeholder_id', config('constants.messageplaceholder.inbox'))
            ->orWhere('placeholder_id', config('constants.messageplaceholder.trash'))
            ->join('users', 'user_id', '=', 'users.id')
            ->select('message_placeholders.id', 'user_id', 'username', 'message_placeholders.message_id');
    }
    /* public function getSenderAttribute()
     {
         return User::select(['id','username'])->find($this->sender_id);
     }

     public function getReceiverAttribute()
     {
         return User::select(['id','username'])->find($this->receiver_id);
     }*/
}
