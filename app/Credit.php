<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    public function cast(){
        return $this->hasMany('Zoomov\FilmCastCredit')
            ->join('film_casts', 'film_cast_id', 'film_casts.id')
            ->join('filmakers', 'filmaker_id', '=', 'filmakers.id')
            ->selectRaw('film_cast_id, credit_id, film_casts.id,filmaker_id, concat(last_name," " ,first_name) as name');
    }
}
