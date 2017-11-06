<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class ReminderPlaceholder extends Model
{
    protected $fillable = ['checked', 'reminder_id', 'placeholder_id', 'user_id'];
    public function message()
    {
        return $this->belongsTo('Zoomov\Reminder');
    }
}
