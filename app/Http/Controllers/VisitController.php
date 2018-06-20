<?php

namespace Zoomov\Http\Controllers;

use Illuminate\Http\Request;

use Zoomov\Http\Requests;
use Auth;
use DB;
use Zoomov\Project;
use Zoomov\ProjectComment;
use Zoomov\ProjectFollower;
use Zoomov\ProjectLover;
use Zoomov\ProjectView;

class VisitController extends Controller
{
    protected $params;

    function __construct() {
        $this -> params = "projects.id, title, synopsis, projects.genre_id, projects.user_id, projects.city_id, username, duration, countries.name_".app()->getLocale()." as country,
           IFNULL(recommendations.reason,'') as recommendation, IFNULL(view.cnt, 0) as views_cnt, IFNULL(follower.cnt, 0) as followers_cnt, IFNULL(comment.cnt, 0) as comments_cnt, IFNULL(lovers.cnt, 0) as lovers_cnt,  
           projects.updated_at, start_at, finish_at, projects.active, FLOOR((unix_timestamp(finish_at) - unix_timestamp(now()))/60/60/24) as daterest, datediff(projects.finish_at, projects.created_at) as datediff";
        if(auth()->check()){
            $this->params .= ',IFNULL(myfollow.follow, 0) as myfollow, IFNULL(mylove.love, 0) as mylove';
        }
        else{
            $this->params .= ', 0 as myfollow, 0 as mylove';
        }
    }

    protected function projects($user=null, $active = null)
    {
        $projects = $this->choose();

        if(!is_null($user)){
            $projects = $projects->where('projects.user_id', $user);
        }

        if(!is_null($active)){
            $projects = $projects->whereRaw('projects.active'.$active);
        }

        return $this->selection($projects);
    }

    protected function choose(){
        $projects = Project::join('users', 'users.id', '=', 'projects.user_id')
            ->join('genres', 'projects.genre_id', '=', 'genres.id')
            ->join('cities', 'projects.city_id', '=', 'cities.id')
            ->join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->leftJoin('recommendations', 'projects.id', '=', 'recommendations.project_id')
            ->leftJoin(DB::raw("(select sum(count) as cnt, project_id from project_views group by project_id) view"), 'projects.id', '=', 'view.project_id')
            ->leftJoin(DB::raw("(select count(id) as cnt, project_id from project_followers group by project_id) follower"), 'projects.id', '=', 'follower.project_id')
            ->leftJoin(DB::raw("(select count(id) as cnt, project_id from project_lovers group by project_id) lovers"), 'projects.id', '=', 'lovers.project_id')
            ->leftJoin(DB::raw("(select count(id) as cnt, project_id from (select id, project_id from project_comments where deleted = 0) comments group by project_id) comment"),
                'projects.id', '=', 'comment.project_id');
            if(auth()->check()){
                $projects = $projects ->leftJoin(DB::raw("(select count(id) as follow, project_id from project_followers where user_id = '".auth()->id()."' group by project_id) myfollow"), 'projects.id', '=', 'myfollow.project_id')
                    ->leftJoin(DB::raw("(select count(id) as love, project_id from project_lovers where user_id = '".auth()->id()."' group by project_id) mylove"), 'projects.id', '=', 'mylove.project_id');
            }

            return $projects;
    }

    protected function selection($projects, $str=null)
    {
        $this->params.= ', genres.name_' . app()->getLocale() . ' as genre_name, cities.name_' . app()->getLocale() . ' as city_name';
        if(!is_null($str)){
            $this->params.=','.$str;
        }

        return $projects->selectRaw( $this -> params);
            /* 'projects.id', 'title', 'synopsis', 'genre_id', 'genres.name_' . app()->getLocale() . ' as genre_name', 'projects.user_id', 'username', 'duration',
            'projects.city_id', DB::raw('cities.name_' . app()->getLocale() . ' as city_name'), DB::raw('IF(recommendations.project_id IS NULL, 0, 1) as recommended'),
            'cities.department_id', 'departments.country_id', 'countries.sortname as sortname', DB::raw('IF(myfollow.project_id IS NULL, 0, 1) as myfollow'),
            DB::raw('IFNULL(view.cnt, 0) as views_cnt'), DB::raw('IFNULL(follower.cnt, 0) as followers_cnt'), DB::raw('IFNULL(comment.cnt, 0) as comments_cnt'),
            'projects.updated_at', 'start_at', 'finish_at', 'projects.active',
            DB::raw('FLOOR((unix_timestamp(finish_at) - unix_timestamp(now()))/60/60/24) as daterest'),
            DB::raw('datediff(finish_at, projects.created_at) as datediff'));*/
    }
}
