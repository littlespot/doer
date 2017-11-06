<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class PotentialWork extends Model
{
    public $timestamps = false;

    public function occupations()
    {
        return $this->hasMany('Zoomov\PotentialWorkOccupation')
            ->join('occupations', 'occupation_id', '=', 'occupations.id')
            ->select('potential_work_id', 'occupation_id', 'occupations.name');
    }
}
