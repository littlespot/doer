<?php

namespace Zoomov\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Validator;
use Zoomov\Changement;
use Zoomov\ReportComment;
use Zoomov\ReportTag;
use Zoomov\Project;
use Zoomov\Report;
use Zoomov\Tag;
use Zoomov\User;
use Zoomov\UserOccupation;

class ReportController extends Controller
{
    public function index(Request $request){
        $project = $request->input('project', '');

        if($project == ''){
            return Tag::whereRaw("id in (select tag_id from report_tags)")
                ->select('tags.label', 'tags.id')
                ->orderByRaw('convert(tags.label using gb2312)')
                ->get();
        }
        else{
            return Tag::whereRaw("id in (select tag_id from report_tags t inner join reports r on t.report_id = r.id and r.project_id = '".$project."')")
                ->select('tags.label', 'tags.id')
                ->orderByRaw('convert(tags.label using gb2312)')
                ->get();
        }
    }

    public function show($id)
    {
        $report = Report::join(DB::raw("(select projects.id, title, user_id as planner_id, username as planner_name from projects inner join users on user_id = users.id) projects"), function ($join){
                $join->on('reports.project_id', '=', 'projects.id');
            })
            ->join('users', 'reports.user_id', '=', 'users.id')
            ->leftJoin(DB::raw("(select count(id) as cnt, report_id from report_lovers group by report_id) lovers"), function ($join) {
                $join->on('lovers.report_id', '=', 'reports.id');
            })
            ->leftJoin(DB::raw("(select id, report_id from report_lovers where user_id = '".Auth::id()."') mylove"), function ($join) {
                $join->on('mylove.report_id', '=', 'reports.id');
            })
            ->selectRaw("reports.id, reports.title, reports.synopsis, reports.content, reports.updated_at, reports.created_at, project_id, projects.title as project_title,
                planner_id, planner_name, IFNULL(lovers.cnt,0) as lovers_cnt, IFNULL(mylove.id, 0) as mylove, reports.user_id, users.username,
                reports.user_id = '".Auth::id()."' as admin, YEAR(reports.created_at) as year, MONTH(reports.created_at) month, DAYOFMONTH(reports.created_at) day")
            ->find($id);

        $tags = Tag::join(DB::raw("(select tag_id from report_tags where report_id = '".$id."') reports"), function ($join) {
                $join->on('reports.tag_id', '=', 'tags.id');
            })->join(DB::raw("(select count(id) as cnt, tag_id from report_tags group by tag_id) counter"), function ($join) {
                $join->on('counter.tag_id', '=', 'tags.id');
            })
            ->select('tags.label', 'counter.cnt')
            ->get();

        return view('visit.report', ['report' => $report, 'tags'=>$tags]);
    }


    public function personal($id, Request $request)
    {
        $tab = $request->input('tab', 'writes');

        $user = User::join('cities', 'city_id', '=', 'cities.id')
            ->join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->leftJoin(DB::raw("(select count(id) as cnt, user_id from reports group by user_id) reports"), function ($join) {
                $join->on('reports.user_id', '=', 'users.id');
            })
            ->leftJoin(DB::raw("(select count(id) as cnt, user_id from report_lovers group by user_id) lovers"), function ($join) {
                $join->on('lovers.user_id', '=', 'users.id');
            })
            ->leftJoin(DB::raw("(select count(id) as cnt, user_id from report_comments group by user_id) comments"), function ($join) {
                $join->on('comments.user_id', '=', 'users.id');
            })
            ->selectRaw("users.id, username, cities.name_".Auth::user()->locale." as city_name, countries.sortname,
                IFNULL(reports.cnt, 0) as writes_cnt, IFNULL(lovers.cnt, 0) as lovers_cnt, IFNULL(comments.cnt, 0) as comments_cnt")
            ->find($id);

        $occupations = UserOccupation::where('user_id', $id)
            ->join('occupations', 'occupation_id', '=', 'occupations.id')
            ->select('user_id', 'name')
            ->get();

        return view('visit.reports', ["user"=>$user, "occupations"=>$occupations, "admin" => $id == Auth::id(), "tab"=>$tab]);
    }

    public function writes($id, Request $request){
        $order = $request->input('order', 'created_at');

        $params = "reports.id, reports.title, reports.synopsis, reports.updated_at, reports.created_at, project_id, projects.title as project_title,
            IFNULL(lovers.cnt,0) as lovers_cnt, IFNULL(comments.cnt, 0) as comments_cnt, 
            reports.user_id = '".Auth::id()."' as mine, YEAR(reports.created_at) as year, MONTH(reports.created_at) month, DAYOFMONTH(reports.created_at) day";

        $reports = Report::where('reports.user_id', $id)
            ->join('projects','project_id', '=', 'projects.id')
            ->leftJoin(DB::raw("(select count(id) as cnt, report_id from report_lovers group by report_id) lovers"), function ($join) {
                $join->on('lovers.report_id', '=', 'reports.id');
            })
            ->leftJoin(DB::raw("(select count(id) as cnt, report_id from report_comments where deleted = 0 group by report_id) comments"), function ($join) {
                $join->on('comments.report_id', '=', 'reports.id');
            });

        if($id != Auth::id()){
            $reports = $reports->leftJoin(DB::raw("(select id, report_id from report_lovers where user_id = '".Auth::id()."') mylove"), function ($join) {
                $join->on('mylove.report_id', '=', 'reports.id');
            });

            $params .= ', IFNULL(mylove.id, 0) as mylove';
        }
        else {
            $params .= ', 1 as mylove';
        }

        return $reports->selectRaw($params)
            ->orderBy('reports.'.$order, 'desc')
            ->paginate(12);
    }

    public function loves($id, Request $request){
        $order = $request->input('order', 'created_at');

        $params = "reports.id, reports.title, reports.synopsis, reports.updated_at, reports.created_at, IFNULL(lovers.cnt,0) as lovers_cnt, IFNULL(comments.cnt, 0) as comments_cnt, 
            project_id, projects.title as project_title, reports.user_id, username, l.created_at as loved_at,
            reports.user_id = '".Auth::id()."' as mine, YEAR(l.created_at) as year, MONTH(l.created_at) month, DAYOFMONTH(l.created_at) day,
            FLOOR((unix_timestamp(now()) - unix_timestamp(reports.created_at))/60/60/24) <1  as newest";

        $reports = Report::join(DB::raw("(select report_id, created_at from report_lovers where user_id='".$id."') l"), function ($join) {
                $join->on('l.report_id', '=', 'reports.id');
            })
            ->join('users', 'reports.user_id', '=', 'users.id')
            ->join('projects','project_id', '=', 'projects.id')
            ->leftJoin(DB::raw("(select count(id) as cnt, report_id from report_lovers group by report_id) lovers"), function ($join) {
                $join->on('lovers.report_id', '=', 'reports.id');
            })
            ->leftJoin(DB::raw("(select count(id) as cnt, report_id from report_comments group by report_id) comments"), function ($join) {
                $join->on('comments.report_id', '=', 'reports.id');
            });

        if($id != Auth::id()){
            $reports = $reports->leftJoin(DB::raw("(select id, report_id from report_lovers where user_id = '".Auth::id()."') mylove"), function ($join) {
                $join->on('mylove.report_id', '=', 'reports.id');
            });

            $params .= ', IFNULL(mylove.id, 0) as mylove';
        }
        else {
            $params .= ', 1 as mylove';
        }

        return $reports->selectRaw($params)
            ->orderBy('reports.'.$order, 'desc')
            ->paginate(12);
    }

    public function comments($id, Request $request){
        $order = $request->input('order', 'created_at');

        $params = "comments.id, comments.report_id, reports.title, reports.updated_at, reports.created_at, reports.user_id, username,
            comments.message, YEAR(comments.created_at) as year, MONTH(comments.created_at) month, DAYOFMONTH(comments.created_at) day,
            IFNULL(supports.cnt, 0) as supports_cnt,  IFNULL(counters.cnt, 0) as comments_cnt";

        $comments = Report::join(DB::raw("(select id, report_id, message,created_at from report_comments where user_id='".$id."' and deleted = 0) comments"), function ($join){
                $join->on('comments.report_id', '=', 'reports.id');
            })
            ->join('users', 'reports.user_id', '=', 'users.id')
            ->leftJoin(DB::raw("(select count(id) as cnt, report_comment_id from report_comment_supports group by report_comment_id) supports"), function ($join) {
                $join->on('report_comment_id', '=', 'comments.id');
            })
            ->leftJoin(DB::raw("(select count(id) as cnt, report_id from report_comments group by report_id) counters"), function ($join) {
                $join->on('counters.report_id', '=', 'reports.id');
            });

        if($id != Auth::id()){
            $comments = $comments->leftJoin(DB::raw("(select id, report_comment_id from report_comment_supports where user_id = '".Auth::id()."') mysupport"), function ($join) {
                $join->on('mysupport.report_comment_id', '=', 'comments.id');
            });

            $params .= ', IFNULL(mysupport.id, 0) as mysupport';
        }
        else {
            $params .= ', 1 as mysupport';
        }

        return $comments->selectRaw($params)
            ->orderBy('comments.'.$order, 'desc')
            ->paginate(12);
    }

    public function create($id){
        $project = Project::find($id);
        $user = User::find($project->user_id);
        return view('user.report', ['id'=>$project->id, 'title'=>$project->title, 'user_id'=>$project->user_id, 'username'=>$user->username]);
    }

    public function edit($id)
    {
        $report = Report::join(DB::raw("(select projects.id, title, user_id as planner_id, username as planner_name from projects inner join users on user_id = users.id) projects"), function ($join){
            $join->on('reports.project_id', '=', 'projects.id');
        })
            ->selectRaw("reports.id, reports.user_id, reports.title, reports.synopsis, reports.content, reports.updated_at, reports.created_at, project_id, projects.title as project_title, planner_id, planner_name")
            ->find($id);
        $tags = ReportTag::join('tags', 'tag_id', '=', 'tags.id')
            ->where('report_id', $id)
            ->select('report_tags.id', 'tag_id', 'tags.label')
            ->get();
        $report->tags = $tags;
        return $report->user_id == Auth::id() ? view('project.report', $report) : null;
    }

    public function store(Request $request)
    {
        $this->validator($request->all())->validate();
        if($request->has('images')){

            $files = Storage::disk('images')->files('/uploads/reports/'.$request['parent_id']);
            $names = $request['images'];
            foreach ($files as $file){
                if(!in_array(basename($file), $names)){
                    unlink($file);
                }
            }
        }
        $report = Report::create([
            "project_id" => $request->project_id,
            "title" => $request->title,
            "synopsis" => $request->synopsis,
            "content" => $request['editor'],
            "user_id" => Auth::User()->id
        ]);

        Changement::create([
            'event_id' => $report->id,
            'user_id' => Auth::id(),
            'username' => Auth::user()->username,
            'title' => $report->title,
            'content' => $report->synopsis
        ]);

        foreach ($request->tags as $key=> $value) {
            if($key == 0){
                $tag = Tag::create([
                    'user_id' => Auth::id(),
                    'label' => $value
                ]);

                ReportTag::create([
                    'report_id'=>$report->id,
                    'tag_id'=>$tag->id
                ]);
            }
            else{
                ReportTag::create([
                    'report_id'=>$report->id,
                    'tag_id'=>$key
                ]);
            }
        }
        return redirect('project/'.$report->project_id.'?tab=2');
    }

    public function update($id, Request $request)
    {
        $this->validator($request->all());
        $report = Report::find($id);

        if(is_null($report) || $report->user_id != Auth::id()){
            return Response('NO AUTHORITY', 501);
        }

        $changement = ["event_id"=>$id, "title" => null, "content" => null, "user_id" => Auth::id(), "username" =>  Auth::user()->username];

        $change = false;

        if($report->title != $request->title){
            $report->title = $request->title;
            $changement["title"] = $request->title;
            $change |= true;
        }
        if($report->synopsis != $request->synopsis){
            $report->synopsis = $request->synopsis;
            $changement["content"] = $request->synopsis;
            if($request->has('images')){

                $files = Storage::disk('images')->files('/uploads/reports/'.$request['parent_id']);
                $names = $request['images'];
                foreach ($files as $file){
                    if(!in_array(basename($file), $names)){
                        unlink($file);
                    }
                }
            }
            $change |= true;
        }

        if($change){
            DB::table("changements")->insert($changement);
        }

        if($report->content != $request['editor']){
            $report->content = $request['editor'];
            $change |= true;
        }

        if($change){
            $report->save();
        }

        $tags = implode(",", array_keys($request->tags));

        DB::table('report_tags')->whereRaw("tag_id not in (".$tags.")")->delete();

        $reportTags = json_decode(ReportTag::where('report_id', $id)->select('tag_id')->get(), true);

        foreach ($request->tags as $key=> $value) {
            if($key == 0){
                $tag = Tag::create([
                    'user_id' => Auth::id(),
                    'label' => $value
                ]);

                ReportTag::create([
                    'report_id'=>$id,
                    'tag_id'=>$tag->id
                ]);
            }
            else if(array_search($key, $reportTags) < 0){
                ReportTag::create([
                    'report_id'=>$id,
                    'tag_id'=>$key
                ]);
            }
        }

        return redirect('project/'.$report->project_id.'?tab=2');
    }

    public function destroy($id)
    {
        try {

            $report = Report::find($id);
            if(is_null($report) || $report->user_id !=  Auth::User()->id){
                return;
            }

            DB::table('changements')->where('event_id', $id)->update(['deleted' => 1]);

            DB::table('report_tags')->where('report_id', $id)->delete();

            DB::table('report_lovers')->where('report_id', $id)->delete();

            $comments = ReportComment::where('report_id', $id)->get();

            foreach ($comments as $comment){
                DB::table('report_comment_supports')->where('report_comment_id', $comment->id)->delete();
                $comment->delete();
            }

            $report->delete();

            return Response('OK', 200);
        }catch (Exception $e) {
            return $e->getMessage();
        }
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'project_id'=>'required',
            'title'=>'required|min:4|max:100',
            'synopsis'=>'required|min:4|max:400',
            'editor'=>'required|min:15'
        ]);
    }
}
