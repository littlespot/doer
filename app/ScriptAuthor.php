<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class ScriptAuthor extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = ['script_id', 'user_id', 'author_id'];
}
