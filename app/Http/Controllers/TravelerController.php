<?php

namespace Zoomov\Http\Controllers;

use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;

use Prophecy\Promise\ReturnArgumentPromise;
use Session;
use DB;
use Lang;
use Zoomov\City;
use Zoomov\Genre;
use Zoomov\Occupation;
use Zoomov\Project;
use Zoomov\ProjectComment;
use Zoomov\ProjectLanguage;
use Zoomov\ProjectRecruitment;
use Zoomov\ProjectTeam;
use Zoomov\Question;
use Zoomov\Report;

class TravelerController extends Controller
{
    private $params;
    private $locale;

    function __construct() {
        $this -> params = "projects.id, title, synopsis, projects.genre_id, projects.user_id, projects.city_id, username, duration, countries.sortname,
           IFNULL(reason,'') as recommendation, IFNULL(view.cnt, 0) as views_cnt, IFNULL(follower.cnt, 0) as followers_cnt, IFNULL(comment.cnt, 0) as comments_cnt, IFNULL(lovers.cnt, 0) as lovers_cnt,  
           projects.updated_at, start_at, finish_at, projects.active,
           FLOOR((unix_timestamp(finish_at) - unix_timestamp(now()))/60/60/24) as daterest,
           datediff(finish_at, projects.created_at) as datediff";

        $this->locale = Session::get('locale');
        if(is_null($this->locale)){
            $this->locale = 'en';
            Session::set('locale', 'en');
        }
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'password' => 'required'
        ]);

        $pwd = $request['password'];

        $users = DB::table('travelers')->get();
        $traveler = null;

        foreach ($users as $user){
            if(password_verify($pwd, $user->password)){
                if(!$user->active){
                    return Response(Lang::get('auth.blocked'), 501);
                }
                return $this->updateView($request, $user->id);
            }
        }
        return Response(Lang::get('auth.failed'), 501);
    }

    public function logout(Request $request){
        $request->session()->forget($request->session()->token());
        return redirect()->guest('travel');
    }

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

        return view('traveler.home', ["pictures" => $pictures, "ratio" => $minRatio, "categories" => Genre::select('id', 'name_'.$this->locale.' as name')->get()]);
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
                'projects.id', '=', 'comment.project_id');
        $recommends = $this->selection($projects)
            ->orderBy('projects.updated_at')
            ->orderBy('views_cnt', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        $left = "LEFT JOIN (select reason, project_id from recommendations group by project_id) recommendations on projects.id = recommendations.project_id 
                 LEFT JOIN (select sum(count) as cnt, project_id from project_views group by project_id) view ON projects.id = view.project_id 
                 LEFT JOIN (select count(id) as cnt, project_id from project_followers group by project_id) follower ON projects.id = follower.project_id 
                 LEFT JOIN (select count(id) as cnt, project_id from (select id, project_id from project_comments where deleted = 0) comments group by project_id) comment ON projects.id = comment.project_id 
                 LEFT JOIN (select count(id) as cnt, project_id from project_lovers) lovers ON projects.id = lovers.project_id";
        $query = "(SELECT id, genre_id, updated_at FROM projects p ORDER BY genre_id, updated_at desc) z";
        $query = " (SELECT @genre:= 0) s, (SELECT @randk:= 0) x,".$query;
        $query = " (SELECT id, @rank:=CASE WHEN @genre <> genre_id THEN 1 ELSE @rank + 1 END AS rn, @genre:=genre_id AS clset FROM".$query .") w ";
        $rank = DB::select("SELECT ".$this->params.", genres.name_". $this->locale." as genre_name, cities.name_".$this->locale.
            " as city_name FROM projects inner join users on projects.user_id = users.id inner join genres on projects.genre_id = genres.id 
            inner join cities on projects.city_id = cities.id inner join departments on cities.department_id = departments.id 
            inner join countries on departments.country_id = countries.id ".$left." WHERE projects.active = 1 AND projects.id IN (SELECT id FROM".$query.
            "WHERE rn < 4) ORDER BY projects.updated_at desc");

       return \Response::json(['recommendations' => $recommends, 'latest'=>$rank]);
    }

    public function show($id, Request $request)
    {
        $params = Project::where('projects.active', '>', 0)
            ->join('users', 'users.id', '=', 'projects.user_id')
            ->join('genres', 'projects.genre_id', '=', 'genres.id')
            ->join('cities', 'projects.city_id', '=', 'cities.id')
            ->join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->leftJoin(DB::raw("(select reason, project_id from recommendations group by project_id) recommendations"), 'projects.id', '=', 'recommendations.project_id')
            ->leftJoin(DB::raw("(select sum(count) as cnt, project_id from project_views group by project_id) view"), 'projects.id', '=', 'view.project_id')
            ->leftJoin(DB::raw("(select count(id) as cnt, project_id from project_followers group by project_id) follower"), 'projects.id', '=', 'follower.project_id')
            ->leftJoin(DB::raw("(select count(id) as cnt, project_id from project_lovers group by project_id) lovers"), 'projects.id', '=', 'lovers.project_id')
            ->leftJoin(DB::raw("(select count(id) as cnt, project_id from (select id, project_id from project_comments where deleted = 0) comments group by project_id) comment"),
                'projects.id', '=', 'comment.project_id')
            ->leftJoin(DB::raw("(select count(id) as cnt, project_id from questions group by project_id) questions"), function ($join){
                $join->on('projects.id', '=', 'questions.project_id');
            })
            ->leftJoin(DB::raw("(select count(id) as cnt, genre_id from projects where active = 1 group by genre_id) genres"), function ($join){
                $join->on('projects.genre_id', '=', 'genres.genre_id');
            })
            ->leftJoin(DB::raw("(select count(id) as cnt, project_id from project_teams where project_id = '".$id."') team"), function ($join) {
                $join->on('projects.id', '=', 'team.project_id');
            })
            ->with('scripts', 'sponsor');

        $project = $this->selection($params,
            "false as admin, description, 0 as role, IFNULL(genres.cnt, 0) as genres_cnt, IFNULL(questions.cnt,0) as questions_cnt")
            ->find($id);

        $budget = DB::table('budgets')->where('project_id', $id)->join('budget_types', 'budget_type_id', '=', 'budget_types.id')
            ->select('budgets.quantity', 'budgets.comment', 'budget_types.name_'.$this->locale.' as name')
            ->get();

        $project->budget = $budget;

        $total = 0;

        foreach ($budget as $b){
            $total += $b->quantity;
        }

        $lang = ProjectLanguage::join('languages','language_id','=','languages.id')
            ->select('project_id','language_id','languages.id','name_'.$this->locale.' as name', 'name as code');

        return view('traveler.project', ['project' => $project, 'total' => $total, 'lang' => $lang, 'tab'=>$request->input('tab', 0)]);
    }

    public function filter(Request $request){
        $genre = $request->input('genre',0);
        $city = $request->input('city',0);
        $person = $request->input('person',0);
        $order = $request->input('order', 'updated_at');
        $projects =  Project::where('projects.active', '>', 0)
            ->join('users', 'users.id', '=', 'projects.user_id')
            ->join('genres', 'projects.genre_id', '=', 'genres.id')
            ->join('cities', 'projects.city_id', '=', 'cities.id')
            ->join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->leftJoin(DB::raw("(select reason, project_id from recommendations group by project_id) recommendations"), 'projects.id', '=', 'recommendations.project_id')
            ->leftJoin(DB::raw("(select sum(count) as cnt, project_id from project_views group by project_id) view"), 'projects.id', '=', 'view.project_id')
            ->leftJoin(DB::raw("(select count(id) as cnt, project_id from project_followers group by project_id) follower"), 'projects.id', '=', 'follower.project_id')
            ->leftJoin(DB::raw("(select count(id) as cnt, project_id from project_lovers group by project_id) lovers"), 'projects.id', '=', 'lovers.project_id')
            ->leftJoin(DB::raw("(select count(id) as cnt, project_id from (select id, project_id from project_comments where deleted = 0) comments group by project_id) comment"),
                'projects.id', '=', 'comment.project_id');

        if($person > 0){
            $projects = $projects->join('project_recruitments', function ($join) use($person){
                $join->on('project_recruitments.project_id', '=', 'projects.id')
                    ->where('project_recruitments.occupation_id', '=', $person);
            });
        }
        if($genre > 0){
            $projects = $projects->where('projects.genre_id', $genre);
        }

        if($city > 0){
            $projects = $projects->where('projects.city_id', $city);
        }

        return $projects->selectRaw($this->params.', genres.name_' . $this->locale . ' as genre_name, cities.name_' . $this->locale . ' as city_name')->orderBy('projects.'.$order)->paginate(9);
    }

    public function projects(Request $request)
    {
        $cities = City::whereExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('projects')
                ->where('projects.active', '>', '0')
                ->whereRaw('projects.city_id = cities.id');
        })
            ->join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->select('cities.id', DB::raw('cities.name_'.$this->locale.' as name'), 'countries.sortname')
            ->orderBy('cities.name_'.$this->locale)
            ->get();

        $genres = Genre::select('id', DB::raw('name_'.$this->locale.' as name'), 'ordre')->orderBy('ordre')->get();

        $filter = '{"genre":'.$request->input('genre', 0).', "city":'.$request->input('city', 0).', "person":'.$request->input('person', 0).'}';
        return view('traveler.discover', ["locations"=>$cities, "genres"=>$genres, 'occupations'=>Occupation::all(), "filter"=>$filter]);
    }

    public function event($id){
        $teams = ProjectTeam::where("project_id", $id)
            ->join("users", "project_teams.user_id", "=", "users.id")
            ->join("project_team_occupations", "project_team_id", "=", "project_teams.id")
            ->join("occupations", "occupations.id", "=", "occupation_id")
            ->selectRaw("project_teams.user_id, username, date(project_team_occupations.created_at) as created_at, occupations.name, 't' as type")
            ->orderBy('project_team_occupations.created_at', 'DESC')
            ->get();

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

        return \Response::json(array("reports"=>$reports, "teams"=>$teams, "events"=>$events));
    }

    public function team($id)
    {
        $project = Project::find($id);

        if(is_null($project) || !$project->active)
        {
            return Response("NOT AUTHORIZED", 501);
        }

        return ProjectTeam::where('project_id', $id)
            ->leftJoin(DB::raw("(select users.id, username, concat(cities.name_" . $this->locale . ", '(', sortname, ')') as location 
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

    public function comment($id)
    {
        $project = Project::find($id);

        if(is_null($project) || !$project->active)
        {
            return Response("NOT AUTHORIZED", 501);
        }

        return ProjectComment::where('project_id',$id)
            ->where('deleted', 0)
            ->with('parent')
            ->join('users', 'users.id', '=', 'project_comments.user_id')
            ->leftJoin(DB::raw("(select p.id, message, p.user_id, u.username from project_comments p inner join users u 
                on p.user_id = u.id where p.project_id = '".$id." and p.deleted = 0') parent"),
                'parent.id', '=', 'project_comments.parent_id')
            ->leftJoin(DB::raw("(select count(id) as cnt, project_comment_id from project_comment_supports group by project_comment_id) supports"),
                'supports.project_comment_id', '=', 'project_comments.id')
            ->selectRaw("project_comments.id, project_comments.user_id, users.username,  project_comments.message, project_comments.created_at, 
                parent_id,  FLOOR((unix_timestamp(now()) - unix_timestamp(project_comments.created_at))/60/60/24) <1  as newest,  
                IFNULL(supports.cnt, 0) as supports_cnt")
            ->orderBy('created_at', 'desc')
            ->paginate(12);
    }

    public function question($id)
    {
        return Question::where('project_id', $id)
            ->join('users', 'users.id', '=', 'user_id')
            ->leftJoin(DB::raw("(select count(id) as cnt, question_id from question_answers group by question_id) answer"), function ($join) {
                $join->on('answer.question_id', '=', 'questions.id');
            })
            ->leftJoin(DB::raw("(select count(id) as cnt, question_id from question_followers group by question_id) follower"), function ($join) {
                $join->on('follower.question_id', '=', 'questions.id');
            })
            ->selectRaw("questions.id, subject, content, questions.created_at, questions.updated_at, IFNULL(follower.cnt, 0) as followers_cnt, 0 as mine, user_id, username, IFNULL(answer.cnt, 0) as cnt,
                    FLOOR((unix_timestamp(now()) - unix_timestamp(questions.created_at))/60/60/24) <1  as newest, 0 as myfollow")
            ->orderBy('created_at', 'desc')
            ->paginate(12);
    }

    public function recruitment($id)
    {
        $project = Project::find($id);

        if(is_null($project) || !$project->active)
        {
            return Response("NOT AUTHORIZED", 501);
        }

        return ProjectRecruitment::where('project_id', $id)->join('occupations','occupation_id', '=', 'occupations.id')
            ->selectRaw("project_recruitments.id,project_recruitments.quantity,project_recruitments.description, occupation_id, occupations.name, 0 as application")
            ->get();
    }

    protected function selection($projects, $str=null)
    {
        $this->params.= ', genres.name_'.$this->locale.' as genre_name, cities.name_' .$this->locale. ' as city_name';
        if(!is_null($str)){
            $this->params.=','.$str;
        }

        return $projects->selectRaw( $this -> params);
    }

    private function updateView(Request $request, $id){
        $token = $request->session()->token();

        if (!is_string($token))
        {
            Session::set('_token', uniqid('s'));
        }

        DB::table("traveler_connections")->insert(["traveler_id"=>$id, "ip"=>$this->getUserIP()]);
        DB::update('update travelers set views = views + 1 where id = ?', [$id]);
        Session::set($request->session()->token(), true);

        return Response('OK', 200);
    }

    private function getUserIP()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            return $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            return $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            return $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            return $_SERVER['REMOTE_ADDR'];
        else
            return null;
    }
}
