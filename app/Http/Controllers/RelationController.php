<?php

namespace Zoomov\Http\Controllers;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Zoomov\City;
use Zoomov\Relation;
use Zoomov\Outsiderauthor;
use Zoomov\User;

class RelationController extends Controller
{
    public function index()
    {
        $id = auth()->id();

        $users = User::join(DB::raw("(select fan_id from relations where relations.idol_id = '".$id."' and love = 1) friends"), function ($join) {
            $join->on('friends.fan_id', '=', 'users.id');
        })
            ->join('cities', 'city_id', '=', 'cities.id')
            ->join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->selectRaw("users.id, username, concat(cities.name_".app()->getLocale().", '(', countries.name_".app()->getLocale().", ')')  as location, CONCAT('/profile/', users.id) as link, 0 as outsider");

        return Outsiderauthor::where('user_id', $id)
            ->selectRaw("outsiderauthors.id, outsiderauthors.name as username, email as location, outsiderauthors.link, 1 as outsider")
            ->union($users)
            ->orderByRaw('convert(username using gb2312)')
            ->get();
    }

    public function mine(){
        $id = auth()->id();
        $fans = Relation::where('idol_id', $id)->count();
        $idols = Relation::where('fan_id', $id)->count();
        $city = City::select('department_id', 'name_'.app()->getLocale())->find(Auth::user()->city_id);


        return \Response::json(array('id'=>$id, 'presentation'=>Auth::user()->presentation, 'username'=>Auth::user()->username, 'idols_cnt'=>$idols, 'fans_cnt' => $fans,
            'city_id'=>Auth::user()->city_id, 'city_name'=>$city->name, 'relation'=>'Self'));
    }

