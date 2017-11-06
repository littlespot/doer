<?php

namespace Zoomov\Http\Controllers;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Auth;
use DB;
use Storage;
use Validator;
use Zoomov\Budget;
use Zoomov\BudgetType;
use Zoomov\City;

use Zoomov\Genre;
use Zoomov\Language;
use Zoomov\Occupation;
use Zoomov\Outsiderauthor;
use Zoomov\Project;
use Zoomov\ProjectLanguage;
use Zoomov\ProjectLover;
use Zoomov\ProjectTeam;
use Zoomov\ProjectRecruitment;

use Zoomov\ProjectView;
use Zoomov\Question;
use Zoomov\Report;
use Zoomov\ProjectFollower;
use Zoomov\Script;

use Zoomov\Sponsor;
use Zoomov\ProjectComment;

class ProjectController extends VisitController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $cities = City::whereExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('projects')
                ->where('projects.active', '>', '0')
                ->whereRaw('projects.city_id = cities.id');
        })
            ->join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->select('cities.id', DB::raw('cities.name_'.Lang::locale().' as name'), 'countries.sortname')
            ->orderByRaw('convert(cities.name_'.Lang::locale().' using gb2312)')
            ->get();

        $genres = Genre::select('id', DB::raw('name_'.Lang::locale().' as name'), 'ordre')->orderBy('ordre')->get();

        $filter = '{"genre":'.$request->input('genre', 0).', "city":'.$request->input('city', 0).', "person":'.$request->input('person', 0).'}';
        return view('discover', ["locations"=>$cities, "genres"=>$genres, 'occupations'=>Occupation::all(), "filter"=>$filter]);
    }

    public function refresh(Request $request){
        return $this->filter($request->input('genre',0), $request->input('city',0), $request->input('person',0), $request->input('order', 'updated_at'));
    }

    public function search()
    {
        $projects = Project::join('users', 'user_id', '=', 'users.id')
            ->orderByRaw('convert(users.username using gb2312)')
            ->orderByRaw('convert(title using gb2312)')
            ->select('projects.id', 'title', 'user_id', 'username', 'projects.updated_at', 'start_at', 'finish_at',
                DB::raw('datediff(finish_at, projects.created_at) as datediff'))
            ->where('projects.active', '>', 0)
            ->get();

        return \Response::json($projects);
    }

    public function find($item){
        $projects = Project::join('users', 'user_id', '=', 'users.id')
            ->where(DB::raw("projects.active= 1 and (username like '%".$item."%' 
            or title like '%".$item."%')"))
            ->select('projects.id', 'username', 'title')
            ->get();
        return \Response::json($projects);
    }

    public function detail($id, Request $request)
    {

        $project = Project::join('users', 'users.id', '=', 'projects.user_id')
            ->join('genres', 'projects.genre_id', '=', 'genres.id')
            ->join('cities', 'projects.city_id', '=', 'cities.id')
            ->join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->with('scripts', 'budget', 'sponsor', 'lang', 'recruit')
            ->selectRaw( "projects.id, title, synopsis, projects.genre_id, projects.user_id, projects.city_id, username, duration, countries.sortname,
                        genres.name_". Auth::user()->locale ." as genre_name, cities.name_". Auth::user()->locale ." as city_name, projects.updated_at, start_at, finish_at, projects.active,
                        projects.user_id = '".Auth::id()."' as admin, description, FLOOR((unix_timestamp(finish_at) - unix_timestamp(now()))/60/60/24) as daterest,
                        datediff(finish_at, projects.created_at) as datediff")
            ->find($id);

        $genres_cnt = Project::where('active', '>', 0)->where('genre_id', $project->genre_id)->count();

        if($project->active) {
            $views = ProjectView::where('project_id', $id)->get();
            $followers = ProjectFollower::where('project_id', $id)->select('user_id')->pluck('user_id')->toArray();
            $lovers = ProjectLover::where('project_id', $id)->select('user_id')->pluck('user_id')->toArray();
            $questions_cnt = Question::where('project_id', $id)->count();
            $comments_cnt = ProjectComment::where('project_id', $id)->count();

            $myFollower = $project->admin || in_array(Auth::id(), $followers);

            $myLover = $project->admin || in_array(Auth::id(), $lovers);
            /*$project->views_cnt = $views_cnt;
            $project->followers_cnt = $followers_cnt;
            $project->lovers_cnt = $lovers_cnt;
            $project->comments_cnt = $comments_cnt;
            $project->genres_cnt = $genres_cnt;
            $project->questions_cnt = $questions_cnt;

            $project->myfollow = $myFollower ? 1 :0;
            $project->myLover = $myLover ? 1 :0;*/

            $views_cnt = $views->sum('count');

            if (!$project->admin) {
                $view = $views->where('user_id', Auth::id())->first();
                if (is_null($view)) {
                    ProjectView::create([
                        "project_id" => $project->id,
                        "user_id" => Auth::id()
                    ]);
                } else {
                    $view->count += 1;
                    $view->save();
                }
            }

            return view('visit.project', ['project' => $project, 'role'=> ProjectTeam::where('user_id', Auth::id())->where('project_id', $project->id)->exists(),
                'tab' => $request->input('tab', 0), 'myfollow' => $myFollower, 'mylove' => $myLover, 'views_cnt' => $views_cnt,
                'followers_cnt' => count($followers), 'lovers_cnt' => count($lovers), 'genres_cnt' => $genres_cnt, 'questions_cnt' => $questions_cnt, 'comments_cnt' => $comments_cnt]);
        }
        else if(!$project->admin && !$project->role){
            return view('errors.501');
        }
        else{
            return view('visit.project', ['project' => $project, 'tab' => $request->input('tab', 0), 'myfollow' => true, 'mylove' => true, 'views_cnt' => 0,
                'followers_cnt' => 0, 'lovers_cnt' => 0, 'genres_cnt' => $genres_cnt, 'questions_cnt' => 0, 'comments_cnt' => 0]);
        }
    }

    public function edit($id, Request $request)
    {
        $project = null;
        if($request->has('step')){
            $step = $request->input('step');
        }
        else{
            $project = Project::select('id', 'active', 'title', 'description', 'user_id')->find($id);

            $this->validEdit($project);

            if(strlen($project->description) < 200){
                $step = 1;
            }
            else{
                $step = 2;
            }
        }

        switch ($step) {
            case 0:
                $project = Project::join('genres', 'projects.genre_id', '=', 'genres.id')
                    ->join('cities', 'projects.city_id', '=', 'cities.id')
                    ->join('departments', 'department_id', '=', 'departments.id')
                    ->join('countries', 'country_id', '=', 'countries.id')
                    ->selectRaw("projects.id, title, synopsis, duration, projects.user_id, countries.name_" . Lang::locale() . " as country,
                        departments.name_" . Lang::locale() . " as department, date(finish_at) as finish_at, 
                        projects.active, genres.name_" . Lang::locale() . " as genre_name, user_id,
                        cities.name_" . Lang::locale() . " as city, char_length(description) as count")
                    ->find($id);

                $this->validEdit($project);
                $languages = Language::leftJoin(DB::raw("(select 1 as chosen, language_id from project_languages where project_id='".$id."') pl"), function ($join) {
                        $join->on('language_id', '=', 'languages.id');
                    })
                    ->selectRaw("languages.id, name_" . Lang::locale() . " as name, rank, IFNULL(chosen, 0) as chosen")
                    ->orderBy('rank')
                    ->get();

                return view('project.basic', ["project" => $project, "step"=>$step,
                    "langs" => $languages->where('chosen', 1)->values(),
                    "languages" => $languages]);
            case 1:
                if(is_null($project)){
                    $project = Project::select('id', 'active',  'title', 'description', 'user_id')->find($id);
                }
                $this->validEdit($project);
                return view('project.description', ["step"=>$step, "project" => $project]);
            case 2:
                return $this->container($id, $project);
            case 3:
                if(is_null($project)){
                    $project = Project::select('id', 'active',  'title', 'user_id')->find($id);
                }

                $this->validEdit($project);

                $authors = Outsiderauthor::where('user_id', Auth::id())
                    ->selectRaw("outsiderauthors.id, outsiderauthors.name as username, email as location, outsiderauthors.link, 1 as outsider")
                    ->orderBy('username')
                    ->get();

                $occupations = Occupation::where('name', '<>', 'Planner')
                    ->selectRaw("id, name_".Lang::locale()." as name")
                    ->get();
                return view('project.team', ["step"=>$step, "users"=>$authors, "occupations"=>$occupations, "project" => $project]);

            case 4:
                if(is_null($project)){
                    $project = Project::select('id', 'active',  'title', 'user_id')->find($id);
                }
                $this->validEdit($project);
                $occupations = Occupation::where('name', '<>', 'Planner')
                    ->selectRaw("id, name_".Lang::locale()." as name")
                    ->orderBy('name')
                    ->get();
                $recruitment = ProjectRecruitment::where('project_id', $id)->join('occupations','occupation_id', '=', 'occupations.id')
                    ->leftJoin(DB::raw("(select 1 as applied, project_recruitment_id from applications where sender_id = '".Auth::id()."' group by project_recruitment_id) application"),function ($join){
                        $join->on('application.project_recruitment_id', '=', 'project_recruitments.id');
                    })
                    ->selectRaw("project_recruitments.id,project_recruitments.quantity,project_recruitments.description, occupation_id, occupations.name, IFNULL(application.applied, 0) as application")
                    ->get();
                return view('project.recruitment', ["step"=>$step, "recruitment"=>$recruitment, "occupations"=>$occupations,"project" => $project]);
        }
    }

    public function ask($id)
    {
        $projects = Project::join('users', 'user_id', '=', 'users.id')->select('projects.id', 'title', 'user_id', 'username')->find($id);

        return view('user.question', $projects);
    }

    public function report($id)
    {
        $projects = Project::join('users', 'user_id', '=', 'users.id')->select('projects.id', 'title', 'user_id', 'username')->find($id);

        return view('user.report', $projects);
    }

    public function reports($id, Request $request)
    {
        $order = $request->input('order', 'created_at');
        $except = $request->input('report_id', 0);

        $reports = Report::join('users', 'reports.user_id', '=', 'users.id')
            ->leftJoin(DB::raw("(select count(id) as cnt, report_id from report_comments group by report_id) comments"), function ($join) {
                $join->on('comments.report_id', '=', 'reports.id');
            })
            ->leftJoin(DB::raw("(select count(id) as cnt, report_id from report_lovers group by report_id) lovers"), function ($join) {
                $join->on('lovers.report_id', '=', 'reports.id');
            })
            ->leftJoin(DB::raw("(select id, report_id from report_lovers where user_id = '".Auth::id()."') mylove"), function ($join) {
                $join->on('mylove.report_id', '=', 'reports.id');
            })
            ->selectRaw("reports.id, reports.user_id, username, reports.title, reports.synopsis, reports.created_at, reports.user_id = '".Auth::id()."' as mine, 
                IFNULL(comments.cnt,0) as comments_cnt, IFNULL(lovers.cnt, 0) as lovers_cnt, IFNULL(mylove.id, 0) as mylove")
            ->where('project_id', $id);

        if($except > 0){
            $reports = $reports->where('reports.id', '<>', $except);
        }

        return  $reports->orderBy('reports.'.$order, 'desc')
            ->paginate(12);
    }

    public function followers($id){
        $user_id = Auth::id();
        $follower = ProjectFollower::where('project_id', $id)->where('user_id', $user_id)->first();
        try {
            if ($follower) {
                $follower->delete();
                return '0';
            } else {
                $follower = new ProjectFollower;
                $follower->project_id = $id;
                $follower->user_id = $user_id;
                $follower->save();
                return $follower->id;
            }
        }
        catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function lovers($id){
        $user_id = Auth::id();
        $lover = ProjectLover::where('project_id', $id)->where('user_id', $user_id)->first();
        try {
            if ($lover) {
                $lover->delete();
                return '0';
            } else {
                $lover =ProjectLover::create([
                    'project_id' => $id,
                    'user_id' => $user_id
                ]);

                return $lover->id;
            }
        }
        catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function commonFollowers($id){
        return ProjectFollower::where('project_followers.user_id', Auth::id())
            ->join(DB::raw("(select project_id as pid from project_followers where project_followers.user_id = '".$id."') team"), function ($join) {
                $join->on('team.pid', '=', 'project_followers.project_id');
            })
            ->join('projects','project_followers.project_id', '=', 'projects.id')
            ->select('projects.id', 'title', 'synopsis', 'active', 'genre_id', 'city_id', 'projects.user_id',
                'projects.updated_at', 'start_at', 'finish_at',
                DB::raw('FLOOR((unix_timestamp(finish_at) - unix_timestamp(now()))/60/60/24) as daterest'), DB::raw('datediff(finish_at, projects.created_at) as datediff'))
            ->get();
    }

    public function description(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'editor'=>'required|min:200',
        ]);

        $project = Project::find($request->id);
        $validator->after(function($validator) use ($request, $project)
        {
            if($project->user_id != Auth::id()){
                $validator->errors()->add('authorize', trans('project.ERRORS.permission'));
        }
            if($project->active == 2){
                $validator->errors()->add('phase', trans('project.ERRORS.phase'));
            }
        });

        if ($validator->fails()) {
            return back()->withInput()->withErrors('validator');
        }

        if($request->has('images')){

            $files = Storage::disk('images')->files('/uploads/projects/'.$project->id);
            $names = $request['images'];
            foreach ($files as $file){
                if(!in_array(basename($file), $names)){
                    unlink($file);
                }
            }
        }

        $project->description = $_POST['editor'];
        $project->save();

        return $request->input('returnFlag', 0) ? redirect('/project/'.$project->id) : $this->container($project->id, $project);
    }

    public function store(Request $request){
        $project = Project::find($request->id);
        $this->validEdit($project);

        if($request['step'] == 0){
            if($request->has('duration')){
                $project->duration = $request['duration'];
                $project->save();
            }

            if($request->has('lang')){

                $oldLang = ProjectLanguage::where('project_id', $project->id)->pluck('language_id')->all();

                $toRemove = $request->has('lang')  ? array_diff($oldLang, $request['lang']) : $oldLang;

                DB::table('project_languages')->where('project_id', $project->id)->whereIn('language_id', $toRemove)->delete();
                $toAdd = $request->has('lang')  ? array_diff($request['lang'], $oldLang) : [];

                foreach ($toAdd as $lang){
                    ProjectLanguage::create([
                        'project_id' => $project->id,
                        'language_id' => $lang
                    ]);
                }
            }
            else{
                DB::table("project_languages")->where('project_id', $project->id)->delete();
            }

            return $request->input('returnFlag', 0) ? redirect('/project/'.$project->id) : redirect('/admin/projects/'.$project->id.'?step=1');
        }
        else{
            $this->validate($request, ['editor'=>'required|min:200']);

            $project->description = $_POST['editor'];
            $project->save();
            return redirect()->guest('/admin/projects/'.$project->id.'?step=2');
        }
    }

    public function finish(Request $request)
    {
        $project = Project::find($request->id);

        $project->active = 2;
        $project->save();
        return Response( $project->active, 200);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'id' => 'required',
            'title' => 'required|max:40',
            'description' => 'required|min:15',
            'synopsis' => 'required|min:40|max:256',
            'city_id' => 'required',
            'languages' => 'max:20',
            'duration' => 'required|max:3',
            'genre_id' => 'required',
            'finish_at' => 'required'
        ]);
    }

    private function validEdit($project){
        if ($project->user_id != Auth::id()) {
            throw new \Illuminate\Auth\Access\AuthorizationException(trans('messages.error'));
        }

        if($project->active != 1){
            throw new \Illuminate\Auth\Access\AuthorizationException(trans('messages.error'));
        }
    }

    private function supression($id){
        DB::table('project_recruitments')->where('project_id', $id)->detlete();


        DB::table('sponsors')->where('project_id', $id)->delete();

        DB::table('budgets')->where('project_id', $id)->delete();

        $team = Script::where('project_id', $id)->get();

        foreach ($team as $member) {
            DB::table('script_authors')->where('script_id', $member->id)->delete();
            $member->delete();
        }

        DB::table('events')->where('project_id', $id)->delete();

  //      DB::table('project_languages')->where('project_id', $id)->delete();

//        DB::table('reports')->where('project_id', $id)->delete();

        //DB::table('project_views')->where('project_id', $id)->delete();

    //    DB::table('project_followers')->where('project_id', $id)->delete();

        $path = public_path() . '/context/projects/' . $id;

        if (file_exists($path . '.jpg')) {
            unlink($path . '.jpg');
        }

        if (file_exists($path . '.thumb.jpg')) {
            unlink($path . '.thumb.jpg');
        }

        if (file_exists($path . '.small.jpg')) {
            unlink($path . '.small.jpg');
        }
    }
    private function container($id, $project=null){
        if(is_null($project)){
            $project = Project::select('id', 'active', 'title', 'user_id')->find($id);
        }

        $this->validEdit($project);
        $userid = Auth::id();

        $users = ProjectTeam::where('project_id', $id)
            ->join('users', 'project_teams.user_id', '=', 'users.id')
            ->selectRaw("users.id, username, email as location, concat('/profile/',users.id) as link, 0 as outsider");

        $authors = Outsiderauthor::where('user_id', $userid)
            ->selectRaw("outsiderauthors.id, outsiderauthors.name as username, email as location, outsiderauthors.link, 1 as outsider")
            ->union($users)
            ->orderBy('username')
            ->get();
        $types  = BudgetType::select('id', 'name_'.Lang::locale().' as name')->get();
        $budgets = Budget::where('project_id', $id)
            ->join('budget_types', 'budget_type_id', '=', 'budget_types.id')
            ->select('budgets.id', 'quantity', 'comment', 'budget_type_id', 'budget_types.name_'.Lang::locale().' as name')
            ->get();

        $sponsors = Sponsor::where('project_id', $id)
            ->leftJoin('users', 'sponsors.user_id', '=', 'users.id')
            ->leftJoin('outsiderauthors', 'sponsors.user_id', '=', 'outsiderauthors.id')
            ->selectRaw('sponsors.id, quantity, IFNULL(username, IFNULL(outsiderauthors.name, sponsor_name)) as sponsor_name, sponsors.user_id, sponsed_at')
            ->get();

        $scripts = Script::where('project_id', $id)
            ->with('authors')
            ->selectRaw('scripts.id, link, title, description, scripts.created_at')
            ->get();

        return view('project.container', ["step"=>2, "project"=>$project, "types"=>$types, "authors"=>$authors, "budgets" => $budgets, "sponsors"=>$sponsors, "scripts"=>$scripts]);
    }

    private function filter($genre = 0, $city=0, $person = 0, $order='updated_at'){
        $projects = $this->projects(null, '> 0');

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

        return $projects->orderBy('projects.'.$order)->paginate(9);
    }
}
