<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class MessagePlaceholder extends Model
{
    protected $fillable = ['message_id', 'checked','placeholder_id', 'user_id', 'parent_id'];
    public function message()
    {
        return $this->belongsTo('Zoomov\Message')->select('id','body');
    }
}
