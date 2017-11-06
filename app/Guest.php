<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected  $fillable = ['project_id', 'user_id', 'code'];
}
