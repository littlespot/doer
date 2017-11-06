<?php

namespace Zoomov;
use Auth;
use Zoomov\Project;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    protected $fillable = ['project_id', 'quantity', 'user_id', 'sponsor_name', 'sponsed_at'];
}
