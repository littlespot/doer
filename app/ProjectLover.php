<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class ProjectLover extends Model
{
    public $timestamps = false;
    protected $fillable = ['project_id', 'user_id'];
}
