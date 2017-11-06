<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class Potential extends Model
{
    public $timestamps = false;

    public function works()
    {
        return $this->hasMany('Zoomov\PotentialWork')
            ->select('id', 'title', 'description', 'url');
    }
}
