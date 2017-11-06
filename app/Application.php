<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    //protected $appends = ['sender','receiver','occupation', 'project'];
 //   protected $visible = ['id', 'motivation', 'accepted','sender','receiver', 'recruitment', 'created_at'];

    protected $fillable = ['motivation', 'sender_id','receiver_id', 'project_recruitment_id'];

    public function placeholder()
    {
        return $this->hasMany('Zoomov\Placeholder');
    }

    public function getSenderAttribute()
    {
        return User::select(['id','username'])->find($this->sender_id);
    }

    public function getReceiverAttribute()
    {
        return User::select(['id','username'])->find($this->receiver_id);
    }

    public function getRecruitmentAttribute()
    {
        return ProjectRecruitment::select(['id','occupation_id','project_id'])->find($this->project_recruitment_id);
    }

    public function getProjectAttribute()
    {
        return Project::select(['id','title'])->find($this->recruitment->project_id);
    }

    public function getOccupationAttribute()
    {
        return Occupation::select(['id','name'])->find($this->recruitment->occupation_id);
    }
}
