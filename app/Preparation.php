<?php

namespace Zoomov;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Zoomov\Department;

class Preparation extends Model
{
    public $incrementing = false;
    protected $fillable = ['id', 'title', 'synopsis', 'duration', 'description', 'user_id', 'city_id', 'genre_id', 'languages', 'finish_at', 'created_at'];
    protected $appends = ['admin'];

    public function genre()
    {
        return $this->belongsTo('Zoomov\Genre')->select('name');
    }

    public function user()
    {
        return $this->belongsTo('Zoomov\User');
    }
    public function team()
    {
        return $this->hasMany('Zoomov\ProjectTeam', 'project_id', 'id');
    }

    public function recruit()
    {
        return $this->hasMany('Zoomov\ProjectRecruitment', 'project_id', 'id');
    }

    public function scripts()
    {
        return $this->hasMany('Zoomov\Script' , 'project_id', 'id');
    }

    public function budget()
    {
        return $this->hasMany('Zoomov\Budget', 'project_id', 'id');
    }

    public function sponsor()
    {
        return $this->hasMany('Zoomov\Sponsor', 'project_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo('Zoomov\City','city_id','id');
    }

    public function getAdminAttribute()
    {
        return $this->user_id == Auth::User()->id;
    }
}
