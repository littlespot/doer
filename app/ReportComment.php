<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class ReportComment extends Model
{

    protected $fillable = ['report_id', 'user_id', 'message', 'parent_id'];
    public function parent()
    {
        return $this->belongsTo('Zoomov\ReportComment', 'parent_id', 'id')
            ->join('users', 'users.id', '=', 'user_id')
            ->where('deleted', '0')
            ->select('report_comments.id', 'parent_id', 'username', 'message', 'user_id');
    }

    public function support()
    {
        return $this->hasMany('Zoomov\ReportCommentSupport')
            ->join('users', 'users.id', '=', 'user_id')
            ->select('user_id', 'username', 'report_comment_id');
    }
}
