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
        return Language::select('id', 'iso1', 'name_'.app()->getLocale().' as name', 'rank')
            ->orderBy('rank')
            ->orderByRaw('convert(name using gb2312)')
            ->get();
    }

    public function show($lang){
        app()->setLocale($lang);
        session(['locale'=>$lang]);

        return redirect(url()->previous());
     /*   if(array_key_exists($lang, config('constants.language'))){
            if(auth()->check()){
                $user = auth()->user();
                $user->locale = $lang;
                $user->save();
            }
            else{
                session(['locale'=>$lang]);
            }
            return back();
        }
        else{
            return back();
        }*/
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
            session(['locale'=>$lang]);
        }
        
        return back()->withCookie('XSRF-TOKEN');
    }
}
