<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class UserActivation extends Model
{
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['id', 'code', 'active', 'created_at'];
}
