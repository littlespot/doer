<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['project_id','title', 'user_id', 'username', 'content', 'type', 'related_id', 'deleted', 'created_at'];

    public function changements(){
        return $this->hasMany('Zoomov\Changement');
    }
}
