<?php

namespace Zoomov\Http\Controllers;
use Auth;
use Config;
use DB;

use Session;
use Zoomov\Application;
use Zoomov\ApplicationPlaceholder;
use Zoomov\City;
use Zoomov\Invitation;
use Zoomov\InvitationPlaceholder;
use Zoomov\MessagePlaceholder;
use Zoomov\Notification;
use Zoomov\Reminder;
use Zoomov\ReminderPlaceholder;
use Zoomov\SnsUser;
use Zoomov\User;
use Zoomov\Project;
use Zoomov\Relation;
use Zoomov\UserOccupation;
use Illuminate\Http\Request;

class ProfileController extends VisitController
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $fans = Relation::where('idol_id', $user->id)->get();
        $idols = Relation::where('fan_id', $user->id)->get();
        $friends = Relation::where('idol_id', $user->id)->where('love', 1)->count();
        $city = City::join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->select('cities.name_'.app()->getLocale().' as name', 'countries.name_'.app()->getLocale().' as country');

        $occupations = UserOccupation::where('user_id', $user->id)
            ->join('occupations', 'user_occupations.occupation_id', '=', 'occupations.id')
            ->select('occupations.name_'.app()->getLocale().' as name')
            ->get();

        $catalogues = trans('personal.PROJECTS');

        return view('profile', ['user'=>$user,'admin'=>1,  'city'=>$city->find($user->city_id), 'fans_cnt'=>$fans->count(), 'friends_cnt'=>$friends,
            'catalogues'=>$catalogues, 'idols_cnt'=>$idols->count(), 'relation'=>'Self', 'admin'=>true, 'occupations'=>$occupations, 'anchor'=>$request->input('anchor', 'creator')]);
    }

    public function messages(){
        $inbox = config('constants.messageplaceholder.inbox');
        $outbox = config('constants.messageplaceholder.outbox');

        $app = Invitation::where('accepted', null)
            ->join(DB::raw("(select invitation_id from invitation_placeholders where user_id = '".auth()->id()."'
               and placeholder_id=".$inbox.") place"), function ($join) {
                $join->on('invitations.id', '=', 'place.invitation_id');
            })
            ->count();

        $rem = MessagePlaceholder::where('checked', 0)
            ->where('user_id', auth()->id())
            ->where('placeholder_id',config('constants.messageplaceholder.inbox'))
            ->count();

        $counterApp = InvitationPlaceholder::where('user_id', auth()->id())
            ->whereRaw("placeholder_id in (".$inbox.",".$outbox.")")
            ->selectRaw('placeholder_id = '.$outbox.' as outbox, count(id) as cnt')
            ->groupBy("placeholder_id")
            ->orderBy('outbox')
            ->get();

        $counterRem = MessagePlaceholder::where('user_id', auth()->id())
            ->whereRaw("placeholder_id in (".$inbox.",".$outbox.")")
            ->selectRaw('placeholder_id = '.$outbox.' as outbox, count(id) as cnt')
            ->groupBy("placeholder_id")
            ->orderBy('outbox')
            ->get();

        $roles = Project::where(['active' => 1, 'user_id' =>auth()->id()])
            ->get(['id', 'title']);

        return view('messages', ["invitations_cnt" => $app, "messages_cnt"=>$rem, "roles"=>$roles, "invitations"=>$counterApp, "messages"=>$counterRem]);
    }

    public function notifications(){
        $inbox = config('constants.messageplaceholder.inbox');
        $outbox = config('constants.messageplaceholder.outbox');

        DB::table('notification_receivers')->where('notification_receivers.user_id', auth()->id())
            ->where('checked', false)
            ->update(['checked'=>true]);

        $notifications = Notification::join('notification_receivers', 'notification_id', '=', 'notifications.id')
            ->where('notification_receivers.user_id', auth()->id())
            ->select('notification_receivers.id','title','body','created_at', 'checked')
            ->orderBy('created_at','desc')
            ->get();

        $app = Application::where('accepted', null)
            ->join(DB::raw("(select application_id from application_placeholders where user_id = '".auth()->id()."'
               and placeholder_id=".$inbox.") place"), function ($join) {
                $join->on('applications.id', '=', 'place.application_id');
            })
            ->count();

        $appNoAnswer = Application::where('accepted', null)
            ->join(DB::raw("(select application_id from application_placeholders where user_id = '".auth()->id()."'
               and placeholder_id=".$outbox.") place"), function ($join) {
                $join->on('applications.id', '=', 'place.application_id');
            })
            ->count();

        $counterApp = ApplicationPlaceholder::where('user_id', auth()->id())
            ->whereRaw("placeholder_id in (".$inbox.",".$outbox.")")
            ->selectRaw('placeholder_id = '.$outbox.' as outbox, count(id) as cnt')
            ->groupBy("placeholder_id")
            ->orderBy('outbox')
            ->get();

        $reminderUnchecked = ReminderPlaceholder::where('checked', 0)
            ->where('user_id', auth()->id())
            ->where('placeholder_id',$inbox)
            ->count();

        $reminderUnread = Reminder::where('sender_id', auth()->id())
            ->whereRaw("exists (select 1 from reminder_placeholders where checked = 0 and placeholder_id = ".$inbox.
                " and reminder_id = reminders.id)")
            ->count();

        $counterRem = ReminderPlaceholder::where('user_id', Auth::id())
            ->whereRaw("placeholder_id in (".$inbox.",".$outbox.")")
            ->selectRaw('placeholder_id = '.$outbox.' as outbox, count(id) as cnt')
            ->groupBy("placeholder_id")
            ->orderBy('outbox')
            ->get();

        $roles = Project::whereRaw("active = 1 and exists (select 1 from project_teams where user_id = '".auth()->id()."' and project_teams.project_id = projects.id)")
            ->selectRaw("id, title, user_id, user_id = '".auth()->id()."' as admin")
            ->get();

        return view('notifications', ["notifications"=>$notifications,"applications_cnt" => $app, "appNoAnswer"=>$appNoAnswer,
            "reminderUnchecked"=>$reminderUnchecked, "reminderUnread"=>$reminderUnread, "roles"=>$roles, "applications"=>$counterApp, "reminders"=>$counterRem]);
    }

    public function show($id, Request $request)
    {
        if($id == auth()->id()){
            return $this->index($request);
        }

        $fans = Relation::where('idol_id', $id)->get();
        $idols = Relation::where('fan_id', $id)->get();
        $friends = Relation::where('idol_id', $id)->where('love', 1)->count();
        $city = City::join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->select('cities.name_'.app()->getLocale().' as name', 'countries.name_'.app()->getLocale().' as country');

        $occupations = UserOccupation::where('user_id', $id)
            ->join('occupations', 'user_occupations.occupation_id', '=', 'occupations.id')
            ->select('occupations.name_'.app()->getLocale().' as name')
            ->get();

        $user = User::find($id);

        $relation = 'Stranger';

        $myFan = $idols->where('idol_id', auth()->id())->first();
        if(is_null($myFan)){
            $myIdol = $fans->where('fan_id', auth()->id())->first();

            if(!is_null($myIdol)) {
                if($myIdol->love){
                    $myIdol->love = 0;
                    $myIdol->save();
                }

                $relation = 'Idol';
            }
        }
        else{
            $relation = $myFan->love ? 'Friend' : 'Fan';
        }
        $catalogues = ['creator'=>trans('personal.PROJECTS.creator'), 'participator'=>trans('personal.PROJECTS.participator')];

        return view('profile', ['user'=>$user, 'admin'=>0, 'city'=>$city->find($user->city_id), 'friends_cnt'=>$friends, 'fans_cnt'=>$fans->count(), 'idols_cnt'=>$idols->count(),
            'catalogues'=>$catalogues, 'relation'=>$relation, 'admin'=>false, 'occupations'=>$occupations, 'anchor'=>'creator']);
    }

    public function sns($id){
        return SnsUser::where('user_id', $id)
            ->join('sns', 'sns.id', '=', 'sns_users.sns_id')
            ->select('sns_id', 'sns_name', 'user_id', 'sns.type')
            ->get();
    }

    public function update($id)
    {
        $user = aut()->user();
        $user->locale = $id;
        session('locale', $id);
        $user->save();
        return $id;
    }

    public function plans($id)
    {
        return $this->projects($id, '>0')->orderBy('projects.updated_at', 'desc')->get();
    }

    public function follows($id){
        $p = $this->choose();
        $projects = $p->join('project_followers', 'project_followers.project_id','=', 'projects.id')->where('project_followers.user_id', $id);
        return $this->selection($projects)->orderBy('projects.updated_at', 'desc')->get();

    }

    public function loves($id){
        $p = $this->choose();
        $projects = $p->join('project_lovers', 'project_lovers.project_id','=', 'projects.id')->where('project_lovers.user_id', $id);
        return $this->selection($projects)->orderBy('projects.updated_at', 'desc')->get();

    }

    public function members($id){
        return Project::where('projects.active', '>0')
            ->where('user_id', '<>', $id)
            ->join('users', 'user_id', '=', 'users.id')
            ->join(DB::raw("(select project_teams.project_id, GROUP_CONCAT(occupations.name) as role from
                project_teams inner join
                project_team_occupations on
                project_team_occupations.project_team_id = project_teams.id
                inner join occupations on project_team_occupations.occupation_id = occupations.id
                where project_teams.user_id = '".$id."' group by project_teams.project_id) team"), function ($join) {
                $join->on('team.project_id', '=', 'projects.id');
            })
            ->join('genres', 'genre_id', '=', 'genres.id')
            ->join('cities', 'projects.city_id', '=', 'cities.id')
            ->join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->select('projects.id', 'title', 'synopsis', 'projects.active', 'genre_id','duration',
                'genres.name_'.app()->getLocale().' as genre_name', 'projects.city_id', 'cities.name_'.app()->getLocale().' as city_name',
                'cities.department_id', 'departments.country_id',
                'countries.sortname as sortname',
                'user_id', 'username', DB::raw('DATE_FORMAT(projects.updated_at, "%Y-%m-%d %h:%i:%s") as updated_at'), 'start_at', 'finish_at',
                DB::raw('FLOOR((unix_timestamp(finish_at) - unix_timestamp(now()))/60/60/24) as daterest'),
                DB::raw('datediff(finish_at, projects.created_at) as datediff'))
            ->get();
    }

    public function removeNotification($id){
        DB::table('notification_receivers')->where('id', $id)
            ->where('user_id', auth()->id())
            ->delete();
    }
}
