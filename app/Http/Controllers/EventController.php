<?php

namespace Zoomov\Http\Controllers;
use App;
use Auth;
use DB;
use Illuminate\Auth\Access\Response;
use Zoomov\Event;
use Zoomov\ProjectTeam;
use Zoomov\Report;
use Zoomov\Project;

class EventController extends Controller
{
    public function show($id){
        $project = Project::find($id);

        $teams = ProjectTeam::where("project_id", $id)
            ->join("users", "project_teams.user_id", "=", "users.id")
            ->join("project_team_occupations", "project_team_id", "=", "project_teams.id")
            ->join("occupations", "occupations.id", "=", "occupation_id")
            ->selectRaw("project_teams.user_id, username, date(project_team_occupations.created_at) as created_at, occupations.name_".App::getLocale()." as name, 't' as type")
            ->orderBy('project_team_occupations.created_at', 'DESC')
            ->get();

        if($project->user_id == Auth::id()){
            $reports = Report::with('changements')->where("project_id", $id)
                ->join("users", "reports.user_id", "=", "users.id")
                ->join(DB::raw("(SELECT user_id, GROUP_CONCAT(o.name) AS occupations FROM project_teams AS t1 INNER JOIN project_team_occupations AS t2 
                ON t1.id=t2.project_team_id INNER JOIN occupations o on t2.occupation_id = o.id WHERE t1.project_id = '".$id."' GROUP BY t1.user_id) as roles"), function ($join){
                    $join->on("reports.user_id", "=", "roles.user_id");
                })
                ->leftJoin(DB::raw("(select count(id) as cnt, report_id from report_lovers group by report_id) lovers"), function($join){
                    $join->on('reports.id', '=', 'lovers.report_id');
                })
                ->leftJoin(DB::raw("(select count(id) as cnt, report_id from report_comments group by report_id) comments"), function($join){
                    $join->on('reports.id', '=', 'comments.report_id');
                })
                ->selectRaw("reports.id, title, synopsis, reports.created_at, reports.user_id, username, roles.occupations, 'r' as type, 
                IFNULL(lovers.cnt, 0) as lovers_cnt, IFNULL(comments.cnt, 0) as comments_cnt")
                ->orderBy('reports.created_at', 'DESC')
                ->get();

            $events = Event::where('project_id', $id)
                ->with('changements')
                ->selectRaw('events.id, related_id, title, date(created_at) as created_date, time(created_at) as created_time, user_id, username, content, type, events.deleted')
                ->orderBy('created_date', 'DESC')
                ->orderBy('type')
                ->orderBy('created_time')
                ->get();
        }
        else{
            $reports = Report::where("project_id", $id)
                ->join("users", "reports.user_id", "=", "users.id")
                ->join(DB::raw("(SELECT user_id, GROUP_CONCAT(o.name) AS occupations FROM project_teams AS t1 INNER JOIN project_team_occupations AS t2 
                ON t1.id=t2.project_team_id INNER JOIN occupations o on t2.occupation_id = o.id WHERE t1.project_id = '".$id."' GROUP BY t1.user_id) as roles"), function ($join){
                    $join->on("reports.user_id", "=", "roles.user_id");
                })
                ->leftJoin(DB::raw("(select count(id) as cnt, report_id from report_lovers group by report_id) lovers"), function($join){
                    $join->on('reports.id', '=', 'lovers.report_id');
                })
                ->leftJoin(DB::raw("(select count(id) as cnt, report_id from report_comments group by report_id) comments"), function($join){
                    $join->on('reports.id', '=', 'comments.report_id');
                })
                ->selectRaw("reports.id, title, synopsis, reports.created_at, reports.user_id, username, roles.occupations, 'r' as type, 
                IFNULL(lovers.cnt, 0) as lovers_cnt, IFNULL(comments.cnt, 0) as comments_cnt")
                ->orderBy('reports.created_at', 'DESC')
                ->get();
            $events = [];
        }

        return \Response::json(array("reports"=>$reports, "teams"=>$teams, "events"=>$events));
    }

    public function destroy($id){
        $event = Event::find($id);

        if(is_null($event)){
            return null;
        }

        $project = Project::find($event->project_id);

        if(is_null($project) || $project->user_id != Auth::id()){
            return null;
        }

        $event->delete();

        return Response('OK', 200);
    }
}
