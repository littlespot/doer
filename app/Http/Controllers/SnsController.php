<?php

namespace Zoomov\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use Zoomov\Http\Requests;
use Zoomov\Sns;
use Zoomov\SnsUser;

class SnsController extends Controller
{
    public function index(){
        return Sns::leftJoin('sns_users', function ($join){
            $join->on('sns.id', '=', 'sns_users.sns_id')
                ->where('sns_users.user_id', '=', Auth::id());
            })
            ->select('sns.id', 'sns.type', 'sns.url', 'sns.name'.(app()->getLocale() == 'zh' ? '_zh' : '').' as name', 'sns_users.id as sns_id', 'sns_users.sns_name')
            ->get()
            ->groupBy('type')
            ->toArray();
    }

    public function show($id){
        return Sns::join('sns_users', function ($join) use($id){
                $join->on('sns.id', '=', 'sns_users.sns_id')
                    ->where('sns_users.user_id', '=', $id);
            })
            ->select('sns.id', 'sns.type', 'sns.url', 'sns.name'.(app()->getLocale() == 'zh' ? '_zh' : '').' as name', 'sns_users.id as sns_id', 'sns_users.sns_name')
            ->orderBy('sns.id')
            ->get();
    }

    public function store(Request $request){
        $sns = new SnsUser;
        $sns->sns_id = $request->id;
        $sns->user_id = auth()->id();
        $sns->sns_name = $request->sns_name;
        $sns->created_at = gmdate("Y-m-d H:i:s", time());
        $sns->save();
        return $sns->id;
    }

    public function update($id, Request $request){
        $sns = SnsUser::find($id);
        if(is_null($sns)){
            $this->store($request);
        }
        else if($sns->user_id == auth()->id() && $sns->sns_name != $request->sns_name){
            $sns->sns_name = $request->sns_name;
            $sns->updated_at = gmdate("Y-m-d H:i:s", time());
            $sns->save();
        }
    }

    public function destroy($id){
        $sns = SnsUser::find($id);
        if(!is_null($sns) && $sns->user_id == Auth::id()){
            $sns->delete();
        }
    }
}
