<?php

namespace Zoomov\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use DB;
use Config;
use Validator;
use Zoomov\ProjectTeam;
use Zoomov\Reminder;
use Zoomov\ReminderPlaceholder;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        $param = $request->input('box', 'in');
        $box = config('constants.messageplaceholder.'.$param.'box');

        $reminders = Reminder::join(DB::raw("(select reminder_id, id, checked, created_at
               from reminder_placeholders where user_id = '".auth()->id()."'
               and placeholder_id=".$box.") place"), function ($join) {
            $join->on('reminders.id', '=', 'place.reminder_id');
        })
            ->join('projects', 'reminders.project_id', '=', 'projects.id');

        if($param == "in"){
           return $reminders->join('users', 'reminders.sender_id', '=', 'users.id')
                ->select('reminders.id', 'reminders.subject', 'place.id as place_id', 'reminders.project_id',
                    'projects.title', 'reminders.sender_id', 'checked', 'place.created_at', 'username')
                ->orderBY('created_at','desc')
                ->paginate(20);
        }
        else{
            return $reminders
                ->with('receivers')
                ->select('reminders.id', 'reminders.subject', 'place.id as place_id', 'projects.title', 'checked', 'place.created_at')
                ->orderBY('created_at','desc')
                ->paginate(20);
        }
    }

    public function show($id, Request $request)
    {
        ReminderPlaceholder::where(['reminder_id'=>$id, 'placeholder_id'=>config('constants.messageplaceholder.inbox'), 'user_id'=>auth()->id()])
            ->update(['checked' => 1]);

        $counter = ReminderPlaceholder::where('reminder_id', $id)
            ->where(['reminder_id'=>$id, 'placeholder_id'=>config('constants.messageplaceholder.inbox'), 'checked'=>0])
            ->exists();

        if(!$counter){
            DB::table("reminder_placeholders")->where(['reminder_id'=>$id, 'placeholder_id'=>config('constants.messageplaceholder.outbox')])
                ->update(['checked' => 1]);
        }
    }

    public function update($id){
        $place = ReminderPlaceholder::find($id);

        if($place->user_id != auth()->id() || $place->place_id != config('constants.messageplaceholder.inbox')){
            return null;
        }

        $place->checked = true;
        $place->save();
    }

    public function store(Request $request){
        $this->validate($request,[
            'subject' => 'required|min:4|max:40',
            'project' => 'required'
        ]);

        $team = ProjectTeam::where('project_id',  $request['project']['id'])->whereNotNull('user_id')->pluck('user_id')->toArray();

        if(is_null($team) || !in_array(auth()->id(), $team)){
            return Response(trans('notification.ERRORS.require_authorization', ['title'=>$request['project']['title']]), 200);
        }

        if(sizeof($team) == 1){
            return Response(trans('notification.ERRORS.require_team',  ['title'=>$request['project']['title']]), 200);
        }

        $message = Reminder::create([
            "project_id" => $request['project']['id'],
            "subject" => $request->subject,
    /*        "body" => $request->body,*/
            "sender_id" => auth()->id()
        ]);

        foreach ($team as $member){
            ReminderPlaceholder::create([
                "reminder_id" => $message->id,
                "placeholder_id" => config('constants.messageplaceholder.'.($member == auth()->id() ? 'out' :'in').'box'),
                "user_id" => $member
            ]);
        }

        return Response('OK', 200);
    }

    public function destroy($id){
        $place = ReminderPlaceholder::where(['reminder_id'=>$id, 'user_id'=>auth()->id()])->first();

        if($place){
            $reminder = Reminder::find($id);
            if($place->placeholder_id == config('constants.messageplaceholder.outbox') && $reminder->sender_id == auth()->id()){
                $place->delete();
                if(DB::table('reminder_placeholders')->where('reminder_id', $id)->exists()){
                    DB::table('reminder_placeholders')->where(['reminder_id'=>$id, 'placeholder_id'=>config('constants.messageplaceholder.inbox')])
                        ->update(['checked'=>2]);
                }
                else{
                    $reminder->delete();
                }
            }
            else if($place->placeholder_id == config('constants.messageplaceholder.inbox') && $place->checked){
                $place->delete();
                if(!DB::table('reminder_placeholders')->where('reminder_id',$id)->exists()){
                    $reminder->delete();
                }
            }
        }
    }
}
