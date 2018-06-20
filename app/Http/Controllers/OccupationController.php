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
            ->selectRaw("id, name_".($request->lang ? $request->lang : app()->getLocale())." as name, 0 as old")
            ->get();
    }

    public function show($id)
    {
        return UserOccupation::where('user_id', $id)
            ->join('occupations', 'user_occupations.occupation_id', '=', 'occupations.id')
            ->selectRaw("occupations.id, occupations.name_".app()->getLocale())
            ->get();
    }
}
