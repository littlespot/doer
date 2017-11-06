<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class QuestionAnswerSupport extends Model
{
    public $timestamps = false;
    protected $fillable = ['question_answer_id', 'user_id'];
}
