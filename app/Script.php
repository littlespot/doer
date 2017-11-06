<?php

namespace Zoomov;
use DB;
use Auth;
use Illuminate\Database\Eloquent\Model;

class Script extends Model
{
    protected $fillable = ['project_id', 'title', 'description', 'link', 'created_at'];

    public function project()
    {
        return $this->belongsTo('Zoomov\Project');
    }

    public function authors()
    {
        return $this->hasMany('Zoomov\ScriptAuthor')
            ->leftJoin('users', 'script_authors.user_id', '=', 'users.id')
            ->leftJoin('outsiderauthors', 'script_authors.author_id', '=', 'outsiderauthors.id')
            ->selectRaw("script_id, script_authors.user_id, IFNULL(users.id, outsiderauthors.id) as id, IFNULL(users.username, outsiderauthors.name) as name, outsiderauthors.email");
    }
}
