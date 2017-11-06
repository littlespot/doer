<?php

namespace Zoomov\Http\Controllers;
use Auth;
use Config;
use DB;
use Lang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Zoomov\AdminInvitation;
use Zoomov\Country;
use Zoomov\Department;
use Zoomov\City;
use Zoomov\Genre;
use Zoomov\Invitation;
use Zoomov\Potential;
use Zoomov\PotentialWork;
use Zoomov\Project;
use Zoomov\ProjectComment;
use Zoomov\ProjectTeam;
use Zoomov\Question;
use Zoomov\QuestionAnswer;
use Zoomov\Report;
use Zoomov\ReportComment;
use Zoomov\Script;
use Zoomov\User;
use Zoomov\Video;

class AdminController extends Controller
{
    public function index()
    {
        $projects = Project::select('id', 'title', 'active', 'updated_at', 'created_at', DB::raw('FLOOR((unix_timestamp(projects.finish_at) - unix_timestamp(now()))/60/60/24) as daterest'))
            ->orderBy('active')->orderBy('created_at')->get();

        return view('admins.projects', ['projects' => $projects]);
    }

    public function show($id)
    {
        $project = Project::with('user')
            ->select('projects.id', 'projects.user_id', 'title', 'synopsis', 'description',
                'genre_id', 'projects.city_id', 'user_id', 'active', 'projects.created_at',
                'projects.updated_at', 'start_at', 'finish_at',
                DB::raw('FLOOR((unix_timestamp(projects.finish_at) - unix_timestamp(now()))/60/60/24) as daterest'),
                DB::raw('datediff(finish_at, projects.created_at) as datediff'))
            ->find($id);

        return view('admins.project', ['project' => $project]);
    }

    public function home()
    {
        $users = User::where('professional',0)->select('id','email', 'username','active')->orderBy('active')->get();

        return view('admins.index', ['users' => $users]);
    }

    public function departments($id)
    {
        return \Response::json(Department::where('country_id', $id)
            ->select('id', 'name_en as name')
            ->orderBy('name_en')
            ->get());
    }

    public function cities($id)
    {
        return City::where('department_id', $id)
            ->select('id', 'name_en as name')
            ->orderBy('name_en')
            ->get();
    }

    public function travelers(){
        $users = DB::select("select t.id, t.username, t.active, views, IFNULL(ip_cnt, 0) as ip_cnt  
            from travelers t left join (select count(ip) as ip_cnt, traveler_id from traveler_connections group by traveler_id) c 
            on c.traveler_id = t.id");

        return view('admins.travelers', ['users' => $users]);
    }

    public function traveler($id){
        $connections =  DB::select("select t.ip, t.connection_at, c.cnt as views  
            from traveler_connections t inner join (select count(id) as cnt, ip from traveler_connections where traveler_id =".$id." group by ip) c 
            on t.traveler_id =".$id." and c.ip = t.ip");

        $traveler = DB::table('travelers')->selectRaw("id, username, active, created_at")->find($id);

        return view('admins.traveler', ['traveler' => $traveler, 'connections'=>$connections]);
    }

    public function createTraveler(Request $request){
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        $username = $request->username;

        $id = DB::table('travelers')->insertGetId(['username'=>$username, 'password'=>bcrypt($request->password)]);

        return Response(['id'=>$id, 'username'=>$username],200);
    }

    public function updateTraveler($id, Request $request){
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        DB::update("update travelers set active = 1, username = '".$request->username."', password = '".bcrypt($request->password)."'where id = ?", [$id]);
    }

    public function removeTraveler($id){
        DB::update("update travelers set active = 0 where id = ?", [$id]);
        return Response('OK', 200);
    }

    public function professionals(){
        $users = User::where('professional',1)->join('cities', 'city_id', '=', 'cities.id')
            ->join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->join(DB::raw("(select group_concat(name_en) as roles, professional_id from
                    (select name_en, professional_id from professional_roles p inner join roles r 
                    on p.role_id = r.id) x
                    group by professional_id) roles"), function ($join) {
                $join->on('professional_id', '=', 'users.id');
            })
            ->select("users.id", "users.username as name", "countries.name_en as country", "roles.roles", "users.created_at")
            ->get();
        $countries = Country::select('id', 'name_en as name', 'sortname', 'phonecode')
            ->orderBy('name')
            ->get();

        $roles = DB::table("roles")->select('id', 'name_en as name')->get();

        return view('admins.professionals', ['professionals' => $users, 'countries' => $countries, 'roles'=>$roles]);
    }

    public function professional($id){
        $user = User::join('cities', 'city_id', '=', 'cities.id')
            ->join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->join(DB::raw("(select group_concat(name_en) as roles, professional_id from
                    (select name_en, professional_id from professional_roles p inner join roles r 
                    on p.role_id = r.id) x
                    group by professional_id) roles"), function ($join) {
                $join->on('professional_id', '=', 'users.id');
            })
            ->select("users.id", "users.username", "last_name", "first_name", "users.active",
                "users.phone", "mobile", "countries.phonecode",
                "countries.name_en as country", "address", "cities.name_en as city", "departments.name_en as department",
                "users.updated_at", "roles.roles", "users.created_at")
            ->find($id);

        $countries = Country::select('id', 'name_en as name', 'sortname', 'phonecode')
            ->orderBy('name')
            ->get();

        $roles = DB::table("roles")->select('id', 'name_en as name')->get();

        return view('admins.professional', ['professional' => $user, 'countries' => $countries, 'roles'=>$roles]);
    }


