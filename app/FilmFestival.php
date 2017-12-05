<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class FilmFestival extends Model
{
    protected $fillable = ['film_id', 'year', 'event', 'city_id', 'competition'];

    public function rewards()
    {
        return $this->hasMany('Zoomov\FilmFestivalReward');
    }
}
