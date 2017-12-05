<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class FilmFestivalReward extends Model
{
    public $timestamps = false;
    protected $fillable = ['film_festival_id', 'reward'];
}