    public function addProfessional(Request $request){
        $this->validate($request, [
            'username' => 'required',
            'email' => 'required',
            'address' => 'required',
            'city_id' => 'required',
            'role_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'postal' => 'required',
            'phone' => 'required',
            'mobile' => 'required'
        ]);

        $professional = User::create(array_merge(
            $request->except('role_id', 'country_id', 'depart_id'),
            ['id'=>$this->uuid('z'), 'password' => $this->generateStrongPassword()]
        ));
        $professional->address = $request->address .', '.$request->postal;
        $professional->professional = 1;
        $professional->save();
        foreach ($request->role_id as $role){
            DB::table('professional_roles')->insert(["professional_id" => $professional->id, "role_id" => $role]);
        }

        Mail::send('emails.'.$professional->locale.'.hello', ['user' => $professional->username, 'key' => $professional->password], function($message) use ($professional)
        {
            $message->to($professional->email, 'ZOOMOV')->subject(trans('auth.welcome', ['name' => $professional->username]));
        });

        return View('auth.activation',  ['name' => $professional->username, 'email'=>$professional->email]);
    }

    public function updateProfessionals($id, Request $request){
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        DB::update("update travelers set active = 1, username = '".$request->username."', password = '".bcrypt($request->password)."'where id = ?", [$id]);
    }

    public function deleteProfessional($id){
        DB::table('professional_roles')->where("professional_id", $id)->delete();
        DB::table('users')->where('id', $id)->delete();
        return Response('OK', 200);
    }

    public function videos(){
        $videos = Video::join('genres', 'genre_id', '=', 'genres.id')
            ->select('videos.id', 'title', 'genres.name_en as genre', 'created_at', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        $genres = Genre::select('id', 'name_en as name')->orderBy('name_en')->get();

        return view('admins.videos', ['videos' => $videos, 'genres' => $genres]);
    }

    public function video($id){
        $video =  Video::join('genres', 'genre_id', '=', 'genres.id')
            ->select('videos.id', 'title', 'genres.name_en as genre', 'link', 'description', 'created_at', 'updated_at')
            ->where('videos.id', $id)
            ->first();

        $tags = DB::table('video_tags')->join('tags', 'tag_id', '=', 'tags.id')
            ->where('video_id', $id)
            ->select("tag_id", "label")
            ->orderBy("label")
            ->get();

        $tag = '';
        foreach($tags as $item) {
            $tag .= $item->label.",";
        }
        $genres = Genre::select('id', 'name_en as name')->orderBy('name_en')->get();
        return view('admins.video', ['video' => $video, 'tag'=>substr($tag, 0, -1), 'tags'=>$tags, 'genres' => $genres]);
    }

    public function createVideo(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'link' => 'required',
            'description' => 'required',
            'genre_id' => 'required',
            'tags' => 'required'
        ]);

