<?php

namespace Zoomov\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Zoomov\Event;
use Zoomov\Project;

class EventRelatedController extends Controller
{
    protected function getProject($id)
    {
        $project = Project::select('id', 'user_id', 'title', 'active')->find($id);

        if((!$project->active && $project->user_id == Auth::id()) || $project->active){
            return $project;
        }

        return null;
    }

    protected function getEvent($id, $type)
    {
        $event = Event::where('related_id', $id)->where("type", $type)->first();
        return is_null($event) ? ["event_id" => 0, "title" => null, "content" => null]: ["event_id" => $event->id, "title" => null, "content" => null, "user_id" => $event->user_id, "username" => $event->username];
    }

    protected function afterDelete($id, $type)
    {
        $event = Event::where('related_id', $id)->where("type", $type)->first();
        if(!is_null($event)){
            $event->deleted = 1;
            $event->save();
            DB::table('changements')->where('event_id', $event->id)->update(['deleted' => 1]);
        }
    }
}
