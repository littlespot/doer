<?php

namespace Zoomov\Http\Controllers;
use Auth;
use DB;
use Validator;
use Illuminate\Http\Request;
use League\Flysystem\Exception;
use Zoomov\Question;
use Zoomov\QuestionAnswer;
use Zoomov\QuestionFollower;
use Zoomov\QuestionTag;
use Zoomov\Tag;
use Zoomov\User;
use Zoomov\UserOccupation;

class QuestionController extends Controller
{
    private $query = 'questions.id, subject, content, questions.created_at, questions.updated_at, IFNULL(follower.cnt, 0) as followers_cnt';

    public function index(){
        $user = auth()->id();
        $questions = Question::where('user_id', $user)
            ->select('content', 'created_at');

        $answers = QuestionAnswer::where('user_id', $user)
            ->join('questions', 'question_answers.question_id', '=', 'questions.id')
            ->select('content', 'subject', 'question_answers.created_at');

        return [$questions, $answers];
    }

    public function display($id){
        return Question::where('project_id', $id)
            ->join('users', 'users.id', '=', 'user_id')
            ->leftJoin(DB::raw("(select count(id) as cnt, question_id from question_answers group by question_id) answer"), function ($join) {
                $join->on('answer.question_id', '=', 'questions.id');
            })
            ->leftJoin(DB::raw("(select count(id) as cnt, question_id from question_followers group by question_id) follower"), function ($join) {
                $join->on('follower.question_id', '=', 'questions.id');
            })
            ->leftJoin(DB::raw("(select id, question_id from question_followers where user_id = '".auth()->id()."') myfollow"), function ($join) {
                $join->on('myfollow.question_id', '=', 'questions.id');
            })
            ->selectRaw("questions.id, subject, 
                    questions.created_at, questions.updated_at, IFNULL(follower.cnt, 0) as followers_cnt,
                    (CASE WHEN user_id = '".auth()->id()."' THEN 1 ELSE  0 END) as mine, user_id, username, IFNULL(answer.cnt, 0) as cnt,
                    FLOOR((unix_timestamp(now()) - unix_timestamp(questions.created_at))/60/60/24) <1  as newest, IFNULL(myfollow.id,0) as myfollow")
            ->orderBy('created_at', 'desc')
            ->paginate(12);
    }

    public function show($id, Request $request){
        $question = Question::join(DB::raw("(select projects.id, title, user_id, username from projects inner join users on user_id = users.id) projects"), function ($join) {
                $join->on('projects.id', '=', 'project_id');
            })
            ->join('users', 'questions.user_id', '=', 'users.id')
            ->leftJoin(DB::raw("(select count(id) as cnt, question_id from (select id, question_id from question_answers where user_id = '".auth()->id()."') mine group by question_id) answers"), function ($join) {
                $join->on('questions.id', '=', 'answers.question_id');
            })
            ->leftJoin(DB::raw("(select count(id) as cnt, question_id from question_followers group by question_id) follower"), function ($join) {
                $join->on('follower.question_id', '=', 'questions.id');
            })
            ->leftJoin(DB::raw("(select id, question_id from question_followers where user_id = '".auth()->id()."') myfollow"), function ($join) {
                $join->on('myfollow.question_id', '=', 'questions.id');
            })
            ->selectRaw($this->query.', project_id, title, projects.user_id as planner_id, projects.username as planner_name, questions.user_id, users.username, 
                IFNULL(myfollow.id, 0) as myfollow, IFNULL(answers.cnt, 0) as mineCnt')
            ->find($id);

        $tags = Tag::join(DB::raw("(select tag_id from question_tags where question_id = '".$id."') questions"), function ($join) {
                    $join->on('questions.tag_id', '=', 'tags.id');
                })->join(DB::raw("(select count(id) as cnt, tag_id from question_tags group by tag_id) counter"), function ($join) {
                    $join->on('counter.tag_id', '=', 'tags.id');
                })
                ->select('tags.label', 'counter.cnt')
                ->get();

        return view('visit.question', ["question" => $question, "tags"=>$tags, "answer"=>$request->input("answer", 0)]);
    }

    public function tags(){
        return Tag::join(DB::raw("(select distinct tag_id from question_tags) tags"), function ($join) {
                    $join->on('tags.tag_id', '=', 'tags.id');
                })->select('tags.id', 'tags.label')->get();
    }

    public function edit($id){
        $question = Question::join('projects', 'project_id', '=', 'projects.id')
            ->join('users', 'projects.user_id', '=', 'users.id')
            ->selectRaw('questions.id, subject, content,project_id, title, projects.user_id as planner_id, 
                users.username as planner_name, questions.user_id')
            ->with('tags')
            ->find($id);

        if($question->user_id != Auth::id()){
            return response('NO AUTH', 401);
        }

        return view('project.question', $question);
    }

    public function change(Request $request){
        $question = Question::find($request->id);
        if($question->user_id != Auth::id()){
            return response('NO AUTH', 401);
        }

        $question->subject = $request['subject'];
        $question->content = $request['editor'];
        $question->save();
        $old = QuestionTag::where('question_id', $question->id)->pluck('tag_id')->all();
        $remove = array_diff($old, $request->tags);
        $add = array_diff($request->tags, $old);

        DB::table('question_tags')->whereIn('tag_id', $remove)->delete();

        foreach ($add as $key=> $value) {
            if($key == 0){
                $tag = Tag::create([
                    'user_id' => auth()->id(),
                    'label' => $value,
                    'created_at' => gmdate("Y-m-d H:i:s", time())
                ]);
                $key = $tag->id;
            }

            QuestionTag::create([
                'question_id' => $question->id,
                'user_id' => auth()->id(),
                'tag_id' => $key,
                'created_at' => gmdate("Y-m-d H:i:s", time())
            ]);
        }

        return redirect()->guest('/questions/'.$question->id);
    }

    public function relates($id){
        return Question::join(DB::raw("(select distinct question_id from question_tags where tag_id in (select tag_id from question_tags where question_id='".$id."')) tags"), function ($join){
            $join->on('questions.id', '=', 'tags.question_id');
        })
            ->join('projects', 'project_id', '=', 'projects.id')
            ->join('users', 'questions.user_id', '=', 'users.id')
            ->select('question_id', 'questions.created_at', 'subject', 'project_id', 'title', 'questions.user_id', 'username')
            ->orderBy('questions.created_at', 'desc')
            ->paginate(10);
    }

    public function personal($id, Request $request){
        $tab = $request->input('tab', 'asks');

        $user = User::join('cities', 'city_id', '=', 'cities.id')
            ->join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->leftJoin(DB::raw("(select count(id) as cnt, user_id from questions group by user_id) questions"), function ($join) {
                $join->on('questions.user_id', '=', 'users.id');
            })
            ->leftJoin(DB::raw("(select count(id) as cnt, user_id from question_followers group by user_id) follower"), function ($join) {
                $join->on('follower.user_id', '=', 'users.id');
            })
            ->leftJoin(DB::raw("(select count(id) as cnt, user_id from question_answers group by user_id) answers"), function ($join) {
                $join->on('answers.user_id', '=', 'users.id');
            })
            ->leftJoin(DB::raw("(select count(id) as cnt, user_id from question_answer_supports group by user_id) support"), function ($join) {
                $join->on('support.user_id', '=', 'users.id');
            })
            ->selectRaw("users.id, username, cities.name_".app()->getLocale()." as city_name, countries.sortname,
                IFNULL(questions.cnt, 0) as asks_cnt, IFNULL(answers.cnt, 0) as answers_cnt, IFNULL(follower.cnt, 0) as follows_cnt, 
                IFNULL(support.cnt, 0) as supports_cnt")
            ->find($id);

        $occupations = UserOccupation::where('user_id', $id)
            ->join('occupations', 'occupation_id', '=', 'occupations.id')
            ->select('user_id', 'name')
            ->get();

        return view('visit.answers', ["user"=>$user, "occupations"=>$occupations, "admin" => $id == auth()->id(), "tab"=>$tab]);
    }

    public function asks($id, Request $request){
        $order = $request->input('order', 'updated_at');

        $questions = Question::where('questions.user_id', $id);

        return $this->chooseQuestion($id, $questions, ",YEAR(questions.created_at) as year, MONTH(questions.created_at) month, DAYOFMONTH(questions.created_at) day")
            ->orderBy('questions.'.$order, 'desc')->paginate(20);
    }

    public function follows($id, Request $request)
    {
        $order = $request->input('order', 'updated_at');
        $questions = Question::join(DB::raw("(select question_id, created_at from question_followers where user_id = '".$id."') f"), function ($join){
                $join->on("f.question_id", "=", "questions.id");
            })
            ->join('users', 'questions.user_id', '=', 'users.id');

        return $this->chooseQuestion($id, $questions, ', questions.user_id, username, YEAR(f.created_at) as year, MONTH(f.created_at) month, DAYOFMONTH(f.created_at) day')
            ->orderBy('questions.'.$order, 'desc')->paginate(20);
    }

    public function answers($id){
        $order = 'created_at';

        $answers = QuestionAnswer::where('question_answers.user_id', $id);

        return $this->chooseAnswer($id, $answers, ", YEAR(question_answers.created_at) as year, MONTH(question_answers.created_at) month, DAYOFMONTH(question_answers.created_at) day")
            ->orderBy('question_answers.'.$order, 'desc')
            ->paginate(20);
    }

    public function supports($id, Request $request)
    {
        $order = $request->input('order', 'created_at');
        
        $answers = QuestionAnswer::join(DB::raw("(select question_answer_id, created_at from question_answer_supports where user_id = '".$id."') s"), function ($join){
                $join->on("question_answers.id", "=", "s.question_answer_id");
            })
            ->join('users', 'users.id', '=', 'question_answers.user_id');

        return $this->chooseAnswer($id, $answers, ", question_answers.user_id, username, YEAR(s.created_at) as year, MONTH(s.created_at) month, DAYOFMONTH(s.created_at) day")
            ->orderBy('question_answers.'.$order, 'desc')
            ->paginate(20);
    }

    private function chooseQuestion($id, $questions, $str=''){
        $params = $this->query.", project_id, title, IFNULL(answer.cnt, 0) as answers_cnt, questions.user_id = '".auth()->id()."' as mine".$str;

        $questions = $questions->join('projects','projects.id', '=', 'project_id');
        if($id != Auth::id()){
            $questions = $questions->leftJoin(DB::raw("(select id, question_id from question_followers where user_id = '".auth()->id()."') myfollow"), function ($join) {
                $join->on('myfollow.question_id', '=', 'questions.id');
            });

            $params .= ', IFNULL(myfollow.id, 0) as myfollow';
        }
        else {
            $params .= ', 1 as myfollow';
        }

         $questions = $questions->leftJoin(DB::raw("(select count(id) as cnt, question_id from question_answers group by question_id) answer"), function ($join) {
                $join->on('answer.question_id', '=', 'questions.id');
            })
            ->leftJoin(DB::raw("(select count(id) as cnt, question_id from question_followers group by question_id) follower"), function ($join) {
                $join->on('follower.question_id', '=', 'questions.id');
           });

        return $questions->selectRaw($params);
    }

    private function chooseAnswer($id, $answers, $str=''){
        $params = "question_answers.id, question_answers.content, question_answers.question_id, questions.subject, question_answers.user_id = '".auth()->id()."' as mine,
            answer.cnt as answers_cnt, question_answers.created_at, IFNULL(support.cnt, 0) as supports_cnt".$str;

        $answers = $answers->join('questions', 'questions.id', '=', 'question_answers.question_id')
            ->join(DB::raw("(select count(id) as cnt, question_id from question_answers group by question_id) answer"), function ($join) {
                $join->on('answer.question_id', '=', 'questions.id');
            })
            ->leftJoin(DB::raw("(select count(id) as cnt, question_answer_id from question_answer_supports group by question_answer_id) support"), function ($join) {
                $join->on('question_answers.id', '=', 'support.question_answer_id');
            });

        if($id != Auth::id()) {
            $answers = $answers->leftJoin(DB::raw("(select id as mine, question_answer_id from question_answer_supports where user_id = '" . auth()->id() . "') mysupport"),
                'mysupport.question_answer_id', '=', 'question_answers.id');

            $params .= ', IFNULL(mysupport.mine, 0) as mysupport';
        }
        else {
            $params .= ', 1 as mysupport';
        }

        return $answers->selectRaw($params);
    }

    public function store(Request $request)
    {
        $id = auth()->id();
        $time = gmdate("Y-m-d H:i:s", time());

        $this->validate($request, [
            'project_id'=>'required',
            'subject' => 'required|min:4|max:100',
            'editor' => 'required|min:15|max:4000',
            'tags' => 'required'
        ]);

        try{
            $question = Question::create([
                'id' => $this->uuid('q'),
                'project_id' => $request['project_id'],
                'user_id' => $id,
                'content' => $request['editor'],
                'subject' => $request['subject'],
                'created_at' => $time
            ]);
            foreach ($request['tags'] as $key=> $value) {
                if($key == 0){
                    $tag = Tag::create([
                        'user_id' => $id,
                        'label' => $value,
                        'created_at' => $time
                    ]);

                    $key = $tag->id;
                }

                QuestionTag::create([
                    'question_id' => $question->id,
                    'user_id' => $id,
                    'tag_id' => $key,
                    'created_at' => $time
                ]);
            }

            return redirect('project/'.$question->project_id.'?tab=4');
        }
        catch (Exception $e) {
            return back()->withInput()->withErrors('validator')->with('message', $e->getMessage());
        }
    }

    public function update($id)
    {
        $follow = QuestionFollower::where('question_id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (is_null($follow)) {
            $question = Question::find($id);

            if(is_null($question)){
                return \Response::json(array("cnt" => -1, "myfollow" => 0));
            }

            try {
                QuestionFollower::create([
                    'question_id' => $id,
                    'user_id' => auth()->id(),
                    'created_at' => gmdate("Y-m-d H:i:s", time())
                ]);

                return \Response::json(array("cnt" => 1, "myfollow" => 1));
            } catch (Exception $e) {
                return $e->getMessage();
            }
        } else {
            try {
                $follow->delete();
                return \Response::json(array("cnt" => -1, "myfollow" => 0));
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    }

    public function destroy($id, Request $request){
        $question = Question::find($id);
        if(is_null($question) || $question->user_id != Auth::id()){
            return null;
        }
        else {
            try{
                DB::table('question_tags')->where('question_id', $id)->delete();

                DB::table('question_followers')->where('question_id', $id)->delete();

                $page = $request->input('page', 0);
                if($page > 0){
                    $project = $question->project_id;
                    $question->delete();
                    return $this->display($question->project_id, $project);
                }
                else{
                    $question->delete();
                    return Response('ok', 200);
                }
            }
            catch (Exception $e){
                return $e;
            }
        }
    }
}
