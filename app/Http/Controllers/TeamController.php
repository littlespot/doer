<?php

namespace Zoomov\Http\Controllers;

use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;
use DB;
use Auth;
use Zoomov\Changement;
use Zoomov\Event;
use Zoomov\Occupation;
use Zoomov\ProjectTeamOccupation;
use Zoomov\Script;
use Zoomov\Outsiderauthor;
use Zoomov\User;
use Zoomov\ProjectTeam;

class TeamController extends EventRelatedController
{
    public function index()
    {
        return User::where('active', 1)
            ->where('id', '<>', Auth::id())
            ->select('id', 'username')
            ->get();
    }

    public function show($id)
    {
        $projet = $this->getProject($id);

        if (is_null($projet)) {
            return Response("NOT AUTHORIZED", 501);
        }

        return ProjectTeam::where('project_id', $id)
            ->leftJoin(DB::raw("(select users.id, username, concat(cities.name_" . Auth::user()->locale . ", '(', sortname, ')') as location 
                from users inner join cities on city_id = cities.id inner join departments on department_id = departments.id inner join countries on country_id =countries.id) users"), function ($join) {
                $join->on('project_teams.user_id', '=', 'users.id');
            })
            ->leftJoin('outsiderauthors', 'outsider_id', '=', 'outsiderauthors.id')
            ->selectRaw("IFNULL(users.id, outsider_id) as user_id, IFNULL(username, outsiderauthors.name) as username, deleted,
                project_teams.user_id IS NULL as outsider, IFNULL(users.location, outsiderauthors.email) as location, outsiderauthors.link, project_teams.id, project_teams.updated_at")
            ->with('occupation')
            ->orderBy('outsider')
            ->orderBy('project_teams.updated_at', 'desc')
            ->paginate(24);
    }

    public function update($id, Request $request)
    {
        $team = ProjectTeam::find($id);
        $project = $this->getProject($team->project_id);
        if (is_null($project)) {
            return Response("NOT AUTHORIZED", 501);
        }

        $changed = true;

        $olderRoles = ProjectTeamOccupation::where('project_team_id', $id)->pluck('occupation_id')->all();

        $newRols = explode(",", $request->roles);

        $toRemove = array_diff($olderRoles, $newRols);

        $toAdd = array_diff($newRols, $olderRoles);
        $result = '';

        if(($key = array_search($this->planner, $toRemove)) !== false) {
            $result = 'P';
            unset($toRemove[$key]);
        }

        if(($key = array_search($this->planner, $toAdd)) !== false) {
            $result = 'P';
            unset($toAdd[$key]);
        }

        if(($key = array_search($this->writer, $toRemove)) !== false){
            $author = Script::where('project_id', $team->project_id)
                ->join(DB::raw("(select script_id from script_authors where ".($team->user_id ? "user_id = '".$team->user_id: "authoer_id='".$team->outsider_id)."') authors") ,function ($join){
                    $join->on('authors.script_id', '=', 'scripts.id');
                })->exists();

            if($author){
                unset($toRemove[$key]);
                $result.='T';
            }
        }

        if(sizeof($toRemove)){
            $changed = true;
            DB::table('project_team_occupations')->where('project_team_id', $id)->whereIn('occupation_id', $toRemove)->delete();
        }

        $changed |= sizeof($toAdd) > 0;

        foreach ($toAdd as $role){
            ProjectTeamOccupation::create([
                "project_team_id" => $id,
                "occupation_id" => $role
            ]);
        }

        if($changed && $project->active == 1){
            if($team->user_id){
                $user = User::select('id', 'username as name')->find($team->user_id);
            }
            else{
                $user = Outsiderauthor::select('id', 'name')->find($team->outsider_id);
            }

            $removedRoles = Occupation::whereIn('id', $toRemove)->selectRaw("concat('<deleted>', 'name_".Auth::user()->locale."', '</deleted>') as role")->pluck('role')->all();
            $addedRoles = Occupation::whereIn('id', $toAdd)->selectRaw("concat('<added>', 'name_".Auth::user()->locale."', '</added>') as role")->pluck('role')->all();

            Event::create([
                'project_id' => $team->project_id,
                'user_id' => $user->id,//implode(",", $toRemove),
                'username' => $user->name,//implode(",", $toAdd),
                'title' => sizeof($toRemove) ? (sizeof($toAdd) ? "modified" : "removed") : "added",
                'content' => implode(',', $removedRoles) .' '. implode(',', $addedRoles),
                'type' => 't',
                'related_id' => $id
            ]);
        }

        return Response($result, 200);
    }

