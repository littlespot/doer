<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class FilmCastCredit extends Model
{
    public $timestamps = false;
    protected $fillable = ['film_cast_id', 'credit_id'];
}
