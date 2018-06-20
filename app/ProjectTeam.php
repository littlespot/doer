<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;
use Auth;

class ProjectTeam extends Model
{
    public $incrementing = false;
    protected $fillable = ['id','project_id', 'user_id', 'outsider_id'];

    public function user()
    {
        return $this->belongsTo('Zoomov\User')
            ->select('id','username')->first();
    }

    public function occupation()
    {
        return $this->hasMany('Zoomov\ProjectTeamOccupation')
            ->join('occupations', 'occupations.id', '=', 'occupation_id')
            ->selectRaw("occupation_id, name_".app()->getLocale()." as name, 0 as old, project_team_id");
    }
}
