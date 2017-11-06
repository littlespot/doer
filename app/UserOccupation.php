<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class UserOccupation extends Model
{
    protected $fillable = ['user_id','occupation_id'];
}
