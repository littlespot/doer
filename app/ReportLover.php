<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class ReportLover extends Model
{
    public $timestamps = false;

    protected $fillable = ['report_id', 'user_id'];
}
