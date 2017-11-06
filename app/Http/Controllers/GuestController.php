<?php

namespace Zoomov\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Zoomov\Project;

class GuestController extends Controller
{
    public function show($id){
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        if($lang != 'zh' && $lang != 'fr'){
            $lang = 'en';
        }

        $project = Project::with('scripts', 'budget', 'sponsor', 'recruit')
            ->join('guests', 'projects.id', '=', 'guests.project_id')
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
            return view('guest', $project);
        }
    }
}
