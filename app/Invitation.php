<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;
use Zoomov\User;
use Zoomov\Project;
use Zoomov\Occupation;
class Invitation extends Model
{
    public $timestamps = false;
  //  protected $appends = ['occupation', 'project'];
   protected $fillable = ['message', 'sender_id','receiver_id','occupation_id', 'project_id', 'quit'];

    public function placeholder()
    {
        return $this->hasMany('Zoomov\Placeholder');
    }

     public function getProjectAttribute()
    {
        return Project::select(['id','title'])->find($this->project_id);
    }
    
    public function getOccupationAttribute()
    {
        return Occupation::select(['id','name'])->find($this->occupation_id);
    }
}
