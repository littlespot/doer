<?php

namespace Zoomov\Http\Controllers;
use Auth;
use DB;
use Validator;
use Config;
use Illuminate\Http\Request;
use Zoomov\InvitationPlaceholder;
use Zoomov\Project;
use Zoomov\Invitation;
use Zoomov\InvitationInvitationplaceholder;
use Zoomov\ProjectTeam;
use Zoomov\ProjectTeamOccupation;

class InvitationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $box = $request->input('box', 'in');
        $user = $box == 'in' ? 'sender_id' : 'receiver_id';

        return Invitation::join(DB::raw("(select invitation_id, id, checked from invitation_placeholders where 
                  user_id = '".Auth::User()->id."'
                  and placeholder_id=".config('constants.messageplaceholder.'.$box.'box').") place"), function ($join) {
            $join->on('invitations.id', '=', 'place.invitation_id');
            })
            ->join('users', $user, '=', 'users.id')
            ->join('projects', 'project_id', '=', 'projects.id')
            ->leftJoin('occupations', 'occupation_id', '=', 'occupations.id')
            ->selectRaw('invitations.id, project_id, projects.title, quit, occupation_id, occupations.name_'.app()->getLocale().' as name, '.$user
                .' as user_id, username, accepted, checked, invitations.created_at')
            ->orderBy('invitations.created_at', 'desc')
            ->paginate(20);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id, Request $request)
    {
        if($request->input('checked', 0) == 0){
            $place = InvitationPlaceholder::where('invitation_id', $id)
                ->where('placeholder_id', config('constants.messageplaceholder.inbox'))
                ->where('user_id', Auth::id())
                ->first();

            $place->checked = 1;
            $place->save();
        }

        return Invitation::select('id','message as letter')->find($id);
    }

    public function update($id, Request $request){
        $invitation = Invitation::find($id);
        if($invitation->receiver_id != auth()->id() || !is_null($invitation->accepted)) {
            return Response('NOT ALLOWED', 200);
        }
        try{
            $invitation->accepted = $request->input('accept', 0);
            $invitation->save();
            if($invitation->accepted) {
                $team = ProjectTeam::where('project_id', $invitation->project_id)->where('user_id', Auth::id())->first();
                if(!$invitation->quit){
                    if (is_null($team)) {
                        $team = ProjectTeam::create([
                            "project_id" => $invitation->project_id,
                            "user_id" => auth()->id(),
                            "id" => $this->uuid('t')
                        ]);
                    }

                    ProjectTeamOccupation::create([
                        "project_team_id" => $team->id,
                        "occupation_id" => $invitation->occupation_id
                    ]);
                }
                else if(!is_null($team)){
                    if(is_null($invitation->occupation_id)){
                        DB::table('project_team_occupations')->where('project_team_id', $team->id)->delete();
                        $team->delete();
                    }
                    else{
                        DB::table('project_team_occupations')->where('project_team_id', $team->id)->where('occupation_id', $invitation->occupation_id)->delete();
                    }
                }
            }

            $place = InvitationPlaceholder::where('invitation_id', $id)
                ->where('placeholder_id',  config('constants.messageplaceholder.outbox'))
                ->where('user_id', $invitation->sender_id)
                ->first();

            if(!is_null($place)){
                $place->checked = 0;
                $place->save();
            }

            return Response('OK', 200);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $quit = $request->input('quit', 0);
        $validator = $this->validator($request->all());
        if ($validator->fails() || (!$quit && is_null($request->occupation_id))) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $project = Project::find($request->project_id);
        if(is_null($project) || $project->user_id != Auth::id()) {
            return Response('NOT ALLOWED', 501);
        }

        if($quit){
            $team = ProjectTeam::where('project_id', $request->project_id)->where('user_id', $request->receiver_id)->first();

            if(is_null($team)){
                return;
            }

            if($request->occupations){
                $olderRoles = ProjectTeamOccupation::where('project_team_id', $team->id)->pluck('occupation_id')->all();

                $toRemove = array_diff($olderRoles, $request->occupations);

                $toAdd = array_diff($request->occupations, $olderRoles);

                foreach ($toRemove as $role){
                    $this->invite($request->project_id, $request->message, $request->receiver_id, $role, 1);
                }

                foreach ($toAdd as $role){
                    $this->invite($request->project_id, $request->message, $request->receiver_id, $role, 0);
                }
            }
            else{
                $this->invite($request->project_id, $request->message, $request->receiver_id, null, 1);
                $team->deleted = 1;
                $team->save();
            }
        }
        else{
            $result = $this->invite($request->project_id, $request->message, $request->receiver_id, $request->occupation_id, 0);
            if($result){
                return $result->id;
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id, Request $request)
    {
        $box = $request->input('box', 'in');
        $invitation = Invitation::find($id);

        if(is_null($invitation)){
            return Response('NOT FOUND', 404);
        }

        if($box == 'out'){
            if($invitation->sender_id != Auth::id()){
                return Response('NOT ALLOWED', 501);
            }

            if(is_null($invitation->accepted)){
                DB::table('invitation_placeholders')->where('invitation_id', $id)->delete();
                $invitation->delete();
            }
            else{
                DB::table('invitation_placeholders')->where('invitation_id', $id)
                    ->where('placeholder_id', config('constants.messageplaceholder.'.$box.'box'))
                    ->where('user_id', Auth::id())
                    ->delete();
            }

            return Response('OK', 200);
        }
        else{
            if($invitation->receiver_id != Auth::id()){
                return Response('NOT ALLOWED', 501);
            }

            if(is_null($invitation->accepted)){
                $invitation->accepted = 0;
                $invitation->save();
            }

            DB::table('invitation_placeholders')->where('invitation_id', $id)
                ->where('placeholder_id', config('constants.messageplaceholder.'.$box.'box'))
                ->where('user_id', Auth::id())
                ->delete();
            return Response('OK', 200);
        }
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'receiver_id' => 'required',
            'message' => 'required',
            'project_id' => 'required'
        ]);
    }

    private function invite($project_id, $message, $receiver_id, $occupation_id, $quit){
        $invitation = Invitation::create([
            "project_id" => $project_id,
            "message" => $message,
            "receiver_id" => $receiver_id,
            "sender_id" => auth()->id(),
            "quit" => $quit,
            "occupation_id" => $occupation_id
        ]);

        InvitationPlaceholder::create([
            "invitation_id" => $invitation->id,
            "user_id" => $invitation->receiver_id,
            "placeholder_id" => config('constants.messageplaceholder.inbox')
        ]);

        InvitationPlaceholder::create([
            "invitation_id" => $invitation->id,
            "user_id" => $invitation->sender_id,
            "placeholder_id" => config('constants.messageplaceholder.outbox')
        ]);

        return $invitation;
    }
}
