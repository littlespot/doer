<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;
use Auth;

class ProjectRecruitment extends Model
{
    public $incrementing = false;
    protected $fillable = ['id','occupation_id', 'project_id', 'description', 'quantity'];
}
