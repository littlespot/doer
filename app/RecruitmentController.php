<?php

namespace Zoomov\Http\Controllers;

use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;

use Auth;
use Config;
use DB;
use Zoomov\Application;
use Zoomov\ApplicationPlaceholder;
use Zoomov\Project;
use Zoomov\ProjectRecruitment;

class RecruitmentController extends EventRelatedController
{
    public function index(Request $request)
    {
        if($request->unactive && is_null($this->getProject($request->project_id)))
        {
            return Response("NOT AUTHORIZED", 501);
        }

        return ProjectRecruitment::where('project_id', $request->project_id)->join('occupations','occupation_id', '=', 'occupations.id')
            ->select('project_recruitments.id','project_recruitments.quantity','project_recruitments.description', 'occupation_id', 'occupations.name')
            ->get();
    }

    public function show($id, Request $request)
    {
        if($request->unactive && is_null($this->getProject($id)))
        {
            return Response("NOT AUTHORIZED", 501);
        }

        return ProjectRecruitment::where('project_id', $id)->join('occupations','occupation_id', '=', 'occupations.id')
            ->leftJoin(DB::raw("(select 1 as applied, project_recruitment_id from applications where sender_id = '".Auth::id()."' group by project_recruitment_id) application"),function ($join){
                $join->on('application.project_recruitment_id', '=', 'project_recruitments.id');
            })
            ->selectRaw("project_recruitments.id,project_recruitments.quantity,project_recruitments.description, occupation_id, occupations.name, IFNULL(application.applied, 0) as application")
            ->get();
    }

    public function update($id, Request $request)
    {
        $recruit = ProjectRecruitment::find($id);
        $project = Project::select('id', 'user_id', 'title', 'active')->find($recruit->project_id);

        if($project->user_id != Auth::id()){
            return Response('NOT AUTHORIZED', 501);
        }

        $recruit->description = $request->description;
        $recruit->quantity = $request->quantity;

        if(is_null($project->active) ||  $project->active == 0){
            $recruit->occupation_id = $request->occupation_id;
        }

        $recruit->save();

        return $recruit;
    }

    public function store(Request $request)
    {
        $project = $this->getProject($request->project_id);

        if(is_null($project)) {
            return Response('NOT AUTHORIZED', 501);
        }

        try {
            return ProjectRecruitment::create([
                "id" =>  $this->uuid('r'),
                "project_id" => $request->project_id,
                "occupation_id" => $request->occupation_id,
                "quantity" => $request->quantity,
                "description" => $request->description
            ]);
        }catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($id, Request $request)
    {
        try {
            $recruit = ProjectRecruitment::find($id);

            if(is_null($recruit)){
                return Response('NOT FOUND', 404);
            }

            $project = $this->getProject($recruit->project_id);

            if(is_null($project)) {
                return Response('NOT AUTHORIZED', 501);
            }

            if($project->active == 1){
                $applications = Application::where('project_recruitment_id', $recruit->id)->whereNull('accepted')->get();

                foreach ($applications as $application){
                    $placeholders = ApplicationPlaceholder::where('application_id', $application->id)->get();

                    foreach ($placeholders as $placeholder){
                        $checked = $placeholder->placeholder_id == config('constants.messageplaceholder.inbox');

                        if($placeholder->checked != $checked){
                            $placeholder->checked = $checked;
                            $placeholder->save();
                        }
                    }

                    $application->accepted = 0;

                    $application->save();
                }
            }

            $recruit->delete();

            return Response('OK', 200);
        }catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
