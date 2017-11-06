<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['title', 'synopsis', 'content', 'project_id', 'user_id'];

    public function tags()
    {
        return $this->hasMany('Zoomov\ReportTag')
            ->join('tags', 'tag_id', '=', 'tags.id')
            ->select('report_id', 'label', 'tags.id');
    }

    public function changements(){
        return $this->hasMany('Zoomov\Changement', 'event_id');
    }
}
