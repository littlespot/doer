<?php

namespace Zoomov\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Zoomov\Report;
use Zoomov\ReportLover;

class ReportLoverController extends Controller
{
    public function show($id)
    {
        return ReportLover::where('report_id', $id)
            ->join('users', 'report_lovers.user_id', '=', 'users.id')
            ->select('users.id', 'users.username')
            ->get();
    }

    public function update($id){
        $lover = Report::find($id);

        if(is_null($lover)){
            return \Response::json(array("cnt" => -1, "mylove" => 0));
        }

        if($lover->user_id == Auth::id()){
            return;
        }

        $support = ReportLover::where('user_id', Auth::id())
            ->where('report_id', $id)
            ->first();

        if(!$support){
            ReportLover::create([
                "report_id" => $id,
                "user_id" => Auth::id()
            ]);

            return \Response::json(array("cnt" => 1, "mylove" => 1));
        }
        else{
            $support->delete();
            return \Response::json(array("cnt" => -1, "mylove" => 0));
        }
    }

    public function store(Request $request)
    {
        try {
            $lover = New ReportLover();
            $lover->report_id = $request->id;
            $lover->user_id = Auth::User()->id;
            $lover->save();

            return '';
        }catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($id)
    {
        try {
            $lover = ReportLover::find($id);
            if($lover->user_id == Auth::User()->id){
                $lover->delete();
            }

            return '';
        }catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
