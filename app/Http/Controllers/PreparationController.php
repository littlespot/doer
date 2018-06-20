<?php

namespace Zoomov\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App;
use Auth;
use Config;
use DB;
use Validator;
use Storage;
use Zoomov\Budget;
use Zoomov\BudgetType;
use Zoomov\Country;
use Zoomov\Department;
use Zoomov\City;
use Zoomov\Event;
use Zoomov\Genre;
use Zoomov\Guest;
use Zoomov\Language;
use Zoomov\Mail\Team;
use Zoomov\Occupation;
use Zoomov\Outsiderauthor;
use Zoomov\Project;
use Zoomov\ProjectLanguage;
use Zoomov\ProjectRecruitment;
use Zoomov\ProjectTeam;
use Zoomov\ProjectTeamOccupation;
use Zoomov\Script;
use Zoomov\ScriptAuthor;
use Zoomov\Sponsor;
use Zoomov\User;

class PreparationController extends Controller
{
    public function index(Request $request)
    {
        $countries = Country::where('region', '<>', 1)->select('id', 'sortname', 'name_' . app()->getLocale(). ' as name')->orderBy('rank')->orderByRaw('convert(name_' . app()->getLocale() .' using gbk) ASC')->get();

        $languages = Language::selectRaw("id, rank, name_" . app()->getLocale() . " as name, false as chosen")
            ->orderBy('rank')
            ->get();

        $genres = Genre::select('id', 'name_' . app()->getLocale() . ' as name')->get();

        return view('user.creation', ["step" => 0, "countries" => $countries, "languages" => $languages, "genres" => $genres]);
    }

    public function edit(Project $project, $step){
        switch ($step){
            case 1:
                return view('preparation.description', ['project'=>$project, 'step'=>$step]);
        /*    case 2:
                return view('preparation.team', ['project'=>$project, 'step'=>$step]);*/
            default:
                return view('preparation.basic', ['project'=>$project, 'step'=>$step]);
        }
    }

