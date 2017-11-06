<?php

namespace Zoomov\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use DB;
use Zoomov\Language;
use Zoomov\Video;
use Zoomov\Genre;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $languages = Language::select('id','name_'.Auth::user()->locale.' as name')->get();

        $genres = Genre::select('id', DB::raw('name_'.Auth::user()->locale.' as name'), 'ordre')->orderBy('ordre')->get();

        return view('videos', ["languages"=>$languages, "genres"=>$genres]);
    }

    public function refresh(Request $request){
        return $this->filter($request->input('genre', 0), $request->input('language', 0), $request->input('duration', '>0'), $request->input('order', 'created_at'), $request->input('direction', 'desc'));
    }

    private function filter($genre = 0, $language = 0, $duration = '>0', $order='created_at', $direction='desc'){
        $videos = Video::whereRaw('duration '.$duration)->with('lang');

        if($genre > 0){
            $videos = $videos->where('genre_id', $genre);

            if($language > 0){
                $videos = $videos->whereExists(function ($query, $language) {
                    $query->select(DB::raw(1))
                        ->from('video_languages')
                        ->whereRaw('video_id = videos.id ans language_id = '.$language);
                });
            }
        }
        else if($language > 0){
            $videos = $videos->whereExists(function ($query, $language) {
                $query->select(DB::raw(1))
                    ->from('video_languages')
                    ->whereRaw('video_id = videos.id ans language_id = '.$language);
            });
        }

        return $videos->leftJoin(DB::raw("(select sum(count) as cnt, video_id from video_views group by video_id) views"), function ($join) {
                $join->on('views.video_id', '=', 'videos.id');
            })
            ->selectRaw('id, title, duration, description, link, created_at, IFNULL(views.cnt, 0) as view_cnt')
            ->orderBy($order, $direction)->paginate(9);
    }
}
