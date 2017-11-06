<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class QuestionFollower extends Model
{
    public $timestamps = false;
    protected $fillable = ['question_id', 'user_id', 'created_at'];
}
