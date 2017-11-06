<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class ProjectFollower extends Model
{
    public $timestamps = false;

    public function project()
    {
        return $this->belongsTo('Zoomov\Project');
    }
}