    public function store(Request $request)
    {
        $project =$this->getProject($request->project_id);
        if (is_null($project)) {
            return Response("NOT AUTHORIZED", 501);
        }

        $user_id = $request->user_id;

        if($project->active == 1 && $user_id && $user_id[0] == 'z'){
            return Response('NOT AFTER SUBMITTED', 502);
        }

        $roles = explode(",", $request->roles);

        if(($key = array_search($this->planner, $roles)) !== false) {
            unset($roles[$key]);
        }

        $oldmember = false;
        if(!$user_id){
            $author = Outsiderauthor::create([
                'id' => $this->uuid('o'),
                'name' => $request->username,
                'link' => $request->link,
                'email' => $request->location,
                'user_id' => Auth::id()
            ]);

            $team = ProjectTeam::create([
                "id" => $this->uuid("t"),
                "project_id" =>$request->project_id,
                "outsider_id" =>$author->id
            ]);
        }
        elseif($user_id[0] == 'z'){
            $team = ProjectTeam::where('project_id', $request->project_id)->where('user_id', $user_id)->first();
            if(is_null($team)){
                $team = ProjectTeam::create([
                    "id" => $this->uuid("t"),
                    "project_id" =>$request->project_id,
                    "user_id" =>$user_id
                ]);
            }
            else{
                $oldmember = true;
            }
        }
        else{
            $team = ProjectTeam::where('project_id', $request->project_id)->where('outsider_id', $user_id)->first();
            if(is_null($team)) {
                $team = ProjectTeam::create([
                    "id" => $this->uuid("t"),
                    "project_id" => $request->project_id,
                    "outsider_id" => $user_id
                ]);
            }
            else{
                $oldmember = true;
            }
        }

        if($oldmember){
            $oldOccupations = ProjectTeamOccupation::where('project_team_id',$team->id)
                ->pluck("occupation_id")
                ->all();

            $roles = array_diff($roles, $oldOccupations);
        }

        foreach ($roles as $role){
            ProjectTeamOccupation::create([
                "project_team_id" => $team->id,
                "occupation_id" => $role
            ]);
        }

        return $team;
    }

    public function destroy($id, Request $request)
    {
        $team = ProjectTeam::find($id);
        $project = $this->getProject($team->project_id);
        if(is_null($project)){
            return Response('NOT AUTHORIZED', 501);
        }

        if($project->active == 1 && $team->user_id){
            return Response('NOT AFTER SUBMITTED', 502);
        }

        if($team->user_id){
            if($team->user_id == Auth::id()){
                return Response('Y', 501);
            }
            if($project->active == 1){
                return Response('P', 501);
            }

            $author = Script::where('project_id', $team->project_id)
                ->join(DB::raw("(select script_id from script_authors where user_id = '".$team->user_id."') authors") ,function ($join){
                    $join->on('authors.script_id', '=', 'scripts.id');
                })->exists();

            if($author){
                return Response('T', 501);
            }

            $user = User::select('id', 'username as name')->find($team->user_id);
        }
        else{
            $author = Script::where('project_id', $team->project_id)
                ->join(DB::raw("(select script_id from script_authors where author_id = '".$team->outsider_id."') authors") ,function ($join){
                    $join->on('authors.script_id', '=', 'scripts.id');
                })->exists();

            if($author){
                return Response('T', 501);
            }

            $user = Outsiderauthor::select('id', 'name')->find($team->outsider_id);
        }

        $occupations = ProjectTeamOccupation::where('project_team_id', $team->id)
            ->join('occupations', 'occupations.id', '=', 'project_team_occupations.occupation_id')
            ->pluck('occupations.name_'.Auth::user()->locale)
            ->all();

        if(!is_null($project->active)){
            Event::create([
                'project_id' => $project->id,
                'user_id' => $user->id,
                'username' => $user->name,
                'title' => implode(',', $occupations),
                'type' => 't',
                'deleted' => 1,
                'related_id' => $team->id,
                'created_at' => $team->created_at
            ]);
        }

        DB::table('project_team_occupations')->where('project_team_id', $id)->delete();

        $team->delete();

        return Response('OK', 200);
    }
}
