<?php

namespace Zoomov\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use Config;
use DB;
use Validator;
use Hash;
use Zoomov\ApplicationPlaceholder;
use Zoomov\City;
use Zoomov\Country;
use Zoomov\InvitationPlaceholder;
use Zoomov\MessagePlaceholder;
use Zoomov\Occupation;
use Zoomov\Project;
use Zoomov\ReminderPlaceholder;
use Zoomov\UserOccupation;

class AccountController extends Controller
{
    public function index()
    {
        $preparations = Project::where('user_id', Auth::User()->id)->whereNull('active')
            ->leftJoin('genres', 'genre_id', '=', 'genres.id')
            ->leftJoin(DB::raw("(select cities.id, sortname, cities.name_".Auth::user()->locale." as name from cities inner join departments on cities.department_id = departments.id inner join countries on departments.country_id = countries.id) city"), function ($join){
              $join->on("city_id", "=", "city.id");
            })
            ->selectRaw("projects.id, title, -1 as active, IFNULL(duration, 0) as duration, synopsis, 
                IFNULL(city.name, '') as city_name , IFNULL(sortname, '') as sortname, IFNULL(genres.name_".Auth::user()->locale.", '') as genre_name, 
                projects.updated_at")
            ->get();

        $projects = Project::where('user_id', Auth::User()->id)->where('active', 0)
             ->join('genres', 'projects.genre_id', '=', 'genres.id')
             ->join('cities', 'projects.city_id', '=', 'cities.id')
             ->join('departments', 'department_id', '=', 'departments.id')
             ->join('countries', 'country_id', '=', 'countries.id')
             ->selectRaw("projects.id, title, projects.active, duration, synopsis,
                 cities.name_".Auth::user()->locale." as city_name, sortname, genres.name_".Auth::user()->locale." as genre_name, projects.updated_at")
            ->orderBy('active')
            ->orderBy('updated_at')
            ->get();

        return view('user.projects', ["projects"=>$projects, "preparations"=>$preparations]);
    }

    public function show($id)
    {
        if($id == 'p'){
            return Project::where('active', 1)->where('user_id', Auth::User()->id)->count();
        }
        else if($id == 'm'){
            $message =  MessagePlaceholder::where('user_id', Auth::User()->id)
                ->where('placeholder_id', config('constants.messageplaceholder.inbox'))
                ->where('checked', false)
                ->count();
            $inv = InvitationPlaceholder::where('user_id', Auth::User()->id)
                ->where('placeholder_id', config('constants.messageplaceholder.inbox'))
                ->join('invitations', 'invitation_id', '=', 'invitations.id')
                ->where('invitations.accepted', null)
                ->count();

            return $message + $inv;
        }
        else if($id == 'n'){
            $app = ApplicationPlaceholder::where('user_id', Auth::User()->id)
                ->where('placeholder_id', config('constants.messageplaceholder.inbox'))
                ->join('applications', 'application_id', '=', 'applications.id')
                ->where('applications.accepted', null)
                ->count();

            $rem = ReminderPlaceholder::where('user_id', Auth::User()->id)
                ->where('placeholder_id', config('constants.messageplaceholder.inbox'))
                ->where('checked', false)
                ->count();

            $notes = DB::table('notification_receivers')
                ->where('user_id',  Auth::User()->id)
                ->where('checked', false)
                ->count();
            return $app + $rem + $notes;
        }
    }

    public function detail(){
        $countries = Country::select('id', 'sortname', 'name_' . Auth::user()->locale . ' as name')
            ->orderByRaw('convert(name_' . Auth::user()->locale .' using gbk) ASC')
            ->get();
        $occupations = Occupation::where('name', '<>', 'Planner')
            ->leftJoin(DB::raw("(select 1 as old, occupation_id from user_occupations where user_id = '".Auth::id()."') as users"), function ($join){
                $join->on('users.occupation_id', '=', 'occupations.id');
            })
            ->selectRaw("id, name_".Auth::user()->locale." as name, IFNULL(users.old, 0) as old")
            ->get();

        if(Auth::user()->city_id){
            $location = City::where('cities.id', Auth::user()->city_id)->join('departments', 'department_id', '=', 'departments.id')
                ->selectRaw("cities.name_".Auth::user()->locale." as city_name, cities.department_id, departments.name_".Auth::user()->locale." as depart_name, country_id")
                ->first();
        }
        else{
            $location = null;
        }

        return view('personal', ["countries" => $countries, "occupations" => $occupations, "location"=>$location]);
    }

    public function preparations(){
        return Project::where('user_id', Auth::User()->id)->whereNull('active')->select('id', 'title')
            ->orderByRaw('convert(title using gb2312)')->get();
    }

    public function messages(){

        $message =  MessagePlaceholder::where('user_id', Auth::User()->id)
            ->where('placeholder_id', config('constants.messageplaceholder.inbox'))
            ->where('checked', false)
            ->count();
        $inv = InvitationPlaceholder::where('user_id', Auth::User()->id)
            ->where('placeholder_id', config('constants.messageplaceholder.inbox'))
            ->join('invitations', 'invitation_id', '=', 'invitations.id')
            ->where('invitations.accepted', null)
            ->count();

        $app = ApplicationPlaceholder::where('user_id', Auth::User()->id)
            ->where('placeholder_id', config('constants.messageplaceholder.inbox'))
            ->join('applications', 'application_id', '=', 'applications.id')
            ->where('applications.accepted', null)
            ->count();

        $rem = ReminderPlaceholder::where('user_id', Auth::User()->id)
            ->where('placeholder_id', config('constants.messageplaceholder.inbox'))
            ->where('checked', false)
            ->count();

        $notes = DB::table('notification_receivers')
            ->where('user_id',  Auth::User()->id)
            ->where('checked', false)
            ->count();

        return [$message + $inv, $app + $rem + $notes];
    }

    public function update($pwd, Request $request)
    {
        $validator = Validator::make($request->all(),  [
            'old' => 'required',
            'password' => 'min:6|max:16|required',
            'password_confirmation' => 'min:6|max:16|same:password'
        ]);
        $user = Auth::user();

        $validator->after(function($validator) use ($user, $pwd)
        {
            if(!Hash::check($pwd, $user->password)){
                $validator->errors()->add('password', trans('auth.failed'));
            }
        });

        if ($validator->fails()) {
            return \Response::json($validator->errors(), 422);
        }

        $user->password = bcrypt($request->password);
        $user->save();
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $changed = false;
        if($user->active == 0){
            $changed = true;
            $user->active = 1;
        }

        $validator = $this->validator($request->all());
        $path = '/context/avatars/'.$user->id.'.jpg';

        if($changed){
            $validator->after(function($validator) use ($request, $path)
            {
                if(!file_exists(public_path($path))){
                    $validator->errors()->add('poster', trans('messages.avatar'));
                }
            });
        }

        if ($validator->fails()) {
            return Response($validator->errors(), 400);
        }

        try{
            if($user->professional){
                return \Response::json($user);
            }

            if($user->username != $request->username){
                if((time() -strtotime($user->usernamed_at))%60/60/60/24 < 30){
                    $changed |= true;
                    $user->username = $request->username;
                    $user->usernamed_at = gmdate("Y-m-d H:i:s", time());
                }
            }

            $city = str_replace("number:", "", $request->input('city_id'));

            if( $user->city_id != $city){
                $changed |= true;
                $user->city_id = $city;
            }

            if($user->sex != $request->sex){
                $changed |= true;
                $user->sex = $request->sex;
            }

            if($user->presentation != $request->presentation){
                $changed |= true;
                $user->presentation = trim($request->presentation);
            }

            if(!$request->birthday){
                if(!$user->birthday){
                    $changed |= true;
                    $user->birthday = null;
                }
            }
            else{
                $birthday = date("Y-m-d", strtotime($request->birthday));
                if(!$user->birthday || $user->birthday != $birthday){
                    $changed |= true;
                    $user->birthday = $birthday;
                }
            }

            if($changed) {
                $user->updated_at = gmdate("Y-m-d H:i:s", time());
                $user->save();
            }

            $oldRoles = UserOccupation::where('user_id', Auth::id())->pluck('occupation_id')->all();
            $toRemove = array_diff($oldRoles, $request->role);
            $toAdd = array_diff($request->role, $oldRoles);

            if(sizeof($toRemove)){
                DB::table('user_occupations')->where('user_id', Auth::id())->whereIn('occupation_id', $toRemove)->delete();
            }

            foreach($toAdd as $occupation){
                UserOccupation::create([
                    "user_id" => Auth::id(),
                    "occupation_id" => $occupation
                ]);
            }

            return \Response::json(array('id'=> $user->id, 'username' => $user->username, 'city_id'=>$user->city_id, 'locale'=>$user->locale));
        }
        catch (Exception $e) {
            return $e->getMessage();
        }
    }


    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|min:2|max:40',
            'presentation' => 'required|min:10|max:800',
            'city_id' => 'required',
            'role' => 'required'
        ]);
    }
}
