<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class ProjectCommentSupport extends Model
{
    public $timestamps = false;
    public $fillable = ['project_comment_id', 'user_id'];
}
