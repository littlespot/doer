<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class Changement extends Model
{
    protected $fillable = ['event_id','title', 'user_id', 'username', 'content'];
}
