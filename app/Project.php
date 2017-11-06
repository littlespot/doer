<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Illuminate\Support\Facades\Lang;
use DB;

class Project extends \Eloquent
{
	public $incrementing = false;
    protected $fillable = ['id', 'user_id', 'genre_id', 'city_id', 'title', 'synopsis', 'description', 'duration', 'start_at', 'finish_at'];

    public function city(){
        return $this->belongsTo('Zoomov\City', 'city_id')
            ->join('cities', 'projects.city_id', '=', 'cities.id')
            ->join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->select('cities.id', 'cities.name_'. Lang::locale(), 'sortname');
    }

    public function genre(){
        return $this->belongsTo('Zoomov\Genre')
            ->select('genres.id', 'genres.name_'. Lang::locale() .' as genre_name');
    }

    public function applications()
    {
        return $this->hasManyThrough('Zoomov\Application', 'Zoomov\ProjectRecruitment', 'project_id', 'project_recruitment_id');
    }

    public function lang(){
        return $this->hasMany('Zoomov\ProjectLanguage')->join('languages','language_id','=','languages.id')
            ->select('project_id','language_id','languages.id','languages.name_'.Lang::locale().' as name', 'languages.name as code', 'languages.rank as rank');
    }

    public function team()
    {
        return $this->hasMany('Zoomov\ProjectTeam');
    }

    public function occupation()
    {
        return $this->hasManyThrough('Zoomov\ProjectTeam', 'Zoomov\ProjectTeamOccupation');
    }

    public function recruit()
    {
        return $this->hasMany('Zoomov\ProjectRecruitment')
            ->join('occupations', 'project_recruitments.occupation_id', '=', 'occupations.id')
            ->leftJoin(DB::raw("(select 1 as applied, project_recruitment_id, accepted from applications where sender_id='".Auth::user()->id."') a"), function ($join) {
                $join->on('project_recruitments.id', '=', 'a.project_recruitment_id');
            })
            ->select('project_id','project_recruitments.id', 'quantity', 'project_recruitments.description', 'occupation_id',
                'occupations.name_'.Lang::locale().' as name', 'applied', 'accepted');
    }

    public function scripts()
    {
        return $this->hasMany('Zoomov\Script');
    }

    public function budget()
    {
        return $this->hasMany('Zoomov\Budget');
    }

    public function sponsor()
    {
        return $this->hasMany('Zoomov\Sponsor');
    }

    public function comments()
    {
        return $this->hasMany('Zoomov\ProjectComment');
    }

    public function user()
    {
        return $this->belongsTo('Zoomov\User')->select('id', 'username');
    }

    public function getDateAttribute(){
        $today = date_create(date("Y-m-d"));
        $start = date_create($this->start_at);
        $finish = date_create($this->finish_at);
        $diff1 = date_diff($finish, $start)->format('%a');
        $diff2 = date_diff($today, $start)->format('%a');
        return [round($diff2*100/$diff1),
            date_diff($finish, $today)->format('%a')];
    }
}
