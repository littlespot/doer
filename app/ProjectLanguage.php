<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class ProjectLanguage extends Model
{
    public $timestamps = false;
    protected $fillable = ['language_id','project_id'];
}
