<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class Department extends \Eloquent
{
    protected $visible = ['id', 'name', 'name_zh', 'cities', 'country_id'];

    public function country()
    {
        return $this->belongsTo('Zoomov\Country','country_id');
    }

    public function cities()
    {
        return $this->hasMany('Zoomov\City');
    }
}
