<?php

namespace Zoomov\Http\Controllers;
use App;
use Auth;
use Illuminate\Http\Request;

use Zoomov\Http\Requests;
use Zoomov\Language;

class LanguageController extends Controller
{
    public function index(){
        return Language::select('id', 'iso1', 'name_'.Auth::user()->locale.' as name', 'rank')
            ->orderBy('rank')
            ->orderByRaw('convert(name using gb2312)')
            ->get();
    }

    public function show($lang){
        if($lang == 'zh' || $lang='en'){
            if(Auth::check()){
                $user = Auth::user();
                $user->locale = $lang;
                $user->save();
            }
            else{
                session(['locale'=>$lang]);
            }
            return $lang;
        }
        else{
            return config('locale');
        }
    }

    public function update($lang){
        if($lang == 'zh' || $lang='en'){
            $user = Auth::user();
            $user->locale = $lang;
            $user->save();
        }

        return back();
    }

    public function store(Request $request){
        $lang = $request['locale'];
        if($lang == 'zh' || $lang='en'){
            if(Auth::check()){
                $user = Auth::user();
                $user->locale = $lang;
                $user->save();
            }
        }
        return back()->withCookie('XSRF-TOKEN');
    }
}
