<?php

namespace Zoomov\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use DB;
use App;
use Zoomov\Country;
use Zoomov\Department;
use Zoomov\City;

class LocationController extends Controller
{
    public function index()
    {
        return Country::select('id', 'name_'.Auth::user()->locale.' as name', 'sortname')
            ->orderByRaw('convert(name_' . Auth::user()->locale .' using gbk) ASC')
            ->get();
    }

    public function cities($id){
        return City::whereExists(function ($query)  use ($id) {
                $query->select(DB::raw(1))
                    ->from('departments')
                    ->whereRaw('departments.country_id = '.$id.' and cities.department_id = departments.id');
            })
            ->select('cities.id', DB::raw('cities.name_'.App::getLocale().' as name'))
            ->orderByRaw('convert(cities.name_'.App::getLocale().' using gb2312)')
            ->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return Department::where('country_id', $id)
            ->select('id', 'name_'.Auth::user()->locale.' as name')
            ->orderBy('name_'.Auth::user()->locale)
            ->get();
    }

    public function projects(){
        return City::whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('projects')
                    ->where('projects.active', '>', '0')
                    ->whereRaw('projects.city_id = cities.id');
            })
            ->join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->select('cities.id', DB::raw('cities.name_'.Auth::user()->locale.' as name'), 'countries.sortname')
            ->orderBy('cities.name_'.Auth::user()->locale)
            ->get();
    }

    public function city($id)
    {
        return City::where('department_id', $id)
            ->select('id', 'name_'.Auth::user()->locale.' as name')
            ->orderByRaw('convert(name_'.Auth::user()->locale.' using gb2312)')
            ->get();
    }

    public function department($id=null){
        if(is_null($id) || $id == 0){
            $id =Auth::user()->city_id;
            if(is_null($id) || $id == 0)
                return null;
        }

        $city = City::findOrFail($id);

        return Department::select('id', 'name_'.Auth::user()->locale.' as name', 'country_id')
            ->orderByRaw('convert(name_'.Auth::user()->locale.' using gb2312)')
            ->find($city->department_id);
    }
}
