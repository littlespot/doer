<?php

namespace Zoomov\Http\Controllers;

use Illuminate\Http\Request;

use Zoomov\Http\Requests;
use Zoomov\Potential;
use Zoomov\PotentialWork;
use Zoomov\PotentialWorkOccupation;

class PotentialController extends Controller
{
    public function index(){

    }

    public function show($id){
        if(Potential::where('email', $id)->exist()){
            return "T";
        }
        else if(User::where('email', $id)->exist()){
            return "T";
        }
        else{
            return "F";
        }
    }

    public function store(Request $request){
        $potential = New Potential();
        $potential->email = $request->email;
        $potential->locale = $request->lang;
        $potential->presentation = $request->presentation;
        $potential->created_at = gmdate("Y-m-d H:i:s", time());
        $potential->save();

        foreach ($request['works'] as $work){
            $pwork = New PotentialWork();
            $pwork->potential_id = $potential->id;
            $pwork->url = $work['url'];
            $pwork->title = $work['title'];
            $pwork->description = $work['description'];
            $pwork->save();

            foreach ($work['occupations'] as $occupation){
                $pocc = New PotentialWorkOccupation();
                $pocc->potential_work_id = $pwork->id;
                $pocc->occupation_id = $occupation['id'];
                $pocc->save();
            }
        }
    }
}
