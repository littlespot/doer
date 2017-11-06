<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class ReportTag extends Model
{
    public $timestamps = false;

    protected $fillable = ['report_id', 'tag_id'];
}
