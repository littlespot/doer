<?php

namespace Zoomov\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;

use Zoomov\ProjectComment;
use Zoomov\ProjectCommentSupport;
class CommentController extends Controller
{
    public function show($id)
    {
        return ProjectComment::where('project_id',$id)
            ->where('deleted', 0)
            ->with('parent')
            ->join('users', 'users.id', '=', 'project_comments.user_id')
            ->leftJoin(DB::raw("(select p.id, message, p.user_id, u.username from project_comments p inner join users u 
                on p.user_id = u.id where p.project_id = '".$id." and p.deleted = 0') parent"),
                'parent.id', '=', 'project_comments.parent_id')
            ->leftJoin(DB::raw("(select count(id) as cnt, project_comment_id from project_comment_supports group by project_comment_id) supports"),
                'supports.project_comment_id', '=', 'project_comments.id')
            ->leftJoin(DB::raw("(select id as supported, project_comment_id from project_comment_supports where user_id ='". auth()->id()."') mysupport"),
                'mysupport.project_comment_id', '=', 'project_comments.id')
            ->selectRaw("project_comments.id, project_comments.user_id, users.username,  project_comments.message, project_comments.created_at, 
                parent_id,  FLOOR((unix_timestamp(now()) - unix_timestamp(project_comments.created_at))/60/60/24) <1  as newest,   
                (CASE WHEN project_comments.user_id = '".auth()->id()."' THEN 1 ELSE 0 END) as mine,
                IFNULL(supports.cnt, 0) as supports_cnt, IFNULL(mysupport.supported, 0) as supported")
            ->orderBy('created_at', 'desc')
            ->paginate(12);
    }

    public function update($id)
    {
        try {
            $userId = Auth::User()->id;

            $support = ProjectCommentSupport::where('user_id', $userId)
                ->where('project_comment_id',  $id)
                ->first();

            if($support != null){
                $support ->delete();
                return '0';
            }
            else {
                ProjectCommentSupport::create([
                    'project_comment_id' => $id,
                    'user_id' => $userId
                ]);

                return '1';
            }
        }catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function store(Request $request)
    {
        try {
            $newMessage = ProjectComment::create([
                "message" => $request->message,
                "parent_id" => $request->parent_id,
                "user_id" => auth()->id(),
                "project_id" => $request->related_id
            ]);
            $newMessage->created_at = date('Y-m-d H:i', time());
            $newMessage->username = Auth::user()->username;
            return $newMessage;
        }catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($id)
    {
        try {
            $message = ProjectComment::find($id);
            if($message->user_id === Auth::id()){
                $message->deleted = 1 ;
                $message->save();
            }

            return '';
        }catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
