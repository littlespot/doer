<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class QuestionTag extends Model
{
    public $timestamps = false;
    protected $fillable = ['question_id', 'tag_id', 'user_id', 'created_at'];
}
