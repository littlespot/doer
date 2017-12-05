<?php

namespace Zoomov\Http\Controllers;
use App;
use Auth;
use Config;
use DB;
use function GuzzleHttp\Psr7\str;
use Storage;
use Illuminate\Http\Request;
use Zoomov\Credit;
use Zoomov\Film;
use Zoomov\FilmCast;
use Zoomov\FilmCastCredit;
use Zoomov\FilmFestival;
use Zoomov\FilmFestivalReward;
use Zoomov\Language;
use Zoomov\City;
use Zoomov\Country;
use Zoomov\Genre;
use Zoomov\User;

class FilmController extends Controller
{
    public function index()
    {
        $id = Auth::id();
        $contact = User::leftJoin('user_contacts', 'user_id', 'users.id')
            ->leftJoin('cities', 'user_contacts.city_id', 'cities.id')
            ->leftJoin('departments','department_id','departments.id')
            ->leftJoin('countries', 'country_id','countries.id')
            ->select('email', 'user_contacts.first_name', 'user_contacts.last_name', 'user_contacts.address', 'user_contacts.zip', 'cities.name as city', 'countries.name_'.App::getLocale().' as country',
                'user_contacts.tel', 'user_contacts.mobile')
            ->where('users.id', $id)
            ->first();

        $films = DB::table('films')->where('user_id', $id)
            ->selectRaw('id, title, year, (CASE completed WHEN pow(2, 12) THEN 1 ELSE 0 END) as completed')
            ->orderBy('completed','desc')
            ->get();

        $copies = sizeof(Storage::disk('public')->files($id));
        return view('film.index', ['contact'=>$contact, 'films'=>$films, 'copies'=> $copies]);
    }

    public function show($id)
    {
        $film = Film::with('country')->find($id);
        $titles = DB::table('film_titles')->where('film_id', $id)->pluck('title');
        $directors = DB::table('film_directors')->join('filmakers', 'filmaker_id', '=', 'filmakers.id')
            ->where('film_id', $id)
            ->selectRaw('filmakers.id, concat(last_name, " ", first_name) as name, first_name, last_name')
            ->get();

        $genres = DB::table('film_genres')->where('film_id', $id)
            ->join('genres', 'genres.id', '=', 'genre_id')
            ->select('name_'.App::getLocale().' as name')
            ->get();

        $countries = DB::table('film_countries')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->where('film_id', $id)
            ->select('countries.id', 'name_'.App::getLocale().' as name')
            ->get();
        $shootings = DB::table('film_shootings')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->where('film_id', $id)
            ->select('countries.id', 'name_'.App::getLocale().' as name')
            ->get();

        if(!$film->dialog){
            $languages =  DB::table('film_languages')
                ->join('languages', 'language_id', '=', 'languages.id')
                ->where('film_id', $id)
                ->select('languages.id', 'name_'.App::getLocale().' as name')
                ->get();
        }
        else{
            $languages = [];
        }

        return view('film.detail', ['film'=>$film, 'directors'=>$directors,'titles'=>$titles, 'genres'=>$genres, 'countries'=>$countries, 'shootings'=>$shootings, 'languages'=>$languages]);
    }