        $admin = Auth::guard('admin')->user()->id;
        $video = Video::create(['id'=>$this->uuid('v'), 'admin_id' => $admin] + $request->except('tags'));

        $tags = explode(',', $request->tags);

        foreach ($tags as $tag) {
            $tag_obj = DB::table('tags')->where('label', $tag)->first();
            if(is_null($tag_obj)){
                $tag_id = DB::table('tags')->insertGetId(['label'=>$tag, 'user_id'=>'a'.$admin]);
                DB::table('video_tags')->insert(['video_id'=>$video->id, 'tag_id'=>$tag_id]);
            }
            else{
                DB::table('video_tags')->insert(['video_id'=>$video->id, 'tag_id'=>$tag_obj->id]);
            }
        }

        return $video;
    }

    public function updateVideo($id, Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'link' => 'required',
            'description' => 'required',
            'genre_id' => 'required',
            'tags' => 'required'
        ]);

        $admin = Auth::guard('admin')->user()->id;
        $video = DB::table('videos')->where('id', $id)->update(['admin_id' => $admin] + $request->except('tags'));

        $tags = explode(',', $request->tags);

        foreach ($tags as $tag) {
            $tag_obj = DB::table('tags')->where('label', $tag)->first();
            if(is_null($tag_obj)){
                $tag_id = DB::table('tags')->insertGetId(['label'=>$tag, 'user_id'=>'a'.$admin]);
                DB::table('video_tags')->insert(['video_id'=>$video->id, 'tag_id'=>$tag_id]);
            }
            else{
                DB::table('video_tags')->insert(['video_id'=>$video->id, 'tag_id'=>$tag_obj->id]);
            }
        }

        return $video;
    }
    public function getInvite()
    {
        $users = Potential::where('deleted_at', null)->select('id', 'email', 'invited', 'presentation')->orderBy('invited')->orderBy('email')->get();

        return view('admins.invite', ['users' => $users]);
    }

    public function getPerson($id)
    {
        $user = Potential::find($id);

        $works = PotentialWork::where('potential_id', $id)->with('occupations')->get();

        return view('admins.person', ['user' => $user,'works' => $works]);
    }

    public function store(Request $request)
    {
        $project = Project::find($request->project);

        $user = User::find($project->user_id);

        $project->active = 1;

        $project->save();

        Mail::send('emails.'.$user->locale.'.project', ['user'=>$user->username, 'name' => $project->title, 'id'=>$project->id], function($message) use ($user)
        {
            $message->to($user->email, 'ZOOMOV')->subject(trans('messages.project'));
        });

        return redirect('/admins/projects');
    }

    public function acceptPerson(Request $request){
        $user = Potential::find($request->user);

        if(is_null($user)){
            return Response('No user', 501);
        }

        $invite = AdminInvitation::create([
            'user_id' => Auth::guard('admin')->user()->id,
            'invitation_code' => $this->uuid('a', 12),
            'created_at' => gmdate("Y-m-d H:i:s", time()),
            'email' => $user->email
        ]);

        $invite->save();

        $user->invited = 1;

        $user->save();

        Mail::send('emails.'.$user->locale.'.invite', ['user'=>$user->email, 'sender' => 'ZOOMOV', 'code'=>$invite['invitation_code']], function($message) use ($user)
        {
            $message->to($user->email, 'ZOOMOV')->subject(trans('auth.invite'));
        });

        return redirect('/admins/invite');
    }

    public function deletePerson($id)
    {
        try{
            $user = Potential::find($id);

            if(is_null($user)){
                return Response('No user', 501);
            }

            if($user->inivted === 1){
                return Response('Inivted', 501);
            }

            $user->deleted_at = gmdate("Y-m-d H:i:s", time());

            $user->save();

            return redirect('/admins/invite');
        }
        catch (Exception $e) {
            return Response($e->getMessage(), 501);
        }
    }
    public function user($id)
    {
        $user = User::leftJoin('cities', 'city_id', '=', 'cities.id')
            ->leftJoin('departments', 'department_id', '=', 'departments.id')
            ->leftJoin('countries', 'country_id', '=', 'countries.id')
            ->leftJoin(DB::raw("(select group_concat(name_en) as roles, user_id from
                    (select name_en, user_id from user_occupations p inner join occupations r 
                    on p.occupation_id = r.id) x
                    group by user_id) roles"), function ($join) {
                $join->on('user_id', '=', 'users.id');
            })
            ->select("users.id", "users.username", "last_name", "first_name", "users.active",
                "users.phone", "mobile", "countries.phonecode",
                "countries.name_en as country", "address", "cities.name_en as city", "departments.name_en as department",
                "users.updated_at", "roles.roles", "users.created_at")
            ->find($id);


        return view('admins.user', ['user' => $user]);
    }

    public function updateUser($id)
    {
        $user = User::find($id);
        if(is_null($user->active)){
            $user->password = uniqid($id);
            $user->save();

            Mail::send('emails.'.$user->locale.'.hello', ['user' => $user->username, 'key' => urldecode($user->password)], function($message) use ($user)
            {
                $message->to($user->email, 'ZOOMOV')->subject(trans('auth.welcome', ['name' => $user->username]));
            });
        }
        return Response('OK', 202);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        DB::table("relations")->whereRaw("fan_id ='".$id."' or idol_id ='".$id."'")->delete();
        DB::table("user_invitations")->where('user_id', $id)->delete();
        DB::table("user_occupations")->where('user_id', $id)->delete();

        DB::table("sns_users")->where('user_id', $id)->delete();
        DB::table("message_placeholders")->where('user_id', $id)->delete();

        $projects = Project::where('user_id', $id)->get();

        foreach ($projects as $project){
            $this->doProject($project->id);
            $project->delete();
        }

        $reports = Report::where('user_id', $id)->get();

        foreach ($reports as $report){
            DB::table('report_tags')->where('report_id', $report->id)->delete();
            DB::table('report_lovers')->where('report_id', $report->id)->delete();
            $answers = ReportComment::where('report_id', $report->id)->get();

            foreach ($answers as $answer){
                DB::table('report_comment_supports')->where('report_comment_id', $answer->id)->delete();
                $answer->delete();
            }
        }

        DB::table('report_lovers')->where('user_id', $id)->delete();

        $questions = Question::where('user_id', $id)->get();

        foreach ($questions as $question){
            DB::table('question_tags')->where('question_id', $question->id)->delete();
            DB::table('question_followers')->where('question_id', $question->id)->delete();
            $answers = QuestionAnswer::where('question_id', $question->id)->get();

            foreach ($answers as $answer){
                DB::table('project_answer_supports')->where('project_answer_id', $answer->id)->delete();
                $answer->delete();
            }
        }


        $user->delete();
        return Response('OK', 202);
    }

    public function destroy($id)
    {
        $project = Project::find($id);
        $this->doProject($id);
        $project->delete();
        return Response('OK', 202);
    }

    public function doProject($id){
        DB::table("project_followers")->where('project_id', $id)->delete();
        DB::table("project_lovers")->where('project_id', $id)->delete();
        DB::table("project_views")->where('project_id', $id)->delete();
        DB::table("project_tags")->where('project_id', $id)->delete();
        $comments = ProjectComment::where('project_id', $id)->get();

        foreach ($comments as $comment){
            DB::table('project_comment_supports')->where('project_comment_id', $comment->id)->delete();
            $comment->delete();
        }

        $invitations = Invitation::where('project_id', $id)->get();

        foreach ($invitations as $invitation){
            DB::table('invitation_placeholders')->where('invitation_id', $invitation->id)->delete();
            $invitation->delete();
        }

        $questions = Question::where('project_id', $id)->get();

        foreach ($questions as $question){
            DB::table('question_tags')->where('question_id', $question->id)->delete();
            DB::table('question_followers')->where('question_id', $question->id)->delete();
            $answers = QuestionAnswer::where('question_id', $question->id)->get();

            foreach ($answers as $answer){
                DB::table('project_answer_supports')->where('project_answer_id', $answer->id)->delete();
                $answer->delete();
            }

            $question->delete();
        }

        $reports = Report::where('project_id', $id)->get();

        foreach ($reports as $report){
            DB::table('report_tags')->where('report_id', $report->id)->delete();
            DB::table('report_lovers')->where('report_id', $report->id)->delete();
            $answers = ReportComment::where('report_id', $report->id)->get();

            foreach ($answers as $answer){
                DB::table('report_comment_supports')->where('report_comment_id', $answer->id)->delete();
                $answer->delete();
            }
        }

        $this->deleteProject($id);
    }

    public function recommendations(){
        $recommends =  Project::where('projects.active', 1)
            ->join('recommendations', 'recommendations.project_id', '=', 'projects.id')
            ->join('users', 'users.id', '=', 'projects.user_id')
            ->join('genres', 'genre_id', '=', 'genres.id')
            ->join('cities', 'projects.city_id', '=', 'cities.id')
            ->join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->selectRaw('projects.id, reason, title, projects.genre_id, projects.user_id, projects.city_id, username, duration, countries.sortname,
                genres.name_en as genre, cities.name_en as city_name, recommendations.id as recommendation_id, recommendations.created_at')
            ->orderBy('genre_id')
            ->orderBy('recommendations.created_at', 'desc')
            ->get();

        $projects = Project::where('projects.active', 1)
            ->whereNotIn('projects.id', $recommends->pluck('id')->all())
            ->join('users', 'users.id', '=', 'projects.user_id')
            ->join('genres', 'genre_id', '=', 'genres.id')
            ->join('cities', 'projects.city_id', '=', 'cities.id')
            ->join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->selectRaw('projects.id, title, synopsis, projects.genre_id, projects.user_id, projects.city_id, username, duration,
                genres.name_en as genre, concat(cities.name_en, "(", countries.sortname, ")") as city, projects.created_at')
            ->orderBy('projects.created_at', 'desc')
            ->get();

        return view('admins.recommendations', ['recommendations' => $recommends, 'projects'=>$projects]);
    }

    public function addRecommendation(Request $request){
        $id = $request->input('replaced', 0);
        if($id){
            DB::table('recommendations')->where('project_id',$id)->delete();
        }

        DB::table('recommendations')->insert(array_merge($request->only('project_id', 'reason'), ['user_id'=> Auth::guard('admin')->user()->id]));

        return redirect()->guest('/admins/recommendations');
    }

    public function deleteRecommendation($id){
        DB::table('recommendations')->where('project_id',$id)->delete();
    }

    private function deleteProject($id){
        DB::table('project_languages')->where('project_id', $id)->delete();
        $recruitments = DB::table("project_recruitments")->where('project_id', $id)->get();

        foreach ($recruitments as $recruitment){
            DB::table("project_recruitment_users")->where('project_recruitment_id', $recruitment->id)->delete();
            $recruitment->delete();
        }

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
    private function generateStrongPassword($length = 12, $add_dashes = false, $available_sets = 'luds')
    {
        $sets = array();
        if(strpos($available_sets, 'l') !== false)
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        if(strpos($available_sets, 'u') !== false)
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if(strpos($available_sets, 'd') !== false)
            $sets[] = '23456789';
        if(strpos($available_sets, 's') !== false)
            $sets[] = '!@#$%&*?';

        $all = '';
        $password = '';
        foreach($sets as $set)
        {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }

        $all = str_split($all);
        for($i = 0; $i < $length - count($sets); $i++)
            $password .= $all[array_rand($all)];

        $password = str_shuffle($password);

        if(!$add_dashes)
            return $password;

        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while(strlen($password) > $dash_len)
        {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;
        return $dash_str;
    }
}
