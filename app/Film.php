<?php

namespace Zoomov;
use App;
use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    public $incrementing = false;
    protected $fillable = ['id', 'user_id', 'title', 'title_inter', 'title_latin',  'month', 'year', 'hour', 'day', 'color',
        'screenplay', 'virgin',
        'second', 'minute', 'country_id', 'special','silent', 'mute', 'conlange', 'school', 'school_name', 'music_rights', 'inter_rights',
        'uploaded', 'completed','created_at',
        'festivals', 'diffusion', 'theaters'];

    public function country(){
        return $this->belongsTo('Zoomov\Country', 'country_id')
            ->select('countries.id', 'countries.name_'. App::getLocale().' as name');
    }
}
