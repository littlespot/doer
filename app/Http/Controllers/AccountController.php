<?php

namespace Zoomov\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use Config;
use DB;
use Illuminate\Support\Facades\Storage;
use Validator;
use Hash;
use Zoomov\ApplicationPlaceholder;
use Zoomov\City;
use Zoomov\User;
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
            ->leftJoin(DB::raw("(select cities.id, sortname, cities.name_".app()->getLocale()." as name from cities inner join departments on cities.department_id = departments.id inner join countries on departments.country_id = countries.id) city"), function ($join){
              $join->on("city_id", "=", "city.id");
            })
            ->selectRaw("projects.id, title, -1 as active, IFNULL(duration, 0) as duration, synopsis, 
                IFNULL(city.name, '') as city_name , IFNULL(sortname, '') as sortname, IFNULL(genres.name_".app()->getLocale().", '') as genre_name, 
                projects.updated_at")
            ->get();

        $projects = Project::where('user_id', auth()->id())->where('active', 0)
             ->join('genres', 'projects.genre_id', '=', 'genres.id')
             ->join('cities', 'projects.city_id', '=', 'cities.id')
             ->join('departments', 'department_id', '=', 'departments.id')
             ->join('countries', 'country_id', '=', 'countries.id')
             ->selectRaw("projects.id, title, projects.active, duration, synopsis,
                 cities.name_".app()->getLocale()." as city_name, countries.name_".app()->getLocale()." as country, genres.name_".app()->getLocale()." as genre_name, projects.updated_at")
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

    public function detail(Request $request){

        $countries = Country::select('id', 'region', 'phonecode', 'name_' . app()->getLocale() . ' as name')
            ->orderBy('rank')
            ->orderByRaw('convert(name_' . app()->getLocale() .' using gbk) ASC')
            ->get();
        $occupations = Occupation::where('name', '<>', 'Planner')
            ->leftJoin(DB::raw("(select 1 as old, occupation_id from user_occupations where user_id = '".auth()->id()."') as users"), function ($join){
                $join->on('users.occupation_id', '=', 'occupations.id');
            })
            ->selectRaw("id, name_".auth()->user()->locale." as name, IFNULL(users.old, 0) as old")
            ->get();
        $user = User::find(auth()->id());
        if($user->city_id){
            $location = DB::table('cities')->where('cities.id', auth()->user()->city_id)->join('departments', 'department_id', '=', 'departments.id')
                ->join('countries', 'country_id', '=', 'countries.id')
                ->selectRaw("cities.name_".app()->getLocale()." as city_name, cities.department_id, departments.name_".app()->getLocale()." as depart_name, 
                    country_id, countries.name_".app()->getLocale()." as country_name, phonecode, phonecode as fix_code, phonecode as mobile_code, postal, cities.id as city_id")
                ->first();
        }
        else{
            $location = null;
        }

        $carnet = DB::table('user_contacts')
            ->leftJoin(DB::raw("(select country_id, department_id, cities.id, phonecode, postal from cities 
                inner join departments on department_id = departments.id inner join countries on country_id = countries.id) as cities"), function ($join){
                $join->on('user_contacts.city_id', '=', 'cities.id');
            })
            ->where('user_contacts.user_id', $user->id)
            ->selectRaw('prefix, first_name, last_name, address, IFNULL(user_contacts.postal, cities.postal) as postal, city_id, company, IFNULL(fix_code, phonecode) as fix_code, fix_number, IFNULL(mobile_code, phonecode) as mobile_code, mobile_number')
            ->first();

        if(!$carnet){
            $contact = ['prefix'=>'', 'last_name'=>'', 'first_name'=>'', 'fix_number'=>'', 'mobile_number'=>'', 'email'=>$user->email, 'address'=>''];
            foreach ((array)$location as $key=>$value){
                $contact[$key] = $value;
            }
        }
        else{
            $contact = [];
            foreach ((array)$carnet as $key=>$value){
                $contact[$key] = $value;
            }

            if(!$carnet->city_id){
                foreach ((array)$location as $key=>$value){
                    $contact[$key] = $value;
                }
            }
        }
        $user->username_datediff = floor((time() -strtotime($user->usernamed_at))/60/60/24);

        return view('personal', ["countries" => $countries, "occupations" => $occupations, "location"=>$location, 'user'=>$user, 'contact'=>$contact, 'previous'=>url()->previous(), 'anchor'=>$request->input('anchor', 'information')]);
    }

    public function preparations(){
        return Project::where('user_id', auth()->id())->whereNull('active')->select('id', 'title')
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
        $user = auth()->user();
        $this->validate($request, [
            'username' => 'required|max:40',
            'presentation' => 'max:800',
            'city_id' => 'required'
        ]);
        preg_match('/(?P<digit>\d+)/', $request['city_id'], $matches);

        $request['city_id'] = $matches[0];
        $oldname = $user->username;
        $user->update($request->only('username', 'city_id', 'sex', 'birthday', 'presentation'));

       if($oldname != $user->username){
           $user->update([
               'usernamed_at' => gmdate("Y-m-d H:i:s", time())
           ]);
       }

        $errors = [];
        if(!Storage::disk('public')->exists('avatars/'.$user->id.'.jpg')) {
           array_add($errors, 'poster');
        }

        $oldRoles = UserOccupation::where('user_id', auth()->id())->pluck('occupation_id')->all();
        $toRemove = array_diff($oldRoles, $request->role);
        $toAdd = array_diff($request->role, $oldRoles);

        if(sizeof($toRemove)){
            DB::table('user_occupations')->where('user_id', auth()->id())->whereIn('occupation_id', $toRemove)->delete();
        }

        foreach($toAdd as $occupation){
            UserOccupation::create([
                "user_id" => auth()->id(),
                "occupation_id" => $occupation
            ]);
        }

        if(!UserOccupation::where('user_id', auth()->id())->exists()) {
            array_add($errors, 'occupation');
        }

        if(sizeof($errors)){
            return back()->withErrors($errors)->withInput();
        }

        if($user->active == 0){
            $user->update(['active'=>1]);
            return redirect('/'.$user->preference?:"home");
        }
        else{
            $countries = Country::select('id', 'phonecode', 'name_' . app()->getLocale() . ' as name')
                ->orderByRaw('convert(name_' . app()->getLocale() .' using gbk) ASC')
                ->get();
            $occupations = Occupation::where('name', '<>', 'Planner')
                ->leftJoin(DB::raw("(select 1 as old, occupation_id from user_occupations where user_id = '".auth()->id()."') as users"), function ($join){
                    $join->on('users.occupation_id', '=', 'occupations.id');
                })
                ->selectRaw("id, name_".auth()->user()->locale." as name, IFNULL(users.old, 0) as old")
                ->get();

            $location = DB::table('cities')->where('cities.id', auth()->user()->city_id)->join('departments', 'department_id', '=', 'departments.id')
                ->join('countries', 'country_id', '=', 'countries.id')
                ->selectRaw("cities.name_".app()->getLocale()." as city_name, cities.department_id, departments.name_".app()->getLocale()." as depart_name, 
                    country_id, countries.name_".app()->getLocale()." as country_name, phonecode, phonecode as fix_code, phonecode as mobile_code, postal, cities.id as city_id")
                ->first();

            $contact = ['prefix'=>'', 'last_name'=>'', 'first_name'=>'', 'fix_number'=>'', 'mobile_number'=>'', 'email'=>$user->email, 'address'=>''];
            foreach ((array)$location as $key=>$value){
                $contact[$key] = $value;
            }


            return view('personal', ["countries" => $countries, "occupations" => $occupations, "location"=>$location, 'user'=>$user, 'contact'=>$contact, 'previous'=>$request['previous'], 'anchor'=>'contact']);
        }
    }

    public function info(Request $request){
        $changed = false;
        $user = User::find(auth()->id());

        if($request->has('username') && $user->username != $request->username){
            if(floor((time() -strtotime($user->usernamed_at))/60/60/24) >= 30){
                $changed |= true;
                $user->username = $request->username;
                $user->usernamed_at = gmdate("Y-m-d H:i:s", time());
            }
        }

        if($request->has('sex')){
            if(!is_numeric(array_search($request->sex, ['m','s','f','o']))){
                $changed |= ($user->sex != 's');
                $user->sex = 's';
            }
            else if($user->sex != $request->sex){
                $user->sex = $request->sex;
                $changed |= true;
            }
        }
        if($request->has('birthday')){
            $birthday = date("Y-m-d", strtotime($request->birthday));

            if(!$user->birthday || $user->birthday != $birthday){
                $changed |= true;

                $user->birthday = $birthday;
            }
        }
        elseif($user->birthday){
            $user->birthday = null;
            $changed |= true;
        }

        if($changed){
            $user->updated_at = gmdate("Y-m-d H:i:s", time());
            $user->save();
        }


        return $user->birthday ? date("Y-m-d", strtotime($request->birthday)) : null;
    }

    public function presentation(Request $request){

        $user = User::find(auth()->id());
        $presentation = $request->input('presentation', '');
        if(!$presentation){
            $presentation = trans('personal.PLACES.description');
        }

        $user->update(['presentation'=>$presentation]);

        return $presentation;
    }

    public function saveOccupation(Request $request){
        $oldRoles = UserOccupation::where('user_id', auth()->id())->pluck('occupation_id')->toArray();
        $toRemove = array_diff($oldRoles, $request->roles);
        $toAdd = array_diff($request->roles, $oldRoles);

        if(sizeof($toRemove) == sizeof($oldRoles) && sizeof($toAdd) == 0){
            return $oldRoles;
        }

        if(sizeof($toRemove)){
            DB::table('user_occupations')->where('user_id', Auth::id())->whereIn('occupation_id', $toRemove)->delete();
        }

        foreach($toAdd as $occupation){
            UserOccupation::create([
                "user_id" => auth()->id(),
                "occupation_id" => $occupation
            ]);
        }

        return null;
    }

    public function removeOccupation($id)
    {
        $occupation = UserOccupation::where([
            "user_id" => auth()->id(),
            "occupation_id" => $id
        ])->first();

        if($occupation){
            return 0;
        }
        else{
          $occupation->delete();
        }
    }

    public function contact(Request $request){

        $this->validate($request,[
            'first_name'=>'required|max:40',
            'last_name'=>'required|max:40',
            'address'=>'required|max:200',
            'postal' => 'required|max:16',
            'city_id'=>'required',
            'mobile_number' =>'required_without:fix_number',
            'mobile_code' => 'required_with:mobile_number',
            'fix_code' => 'required_with:fix_number'
        ]);
        $value = $request->except('anchor', 'previous', '_token');
        $value['city_id'] = str_replace('number:', '', $request['city_id']);
        if(DB::table('user_contacts')->where('user_id', auth()->id())->exists()){

            DB::table('user_contacts')->where('user_id', auth()->id())
                ->update($value);
        }
        else{

            DB::table('user_contacts')->insert(array_add($value, 'user_id', auth()->id()));
        }
        $request->session()->flash('anchor', 'contact');
        $request->session()->flash('status', 'contact');
        return back()->withInput();
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|max:40',
            'presentation' => 'max:800',
            'city_id' => 'required',

        ]);
    }
}
