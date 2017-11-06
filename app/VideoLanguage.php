<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class VideoLanguage extends Model
{
    public $timestamps = false;
    protected $fillable = ['language_id','video_id'];
}
