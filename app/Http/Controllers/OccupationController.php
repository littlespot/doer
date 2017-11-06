<?php

namespace Zoomov\Http\Controllers;

use Auth;
use DB;
use Illuminate\Http\Request;
use Zoomov\Occupation;
use Zoomov\UserOccupation;

class OccupationController extends Controller
{
    public function index(Request $request)
    {
        return Occupation::where('name', '<>', 'Planner')
            ->selectRaw("id, name_".($request->lang ? $request->lang : Auth::user()->locale)." as name, 0 as old")
            ->get();
    }

    public function admin()
    {
        return DB::table('occupations')
            ->where('name', '<>', 'Planner')
            ->leftJoin('user_occupations', function ($join) {
                $join->on('occupations.id', '=', 'user_occupations.occupation_id')
                    ->where('user_occupations.user_id', '=', Auth::id());
            })
            ->select('occupations.id', 'occupations.name', 'user_occupations.id as uid', 'user_occupations.id as old')
            ->orderBy('occupations.name')
            ->orderBy('user_occupations.id')
            ->get();
    }

    public function show($id)
    {
        return UserOccupation::where('user_id', $id)
            ->join('occupations', 'user_occupations.occupation_id', '=', 'occupations.id')
            ->selectRaw("occupations.id, occupations.name_".Auth::user()->locale)
            ->get();
    }
}
