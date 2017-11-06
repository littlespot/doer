<?php

namespace Zoomov\Http\Controllers;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use DB;
use Zoomov\Question;
use Zoomov\QuestionAnswer;
use Zoomov\QuestionAnswerSupport;
use Zoomov\QuestionTag;

class QuestionAnswerController extends Controller
{
    public function index(Request $request){
        return $request;
    }

    public function show($id){
        return QuestionAnswer::where('question_id', $id)
            ->join('users', 'question_answers.user_id', '=', 'users.id')
            ->leftJoin(DB::raw("(select count(id) as cnt, question_answer_id from question_answer_supports group by question_answer_id) supports"),
                'supports.question_answer_id', '=', 'question_answers.id')
            ->leftJoin(DB::raw("(select id as mine, question_answer_id from question_answer_supports where user_id = '".Auth::id()."') mysupport"),
                'mysupport.question_answer_id', '=', 'question_answers.id')
            ->selectRaw("question_answers.id, content, question_id, question_answers.user_id, username, question_answers.created_at,
                IFNULL(supports.cnt,0) as supports_cnt, IFNULL(mysupport.mine,0) as mysupport, question_answers.user_id ='".Auth::id()."' as mine")
            ->orderBy('question_answers.updated_at', 'desc')
            ->paginate(10);
    }

    public function update($id){
        $answer = QuestionAnswer::find($id);

        if(is_null($answer)){
            return \Response::json(array("cnt" => -1, "mysupport" => 0));
        }

        if($answer->user_id == Auth::id()){
            return;
        }

        $support = QuestionAnswerSupport::where('user_id', Auth::id())
            ->where('question_answer_id', $id)
            ->first();

        if(!$support){
            QuestionAnswerSupport::create([
                "question_answer_id" => $id,
                "user_id" => Auth::id()
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
        $this->validate($request, ['editor'=>'required|min:10']);
        $answer = QuestionAnswer::create([
            'question_id' => $request->question_id,
            'user_id' => Auth::id(),
            'content' => $request['editor'],
            'created_at' => gmdate("Y-m-d H:i:s", time()),
            'updated_at' => gmdate("Y-m-d H:i:s", time())
        ]);
        $answer->save();
        return $answer;
    }

    public function destroy($id, Request $request)
    {
        $answer = QuestionAnswer::find($id);
        if (is_null($answer) || $answer->user_id != Auth::id()) {
            return null;
        }

        try {
            DB::table('question_answer_supports')->where('question_answer_id', $id)->delete();
            $page = $request->input('page', 0);

            if($page > 0){
                $question = $answer->question_id;
                $answer->delete();
                return $this->show($question, $request);
            }
            else{
                $answer->delete();
                return Response('OK', 200);
            }

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
