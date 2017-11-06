<?php

namespace Zoomov;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Question extends Model
{
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['id','subject', 'project_id', 'content', 'user_id', 'created_at'];

    public function answers()
    {
        return $this->hasMany('Zoomov\QuestionAnswer')
            ->join('users', 'user_id', '=', 'users.id')
            ->select('content', 'user_id', 'username', 'question_answers.created_at', 'question_id', 'question_answers.id');
    }

    public function tags()
    {
        return $this->hasMany('Zoomov\QuestionTag')
            ->join('tags', 'tag_id', '=', 'tags.id')
            ->select('question_id', 'label', DB::raw('if(question_tags.user_id="'.Auth::user()->id.'", 1, 0) as mine'));
    }

    public function getFollowersAttribute()
    {
        $followers = QuestionFollower::where('question_id', $this->id);

        $user = Auth::user()->id;

        return [$followers->count(), $this->user_id == $user ? null : $followers->where('user_id', Auth::user()->id)->exists()];
    }
}
