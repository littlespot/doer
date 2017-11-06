<?php

namespace Zoomov\Http\Controllers;
use Auth;
use Illuminate\Auth\Access\Response;
use Validator;
use Config;
use DB;
use Illuminate\Http\Request;
use Zoomov\Message;
use Zoomov\MessagePlaceholder;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $id = Auth::id();
        $box = $request->input('box', 'in');

        if ($box == 'in') {
            $parents=  MessagePlaceholder::whereRaw("user_id='".$id."' and placeholder_id=".config('constants.messageplaceholder.inbox')." and parent_id is not null")
                    ->groupBy('parent_id')
                    ->selectRaw('max(message_id) as message_id')
                    ->pluck('message_id');
            return MessagePlaceholder::whereRaw("user_id='".$id."' and placeholder_id=".config('constants.messageplaceholder.inbox')." and parent_id is  null")
                ->orWhereIn('message_id', $parents)
                ->join('messages','message_id','messages.id')
                ->join("users", "sender_id", "=", "users.id")
                ->selectRaw("messages.id, parent_id, messages.subject, sender_id, username, messages.created_at, checked")
                ->orderBy('messages.created_at', 'desc')
                ->paginate(20);
        }
        else {
            return Message::with('receivers')->
                join(DB::raw("(select message_id from message_placeholders where user_id='".$id."' and placeholder_id = '".
                    config('constants.messageplaceholder.' . $box . 'box')."') pl"), function ($join) {
                    $join->on('pl.message_id', '=', 'messages.id');
                })
                ->orderBy('messages.created_at', 'desc')
                ->paginate(20);
        }
    }

    public function show($id, Request $request)
    {
       $message = Message::select('body as letter')
            ->find($id);

        if($request->input('checked', 0) == 0){
            $place = MessagePlaceholder::where('message_id', $id)
                ->where('placeholder_id', config('constants.messageplaceholder.inbox'))
                ->where('user_id', Auth::id())
                ->first();

            $place->checked = 1;
            $place->save();
        }

        $parent = $request->input('parent', 0);
        if($parent > 0){
           $replies = MessagePlaceholder::whereRaw("(parent_id =".$parent." or message_id=".$parent.") and user_id='".Auth::id()."' and message_id <>".$id)
               ->join('messages', 'messages.id', '=', 'message_id')
               ->join('users', 'messages.sender_id', '=', 'users.id')
               ->selectRaw("body, message_placeholders.created_at, sender_id = '".Auth::id()."' as sender, sender_id as user_id, username")
               ->orderBy('message_placeholders.created_at')
               ->get();

           return \Response::json(array("letter"=>$message->letter, "replies"=>$replies));
       }
       else{
           $replies = MessagePlaceholder::whereRaw("parent_id =".$id." and user_id='".Auth::id()."' and message_id <>".$id)
               ->join('messages', 'messages.id', '=', 'message_id')
               ->join('users', 'messages.sender_id', '=', 'users.id')
               ->selectRaw("body, message_placeholders.created_at, sender_id = '".Auth::id()."' as sender, sender_id as user_id, username")
               ->orderBy('message_placeholders.created_at')
               ->get();

           return \Response::json(array("letter"=>$message->letter, "replies"=>$replies));
       }
    }
/*
    public function sent($id)
    {
        $ids = explode('_', $id);
        $message = Message::with('receivers')
            ->find($ids[0]);

        if (is_numeric($ids[2])) {
            $message->replies = MessagePlaceholder::where('parent_id', $ids[2])
                ->where('message_placeholders.id', '<', $ids[1])
                ->join('messages', 'message_id', '=', 'messages.id')
                ->join('users', 'users.id', '=', 'message_placeholders.user_id')
                ->select('messages.id', 'message_placeholders.id as place_id', 'placeholder_id', 'user_id', 'username', 'body', 'messages.created_at')
                ->get();
        }

        return $message;
    }
*/
    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */

    public function store(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $message = Message::create([
            "subject" => $request->subject,
            "sender_id" => Auth::id(),
            "body" => $request->body
        ]);

        MessagePlaceholder::create([
            "parent_id" => is_null($request->parent_id) ? null : $request->parent_id,
            "placeholder_id" => config('constants.messageplaceholder.inbox'),
            "user_id" => $request->receiver_id,
            "message_id" => $message->id,
        ]);

        MessagePlaceholder::create([
            "parent_id" => is_null($request->parent_id) ? null : $request->parent_id,
            "placeholder_id" => config('constants.messageplaceholder.outbox'),
            "user_id" =>  Auth::id(),
            "message_id" => $message->id,
            "checked" => 1
        ]);
    }

    public function update($id, Request $request)
    {
        $message = MessagePlaceholder::find($id);
        if ($request->delete) {
            if ($message->user_id == Auth::User()->id) {
                $message->placeholder_id = config('constants.messageplaceholder.trash');
                $message->save();

                return $message;
            }
        } else if ($message->placeholder_id == config('constants.messageplaceholder.inbox')) {
            $message->checked = $request->checked;
            $message->starred = $request->starred;
            $message->save();
            return $message;
        }

        return null;
    }

    public function destroy($id, Request $request)
    {
        $parent = $request->input('parent', 0);
        if($parent > 0){
            DB::table("message_placeholder")
                ->whereRaw("(parent_id = ".$parent." or message_id =".$parent.") and user_id='".Auth::id()."'")->delete();
        }
        else{
            MessagePlaceholder::where('message_id', $id)
                ->where('user_id', Auth::User()->id)
                ->where('placeholder_id', config('constants.messageplaceholder.'.$request->input('box', 'in').'box'))
                ->delete();
        }

        return Response('OK', 200);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'receiver_id' => 'required',
            'subject' => 'required',
            'body' => 'required'
        ]);
    }
}
