<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class ReportCommentSupport extends Model
{
    public $timestamps = false;

    protected $fillable = ['report_comment_id', 'user_id'];
}
