<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Video extends Model
{
    public $incrementing = false;
    protected $fillable = ['id', 'title', 'duration', 'genre_id', 'link', 'description', 'admin_id'];

    public function lang(){
        return $this->hasMany('Zoomov\VideoLanguage')->join('languages','language_id','=','languages.id')
            ->select('video_id','language_id','languages.id','name_'.Auth::user()->locale.' as name', 'name as code');
    }
}