    public function show($id, Request $request)
    {
        $project = Project::leftJoin('genres', 'genre_id', '=', 'genres.id')
            ->leftJoin('cities', 'projects.city_id', '=', 'cities.id')
            ->leftJoin('departments', 'department_id', '=', 'departments.id')
            ->leftJoin('countries', 'country_id', '=', 'countries.id')
            ->selectRaw('projects.id, title, synopsis, duration, active, user_id,  char_length(description) as count, genres.name_'.app()->getLocale().' as genre_name,
                        genre_id, projects.city_id, cities.name_'.app()->getLocale().' as city_name, countries.name_'.app()->getLocale().' as country, cities.department_id, departments.country_id, finish_at,
                        FLOOR((unix_timestamp(finish_at) - unix_timestamp(now()))/60/60/24) as daterest, datediff(finish_at, projects.created_at) as datediff,
                        description')
            ->with('lang')
            ->find($id);

        $this->validEdit($project);
        if($project->active === 1){
            $project->genres_cnt = Project::where(['genre_id'=>$project->genre_id, 'active'=>1])->count();
        }

        if($request->has('step')){
            $step = $request->input('step', 0);
        }
        else{

            if(strlen($project->description) < 200){
                $step = 1;
            }
            else{
                $step = 2;
            }
        }


        switch ($step) {
            case 0:
                $countries = Country::where('region', '<>', 1)->select('id', 'sortname', 'name_' . app()->getLocale() . ' as name')->orderByRaw('convert(name_' . app()->getLocale() .' using gbk) ASC')->get();

                $languages = Language::leftJoin(DB::raw("(select 1 as chosen, language_id from project_languages where project_id ='".$id."') pl"), function ($join) {
                        $join->on('languages.id', '=', 'language_id');
                    })
                    ->selectRaw("id, name_" . app()->getLocale() . " as name, rank, IFNULL(pl.chosen, 0) as chosen")
                    ->orderBy('rank')->get();

                $genres = Genre::select('id', 'name_' . app()->getLocale() . ' as name')->get();

                return view('preparation.basic', ["step"=>$step, "project" => $project, "countries" => $countries, "languages" => $languages, "genres" => $genres]);
            case 1:
                return view('preparation.description', ["step"=>$step, "project" => $project]);
            case 2:
                return $this->container($id, $project);
                /*
            case 3:
                if(is_null($project)){
                    $project = Project::select('id', 'active',  'title', 'user_id')->find($id);
                }

                $this->validEdit($project);
                $userid = auth()->id();

                $users = User::where('active', 1)
                    ->join('cities', 'city_id', '=', 'cities.id')
                    ->join('departments', 'department_id', '=', 'departments.id')
                    ->join('countries', 'country_id', '=', 'countries.id')
                    ->selectRaw("users.id, username, concat(cities.name_".app()->getLocale().", '(', countries.sortname, ')')  as location,
                        CONCAT('/profile/', users.id) as link, 0 as outsider");

                $authors = Outsiderauthor::where('user_id', $userid)
                    ->selectRaw("outsiderauthors.id, outsiderauthors.name as username, email as location, outsiderauthors.link, 1 as outsider")
                    ->union($users)
                    ->orderByRaw('convert(username using gb2312)')
                    ->get();

                $occupations = Occupation::where('name', '<>', 'Planner')
                    ->selectRaw("id, name_".app()->getLocale()." as name")
                    ->get();

                return view('preparation.team', ["step"=>$step, "users"=>$authors, "occupations"=>$occupations, "project" => $project]);
*/
            case 3:
                $occupations = Occupation::where('name', '<>', 'Planner')
                    ->selectRaw("id, name_".app()->getLocale()." as name")
                    ->orderByRaw('convert(name using gb2312)')
                    ->get();
                $recruitment = ProjectRecruitment::where('project_id', $id)->join('occupations','occupation_id', '=', 'occupations.id')
                    ->leftJoin(DB::raw("(select 1 as applied, project_recruitment_id from applications where sender_id = '".auth()->id()."' group by project_recruitment_id) application"),function ($join){
                        $join->on('application.project_recruitment_id', '=', 'project_recruitments.id');
                    })
                    ->selectRaw("project_recruitments.id,project_recruitments.quantity,project_recruitments.description, occupation_id, 
                        occupations.name_". app()->getLocale()." as name, IFNULL(application.applied, 0) as application")
                    ->get();
                return view('preparation.recruitment', ["step"=>$step, "recruitment"=>$recruitment, "occupations"=>$occupations,"project" => $project]);
        }
    }

    public function preview($id){
        $project = Project::join('users', 'users.id', '=', 'projects.user_id')
            ->join('genres', 'projects.genre_id', '=', 'genres.id')
            ->join('cities', 'projects.city_id', '=', 'cities.id')
            ->join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->leftJoin(DB::raw("(select count(id) + 1 as cnt, genre_id from projects where active = 1 group by genre_id) genres"), function ($join){
                $join->on('projects.genre_id', '=', 'genres.genre_id');
            })
            ->with('scripts', 'budget', 'sponsor', 'lang', 'recruit')
            ->selectRaw('projects.id, title, synopsis, projects.genre_id, projects.user_id, projects.city_id, username, duration, countries.name_'.app()->getLocale().' as country, 
               projects.updated_at, start_at, finish_at, projects.active, projects.description, IFNULL(genres.cnt, 1) as genres_cnt,
               genres.name_' . app()->getLocale() . ' as genre_name, cities.name_' . app()->getLocale() . ' as city_name,
               FLOOR((unix_timestamp(finish_at) - unix_timestamp(now()))/60/60/24) as daterest,
               datediff(finish_at, projects.created_at) as datediff')
            ->find($id);

        if($project->active){
            return redirect()->guest('/project/'.$project->id);
        }
        $genres_cnt = Project::where('active', 1)->where('genre_id', $project->genre_id)->count();
        return view('preparation.preview', ["project"=>$project, 'tab' => 0, 'myfollow' => true, 'mylove' => true, 'views_cnt' => 0,
                'followers_cnt' => 0, 'lovers_cnt' => 0, 'genres_cnt' => $genres_cnt, 'questions_cnt' => 0, 'comments_cnt' => 0]);
    }

    public function update($id, Request $request){
        $project = Project::find($id);
        if ($project->active === 0 || $project->user_id != auth()->id()) {
            return Response('NOT authorised', 501);
        }

        if($request->has('finish_at') && !$request->finish_at){
            $project->update([
                'finish_at' => gmdate("Y-m-d", strtotime($request->finish_at))
            ]);
        }
        else if($request->has('language_id')){
            $lang = ProjectLanguage::where(['project_id'=>$id, 'language_id'=>$request->language_id])->first();
            if($lang){
                $lang->delete();
                return 0;
            }
            else{
               $lang = ProjectLanguage::create(['project_id'=>$id, 'language_id'=>$request->language_id]);
               return $lang->id;
            }
        }
        else if($request->input('city_id','')){
            $project->update(['city_id'=>str_replace('number:','',$request->city_id)]);
        }
        else{
            $project->update($request->all());
        }
    }

    public function description(Request $request){
        $validator =  Validator::make($request->all(), [
            'id' => 'required'
        ]);

        $project = Project::find($request->id);
        $validator->after(function($validator) use ($request, $project)
        {
            if($project->user_id != auth()->id()){
                $validator->errors()->add('authorize', trans('project.ERRORS.invalid.permission'));
            }
            if($project->active === 0){
                $validator->errors()->add('phase', trans('project.ERRORS.invalid.phase'));
            }
            if(strlen(strip_tags($request->editor)) < 200){
                $validator->errors()->add('description', trans('project.ERRORS.minlength.description', ['cnt'=>200]));
            }
        });

        if($validator->fails()){
            return back()->withErrors($validator)
                ->withInput();
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

        $project->description = $request['editor'];
        $project->save();
        if($request->input('sendFlag',0)){
            return $this->activation($project);
        }
        else{
            return redirect('/admin/preparations/'.$project->id.'?step=2');
        }
    }

    public function store(Request $request)
    {
        $id = $request->input('id', null);

        if($id){
            return $this->update($id, $request);
        }
        $validator = $this->validator($request->all());

        $id = auth()->id();

        $root = 'app/public/uploads/projects/'.$id;
        $path = storage_path($root.'.jpg');

        $validator->after(function($validator) use ($request, $path)
        {
            if(!file_exists($path)){
                $validator->errors()->add('poster', trans('project.ERRORS.require.poster'));
            }
        });

        if ($validator->fails()) {
           return back()->withInput()->withErrors('validator');
        }

        $project = Project::create([
            'id' => $this->uuid('p'),
            'user_id' => $id,
            'city_id' => str_replace('number:','',$request->city_id),
            'genre_id' => $request->genre_id,
            'duration' => $request->duration,
            'title' => $request->title,
            'synopsis' => $request->synopsis,
            'description' => trans('project.PLACES.description'),
            'start_at' => date('Y-m-d'),
            'finish_at' => date("Y-m-d", strtotime($request->finish_at))
        ]);

        if($request->has('lang') ) {
            foreach ($request['lang'] as $language) {
                ProjectLanguage::create([
                    'project_id' => $project->id,
                    'language_id' => $language
                ]);
            }
        }

        $dst_path = public_path('/storage/projects/'.$project->id.'.jpg');
        rename($path, $dst_path);

        $small_path = storage_path($root.config('constants.image.small').'.jpg');

        if(file_exists($small_path))
        {
            rename($small_path, public_path('/storage/projects/'.$project->id.config('constants.image.small').'.jpg'));
        }

        $thumbnail_path = storage_path($root.config('constants.image.thumbnail').'.jpg');

        if(file_exists($thumbnail_path)){
            rename($thumbnail_path,  public_path('/storage/projects/'.$project->id.config('constants.image.thumbnail').'.jpg'));
        }

        return redirect('/admin/preparations/'.$project->id.'?step=1');
    }

    public function putonline($id, Request $request)
    {
        $project = Project::find($id);

        if (!is_null($project->active) || $project->user_id != auth()->id()) {
            return Response('NOT authorised', 501);
        }
        //return $request->all();
        $validator = $this->validator($request->all());
        $validator->after(function($validator) use ($request)
        {
            if(!file_exists(public_path() . '/storage/projects/'.$request->id.'.jpg')){
                $validator->errors()->add('poster', trans('project.ERRORS.require.poster'));
            }
        });

        if ($validator->fails()) {
            return back()->withErrors('validator');
        }

        $project->title = $request->title;
        $project->duration = $request->duration;
        $project->synopsis = $request->synopsis;
        $project->city_id = str_replace('number:','',$request->city_id);
        $project->genre_id = $request->genre_id;
        $project->finish_at = date("Y-m-d", strtotime($request->finish_at));

        $project->save();

        $oldLang = ProjectLanguage::where('project_id', $project->id)->pluck('language_id')->toArray();

        $toRemove = $request->has('lang') ?  array_diff($oldLang, $request['lang']) : $oldLang;

        DB::table('project_languages')->where('project_id', $project->id)->whereIn('language_id', $toRemove)->delete();
        $toAdd = $request->has('lang') ? array_diff($request['lang'], $oldLang) : [];

        foreach ($toAdd as $lang){
            ProjectLanguage::create([
                'project_id' => $project->id,
                'language_id' => $lang
            ]);
        }

        if(strlen($project->description) < 200){
            return view('preparation.description', ['project'=>$project, 'step'=>1]);
        }
        else if($request->input('sendFlag',0)){
            return $this->activation($project);
        }
        else{
            return $this->container($id, $project);
        }
    }

    public function send(Request $request){
        $project = Project::find($request->id);
        return $this->activation($project);
    }

    public function destroy($id)
    {
        $project = Project::find($id);

        if (!is_null($project->active) || $project->user_id != Auth::id()) {
            return Response('NOT authorised', 501);
        }
        DB::table("project_languages")->where('project_id', $id)->delete();
        DB::table("project_recruitments")->where('project_id', $id)->delete();

        DB::table("sponsors")->where('project_id', $id)->delete();

        DB::table("budgets")->where('project_id', $id)->delete();

        $scripts = Script::where('project_id', $id)->get();

        foreach ($scripts as $script) {
            DB::table("script_authors")->where('script_id', $script->id)->delete();
            $script->delete();
        }

        $team = ProjectTeam::where('project_id', $id)->get();

        foreach ($team as $member) {
            DB::table("project_team_occupations")->where("project_team_id", $member->id)->delete();
            $member->delete();
        }

        $path = public_path() . '/storage/projects/' . $project->id;

        if (file_exists($path . '.jpg')) {
            unlink($path . '.jpg');
        }

        if (file_exists($path . '.thumb.jpg')) {
            unlink($path . '.thumb.jpg');
        }

        if (file_exists($path . '.small.jpg')) {
            unlink($path . '.small.jpg');
        }

        $project->delete();

        return Response('OK', 202);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'title' => 'required|max:40',
            'synopsis' => 'required|max:256',
            'city_id' => 'required',
            'duration' => 'required|numeric|max:120',
            'genre_id' => 'required|numeric',
            'finish_at' => 'required'
        ]);
    }

    private function activation($project)
    {
        if(is_null($project->active)){
            $this->creation($project);
            $project->active = 0;
            $project->save();

            return  redirect('/project/'.$project->id);
        }
        else if($project->active){
            redirect('/admin/project/'.$project->id);
        }
        else{
            redirect('/project/'.$project->id);
        }
    }

    private function creation($project){
        $id = auth()->id();
        $username =  auth()->user()->username;
        $budgets = DB::table('budgets')->where('project_id', $project->id)
            ->join('budget_types','budget_type_id', '=', 'budget_types.id')
            ->selectRaw("project_id, 'b' as type, budgets.id as related_id, concat(budget_types.name, '-', quantity) as title, comment as content, '".$id."' as user_id, '".$username."' as username")
            ->get()
            ->map(function ($value) {
                return (array)$value;
            })->toArray();

        DB::table('events')->insert($budgets);

        $sponsors = Sponsor::leftJoin('users','user_id', '=', 'users.id')
            ->where('sponsors.project_id', $project->id)
            ->selectRaw("project_id,'m' as type, sponsors.id as related_id, user_id, IFNULL(username, sponsor_name) as username, concat(quantity, ' [', date(sponsed_at), ']') as title")
            ->get()
            ->toArray();

        DB::table('events')->insert($sponsors);

        $scripts = Script::where('project_id', $project->id)
            ->get();

        foreach ($scripts as $script){
            $users = ScriptAuthor::where('script_id', $script->id)
                ->leftJoin('users', function($join){
                    $join->on('users.id', '=', 'user_id');
                })
                ->leftJoin('outsiderauthors', function($join){
                    $join->on('outsiderauthors.id', '=', 'author_id');
                })
                ->selectRaw("IFNULL(username, outsiderauthors.name) as name, CASE when users.id is null THEN outsiderauthors.link ELSE concat('/profile/', users.id) END as link")
                ->orderBy('script_authors.user_id')
                ->get();

            $authors = '';

            foreach ($users as $user){
                $authors .= "<a href='".$user->link."' target='_blank'>".$user->name."</a>".",";
            }

            Event::create([
                'project_id' => $project->id,
                'user_id' => $id,
                'username' => $username,
                'title' => "<a href='".$script->link."' target='_blank'>".$script->title ." [".str_limit($script->created_at, 10, '')."]</a>",
                'content' => rtrim($authors,","),
                'type' => 's',
                'related_id' => $script->id
            ]);
        }

        $team = ProjectTeam::where('project_id', $project->id)
            ->join(DB::raw("(select project_team_id, GROUP_CONCAT(occupations.name) as roles from project_team_occupations inner join occupations on occupation_id = occupations.id group by project_team_id) occupations"),
                function ($join){$join->on('project_team_id', '=', 'project_teams.id');})
            ->leftJoin("users", 'project_teams.user_id', '=', 'users.id')
            ->leftJoin('outsiderauthors', 'outsider_id', '=', 'outsiderauthors.id')
            ->selectRaw("IFNULL(users.locale,'".App::getLocale()."') as locale, IFNULL(users.id, outsiderauthors.id) as user_id, 
                IFNULL(username, outsiderauthors.name) as username, IFNULL(users.email, outsiderauthors.email) as email, roles")
            ->get();

        foreach ($team as $member){
            if($member->locale != 'en'){
                $roles = explode(',', $member->roles);
                $occupations = '';

                foreach ($roles as $role){
                    $occupations.= trans('messages.occupations.'.$role).',';
                }

                $occupations = rtrim($occupations, ",");
            }
            else{
                $occupations = $member->roles;
            }

            $guest = Guest::create([
                "project_id" => $project->id,
                "user_id" => $member->user_id,
                "code" => $this->uuid2('g', $project->id)
            ]);

            Mail::to($member->email)->send(new Team($guest->code, $member->username, $project, $occupations));
        }

        $team = ProjectTeam::where('project_id', $project->id)->where('user_id', Auth::id())->first();

        if(is_null($team)){
            $team = ProjectTeam::create([
                'id' => $this->uuid('t'),
                'project_id' => $project->id,
                'user_id' => auth()->id()
            ]);
        }

        ProjectTeamOccupation::create([
            'occupation_id' => $this->planner,
            'project_team_id' => $team->id
        ]);

        return $project->active;
    }

    private function container($id, $project=null){
        if(is_null($project)){
            $project = Project::select('id', 'active', 'title', 'user_id')->find($id);
        }

        $this->validEdit($project);
        $userid = auth()->id();

        $users = User::where('active', 1)
            ->leftJoin(DB::raw("(select fan_id, love from relations where relations.idol_id = '".$userid."') friends"), function ($join) {
                $join->on('friends.fan_id', '=', 'users.id');
            })
            ->join('cities', 'city_id', '=', 'cities.id')
            ->join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->selectRaw("users.id, username, (CASE users.id WHEN '".$userid."' THEN 2 ELSE IFNULL(love, -1) END) AS love, 
                concat(cities.name_".app()->getLocale().", '(', countries.sortname, ')')  as location, CONCAT('/profile/', users.id) as link, 0 as outsider")
            ->orderBy('love', 'desc')
            ->orderByRaw('convert(username using gb2312)')
            ->get();

        $authors = Outsiderauthor::where('user_id', $userid)
            ->selectRaw("outsiderauthors.id, outsiderauthors.name as username, email as location, outsiderauthors.link, 1 as outsider")
            ->orderByRaw('convert(username using gb2312)')
            ->get();

        $types  = BudgetType::select('id', 'name_'.app()->getLocale().' as name')->get();
        $budgets = Budget::where('project_id', $id)
            ->join('budget_types', 'budget_type_id', '=', 'budget_types.id')
            ->select('budgets.id', 'quantity', 'comment', 'budget_type_id', 'budget_types.name_'.app()->getLocale().' as name')
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

        return view('preparation.container', ["step"=>2, "project"=>$project, "types"=>$types, "users"=>$users, "authors"=>$authors, "budgets" => $budgets, "sponsors"=>$sponsors, "scripts"=>$scripts]);
    }

    private function validEdit($project){
        if ($project->user_id != auth()->id()) {
            throw new \Illuminate\Auth\Access\AuthorizationException(trans('messages.error'));
        }
    }
}
