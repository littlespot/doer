<?php

namespace Zoomov\Http\Controllers;
use Illuminate\Http\Request;
use DB;
use Zoomov\Event;
use Zoomov\Sponsor;

class SponsorController extends EventRelatedController
{
    public function update($id, Request $request)
    {
        try {
            $sponsor = Sponsor::find($id);

            if(is_null($sponsor)) {
                return Response('NOT FOUND', 404);
            }

            $project = $this->getProject($sponsor->project_id);

            if(is_null($project)){
                return Response('NOT AUTHORIZED', 501);
            }

            $changed = 0;

            if($sponsor->quantity != $request->quantity){
                $sponsor->quantity = $request->quantity;
                $changed = 1;
            }

            if( $sponsor->sponsed_at != $request->sponsed_at){
                $sponsor->sponsed_at = date("Y-m-d", strtotime($request->sponsed_at));
                $changed += 2;

            }

            if($sponsor->user_id != $request->user_id || $sponsor->sponsor_name != $request->username){
                $sponsor->user_id = $request->user_id;
                $sponsor->sponsor_name = $request->username;

                $changed += 4;
            }

            if($changed){
                $sponsor->save();

                if($project->active === 1){
                    $changement = $this->getEvent($id, 'm');

                    if($changed != 4){
                        $changement["title"] = $sponsor->quantity ." [".str_limit($sponsor->sponsed_at, 10, '')."]";

                        if($changed >= 5){
                            $changement["user_id"] = $request->user_id;
                            $changement["username"] = $request->username;
                        }
                    }
                    else{
                        $changement["user_id"] = $request->user_id;
                        $changement["username"] = $request->username;
                    }

                    DB::table('changements')->insert($changement);
                }
            }

            return $sponsor;

        }catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function store(Request $request)
    {
        $project = $this->getProject($request -> project_id);

        if(is_null($project)){
            return Response('NOT AUTHORIZED', 501);
        }

        try {
            $sponsor = Sponsor::create([
                "project_id" => $request -> project_id,
                "user_id" => $request->user_id,
                "sponsor_name" => $request->username,
                "quantity" => $request->quantity,
                "sponsed_at" => date("Y-m-d", strtotime($request->sponsed_at))
            ]);

            if($project->active === 1){
                Event::create([
                    'project_id' => $sponsor->project_id,
                    'user_id' => $sponsor->user_id,
                    'username' => $request->username,
                    'title' => $sponsor->qunantity."(". $sponsor->sponsed_at.")",
                    'type' => 'm',
                    'related_id' => $sponsor->id
                ]);
            }

            return $sponsor;
        }catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($id, Request $request)
    {
        try {
            $sponsor = Sponsor::find($id);

            if(is_null($sponsor)) {
                return Response('NOT FOUND', 404);
            }

            $project = $this->getProject($sponsor -> project_id);

            if(is_null($project)){
                return Response('NOT AUTHORIZED', 501);
            }

            if($project->active === 1){
                $this->afterDelete($id, 'm');
            }

            $sponsor->delete();

            return Response('OK', 200);
        }catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
