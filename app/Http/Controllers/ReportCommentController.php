<?php

namespace Zoomov\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Zoomov\ReportComment;
use Zoomov\ReportCommentSupport;

class ReportCommentController extends Controller
{
    public function show($id, Request $request)
    {
        $order = $request->input('order', 'created_at');

        $params = "report_comments.id, report_comments.created_at, report_comments.user_id, username, report_comments.message, report_comments.parent_id, 
            IFNULL(supports.cnt, 0) as supports_cnt, IFNULL(mysupport.id, 0) as mysupport, report_comments.user_id='".auth()->id()."' as mine";

        $comments = ReportComment::where('report_id', $id)->where('deleted', 0)
            ->with('parent')
            ->join('users', 'report_comments.user_id', '=', 'users.id')
            ->leftJoin(DB::raw("(select count(id) as cnt, report_comment_id from report_comment_supports group by report_comment_id) supports"), function ($join) {
                $join->on('report_comment_id', '=', 'report_comments.id');
            })
            ->leftJoin(DB::raw("(select id, report_comment_id from report_comment_supports where user_id = '".auth()->id()."') mysupport"), function ($join) {
                $join->on('mysupport.report_comment_id', '=', 'report_comments.id');
            });

        return $comments->selectRaw($params)
            ->orderBy('report_comments.'.$order, 'desc')
            ->paginate(20);
    }


    public function update($id){
        $report = ReportComment::find($id);

        if(is_null($report)){
            return \Response::json(array("cnt" => -1, "mysupport" => 0));
        }

        if($report->user_id == Auth::id()){
            return;
        }

        $support = ReportCommentSupport::where('user_id', Auth::id())
            ->where('report_comment_id', $id)
            ->first();

        if(!$support){
            ReportCommentSupport::create([
                "report_comment_id" => $id,
                "user_id" => auth()->id()
            ]);

            return \Response::json(array("cnt" => 1, "mysupport" => 1));
        }
        else{
            $support->delete();
            return \Response::json(array("cnt" => -1, "mysupport" => 0));
        }
    }

    public function store(Request $request)
    {
        $this->validate($request,['related_id'=>'required','message'=>'required|min:15|max:400']);
        $newMessage = ReportComment::create([
            "user_id" => Auth::User()->id,
            "report_id" => $request->related_id,
            "parent_id" => $request->parent_id,
            "message" => $request->message
        ]);

        return $newMessage;
    }

    public function destroy($id, Request $request)
    {
        $message = ReportComment::find($id);
        if (is_null($message) || $message->user_id != Auth::id()) {
            return null;
        }

        try {
            $message->deleted = 1 ;
            $message->save();
            $page = $request->input('page', 0);

            if($page > 0){
                return $this->show($message->report_id, $request);
            }
            else{
                return Response('OK', 200);
            }

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
