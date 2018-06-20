<?php

namespace Zoomov;
use App;
use Illuminate\Database\Eloquent\Model;

class Festival extends Model
{
    public $incrementing = false;

    public function genres()
    {
        return $this->belongsToMany('Zoomov\Genre','festival_genres','festival_id', 'genre_id')
            ->select('genres.name_'.app()->getLocale().' as name', 'genres.id as gid');
    }
}
