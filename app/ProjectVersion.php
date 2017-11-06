<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class ProjectVersion extends Model
{
    public $timestamps = false;
    protected $fillable = ['project_id', 'description', 'duration', 'languages'];
}
