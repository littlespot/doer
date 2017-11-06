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
               from reminder_placeholders where user_id = '".Auth::User()->id."'
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
        if($request->input('checked', 0) == 0){
            $place = ReminderPlaceholder::where('reminder_id', $id)
                ->where('placeholder_id', config('constants.messageplaceholder.inbox'))
                ->where('user_id', Auth::id())
                ->first();

            $place->checked = 1;
            $place->save();

            $counter = ReminderPlaceholder::where('reminder_id', $id)
                ->where('placeholder_id', config('constants.messageplaceholder.inbox'))
                ->where('checked', 0)
                ->exists();

            if(!$counter){
                $place = ReminderPlaceholder::where('reminder_id', $id)
                    ->where('placeholder_id', config('constants.messageplaceholder.outbox'))
                    ->first();

                $place->checked = 1;
                $place->save();
            }
        }

        return Reminder::select('id','body as letter')->find($id);
    }

    public function update($id){
        $place = ReminderPlaceholder::find($id);

        if($place->user_id != Auth::id() || $place->place_id != config('constants.messageplaceholder.inbox')){
            return null;
        }

        $place->checked = true;
        $place->save();
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'subject' => 'required|min:4|max:40',
            'body' => 'required|min:10|max:800',
            'project_id' => 'required'
        ]);

        if ($validator->fails()) {
            return new JsonResponse($validator->errors()->getMessages(), 422);
        }

        $team = ProjectTeam::where('project_id', $request->project_id)->select('user_id')->get();

        if(is_null($team) || is_null($team->where('user_id', Auth::id())->first())){
            return null;
        }

        if(sizeof($team) == 1){
            return Response('No team to reminder', 200);
        }

        $message = Reminder::create([
            "project_id" => $request->project_id,
            "subject" => $request->subject,
            "body" => $request->body,
            "sender_id" => Auth::id()
        ]);

        foreach ($team as $member){
            ReminderPlaceholder::create([
                "reminder_id" => $message->id,
                "placeholder_id" => config('constants.messageplaceholder.'.($member->user_id == Auth::id() ? 'out' :'in').'box'),
                "user_id" => $member->user_id
            ]);
        }

        return Response('OK', 200);
    }

    public function destroy($id){
        $place = ReminderPlaceholder::find($id);

        if($place->user_id != Auth::id()){
            return Response('NOT ALLOWED', 501);
        }

        if($place->place_id == config('constants.messageplaceholder.outbox')){
            $reminder = Reminder::find($place->reminder_id);
            if($reminder->sender_id == Auth::id()){
                $place->delete();
            }
            else{
                return Response('NOT ALLOWED', 501);
            }
        }
        else{
            $place->delete();
        }
    }
}
