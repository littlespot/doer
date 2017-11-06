<?php

namespace Zoomov;

class City extends \Eloquent
{
    protected $fillable = ['id', 'name_en', 'name_zh'];

    public function department()
    {
        return $this->belongsTo('Zoomov\Department','department_id');
    }

    public function projects()
    {
        return $this->hasMany('Zoomov\Project');
    }

    public function users()
    {
        return $this->hasMany('Zoomov\User');
    }
}
