<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public $timestamps = false;
    protected $fillable = ['label', 'user_id', 'created_at'];
}
