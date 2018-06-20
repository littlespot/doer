<?php

namespace Zoomov\Http\Controllers;

use Auth;
use DB;
use Config;
use Zoomov\Event;
use Zoomov\Guest;
use Zoomov\Notification;
use Zoomov\User;
use Zoomov\Outsiderauthor;
use Zoomov\ProjectTeam;
use Zoomov\ProjectTeamOccupation;
use Zoomov\Script;
use Zoomov\ScriptAuthor;
use Zoomov\Mail\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ScriptController extends EventRelatedController
{

    public function update($id, Request $request)
    {
        try {
            $script = Script::find($id);
            $project =$this->getProject($script->project_id);
            if(is_null($project)) {
                return Response('NOT AUTHORIZED', 501);
            }

            $changed = false;
            $oldTitle = $script->title;
            $oldLink = "<a href='".$script->link."' target='_blank'>".$script->title ."(".str_limit($script->created_at, 10, '').")</a>";
            if($script->title != $request->title){
                $script->title = $request->title;
                $changed |= true;
            }

            if($script->link != $request->link)
            {
                $script->link = $request->link;
                $changed |= true;
            }

            if($script->description != $request->description)
            {
                $script->description = $request->description;
                $changed |= true;
            }

            $date = date("Y-m-d", strtotime($request->created_at.'+1 day'));

            if($script->created_at != $date)
            {
                $script->created_at = $date;
                $changed |= true;
            }

            if($changed){
                $script->save();
            }

            $alreadyAuthors = ScriptAuthor::where("script_id",$script->id)
                ->selectRaw("IFNULL(user_id, author_id) as id")
                ->distinct()
                ->pluck('id')
                ->all();

            $authors = explode(",", $request->newAuthor);

            $toRemove = array_diff($alreadyAuthors, $authors);

            if(sizeof($toRemove) > 0){
                DB::table('script_authors')->where("script_id",$script->id)
                    ->whereNull('user_id')
                    ->whereIn('author_id', $toRemove)
                    ->delete();

                DB::table('script_authors')->where("script_id", $script->id)
                    ->whereNull('author_id')
                    ->whereIn('user_id', $toRemove)
                    ->delete();
            }

            $toAdd = array_diff($authors, $alreadyAuthors);

            if(sizeof($toAdd) > 0){
                $team = ProjectTeam::where('project_id', $script->project_id)
                    ->selectRaw('id, user_id, outsider_id')
                    ->get();

                $notification = null;

                foreach ($toAdd as $author) {
                    if ($author[0] == 'o'){
                        ScriptAuthor::create([
                            "author_id" => $author,
                            "script_id" => $script->id,
                        ]);

                        $member = $team->where('outsider_id', $author)->first();

                        if($project->active == 1){
                            $this->sendMail($project,  Outsiderauthor::find($author), $script, 'creation');
                        }

                        if (is_null($member)) {
                            $member = ProjectTeam::create([
                                "id" => $this->uuid('t'),
                                "project_id" => $script->project_id,
                                "outsider_id" => $author
                            ]);

                            ProjectTeamOccupation::create([
                                "project_team_id" => $member->id,
                                "occupation_id" => $this->writer
                            ]);
                        } elseif (is_null(ProjectTeamOccupation::where('project_team_id', $member->id)->where('occupation_id', $this->writer)->select('id')->first())) {
                            ProjectTeamOccupation::create([
                                "project_team_id" => $member->id,
                                "occupation_id" => $this->writer
                            ]);
                        }
                    }
                    elseif(is_null($project->active)){
                        ScriptAuthor::create([
                            "user_id" => $author,
                            "script_id" => $script->id,
                        ]);

                        $member = $team->where('user_id', $author)->first();

                        if (is_null($member)) {
                            $member = ProjectTeam::create([
                                "id" => $this->uuid('t'),
                                "project_id" => $script->project_id,
                                "user_id" => $author
                            ]);

                            ProjectTeamOccupation::create([
                                "project_team_id" => $member->id,
                                "occupation_id" => $this->writer
                            ]);
                        } elseif (is_null(ProjectTeamOccupation::where('project_team_id', $member->id)->where('occupation_id', $this->writer)->select('id')->first())) {
                            ProjectTeamOccupation::create([
                                "project_team_id" => $member->id,
                                "occupation_id" => $this->writer
                            ]);
                        }
                    }
                    else if($project->active == 1){
                        $member = $team->where('user_id', $author)->first();

                        if (is_null($member)) {
                            return Response('NOT AUTHORIZED', 501);
                        }

                        ScriptAuthor::create([
                            "user_id" => $author,
                            "script_id" => $script->id,
                        ]);

                        if (is_null(ProjectTeamOccupation::where('project_team_id', $member->id)->where('occupation_id', $this->writer)->select('id')->first())) {
                            ProjectTeamOccupation::create([
                                "project_team_id" => $member->id,
                                "occupation_id" => $this->writer
                            ]);
                        }

                        if(is_null($notification)){
                            $notification = Notification::create([
                                'title'=>trans('notification.title.add_script'),
                                'body'=>trans('notification.body.add_script', ['script'=>$script->title, 'project'=>$project->title, 'user'=>Auth::user()->username])
                            ]);
                        }

                        DB::table('notification_receivers')->insert(['notification_id'=>$notification->id, 'user_id'=>$author]);
                    }
                }
            }

      /*      if($project->active == 1){
                $authors = '';
                $newLink = "<a href='".$script->link."' target='_blank'>".$script->title ."(".str_limit($script->created_at, 10, '').")</a>";

                $users = User::whereIn('id', $toRemove)->selectRaw("id, username as name, email, locale, concat('/profile/', id) as link")
                    ->union(Outsiderauthor::whereIn('id', $toRemove)->selectRaw("id, name, email, '".app()->getLocale()."' as locale, link"))->get();

                foreach($users as $user) {
                    $authors .= "<a class='deleted' href='".$user->link."' target='_blank'>".$user->name."</a>".",";
                    $this->sendMail($project, $user, $oldTitle, $oldLink, $newLink, 'oldAuthor');
                }

                $users = User::whereIn('id', $toAdd)->selectRaw("id, username as name, email, locale, concat('/profile/', id) as link")
                    ->union(Outsiderauthor::whereIn('id', $toAdd)->selectRaw("id, name, email, '".app()->getLocale()."' as locale, link"))->get();

                foreach($users as $user) {
                    $authors .= "<a class='added' href='".$user->link."' target='_blank'>".$user->name."</a>".",";
                    $team = ProjectTeam::where('project_id', $project->id)->where('outsider_id', $user->id)->first();
                    if(is_null($team)){
                        $team = ProjectTeam::create([
                            "id" => $this->uuid('t'),
                            "project_id" => $script->project_id,
                            "outsider_id" => $user->id
                        ]);
                    }

                    $occupation = ProjectTeamOccupation::where('project_team_id', $team->id)->where('occupation_id', $this->writer)->first();
                    if(is_null($occupation))
                    {
                        ProjectTeamOccupation::create([
                            "project_team_id" => $team->id,
                            "occupation_id" => $this->writer
                        ]);
                    }

                    $this->sendMail($project, $user, $oldTitle, $oldLink, $newLink, 'newAuthor');
                }

                $stillAuthor = array_diff($alreadyAuthors, $toRemove);
                $users = User::whereIn('id', $stillAuthor)->selectRaw('id, username as name, email, locale')
                    ->union(Outsiderauthor::whereIn('id', $stillAuthor)->selectRaw("id, name, email, '".app()->getLocale()."' as locale"))->get();

                foreach ($users as $user){
                    $this->sendMail($project, $user, $oldTitle, $oldLink, $newLink, 'modification');
                }

                $changement = $this->getEvent($id, 's');
                if(strlen($authors) > 0){
                    $changement["content"] = rtrim($authors,",");
                }

                $changement["title"] = $newLink;

                if($changement["event_id"]){
                    DB::table("changments")->insert($changement);
                }
                else{
                    Event::create([
                        "project_id" => $script->project_id,
                        "title" => $newLink,
                        "user_id" => auth()->id(),
                        "username" => Auth::user()->username,
                        "content" => $changement["content"],
                        "related_id" => $script->id,
                        "type" => "s"
                    ]);
                }
            }*/

            return $script;

        }catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function store(Request $request)
    {
        $project =$this->getProject($request->project_id);

        if(is_null($project)) {
            return Response('NOT AUTHORIZED', 501);
        }

        try {
            $script = Script::create([
                "project_id" => $request->project_id,
                "title" => $request->title,
                "link" => $request->link,
                "description" => $request->description,
                "created_at" => date("Y-m-d", strtotime($request->created_at.'+1 day'))
            ]);

            $newAuthors = explode(',', $request->newAuthor);

            if(!$this->addAuthor($script, $newAuthors, $project)){
                DB::table('script_authors')->where('script_id', $script->id)->delete();
                $script->delete();
                return Response('NOT AUTHORIZED', 501);
            }

            $newLink = "<a href='".$script->link."' target='_blank'>".$script->title ."(".str_limit($script->created_at, 10, '').")</a>";

            if($project->active == 1){
                $users = User::whereIn('id', $newAuthors)->selectRaw('id, username as name, email, locale')
                    ->union(Outsiderauthor::whereIn('id', $newAuthors)->selectRaw("id, name, email, '".app()->getLocale()."' as locale"))->get();

                $authors = '';

                foreach ($users as $user){
                    $authors .= "<a href='".$user->link."' target='_blank'>".$user->name."</a>".",";

                    //$this->sendMail($project, $user, $script->title,'creation');
                }

                Event::create([
                    'project_id' => $project->id,
                    'user_id' => auth()->id(),
                    'username' => Auth::user()->username,
                    'title' => $newLink,
                    'content' => rtrim($authors,","),
                    'type' => 's',
                    'related_id' => $script->id
                ]);
            }

            return $script;
        }catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($id, Request $request)
    {
        try {
            $script = Script::find($id);

            $project =$this->getProject($script->project_id);

            if(is_null($project)) {
                return Response('NOT AUTHORIZED', 501);
            }

            $notification = null;
            $authors = ScriptAuthor::where('script_id', $script->id)
                ->leftJoin('users', 'users.id', '=', 'user_id')
                ->leftJoin('outsiderauthors', 'outsiderauthors.id', '=', 'author_id')
                ->selectRaw("IFNULL(users.id, outsiderauthors.id) as id, IFNULL(username, outsiderauthors.name) as name, IFNULL(users.email, outsiderauthors.email) as email, 
                    IFNULL(users.locale, '".app()->getLocale()."') as locale,
                    CASE WHEN users.id is null THEN outsiderauthors.link ELSE concat('/profile/', users.id) END as link")
                ->get();

            if($request->author && is_null($project->active)){
                $others = ScriptAuthor::where('script_id','<>', $script->id)
                    ->selectRaw("IFNULL(user_id, author_id) as id")
                    ->pluck('id')
                    ->all();

                $toRemove = array_diff($authors->pluck('id')->all(), $others);

                $members =ProjectTeam::where('project_id', $script->project_id)
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('project_team_occupations')
                            ->whereRaw('project_team_occupations.occupation_id <>'.$this->writer.' and project_team_occupations.project_team_id = project_teams.id');
                    })
                    ->selectRaw('id, IFNULL(user_id, outsider_id) as author_id')
                    ->get();

                $teams = $members->whereIn('author_id', $toRemove)->pluck('id')->all();
                DB::table('project_team_occupations')->whereIn('project_team_id', $teams)->delete();
                DB::table('project_teams')->whereIn('id', $teams)->delete();
            }

            DB::table('script_authors')->where('script_id', $id)->delete();

            if($project->active == 1){
                foreach ($authors as $user){
                    if ($user->id[0] == 'o'){
                        $this->sendMail($project, $user, $script,'suppression');
                    }
                    else{
                        if(is_null($notification)){
                            $notification = Notification::create([
                                'title'=>trans('notification.title.delete_script'),
                                'body'=>trans('notification.body.delete_script', ['script'=>$script->title, 'project'=>$project->title, 'user'=>Auth::user()->username])
                            ]);
                        }

                        DB::table('notification_receivers')->insert(['notification_id'=>$notification->id, 'user_id'=>$user->id]);

                    }
                }
                DB::table('events')->whereRaw("related_id = ".$id." and type = 's'")->delete();
             //   DB::table('events')->whereRaw("related_id = ".$id." and type = 's'")->update(['deleted' => '1']);
            }

            $script->delete();

            return Response('OK', 200);
        }catch (Exception $e) {
            return $e->getMessage();
        }
    }

    private function sendMail($project, $user, $script, $mail){
        $guest = Guest::where('project_id', $project->id)->where('user_id', $user->id)->first();

        if (is_null($guest)) {
            $guest = Guest::create([
                "project_id" => $project->id,
                "user_id" => $user->id,
                "code" => $this->uuid('g', 10, 'guest')
            ]);
        }

        Mail::to($user->email)->send(new Author($project, $script, substr($user->id, 0, 1) == 'o' ? $user->name : $user->username, $guest->code,$mail));
    }

    private function addAuthor($script, $authors, $project)
    {
        $team = ProjectTeam::where('project_id', $script->project_id)
            ->selectRaw('id, user_id, outsider_id')
            ->get();

        $notification = null;

        foreach ($authors as $author) {

            if ($author[0] == 'o'){
                ScriptAuthor::create([
                    "author_id" => $author,
                    "script_id" => $script->id,
                ]);

                $member = $team->where('outsider_id', $author)->first();

                if($project->active == 1){
                    $this->sendMail($project, Outsiderauthor::find($author), $script, 'creation');
                }

                if (is_null($member)) {
                    $member = ProjectTeam::create([
                        "id" => $this->uuid('t'),
                        "project_id" => $script->project_id,
                        "outsider_id" => $author
                    ]);

                    ProjectTeamOccupation::create([
                        "project_team_id" => $member->id,
                        "occupation_id" => $this->writer
                    ]);
                } elseif (is_null(ProjectTeamOccupation::where('project_team_id', $member->id)->where('occupation_id', $this->writer)->select('id')->first())) {
                    ProjectTeamOccupation::create([
                        "project_team_id" => $member->id,
                        "occupation_id" => $this->writer
                    ]);
                }
            }
            elseif(is_null($project->active)){
                ScriptAuthor::create([
                    "user_id" => $author,
                    "script_id" => $script->id,
                ]);

                $member = $team->where('user_id', $author)->first();

                if (is_null($member)) {
                    $member = ProjectTeam::create([
                        "id" => $this->uuid('t'),
                        "project_id" => $script->project_id,
                        "user_id" => $author
                    ]);

                    ProjectTeamOccupation::create([
                        "project_team_id" => $member->id,
                        "occupation_id" => $this->writer
                    ]);
                } elseif (is_null(ProjectTeamOccupation::where('project_team_id', $member->id)->where('occupation_id', $this->writer)->select('id')->first())) {
                    ProjectTeamOccupation::create([
                        "project_team_id" => $member->id,
                        "occupation_id" => $this->writer
                    ]);
                }
            }
            else if($project->active == 1){
                $member = $team->where('user_id', $author)->first();

                if (is_null($member)) {
                    return false;
                }

                ScriptAuthor::create([
                    "user_id" => $author,
                    "script_id" => $script->id,
                ]);

                if (is_null(ProjectTeamOccupation::where('project_team_id', $member->id)->where('occupation_id', $this->writer)->select('id')->first())) {
                    ProjectTeamOccupation::create([
                        "project_team_id" => $member->id,
                        "occupation_id" => $this->writer
                    ]);
                }

                if(is_null($notification)){
                    $notification = Notification::create([
                        'title'=>trans('notification.title.add_script'),
                        'body'=>trans('notification.body.add_script', ['script'=>$script->title, 'project'=>$project->title, 'user'=>Auth::user()->username])
                    ]);
                }

                DB::table('notification_receivers')->insert(['notification_id'=>$notification->id, 'user_id'=>$author]);
            }
        }

        return true;
    }
}
