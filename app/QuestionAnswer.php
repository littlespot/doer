<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class QuestionAnswer extends Model
{
    public $timestamps = false;

    protected $fillable = ['question_id', 'user_id', 'content', 'created_at', 'updated_at'];

    public function supports()
    {
        return $this->hasMany('Zoomov\QuestionAnswerSupports')
            ->join('users', 'user_id', '=', 'users.id')
            ->select('user_id', 'username', 'question_answer_supports.created_at');
    }
}
