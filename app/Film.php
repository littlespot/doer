<?php

namespace Zoomov;
use App;
use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    public $incrementing = false;
    protected $fillable = ['id', 'user_id', 'title', 'title_inter', 'title_latin', 'completed', 'created_at', 'month', 'year', 'hour','music_original', 'screenplay_original', 'color',
        'second', 'minute', 'country_id', 'dialog', 'special','silent', 'language', 'virgin', 'school', 'school_name', 'music_rights', 'inter_rights', 'festivals', 'diffusion', 'theaters'];

    public function country(){
        return $this->belongsTo('Zoomov\Country', 'country_id')
            ->select('countries.id', 'countries.name_'. App::getLocale().' as name');
    }
}
