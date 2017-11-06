<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class Outsiderauthor extends Model
{
    public $incrementing = false;
    protected $fillable = ['id','name', 'email', 'link', 'user_id'];
}
