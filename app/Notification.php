<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['title','body'];
}
