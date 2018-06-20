<?php

namespace Zoomov\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use App;
use Zoomov\Country;
use Zoomov\Department;
use Zoomov\City;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        if($request->has('region')){
            return Country::select('id', 'name_'.app()->getLocale().' as name', 'sortname')
                ->orderBy('rank')
                ->orderByRaw('convert(name_' . app()->getLocale() .' using gbk) ASC')
                ->get();
        }
        else{
            return Country::where('region', '<>', 1)->select('id', 'name_'.app()->getLocale().' as name', 'sortname')
                ->orderBy('rank')
                ->orderByRaw('convert(name_' . app()->getLocale() .' using gbk) ASC')
                ->get();
        }

    }

    public function citiesByCountry($id){
        return City::whereExists(function ($query)  use ($id) {
                $query->select(DB::raw(1))
                    ->from('departments')
                    ->whereRaw('departments.country_id = '.$id.' and cities.department_id = departments.id');
            })
            ->orderByRaw('convert(cities.name_'.app()->getLocale().' using gb2312)')
            ->get(['cities.id', 'cities.name_'.app()->getLocale().' as name']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $city = City::find($id);
        $department = Department::find($city->department_id);
        return ['cities'=>$this->cities($department->id), 'city_id'=>(int)$id, 'departments'=>$this->departments($department->country_id), 'department_id'=>$department->id, 'country_id'=>(string)$department->country_id];
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
            ->select('cities.id', DB::raw('cities.name_'.app()->getLocale().' as name'), 'countries.sortname')
            ->orderBy('cities.name_'.app()->getLocale())
            ->get();
    }

    public function departCities($id)
    {
        $department = Department::find($id);
        $departments = Department::where('country_id', $department->country_id)
            ->orderByRaw('convert(departments.name_'.app()->getLocale().' using gb2312)')
            ->selectRaw('convert(id, char(4)) as id, name_'.app()->getLocale().' as name')
            ->get();

        $cities = City::where('department_id', $id)
            ->orderByRaw('convert(cities.name_'.app()->getLocale().' using gb2312)')
            ->selectRaw('convert(id, char(4)) as id, name_'.app()->getLocale().' as name')
            ->get();

        return ['departments'=>$departments, 'cities'=>$cities];
    }

    public function cities($id)
    {
        return City::where('department_id', $id)
            ->orderByRaw('convert(name_'.app()->getLocale().' using gb2312)')
            ->selectRaw('convert(id, char(5)) as id, name_'.app()->getLocale().' as name')
            ->get();
    }

    public function department($id=null){
        if(is_null($id) || $id == 0){
            $id =Auth::user()->city_id;
            if(is_null($id) || $id == 0)
                return null;
        }

        $city = City::findOrFail($id);

        return Department::select('id', 'name_'.app()->getLocale().' as name', 'country_id')
            ->orderByRaw('convert(name_'.app()->getLocale().' using gb2312)')
            ->find($city->department_id);
    }

    public function departments($id){
        return Department::where('country_id', $id)
            ->orderByRaw('convert(name_'.app()->getLocale().' using gb2312)')
            ->selectRaw('convert(id, char(4)) as id, name_'.app()->getLocale().' as name')
            ->get();
    }

    public function update($id, Request $request){
        $subject = $request->input('subject', 'users');
        if($subject == 'users'){
            DB::table($subject)->where('id', auth()->id())->update(['city_id' => $id]);
        }
        else{
            DB::table($subject)->where('user_id', auth()->id())->update(['city_id' => $id]);
        }

        $city = City::select('department_id', 'name_'.app()->getLocale().' as name')->find($id);
        $department = Department::select('country_id', 'name_'.app()->getLocale().' as name')->find($city->department_id);
        $country = Country::select('id', 'name_'.app()->getLocale().' as name')->find($department->country_id);
        return ['city'=>$city, 'department'=>$department, 'country'=>$country];
    }
}
