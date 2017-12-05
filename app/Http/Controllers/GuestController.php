<?php

namespace Zoomov\Http\Controllers;
use DB;
use App;
use Zoomov\Budget;
use Zoomov\Project;
use Zoomov\ProjectRecruitment;
use Zoomov\Script;
use Zoomov\Sponsor;

class GuestController extends Controller
{
    public function show($id){
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        if($lang != 'zh' && $lang != 'fr'){
            $lang = 'en';
        }
        App::setLocale($lang);
        $project = Project::join('guests', 'projects.id', '=', 'guests.project_id')
            ->join('users', 'users.id', '=', 'projects.user_id')
            ->join('genres', 'genre_id', '=', 'genres.id')
            ->join('cities', 'projects.city_id', '=', 'cities.id')
            ->join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->join(DB::raw("(select count(id) as cnt, project_id from project_teams group by project_id) team"), function ($join) {
                $join->on('projects.id', '=', 'team.project_id');
            })
            ->leftJoin(DB::raw("(select group_concat(d.name_".$lang." SEPARATOR ' / ') as langs, project_id from project_languages l inner join languages d on language_id = d.id group by project_id) languages"), function ($join){
                $join->on("languages.project_id", "=", "projects.id");
            })
            ->leftJoin(DB::raw("(select count(id) as cnt, genre_id from projects where active = 1 group by genre_id) genres"), function ($join){
                $join->on('projects.genre_id', '=', 'genres.genre_id');
            })
            ->where('guests.code', $id)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('project_teams')
                    ->whereRaw('project_teams.project_id = projects.id and (project_teams.user_id = guests.user_id or project_teams.outsider_id = guests.user_id)');
            })
            ->selectRaw("projects.id, projects.active, title, synopsis, projects.description, projects.user_id, users.username, duration, countries.sortname, projects.genre_id, genres.name_".$lang." as genre, 
                projects.city_id, cities.name_".$lang." as city, projects.updated_at, start_at, finish_at, languages.langs, genres.cnt as genres_cnt, team.cnt as members_cnt,
                FLOOR((unix_timestamp(finish_at) - unix_timestamp(now()))/60/60/24) as daterest, datediff(finish_at, projects.created_at) as datediff")
            ->first();

        if(is_null($project)){
            return view('errors.404');
        }
        else{
            $budget = Budget::where('project_id', $project->id)
                ->get();
            $sponsor = Sponsor::where('project_id', $project->id)
                ->get();
            $scripts = Script::where('project_id', $project->id)
                ->with('authors')
                ->selectRaw('id, link,title,description,DATE_FORMAT(created_at, "%Y-%m-%d") as created_at')
                ->orderBy('created_at', 'desc')
                ->get();

            $recruit = ProjectRecruitment::where('project_id', $project->id)
                ->join('occupations', 'project_recruitments.occupation_id', '=', 'occupations.id')
                ->select( 'quantity', 'project_recruitments.description', 'occupations.name_'.App::getLocale().' as name')
                ->get();

            $project->recruit = $recruit;
            $project->scripts = $scripts;
            $project->budget = $budget;
            $project->sponsor = $sponsor;
            return view('guest', $project);
        }
    }
}
