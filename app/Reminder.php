<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;
use Config;

class Reminder extends Model
{
    public $timestamps = false;

    protected $fillable = ['body', 'subject', 'project_id', 'sender_id'];

    public function receivers(){
        return $this->hasMany('Zoomov\ReminderPlaceholder')
            ->where('placeholder_id', config('constants.messageplaceholder.inbox'))
            ->join('users', 'user_id', '=', 'users.id')
            ->select('reminder_placeholders.id', 'user_id', 'username', 'reminder_placeholders.reminder_id', 'checked');
    }
}