    public function friends($id)
    {
        return User::join(DB::raw("(select fan_id, updated_at from relations where relations.idol_id = '".$id."' and love = 1) friends"), function ($join) {
                $join->on('friends.fan_id', '=', 'users.id');
            })
            ->join('cities', 'city_id', '=', 'cities.id')
            ->join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->leftJoin(DB::raw("(select count(fan_id) as cnt, idol_id from relations group by idol_id) fans"), function ($join) {
                $join->on('fans.idol_id', '=', 'users.id');
            })
            ->leftJoin(DB::raw("(select count(idol_id) as cnt, fan_id from relations group by fan_id) idols"), function ($join) {
                $join->on('idols.fan_id', '=', 'users.id');
            })
            ->leftJoin(DB::raw("(select fan_id, (CASE WHEN love=1 THEN 'Friend' ELSE 'Fan' END) as relation from relations where fan_id <>'".auth()->id()."' and relations.idol_id='".auth()->id()."') myfans"), function ($join) {
                $join->on('myfans.fan_id', '=', 'users.id');
            })
            ->leftJoin(DB::raw("(select idol_id, (CASE WHEN love=1 THEN 'Friend' ELSE 'Idol' END) as relation from relations where idol_id <>'".auth()->id()."' and relations.fan_id='".auth()->id()."') myidols"), function ($join) {
                $join->on('myidols.idol_id', '=', 'users.id');
            })
            ->selectRaw("users.id, users.username, users.presentation, countries.name_".app()->getLocale()." as sortname, users.city_id, cities.name_".app()->getLocale()." as city_name,
                    IFNULL(fans.cnt, 0) as fans_cnt, IFNULL(idols.cnt, 0) as idols_cnt,
                    1 as love,(CASE WHEN users.id = '".auth()->id()."' THEN 'Self' ELSE IFNULL(myfans.relation, myidols.relation) END) as relation")
            ->orderBy('friends.updated_at')
            ->paginate(8);
    }

    public function fans($id)
    {

        return User::join(DB::raw("(select fan_id, love, updated_at from relations where relations.idol_id = '".$id."') friends"), function ($join) {
            $join->on('friends.fan_id', '=', 'users.id');
        })
            ->join(DB::raw("(select id, department_id, name_".app()->getLocale()." as city_name from cities) city"), function ($join) {
                $join->on('users.city_id', '=', 'city.id');
            })
            ->join(DB::raw("(select id, country_id from departments) depart"), function ($join) {
                $join->on('city.department_id', '=', 'depart.id');
            })
            ->join(DB::raw("(select id, name_".app()->getLocale()." as sortname from countries) country"), function ($join) {
                $join->on('depart.country_id', '=', 'country.id');
            })
            ->leftJoin(DB::raw("(select count(fan_id) as cnt, idol_id from relations group by idol_id) fans"), function ($join) {
                $join->on('fans.idol_id', '=', 'users.id');
            })
            ->leftJoin(DB::raw("(select count(idol_id) as cnt, fan_id from relations group by fan_id) idols"), function ($join) {
                $join->on('idols.fan_id', '=', 'users.id');
            })
            ->leftJoin(DB::raw("(select fan_id, (CASE WHEN love=1 THEN 'Friend' ELSE 'Fan' END) as relation from relations where fan_id <>'".auth()->id()."' and relations.idol_id='".auth()->id()."') myfans"), function ($join) {
                $join->on('myfans.fan_id', '=', 'users.id');
            })
            ->leftJoin(DB::raw("(select idol_id, (CASE WHEN love=1 THEN 'Friend' ELSE 'Idol' END) as relation from relations where idol_id <>'".auth()->id()."' and relations.fan_id='".auth()->id()."') myidols"), function ($join) {
                $join->on('myidols.idol_id', '=', 'users.id');
            })
            ->selectRaw("users.id, users.username, city.id as city_id, users.presentation, sortname, city.city_name, friends.love,
            IFNULL(fans.cnt, 0) as fans_cnt, IFNULL(idols.cnt, 0) as idols_cnt,
                (CASE WHEN users.id = '".auth()->id()."' THEN 'Self' ELSE IFNULL(myfans.relation, myidols.relation) END) as relation")
            ->orderBy('friends.updated_at')
            ->paginate(8);
    }

    public function idols($id)
    {
        return User::join(DB::raw("(select idol_id, love, updated_at from relations where relations.fan_id = '".$id."') friends"), function ($join) {
            $join->on('friends.idol_id', '=', 'users.id');
        })
            ->join('cities', 'city_id', '=', 'cities.id')
            ->join('departments', 'department_id', '=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->leftJoin(DB::raw("(select count(fan_id) as cnt, idol_id from relations group by idol_id) fans"), function ($join) {
                $join->on('fans.idol_id', '=', 'users.id');
            })
            ->leftJoin(DB::raw("(select count(idol_id) as cnt, fan_id from relations group by fan_id) idols"), function ($join) {
                $join->on('idols.fan_id', '=', 'users.id');
            })
            ->leftJoin(DB::raw("(select fan_id, (CASE WHEN love=1 THEN 'Friend' ELSE 'Fan' END) as relation from relations where fan_id <>'".auth()->id()."' and relations.idol_id='".auth()->id()."') myfans"), function ($join) {
                $join->on('myfans.fan_id', '=', 'users.id');
            })
            ->leftJoin(DB::raw("(select idol_id, (CASE WHEN love=1 THEN 'Friend' ELSE 'Idol' END) as relation from relations where idol_id <>'".auth()->id()."' and relations.fan_id='".auth()->id()."') myidols"), function ($join) {
                $join->on('myidols.idol_id', '=', 'users.id');
            })
            ->selectRaw("users.id, users.username, users.city_id, users.presentation, countries.name_".app()->getLocale()." as sortname, cities.name_".app()->getLocale()." as city_name, friends.love,
                IFNULL(fans.cnt, 0) as fans_cnt, IFNULL(idols.cnt, 0) as idols_cnt,
                (CASE WHEN users.id = '".auth()->id()."' THEN 'Self' ELSE IFNULL(myfans.relation, myidols.relation) END) as relation")
            ->orderBy('friends.updated_at')
            ->paginate(8);
    }

    public function show($id)
    {
        $idols = Relation::where('fan_id', $id)
            ->select('idol_id', 'love')
            ->with('idol')
            ->get();

        $fans = Relation::where('idol_id', $id)
            ->select('fan_id', 'love')
            ->with('fan')
            ->get();

        return [$idols, $fans];
    }

    public function commonFriends($id)
    {
        $friends = Relation::where('idol_id', Auth::id())->where('fan_id', '<>', $id)
            ->join(DB::raw("(select fan_id as uid from relations where relations.idol_id = '".$id."' and fan_id <> '".auth()->id()."' and love = 1) team"), function ($join) {
                $join->on('team.uid', '=', 'relations.fan_id');
            })
            ->join('users','relations.fan_id', '=', 'users.id')
            ->join('cities','users.city_id', '=', 'cities.id')
            ->where('relations.love', '1')
            ->select('relations.fan_id as friend_id', 'users.username', 'users.presentation', 'cities.name as city_name')
            ->distinct()
            ->get();

        $fans = Relation::where('idol_id', Auth::id())->where('fan_id', '<>', $id)
            ->join(DB::raw("(select fan_id as uid, love as tlove from relations where relations.idol_id = '".$id."' and fan_id <> '".auth()->id()."') team"), function ($join) {
                $join->on('team.uid', '=', 'relations.fan_id');
            })
            ->join('users','relations.fan_id', '=', 'users.id')
            ->join('cities','users.city_id', '=', 'cities.id')
            ->select('relations.fan_id', 'users.username', 'users.presentation', 'cities.name as city_name', 'relations.love', 'tlove')
            ->distinct()
            ->get();

        $idols = Relation::where('fan_id', Auth::id())->where('idol_id', '<>', $id)
            ->join(DB::raw("(select idol_id as uid, love as tlove from relations where relations.fan_id = '".$id."' and idol_id <> '".auth()->id()."') team"), function ($join) {
                $join->on('team.uid', '=', 'relations.idol_id');
            })
            ->join('users','relations.idol_id', '=', 'users.id')
            ->join('cities','users.city_id', '=', 'cities.id')
            ->select('relations.idol_id', 'users.username',  'users.presentation', 'cities.name as city_name', 'relations.love', 'tlove')
            ->distinct()
            ->get();

        return [$friends, $idols, $fans];
    }

    public function update($id){
        $me = Auth::User()->id;
        $user = $id;
        if($me == $user){
            return \Response::json(array('fans_cnt' => 0, 'relation' =>'Self'));
        }
        $relation = Relation::where('fan_id',$me)->where('idol_id', $user)->first();

        $lover = Relation::where('idol_id', $me)->where('fan_id', $user)->first();
        $love = !is_null($lover);

        if(is_null($relation))
        {
            Relation::create([
                'fan_id' => $me,
                'idol_id' => $user,
                'love' => $love
            ]);

            if($love && !$lover->love){
                $lover->love = 1;
                $lover->save();
            }
            return \Response::json(array('relation' => ($love ? 'Friend' : 'Idol')));
        }
        else{
            if($love && $lover->love){
                $lover->love = 0;
                $lover->save();
            }

            $relation->delete();

            return \Response::json(array('relation' => ($love ? 'Fan' : 'Stranger')));
        }
    }
}