    public function home($id, $step)
    {
        switch ($step){
            case 1:
                $film = Film::select('id', 'completed', 'title', 'title_latin', 'title_inter')->find($id);
                $languages = Language::selectRaw("id, rank, name as original, name_" . Auth::user()->locale . " as name, false as chosen")
                    ->orderBy('rank')
                    ->get();

                $titles = DB::table('film_titles')->join('languages', 'language_id', 'languages.id')
                    ->selectRaw('title, name_' . Auth::user()->locale . ' as language, language_id')
                    ->where('film_id', $film->id)
                    ->get();

                return view('film.title', ['languages' => $languages, 'film'=>$film, 'titles'=>$titles, 'step'=>$step-1]);
            case 2:
                $film = Film::select('id', 'completed','title',  'month', 'year', 'hour', 'minute', 'second')->find($id);
                $year = date("Y");
                return view('film.duration', ['year'=>$year, 'film'=>$film, 'step'=>$step-1]);
            case 3:
                $countries = Country::selectRaw("id,  name_" . Auth::user()->locale . " as name")
                    ->orderByRaw('convert(name_' . Auth::user()->locale .' using gbk) ASC')
                    ->pluck('name','id');

                $languages = Language::selectRaw("id, rank, name as original, name_" . Auth::user()->locale . " as name, false as chosen")
                    ->orderBy('rank')
                    ->pluck('name','id');

                $production = DB::table('film_countries')->where('film_id', $id)->pluck('country_id');
                $dialog = DB::table('film_languages')->where('film_id', $id)->pluck('language_id');
                $shooting = DB::table('film_shootings')->where('film_id', $id)->pluck('country_id');

                $film = Film::select('id', 'completed', 'title', 'country_id', 'dialog', 'language', 'silent')->find($id);
                return view('film.language', ['countries'=>$countries, 'languages'=>$languages, 'production'=>$production, 'dialog'=>$dialog, 'shooting'=>$shooting, 'film'=>$film, 'step'=>$step-1]);
            case 4:
                $vformats = DB::table('camera_formats')
                    ->leftJoin(DB::raw("(select camera_format_id, id, film_id from film_camera_formats where film_id = '".$id."') a"), function ($join) {
                        $join->on('camera_formats.id', '=', 'a.camera_format_id');
                    })
                    ->selectRaw('camera_formats.id, label_'.App::getLocale().' as label, IFNULL(a.id, 0) as chosen, film_id')
                    ->orderBy('mark')
                    ->orderByRaw('convert(label_'.App::getLocale().' using gbk) ASC')
                    ->get();
                $fformats = DB::table('cine_formats')
                    ->leftJoin(DB::raw("(select cine_format_id, id, film_id from film_cine_formats where film_id = '".$id."') a"), function ($join) {
                        $join->on('cine_formats.id', '=', 'a.cine_format_id');
                    })
                    ->selectRaw('cine_formats.id, label, IFNULL(a.id, 0) as chosen')
                    ->orderBy('id')->get();
                $animations = DB::table('animations')
                    ->leftJoin(DB::raw("(select animation_id, id, film_id from film_animations where film_id = '".$id."') a"), function ($join) {
                        $join->on('animations.id', '=', 'a.animation_id');
                    })
                    ->selectRaw('animations.id, label_'.App::getLocale().' as label, IFNULL(a.id, 0) as chosen')
                    ->orderByRaw('convert(label_'.App::getLocale().' using gbk) ASC')
                    ->get();
                $softwares = DB::table('film_softwares')->where('film_id', $id)->orderBy('order')->pluck('name');
                $film = Film::select('id', 'completed', 'title')->find($id);
                return view('film.shooting', ['vformats'=>$vformats, 'fformats'=>$fformats, 'animations'=>$animations, 'softwares'=>$softwares, 'film'=>$film, 'step'=>$step-1]);
            case 5:
                $pscreens = DB::table('screen_play_formats')
                    ->where('film_id', $id)
                    ->join('play_formats', 'play_format_id', '=', 'play_formats.id')
                    ->leftJoin(DB::raw("(select screen_id, language_id, name_".App::getLocale()." as language, dubbed from screen_subtitles s inner join languages l on s.language_id = l.id where screen_type = 'p') a"), function ($join) {
                        $join->on('screen_play_formats.id', '=', 'a.screen_id');
                    })
                    ->select('screen_play_formats.id', 'play_format_id', 'play_format', 'ratio', 'resolution_x', 'resolution_y', 'english_dubbed', 'size', 'decode as label', 'language_id', 'language', 'dubbed')
                    ->get();
                $vscreens = DB::table('screen_video_formats')
                    ->where('film_id', $id)
                    ->join('sounds', 'sounds.id', '=', 'sound_id')
                    ->join('video_formats', 'video_format_id', '=', 'video_formats.id')
                    ->leftJoin(DB::raw("(select screen_id, language_id, name_".App::getLocale()." as language, dubbed from screen_subtitles s inner join  languages l on s.language_id = l.id where screen_type = 'v') a"), function ($join) {
                        $join->on('screen_video_formats.id', '=', 'a.screen_id');
                    })
                    ->select('screen_video_formats.id', 'video_format_id', 'sound_id', 'ratio', 'standard',  'english_dubbed',
                        'language_id', 'language', 'dubbed', 'sounds.label_'.App::getLocale().' as sound', 'video_formats.label_'.App::getLocale().' as label')
                    ->get();
                $cscreens = DB::table('screen_cine_formats')
                    ->where('film_id', $id)
                    ->join('sounds', 'sounds.id', '=', 'sound_id')
                    ->join('cine_formats', 'cine_format_id', '=', 'cine_formats.id')
                    ->leftJoin(DB::raw("(select screen_id, language_id, name_".App::getLocale()." as language, dubbed from screen_subtitles s inner join languages l on s.language_id = l.id where screen_type = 'c') a"), function ($join) {
                        $join->on('screen_cine_formats.id', '=', 'a.screen_id');
                    })
                    ->select('screen_cine_formats.id', 'cine_format_id', 'sound_id', 'ratio', 'speed',  'english_dubbed', 'reel_count', 'reel_length',
                        'language_id', 'language', 'dubbed', 'sounds.label_'.App::getLocale().' as sound', 'cine_formats.label')
                    ->orderBy('cine_formats.label')
                    ->get();
                $pformats = DB::table('play_formats')->orderBy('decode')->get();
                $vformats = DB::table('video_formats')->select('id','label_'.App::getLocale().' as label')->orderByRaw('convert(label_'.App::getLocale().' using gbk) ASC')->get();
                $cformats = DB::table('cine_formats')->where('screen', 1)->orderBy('label')->get();
                $sounds = DB::table('sounds')->select('id', 'label_'.App::getLocale().' as label', 'digital')
                    ->orderBy('order')
                    ->get();
                $languages = Language::selectRaw("id, rank, name as original, name_" . Auth::user()->locale . " as name, false as chosen")
                    ->orderBy('rank')
                    ->get();

                $film = Film::select('id', 'completed', 'title', 'color', 'special')->find($id);
                return view('film.screen', ['pformats'=>$pformats, 'vformats'=>$vformats, 'cformats'=>$cformats, 'sounds'=>$sounds, 'languages'=>$languages,
                    'pscreens' => $pscreens, 'vscreens' => $vscreens, 'cscreens'=>$cscreens, 'film'=>$film, 'step'=>$step-1]);
            case 6:
                $styles = DB::table('styles')
                    ->leftJoin(DB::raw("(select id, style_id from film_styles where film_id = '".$id."') a"), function ($join) {
                        $join->on('styles.id', '=', 'a.style_id');
                    })
                    ->selectRaw('styles.id, label_'.App::getLocale().' as label, IFNULL(a.id,0) as chosen')
                    ->orderBy('id')
                    ->get();
                $subjects = DB::table('subjects')
                    ->leftJoin(DB::raw("(select id, subject_id from film_subjects where film_id = '".$id."') a"), function ($join) {
                        $join->on('subjects.id', '=', 'a.subject_id');
                    })
                    ->selectRaw('subjects.id, label_'.App::getLocale().' as label, IFNULL(a.id,0) as chosen')
                    ->orderBy('id')
                    ->get();
                $genres = Genre::where('film', '>', 0)
                    ->leftJoin(DB::raw("(select id, genre_id from film_genres where film_id = '".$id."') a"), function ($join) {
                        $join->on('genres.id', '=', 'a.genre_id');
                    })
                    ->selectRaw('genres.id, name_'.App::getLocale().' as name, IFNULL(a.id,0) as chosen')
                    ->orderBy('sequence')
                    ->get();

                $film = Film::select('id', 'completed', 'title')->find($id);

                return view('film.genre', ['genres'=>$genres, 'styles'=>$styles, 'subjects'=>$subjects, 'film'=>$film, 'step'=>$step-1]);
            case 7:
                $lang = $this->getLanguage();

                $synopsis = DB::table('film_synopsis')
                    ->where('film_id', $id)
                    ->where('language_id', $lang->id)
                    ->first(['content']);

                $list = DB::table('film_synopsis')
                    ->where('film_id', $id)
                    ->where('language_id', '<>', $lang->id)
                    ->join('languages', 'language_id', '=', 'languages.id')
                    ->get(['film_synopsis.id', 'content', 'language_id', 'languages.name_'.App::getLocale().' as language']);

                $languages = Language::where('id', '<>', $lang->id)
                    ->selectRaw("id, rank, name_" . Auth::user()->locale . " as name")
                    ->orderBy('rank')
                    ->pluck('name', 'id');

                $film = Film::select('id', 'completed', 'title')->find($id);

                return view('film.synopsis', ['lang'=>$lang, 'languages'=>$languages,'list'=>$list, 'synopsis'=>$synopsis, 'film'=>$film, 'step'=>$step-1]);
            case 8:
                $year = date("Y");

                $countries = Country::selectRaw("id,  name_" . Auth::user()->locale . " as name, sortname")
                    ->orderByRaw('convert(name_' . Auth::user()->locale .' using gbk) ASC')
                    ->get();

                $directors =  DB::table('film_directors')->where('film_id', $id)
                    ->join('filmakers', 'filmaker_id','=', 'filmakers.id')
                    ->selectRaw('filmaker_id, concat(last_name, " ", IFNULL(first_name, "")) as name, first_name, last_name, tel, mobile, born, country_id, prefix, email')
                    ->get();

                $film = Film::select('id', 'completed', 'title', 'virgin')->find($id);
                return view('film.director', [ 'year'=>$year, 'countries'=>$countries, 'directors'=>$directors, 'film'=>$film, 'step'=>$step-1]);
            case 9:
                $year = date("Y");
                $credits = Credit::select('id', 'label_'.App::getLocale().' as label')
                    ->orderBy('id')
                    ->get();
                $casts = FilmCast::where('film_id', $id)
                    ->join('filmakers', 'filmaker_id', '=', 'filmakers.id')
                    ->join('film_cast_credits', 'film_cast_id', '=', 'film_casts.id')
                    ->selectRaw('film_cast_id, credit_id, film_casts.id, filmaker_id, concat(last_name," " ,first_name) as name')
                    ->get()
                    ->groupBy('credit_id')
                    ->all();
                $film = Film::select('id', 'completed', 'title', 'music_original', 'screenplay_original')->find($id);
                return view('film.credit', ['year'=>$year, 'credits'=>$credits, 'casts'=>$casts, 'film'=>$film, 'step'=>$step-1]);
            case 10:
                $year = date("Y");

                $countries = Country::selectRaw("id,  name_" . Auth::user()->locale . " as name, sortname")
                    ->orderByRaw('convert(name_' . Auth::user()->locale .' using gbk) ASC')
                    ->get();
                $producers =  DB::table('film_producers')->where('film_id', $id)
                    ->join('filmakers', 'filmaker_id','=', 'filmakers.id')
                    ->selectRaw('filmaker_id, concat(last_name, " ", IFNULL(first_name, "")) as name, first_name, last_name, tel, mobile, born, country_id, prefix, email')
                    ->get();
                $film = Film::select('id', 'completed', 'title', 'school', 'school_name')->find($id);
                return view('film.producer', ['year'=>$year, 'countries'=>$countries, 'film'=>$film, 'producers'=>$producers, 'step'=>$step-1]);
            case 11:
                $year = date("Y");

                $countries = Country::selectRaw("id,  name_" . Auth::user()->locale . " as name, sortname")
                    ->orderByRaw('convert(name_' . Auth::user()->locale .' using gbk) ASC')
                    ->get();
                $sellers =  DB::table('film_sellers')->where('film_id', $id)
                    ->join('filmakers', 'filmaker_id','=', 'filmakers.id')
                    ->selectRaw('filmaker_id, concat(last_name, " ", IFNULL(first_name, "")) as name, first_name, last_name, tel, mobile, born, country_id, prefix, email')
                    ->get();
                $festivals  = FilmFestival::where('film_id', $id)
                    ->with('rewards')
                    ->join('cities', 'city_id', '=', 'cities.id')
                    ->join('departments', 'department_id', '=', 'departments.id')
                    ->join('countries', 'departments.country_id', '=', 'countries.id')
                    ->selectRaw('film_festivals.id, event, year, competition, countries.name_'.App::getLocale().' as country, cities.name_'.App::getLocale().' as city')
                    ->get();
                $diffusion  = DB::table('film_diffusion')->where('film_id', $id)
                    ->leftJoin('countries', 'film_diffusion.country_id', '=', 'countries.id')
                    ->selectRaw('film_diffusion.id, country_id, film_diffusion.name, year, channel, countries.name_'.App::getLocale().' as country')
                    ->get();
                $theaters  = DB::table('film_theaters')->where('film_id', $id)
                    ->leftJoin('countries', 'film_theaters.country_id', '=', 'countries.id')
                    ->selectRaw('film_theaters.id, country_id, film_theaters.title, year, program, contact, distribution, countries.name_'.App::getLocale().' as country')
                    ->get();
                $film = Film::select('id', 'completed', 'title', 'music_rights', 'inter_rights', 'festivals', 'diffusion','theaters')->find($id);
                return view('film.seller', ['year'=>$year, 'countries'=>$countries, 'film'=>$film, 'sellers'=>$sellers, 'festivals'=>$festivals ,'diffusion'=>$diffusion, 'theaters'=>$theaters, 'step'=>$step-1]);
            case 12:
                $film = Film::select('id', 'completed', 'title')->find($id);
                $folderName = 'film/'.$id. '/pictures';
                if(Storage::disk('public')->exists($folderName)) {
                    $files = Storage::disk('public')->files($folderName);
                    $completed = $this->setStatus(sizeof($files) < 1, $step-1, $film->completed);
                }
                else{
                    $completed = $this->setStatus(true, $step-1, $film->completed);
                }
                if($completed != $film->completed){
                    $film->update(['completed' => $completed]);
                }

                return view('film.media', ['film'=>$film, 'step'=>$step-1]);
            default:
                $film = Film::select('id', 'completed', 'title', 'title_latin', 'title_inter')->find($id);
                return $this->title($film);
        }
    }

