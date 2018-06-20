<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class FilmFestival extends Model
{
    protected $fillable = ['film_id', 'year', 'event', 'city_id', 'competition', 'country_id'];
}
