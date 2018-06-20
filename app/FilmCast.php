<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class FilmCast extends Model
{
    protected $fillable = ['id', 'film_id', 'filmaker_id'];
}