    public function getSynopsis($id){
        $lang = $this-> getLanguage();
        return DB::table('film_synopsis')->where('film_id', $id)
            ->where('language_id', '<>', $lang->id)
            ->join('languages', 'language_id', '=', 'languages.id')
            ->select('film_synopsis.id','language_id','languages.name_'.App::getLocale().' as language', 'content')
            ->get();
    }

    public function getMakers(){
        return  DB::table('filmakers')->where('filmakers.user_id', Auth::id())
            ->leftJoin('countries', 'filmakers.country_id', '=', 'countries.id')
            ->selectRaw('filmakers.id, first_name, last_name, prefix, born, tel, mobile, web, filmakers.country_id, countries.name_'.App::getLocale().' as country')
            ->get();
    }

    public function getMakersWithAddress($id=null, $except = null){

        $db =  DB::table('filmakers')->where('filmakers.user_id', Auth::id());

        if(!is_null($except)){
            $exceptions = DB::table('film_'.$except)->where('film_id', $id)->pluck('filmaker_id');
            $db = $db->whereNotIn('filmakers.id', $exceptions);
        }

        return  $db->leftJoin(DB::raw("(select contact_id, filmaker_id, city_id, address, zip, company from filmaker_contacts inner join contacts on contact_id = contacts.id) a"), function ($join) {
                    $join->on('filmakers.id', '=', 'a.filmaker_id');
                })
                ->leftJoin('cities', 'city_id', '=', 'cities.id')
                ->leftJoin('departments', 'department_id', '=', 'departments.id')
                ->leftJoin('countries', 'departments.country_id', '=', 'countries.id')
                ->selectRaw('filmakers.id, first_name, last_name, prefix, born, tel, mobile, web, filmakers.country_id, address, zip,
                    contact_id, countries.name_'.App::getLocale().' as country, departments.country_id as cid, company,
                    department_id, departments.name_'.App::getLocale().' as department, city_id, cities.name_'.App::getLocale().' as city')
                ->get();
    }

    public function getContacts(){
        return DB::table('contacts')->where('user_id', Auth::id())
            ->leftJoin('cities', 'city_id', '=', 'cities.id')
            ->leftJoin('departments', 'department_id', '=', 'departments.id')
            ->leftJoin('countries', 'departments.country_id', '=', 'countries.id')
            ->selectRaw('contacts.id, address, zip, countries.name_'.App::getLocale().' as country, departments.country_id, company,
                    department_id, departments.name_'.App::getLocale().' as department, city_id, cities.name_'.App::getLocale().' as city')
            ->get();
    }

    public function previewForm($id){
        $film = Film::find($id);
        $status = decbin($film->completed);
        if(strlen($status) < 12){
            return redirect('/film/'.$id.'/'.strlen($status) + 1);
        }
        else{
            $index = strpos($status, '0');
            if(!$index){
                $languages = Language::selectRaw("id, rank, name as original, name_" . Auth::user()->locale . " as name, false as chosen")
                    ->orderBy('rank')
                    ->get();
                $film = Film::find($id);
                if(Storage::disk('public')->exists("film/".$id."/preview")){
                    $files = Storage::disk('public')->files("film/".$id."/preview");
                    $file = sizeof($files)>0 ? $files[0] :null;
                    $size = Storage::disk('public')->size($file);
                    $name = basename($file);
                    $ext = substr($name, strrpos($name,'.')+1);
                }
                else{
                    $file = null;
                    $size = 0;
                    $name = '';
                    $ext = '';
                }
                return view('film.upload', ['languages'=>$languages, 'film'=>$film, 'file'=>$file, 'size'=>$size, 'name'=>$name, 'ext'=>$ext]);
            }
            else{
                return redirect('/film/'.$id.'/'.$index + 1);
            }
        }
    }

    public function postTitle(Request $request){
        $request->validate([
            'title' => 'required|max:80',
            'title_latin' => 'max:80',
            'title_inter' => 'max:80'
        ]);

        $film = Film::find($request->id);

        $titles =  $request->has('titles') ? $request['titles'] : null;

        $oldLang = DB::table('film_titles')->where('film_id', $film->id)->pluck('language_id')->toArray();

        $toRemove = is_null($titles) ? $oldLang : array_diff($oldLang, array_keys($titles));

        DB::table('film_titles')->where('film_id',$film->id)->whereIn('language_id', $toRemove)->delete();

        $toAdd = is_null($titles) ? [] : array_diff(array_keys($titles), $oldLang);

        foreach ($toAdd as $lang){
            DB::table('film_titles')->insert([
                'film_id' => $film->id,
                'language_id' => $lang,
                'title' => $titles[$lang],
                'created_at' => date('Y-m-d h:i:s')
            ]);
        }
        $film->update($request->only('title','title_latin','title_inter'));
        return $this->afterPost($film->id, $film->completed, $request['step']);
    }

    public function postTime(Request $request){
        $film = Film::find($request->id);

        $values = $request->only('year', 'month', 'hour','second', 'minute', 'completed');

        $invalid  = !is_numeric($values['month']) || !is_numeric($values['year']);

        $invalid |= ($values['hour'] == 0 && $values['minute'] ==0 && $values['second'] == 0);

        $step = $request['step'];

        $values['completed'] = $this->setStatus($invalid, $step, $values['completed']);

        $film->update($values);

        return $this->afterPost($film->id, $values['completed'], $step);
    }

    public function postProduction(Request $request)
    {
        $film = Film::find($request->id);

        $silent = $request->input('silent', null);
        $dialog = $request->input('sound', null);

        $invalid = is_null($silent) || is_null($dialog);

        $country_id = $request['country_id'];

        $invalid |= !is_numeric($country_id);

        $step = $request['step'];

        $completed = $this->setStatus($invalid, $step, $request['completed']);

        $filmLang = $request["language"];
        if($dialog == 0){
            DB::table('film_languages')->where('film_id', $film->id)->delete();
            $filmLang = null;
        }
        else{
           $this->setParameters(array_unique($request["dialog"]), 'film_languages', $film->id, 'language_id');
        }

        $film->update([
            "country_id" => $country_id,
            "dialog" => $dialog,
            "silent" => $silent,
            "language" => $filmLang,
            "completed" => $completed
        ]);

        $this->setParameters(array_unique($request["production"]), 'film_countries', $film->id);
        $this->setParameters(array_unique($request["shooting"]), 'film_shootings', $film->id);

        return $this->afterPost($film->id, $completed, $step);
    }

    public function postFormat(Request $request)
    {
        $film = Film::find($request->id);

        $table = 'camera_format';
        $video = $request->has($table) ? $request[$table] : null;
        $invalid = is_null($video);

        $this->setFormat($video, $table, $film->id);

        $table = 'cine_format';
        $video = $request->has($table) ? $request[$table] : null;
        $invalid |= is_null($video);

        $this->setFormat($video, $table, $film->id);

        $table = 'animation';
        $video = $request->has($table) ? $request[$table] : null;
        $this->setFormat($video, $table, $film->id);

        $this->setParameters(array_unique($request['software']), 'film_softwares', $film->id, $col='name');

        $step = $request['step'];
        $completed = $request['completed'];

        $changed = $this->setStatus($invalid, $step,$completed);

        if($changed != $completed){
            $film->update(['completed'=>$changed]);
        }

        return $this->afterPost($film->id, $completed, $step);
    }

    public function postScreen(Request $request)
    {
        $film = Film::find($request->id);
        $color = $request->input('color', null);
        $special = $request->input('special', null);

        $invalid = is_null($color) || is_null($special);

        $invalid |= !DB::table('screen_play_formats')->where('film_id', $film->id)->exists();

        $completed = $request['completed'];
        $step = $request['step'];
        $change = $this->setStatus($invalid, $step, $completed);
        $film->update([
            'color' => $color,
            'special' => $special,
            'completed'=>$change
        ]);

        return $this->afterPost($film->id, $completed, $step);
    }

    public function postGenre(Request $request){
        $film = Film::find($request->id);
        $genres = $request->input('genre', []);

        $this->setParameters(array_unique($genres), 'film_genres', $film->id, 'genre_id');
        $this->setParameters(array_unique($request->input("style", [])), 'film_styles', $film->id, 'style_id');
        $this->setParameters(array_unique($request->input("subject",[])), 'film_subjects', $film->id, 'subject_id');

        $completed = $request['completed'];
        $step = $request['step'];

        $changed = $this->setStatus(sizeof($genres ) < 1, $step, $completed);

        if($changed != $completed){
            $film->update(['completed'=>$changed]);
        }
        return $this->afterPost($film->id, $changed, $step);
    }

    public function postSynopsis(Request $request){
        $film = Film::find($request->id);
        $valid = $request->has('synopsis');
        if($valid){
           $lang = $this->getLanguage();
           $changed =  DB::table('film_synopsis')->where('film_id', $film->id)->where('language_id', $lang->id)->first();
           $content = $request['synopsis'];
           if(is_null($changed)){
               DB::table('film_synopsis')->insert([
                   'film_id' => $film->id,
                   'language_id' => $lang->id,
                   'content' => $content,
                   'created_at' => date('Y-m-d h:i:s')
               ]);
           }
           else if($content != $changed->content){
               DB::table('film_synopsis')->update(['content'=>$content]);
           }
        }

        $step = $request['step'];
        $completed = $request['completed'];
        $changed = $this->setStatus(!$valid, $step, $completed);
        if($changed != $completed){
            $film->update(['completed'=>$changed]);
        }

        return $this->afterPost($film->id, $changed, $step);

    }

    public function postDirector(Request $request){
        $film = Film::find($request->id);
        $virgin = $request->input('virgin', null);

        $invalid = is_null($virgin) || !DB::table('film_directors')->where('film_id', $film->id)->exists();

        $step = $request['step'];
        $completed = $request['completed'];
        $changed = $this->setStatus($invalid, $step, $completed);

        $film->update([
            'virgin' => $virgin,
            'completed'=>$changed
        ]);

        return $this->afterPost($film->id, $changed, $step);
    }

    public function postProducer(Request $request){
        $film = Film::find($request->id);

        $school = $request->input('school', null);
        $school_name = null;
        $invalid = is_null($school);

        if(!$invalid && $school == 1){
            $school_name  = $request['school_name'];
            $invalid = is_null($school_name);
        }

        $invalid |= !DB::table('film_producers')->where('film_id', $film->id)->exists();

        $step = $request['step'];
        $completed = $request['completed'];
        $changed = $this->setStatus($invalid, $step, $completed);

        $film->update([
            'school' => $school,
            'school_name' => $school_name,
            'completed'=>$changed
        ]);

        return $this->afterPost($film->id, $changed, $step);
    }

    public function postRights(Request $request)
    {
        $film = Film::find($request->id);

        $music_rights = $request->input('music_rights', null);
        $inter_rights = $request->input('inter_rights', null);
        $festivals = $request->input('festivals', null);
        $diffusion = $request->input('diffusion', null);
        $theaters = $request->input('theaters', null);
        $invalid = is_null($music_rights) || is_null($inter_rights);

        $invalid |= !DB::table('film_sellers')->where('film_id', $film->id)->exists();

        $step = $request['step'];
        $completed = $request['completed'];
        $changed = $this->setStatus($invalid, $step, $completed);

        $film->update([
            'music_rights' => $music_rights,
            'inter_rights' => $inter_rights,
            'festivals' => $festivals,
            'diffusion' => $diffusion,
            'theaters' => $theaters,
            'completed'=>$changed
        ]);

        return $this->afterPost($film->id, $changed, $step);
    }

    public function postCredit(Request $request)
    {

        $film = Film::find($request->id);

        $music_original = $request->input('music_original', null);
        $screenplay_original = $request->input('screenplay_original', null);

        $invalid = is_null($music_original) || is_null($screenplay_original);

        $invalid |= !DB::table('film_casts')->where('film_id', $film->id)->exists();

        $step = $request['step'];
        $completed = $request['completed'];
        $changed = $this->setStatus($invalid, $step, $completed);

        $film->update([
            'music_original' => $music_original,
            'screenplay_original' => $screenplay_original,
            'completed'=>$changed
        ]);

        $castFound = false;

        if($music_original == 0){
            $credit = Credit::where('original', 'music')->first();
            $casts = DB::table('film_casts')->where('film_id', $film->id)->pluck('id');
            $castFound = true;
            if(sizeof($casts)>0) {
                DB::table('film_cast_credits')->whereIn('film_cast_id', $casts)->where('credit_id', $credit->id)->delete();
            }
        }

        if($screenplay_original == 0){
            $credit = Credit::where('original', 'play')->first();
            if(!$castFound){
                $casts = DB::table('film_casts')->where('film_id', $film->id)->pluck('id');
            }
            if(sizeof($casts)>0){
                DB::table('film_cast_credits')->whereIn('film_cast_id', $casts)->where('credit_id', $credit->id)->delete();
            }
        }
        return $this->afterPost($film->id, $changed, $step);
    }

    public function postMedia(Request $request){
        $step = $request['step'];
        $film = Film::find($request->id);
        return $this->afterPost($film, $film->completed, $step);
    }

    public function saveCredits($id, Request$request){
        $maker = $this->createMaker($id, 'credit', $request);
        $cast = DB::table("film_casts")->insertGetId(['film_id'=>$id, 'filmaker_id'=>$maker->id]);
        foreach ($request['credits'] as $credit) {
            DB::table('film_cast_credits')->insert(['film_cast_id' => $cast, 'credit' => $credit]);
        }

        return $cast;
    }

    public function createMaker($id, $format, Request $request){
        $validator = [
            'last_name' => 'required'
        ];

        $type = substr($format, 0, 1);
        if ($type == 'd') {
            array_merge($validator, ['first_name' => 'required']);
        }
        else if($type == 'p'){
            array_merge($validator, [ 'prefix' => 'required','first_name' => 'required',
                'contact' => 'required',
                'contact.address' => 'required|max:200',
                'contact.zip' => 'required|max:12',
                'contact.city_id' => 'required'
            ]);
        }
        else if($type == 'c'){
            array_merge($validator, ['credits' => 'required']);
        }
        else{
            array_merge($validator, ['prefix' => 'required']);
        }

        $this->validate($request, $validator);

        $user = Auth::id();
        $values = ['prefix'=>$request['prefix'], 'last_name'=>$request['last_name'],'first_name'=>$request['first_name'],'user_id'=>$user];

        if($request->has('id')){
            $maker = DB::table('filmakers')->find($request->id);
        }
        else{
            $maker = DB::table('filmakers')->where($values)->first();
        }

        if(is_null($maker)){
            $maker_id = $this->uuid('m', 10, '13');
            DB::table('filmakers')->insert(array_merge($values, ['id'=>$maker_id]));
            $maker = DB::table('filmakers')->find($maker_id);
        }

        $found = DB::table('film_'.$format.'s')->where([
            'film_id' => $id,
            'filmaker_id' => $maker->id
        ])->exists();

        if(!$found){
            DB::table('film_'.$format.'s')->insert([
                'film_id' => $id,
                'filmaker_id' => $maker->id
            ]);
        }

        return $maker;
    }

    public function saveMaker($id, $format, Request $request)
    {
        $maker = $this->createMaker($id, $format, $request);
        if($request->has('contact')){

            $contact = array_merge($request['contact'], ['user_id' => Auth::id()]);
            $address = DB::table('contacts')->where($contact)->first();
            if (!is_null($address)) {
                $maker_address = DB::table('filmaker_contacts')->where('filmaker_id', $maker->id)->where('contact_id', $address->id)->first();

                if(is_null($maker_address)){
                    DB::table('filmaker_contacts')->insert(['filmaker_id'=>$maker->id, 'contact_id'=>$address->id]);
                }
            }
            else{
                $address= array_merge($contact, ['id'=> $this->uuid('a', 10, '13')]);
                $address_id = DB::table('contacts')->insertGetId($address);
                DB::table('filmaker_contacts')->insert(['filmaker_id'=>$maker->id, 'contact_id'=>$address_id]);
            }
        }

        return $maker->id;
    }

    public function saveFestival($id, Request $request)
    {
        $validator = [
            'film_id'=>$id,
            'year' => 'required',
            'event' => 'required',
            'city_id' => 'required'
        ];

        $this->validate($request, $validator);

        $festival = FilmFestival::create( ['film_id'=>$id,
            'year' => $request['year'],
            'event' => $request['event'],
            'city_id' => $request['city_id'],
            'competition' => $request['competition'],
            'created_at' => time()
        ]);

        if($request->has('rewards')){
            foreach ($request['rewards'] as $reward){
                if(!is_null($reward)){
                    FilmFestivalReward::create([
                        'film_festival_id' => $festival->id,
                        'reward' => $reward
                    ]);
                }

            }
        }

        return $festival->id;
    }

    public function saveDiffusion($id, Request $request)
    {
        $validator = [
            'film_id'=>$id,
            'channel' => 'required',
            'name' => 'required'
        ];

        $this->validate($request, $validator);

        $diffusion = DB::table('film_diffusion')->insertGetId( ['film_id'=>$id,
            'year' => $request['year'],
            'name' => $request['name'],
            'country_id' => $request['country_id'],
            'channel' => $request['channel'],
            'created_at' => gmdate("Y-m-d H:i:s", time())
        ]);

        return $diffusion;
    }

    public function saveTheater($id, Request $request)
    {
        $validator = [
            'film_id'=>$id,
            'program' => 'required',
            'title' => 'required',
            'distribution' => 'required'
        ];

        $this->validate($request, $validator);

        $diffusion = DB::table('film_theaters')->insertGetId( ['film_id'=>$id,
            'year' => $request['year'],
            'program' => $request['program'],
            'country_id' => $request['country_id'],
            'title' => $request['title'],
            'distribution' => $request['distribution'],
            'contact' => $request['contact'],
            'created_at' => gmdate("Y-m-d H:i:s", time())
        ]);

        return $diffusion;
    }

    public function saveSynopsis($id, Request $request){
        $this->validate($request,[
            'language_id' => 'required',
            'content' => 'required|max:400'
        ]);

        $content = $request['content'];
        $language = $request['language_id'];
        $synopsis =  DB::table('film_synopsis')->where('film_id', $id)->where('language_id', $language)->first();
        if(is_null($synopsis)){
            $id = DB::table('film_synopsis')->insertGetId([
                'film_id' => $id,
                'language_id' => $language,
                'content' => $content,
                'created_at' => date('Y-m-d h:i:s')
            ]);
        }
        else if($content != $synopsis->content){
            $id = $synopsis->id;
            DB::table('film_synopsis')->update(['content'=>$content]);
        }

        return $id;
    }

    public function saveCredit($id, Request $request){
        $maker = $this->createMaker($id, 'cast', $request);
        $cast = DB::table('film_casts')->where(['film_id'=>$id, 'filmaker_id'=>$maker->id])->first();
        foreach ($request['credits'] as $credit){
            $value = ['film_cast_id'=> $cast->id, 'credit_id'=>$credit];
            if(!FilmCastCredit::where($value)->exists()){
                FilmCastCredit::create($value);
            }
        }

        return $maker->id;
    }

    public function screenFormat($format, Request $request){
        $values = $request->except( 'subtitle', 'dubbed');
        $validator = [
            $format.'_format_id' => 'required',
            'ratio' => 'required'
        ];

        $type = substr($format,0,1);

        if($type == 'p'){
            array_merge($validator,[
                'resolution_x' => 'required',
                'resolution_y' => 'required',
                'size' => 'numeric']);
        }
        else if($type = 'c'){
            array_merge($validator,['sound_id' => 'required',
                'speed' => 'required']);
        }
        else{
            array_merge($validator,['sound_id' => 'required',
                'standard' => 'required']);
        }

        $this->validate($request, $validator);

        $engdubbed = 0;
        if($request->has('english_dubbed')){
            $english_dubbed = $request['english_dubbed'];
            foreach ($english_dubbed as $d){
                $engdubbed += $d;
            }

            $values['english_dubbed'] = $engdubbed;
        }

        $screen = DB::table('screen_'.$format.'_formats')->where($values)->exists();

        if($screen){
            return [];
        }

        $screen_id = DB::table('screen_'.$format.'_formats')->insertGetId($values);
        $subtitle = $request->input('subtitle', null);
        $dubbed = 0;

        if(!is_null($subtitle) && $request->has('dubbed')){
            $dubbeds = $request['dubbed'];
            foreach ($dubbeds as $d){
                $dubbed += $d;
            }

            DB::table('screen_subtitles')->insert([
                'screen_id'=> $screen_id,
                'screen_type' => substr($format,0,1),
                'language_id' => $subtitle,
                'dubbed' => $dubbed
            ]);
        }

        return [$engdubbed, $dubbed];
    }

    public function upload($id, $format, Request $request){
        $file = $request->file($format);

        $folderName = 'film/'.$id;
        if(!Storage::disk('public')->exists($folderName)) {
            Storage::makeDirectory($folderName);
        }

        $folderName = $folderName.'/'.$format;
        if(!Storage::disk('public')->exists($folderName)) {
            Storage::makeDirectory($folderName);
        }

        $files = Storage::disk('public')->files($folderName);
        if(sizeof($files) > 8){
            return false;
        }
        $name = $file->getClientOriginalName();
        $ext = $file->getClientOriginalExtension();
        $oldName = str_replace($ext, time(),$name);
        $name = $oldName.'.'.$ext;
        $request->file($format)->storeAs(
            '/public/'.$folderName, $name
        );

        if($format == 'pictures'){
            $film = Film::find($id);
            $completed = $this->setStatus(sizeof($files) < 1, 11, $film->completed);
            if($completed != $film->completed){
                $film->update(['completed' => $completed]);
                return ['result'=>$name, 'completed'=>decbin($completed)];
            }
        }

        return ['result'=>$name];
    }

    public function preview($id, Request $request){
        if(!$request->hasFile('preview')){
            return ['result' => 'error'];
        }

        $folderName = 'film/'.$id;
        if(!Storage::disk('public')->exists($folderName)) {
            Storage::makeDirectory($folderName);
        }

        $folderName = $folderName.'/preview';
        if(!Storage::disk('public')->exists($folderName)) {
            Storage::makeDirectory($folderName);
        }

        $files = Storage::disk('public')->files($folderName);
        foreach ($files as $file){
            if(basename($file) == 'preview'){
                unlink($file);
            }
        }
        $file = $request->file('preview');
        $ext = $file->getClientOriginalExtension();
        $path = $file->storeAs(
            '/public/'.$folderName, 'preview.'.$ext
        );

        $film = DB::table('films')->first();
        return ['result' => $path, 'film'=>$film];
    }

    public function store(Request $request){

        $this->validate($request, [
            'title' => 'required|max:80',
            'title_latin' => 'max:80',
            'title_inter' => 'max:80',
            'rights' => 'accepted:true'
        ]);

        $id = $this->uuid('f',10,'faker');
        Film::create([
            'id' => $id,
            'user_id' => Auth::id(),
            'title' => $request['title'],
            'title_latin' => $request['title_latin'],
            'title_inter' => $request['title_inter'],
            'created_at' => time(),
            'completed' => 1
        ]);

        return $id;
    }

    public function remove($id, $format, Request $request){
        $folderName = 'film/'.$id;
        if(!Storage::disk('public')->exists($folderName)) {
            return $request->all();
        }

        $folderName = $folderName.'/'.$format;
        if(!Storage::disk('public')->exists($folderName)) {
            return $request->all();
        }

        Storage::disk('public')->delete($folderName.'/'.$request['key']);
        if($format == 'pictures'){
            $film = Film::find($id);
            $files = Storage::disk('public')->has($folderName);
            $completed = $this->setStatus(sizeof($files) < 1, 11, $film->completed);
            if($completed != $film->completed){
                $film->update(['completed' => $completed]);
            }
        }
        return $request->all();
    }

    public function descrotyScreenFormat($format, $id){
        DB::table('screen_'.$format.'_formats')->delete(['id'=>$id]);
        DB::table('screen_subtitles')->where('screen_id',$id)->where('screen_type', substr($format, 0, 1))->delete();
        return $id;
    }

    public function descrotySynopsis($id, $language_id){
        DB::table('film_synopsis')->where('film_id', $id)->where('language_id', $language_id)->delete();
    }

    public function descrotyMaker($id, $format){
        DB::table('film_'.$format.'s')->where('id', $id)->delete();
    }

    public function descrotyTable($format, $id){
        DB::table('film_'.$format)->where('id', $id)->delete();
    }

    private function getLanguage()
    {
        switch (App::getLocale()) {
            case 'zh':
                $lang = Language::where('name_en', 'Mandarin')->first(['id', 'name_' . App::getLocale() . ' as name']);
                break;
            case 'en':
                $lang = Language::where('name_en', 'English')->first(['id', 'name_' . App::getLocale() . ' as name']);
                break;
            case 'fr':
                $lang = Language::where('name_en', 'French')->first(['id', 'name_' . App::getLocale() . ' as name']);
                break;
            default:
                $lang = Language::where('name_en', 'English')->first(['id', 'name_' . App::getLocale() . ' as name']);
                break;
        }

        return $lang;
    }

    private function setStatus($invalid, $step, $completed){
        $bin = decbin($completed);
        $status = str_pad($bin, 12, '0');
        $status[$step] = $invalid ? 0 : 1;
        return bindec(substr($status, 0, strlen($bin) > $step ? strlen($bin) : $step+1));
    }

    private function setFormat($formats, $table, $film)
    {
        $old = DB::table('film_'.$table.'s')->where('film_id', $film)->pluck($table.'_id')->toArray();

        $toRemove = is_null($formats) ? $old : array_diff($old, $formats);

        DB::table('film_'.$table.'s')->where('film_id', $film)->whereIn($table.'_id', $toRemove)->delete();

        $toAdd = is_null($formats) ? [] : array_diff($formats, $old);

        foreach ($toAdd as $v){
            DB::table('film_'.$table.'s')->insert([
                'film_id' => $film,
                $table.'_id' => $v
            ]);
        }
    }
    private function setParameters($parameters, $table, $film, $col='country_id'){
        $order = 0;
        $size = DB::table($table)->where('film_id', $film)->count();

        foreach ($parameters as $parameter) {
            if (!is_null($parameter) && $parameter != '') {
                $order++;
                if ($size >= $order) {
                    $productions = get_object_vars(DB::table($table)->where('film_id', $film)->where('order', $order)->first());
                    if ($productions[$col] != $parameter) {
                        DB::table($table)->where('film_id', $film)->where('order', $order)->update([$col => $parameter]);
                    }
                } else {
                    DB::table($table)->insert([
                        'film_id' => $film,
                        $col => $parameter,
                        'order' => $order
                    ]);
                }
            }
        }

        DB::table($table)->where('film_id', $film)->where('order', '>',  $order)->delete();

        return $order;
    }

    private function afterPost($id, $completed, $step){
        $status = str_pad(decbin($completed), 12,'0');
        $index = strrpos($status, '0');
        return $index ? 'Y' : 'N';
        if(!$index){
            return redirect('/film/'.$id);
        }

        if($index != $step){
            return redirect('/film/'.$id.'/'.$index);
        }

        return redirect('/films/'.$id);
    }

    public function cities($id)
    {
        return City::where('department_id', $id)
            ->select('id', 'name_en as name')
            ->orderBy('name_en')
            ->get();
    }
}
