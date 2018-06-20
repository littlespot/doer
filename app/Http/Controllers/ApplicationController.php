<?php

namespace Zoomov\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Config;
use Zoomov\ApplicationPlaceholder;
use Zoomov\Application;
use Zoomov\ProjectRecruitment;
use Zoomov\ProjectTeam;
use Zoomov\ProjectTeamOccupation;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $box = $request->input('box', 'in');
        $user = $box == 'in' ? 'sender_id' : 'receiver_id';

        return Application::join(DB::raw("(select id, application_id, created_at, checked  from application_placeholders where user_id = '"
            .auth()->id()."' and placeholder_id=".config('constants.messageplaceholder.'.$box.'box').") place"), function ($join) {
                $join->on('applications.id', '=', 'place.application_id');
            })
            ->join('project_recruitments','project_recruitment_id','=','project_recruitments.id')
            ->join('projects', 'project_recruitments.project_id', '=', 'projects.id')
            ->join('occupations', 'project_recruitments.occupation_id', '=', 'occupations.id')
            ->join('users', $user, '=', 'users.id')
            ->selectRaw('applications.id, projects.id as project_id, projects.title, occupations.name_'.app()->getLocale().' as name, place.id as place_id, place.checked, place.created_at, 
                applications.'.$user.', accepted, username')
            ->orderBy('created_at','desc')
            ->paginate(20);
    }

    public function show($id, Request $request)
    {
        if($request->input('checked', 0) == 0){
            $place = ApplicationPlaceholder::where('application_id', $id)
                ->where('placeholder_id', config('constants.messageplaceholder.inbox'))
                ->where('user_id', Auth::id())
                ->first();

            $place->checked = 1;
            $place->save();
        }

        return Application::select('id','motivation as letter')->find($id);
    }

    public function check($id){
        $place = ApplicationPlaceholder::find($id);
        if(!is_null($place) && $place->user_id == Auth::id()){
            $place->checked = 1;
            $place->save();
        }
    }

    public function update($id, Request $request){
        try {
            $application = Application::find($id);
            if(is_null($application) || $application->receiver_id != auth()->id() || !is_null($application->accepted)){
                return null;
            }

            $application->accepted = $request->input('accept', 0);
            $application->save();

            $place = ApplicationPlaceholder::where('application_id', $id)
                ->where('placeholder_id',  config('constants.messageplaceholder.outbox'))
                ->where('user_id', $application->sender_id)
                ->first();

            if(!is_null($place)){
                $place->checked = 0;
                $place->save();
            }

            if ($application->accepted) {
                $recruitment = ProjectRecruitment::find($application->project_recruitment_id);
                $team = ProjectTeam::where('project_id', $recruitment->project_id)->where('user_id', $application->sender_id)->first();

                if ($team == null) {
                    $team = ProjectTeam::create([
                        "project_id" => $recruitment->project_id,
                        "user_id" => $application->sender_id,
                        "id" => $this->uuid('t')
                    ]);
                }

                ProjectTeamOccupation::create([
                    "project_team_id" => $team->id,
                    "occupation_id" => $recruitment->occupation_id
                ]);

                if ($recruitment->quantity == 1) {
                    $recruitment->delete();
                } else {
                    $recruitment->quantity = $recruitment->quantity - 1;
                    $recruitment->save();
                }
            }

            return Response('OK', 200);
        }
        catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function store(Request $request)
    {
        try {
            $application = Application::create([
                "motivation" => $request->motivation,
                "sender_id" => auth()->id(),
                "receiver_id" => $request->receiver_id,
                "project_recruitment_id" => $request->recruit_id
            ]);

            ApplicationPlaceholder::create([
                "application_id" => $application->id,
                "user_id" => $application->receiver_id,
                "placeholder_id" => config('constants.messageplaceholder.inbox')
            ]);

            ApplicationPlaceholder::create([
                "application_id" => $application->id,
                "user_id" => $application->sender_id,
                "placeholder_id" => config('constants.messageplaceholder.outbox'),
                "checked" => 1
            ]);
        }catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($id, Request $request)
    {
        $box = $request->input('box', 'in');
        $application = Application::find($id);

        if(is_null($application)){
            return null;
        }

        if($box == 'out'){
            if($application->sender_id != Auth::id()){
                return null;
            }

            if(is_null($application->accepted)){
                DB::table('application_placeholders')->where('application_id', $id)->delete();
                $application->delete();
            }
            else{
                DB::table('application_placeholders')->where('application_id', $id)
                    ->where('placeholder_id', config('constants.messageplaceholder.'.$box.'box'))
                    ->where('user_id', Auth::id())
                    ->delete();
            }

            return Response('OK', 200);
        }
        else{
            if($application->receiver_id != Auth::id()){
                return null;
            }

            if(is_null($application->accepted)){
                $application->accepted = 0;
                $application->save();
            }

            DB::table('application_placeholders')->where('application_id', $id)
                ->where('placeholder_id', config('constants.messageplaceholder.'.$box.'box'))
                ->where('user_id', Auth::id())
                ->delete();
            return Response('OK', 200);
        }
    }
}
