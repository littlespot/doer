<?php

namespace Zoomov\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use DB;
use Lang;
use Zoomov\Genre;
use Zoomov\Project;
use Zoomov\User;

class HomeController extends VisitController
{
    public function display(){
        $minRatio = 0;
        $pictures = "";
        $handle = opendir(public_path('/context/carousel'));
        while (false !== ($file = readdir($handle))) {
            list($filesname,$kzm)=explode(".",$file);
            if(strcasecmp($kzm,"gif")==0 or strcasecmp($kzm, "jpg")==0 or strcasecmp($kzm, "png")==0)
            {
                if (!is_dir('./'.$file)) {
                    list($width, $height) = getimagesize(public_path('/context/carousel/').$file);
                    $ratio = round($height/$width,3);
                    if($minRatio >  $ratio){
                        $minRatio = $ratio;
                    }
                }

                $pictures .= $file.',';
            }
        }

        $counts = Project::where('user_id', Auth::id())
            ->groupBy('active')
            ->selectRaw('count(id) as cnt, IFNULL(active, -1) as active')
            ->orderBy('active')
            ->get();

        return view('home', ["counts"=>$counts, "pictures" => $pictures, "ratio" => $minRatio, "categories" => Genre::select('id', 'name_'.Lang::locale().' as name')->get()]);
    }

    public function index(){
        $projects =  Project::where('projects.active', 1)
            ->join('recommendations', 'recommendations.project_id', '=', 'projects.id')
            ->join('users', 'users.id', '=', 'projects.user_id')
            ->join('genres', 'genre_id', '=', 'genres.id')
            ->join('cities', 'projects.city_id', '=', 'cities.id')
            ->join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->leftJoin(DB::raw("(select sum(count) as cnt, project_id from project_views group by project_id) view"), 'projects.id', '=', 'view.project_id')
            ->leftJoin(DB::raw("(select count(id) as cnt, project_id from project_followers group by project_id) follower"), 'projects.id', '=', 'follower.project_id')
            ->leftJoin(DB::raw("(select count(id) as cnt, project_id from project_lovers group by project_id) lovers"), 'projects.id', '=', 'lovers.project_id')
            ->leftJoin(DB::raw("(select count(id) as cnt, project_id from (select id, project_id from project_comments where deleted = 0) comments group by project_id) comment"),
                'projects.id', '=', 'comment.project_id')
            ->leftJoin(DB::raw("(select 1 as follow, project_id from project_followers where user_id = '".Auth::id()."' group by project_id) myfollow"), 'projects.id', '=', 'myfollow.project_id')
            ->leftJoin(DB::raw("(select 1 as love, project_id from project_lovers where user_id = '".Auth::id()."' group by project_id) mylove"), 'projects.id', '=', 'mylove.project_id');
        $recommends = $this->selection($projects)
            ->orderBy('projects.updated_at')
            ->orderBy('views_cnt', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        $left = "LEFT JOIN recommendations on projects.id = recommendations.project_id
                 LEFT JOIN (select sum(count) as cnt, project_id from project_views group by project_id) view ON projects.id = view.project_id 
                 LEFT JOIN (select count(id) as cnt, project_id from project_followers group by project_id) follower ON projects.id = follower.project_id 
                 LEFT JOIN (select count(id) as cnt, project_id from (select id, project_id from project_comments where deleted = 0) comments group by project_id) comment ON projects.id = comment.project_id 
                 LEFT JOIN (select count(id) as cnt, project_id from project_lovers  group by project_id) lovers ON projects.id = lovers.project_id 
                 LEFT JOIN (select 1 as follow, project_id from project_followers where user_id = '".Auth::id()."' group by project_id) myfollow ON projects.id = myfollow.project_id 
                 LEFT JOIN (select 1 as love, project_id from project_lovers where user_id = '".Auth::id()."' group by project_id) mylove ON projects.id = mylove.project_id";
        $query = "(SELECT id, genre_id, updated_at FROM projects p ORDER BY genre_id, updated_at desc) z";
        $query = " (SELECT @genre:= 0) s, (SELECT @rank:= 0) x,".$query;
        $query = " (SELECT id, @rank:=CASE WHEN @genre <> genre_id THEN 1 ELSE @rank + 1 END AS rn, @genre:=genre_id AS clset FROM".$query .") w ";
        $rank = DB::select("SELECT ".$this->params.", genres.name_". Auth::user()->locale." as genre_name, cities.name_". Auth::user()->locale .
            " as city_name FROM projects inner join users on projects.user_id = users.id inner join genres on projects.genre_id = genres.id 
            inner join cities on projects.city_id = cities.id inner join departments on cities.department_id = departments.id 
            inner join countries on departments.country_id = countries.id ".$left." WHERE projects.active = 1 AND projects.id IN (SELECT id FROM".$query.
            "WHERE rn < 4) ORDER BY projects.updated_at desc");

        return \Response::json(['recommendations' => $recommends, 'latest'=>$rank]);
    }

    public function search($key){
        return Project::where('projects.active', '>', '0')
            ->join('users', 'user_id', '=', 'users.id')
            ->where('projects.title','like','%'.$key.'%')
            ->selectRaw('projects.id, title, username as description, concat("projects", "/", projects.id) as image')
            ->get()
            ->union(User::where('active', '>', '0')->where('username','like','%'.$key.'%') ->join('cities', 'city_id', '=', 'cities.id')
                ->selectRaw('users.id, username as title, name_'.app()->getLocale().' as description, concat("avatars", "/", users.id) as image')
                ->get());

    }
}