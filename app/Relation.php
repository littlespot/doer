<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class Relation extends Model
{
    protected $fillable = ['fan_id', 'idol_id', 'love'];

    public function idol()
    {
        return  $this->belongsTo('Zoomov\User', 'idol_id')->select('username','id', 'presentation', 'city_id');
    }

    public function fan()
    {
        return  $this->belongsTo('Zoomov\User', 'fan_id')->select('username','id', 'presentation', 'city_id');
    }
}
