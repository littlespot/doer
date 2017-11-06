<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class ProjectComment extends Model
{
    protected $fillable = ['project_id', 'user_id', 'parent_id', 'message'] ;
    public $timestamps = false;

    public function parent()
    {
        return $this->belongsTo('Zoomov\ProjectComment', 'parent_id', 'id')
            ->join('users', 'users.id', '=', 'user_id')
            ->where('deleted', '0')
            ->select('project_comments.id', 'parent_id', 'username', 'message', 'user_id');
    }

    public function support()
    {
        return $this->hasMany('Zoomov\ProjectCommentSupport')
            ->join('users', 'users.id', '=', 'user_id')
            ->select('user_id', 'username', 'project_comment_id');
    }
}
