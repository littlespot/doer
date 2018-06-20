<?php

namespace Zoomov\Http\Controllers;
use App;
use Auth;
use Config;
use DB;
use Storage;
use Illuminate\Http\Request;
use Zoomov\Credit;
use Zoomov\FestivalInscriptionHonor;
use Zoomov\Film;
use Zoomov\FilmCast;
use Zoomov\FilmCastCredit;
use Zoomov\FilmFestival;
use Zoomov\FilmakerContact;
use Zoomov\Filmaker;
use Zoomov\Helpers\Uploader;
use Zoomov\Language;
use Zoomov\Country;
use Zoomov\Genre;

class MovieController extends ArchiveController
{
    protected $type = 'movie';
    protected function step($film, $step){
        $film->status = str_pad(decbin($film->completed), config('constants.film.movie_step'), '0');
        return $this->showStep($film, $step);
    }

    protected function showStep($film, $step)
    {
        $film->type = $this->type;
        $movie = DB::table('movies')->where('film_id', $film->id)->first();
        if(!$movie){
            return redirect('/archives');
        }
        switch ($step){
            case 1:
                $languages = Language::selectRaw("id, rank, name as original, name_" . app()->getLocale() . " as name, false as chosen")
                    ->orderBy('rank')
                    ->get();

                $titles = DB::table('film_titles')->join('languages', 'language_id', 'languages.id')
                    ->selectRaw('title, name_' . app()->getLocale() . ' as language, language_id')
                    ->where('film_id', $film->id)
                    ->get();
                return view('film.title', ['languages' => $languages, 'film'=>$film, 'titles'=>$titles, 'step'=>$step]);
            case 2:
                $year = date("Y");
                return view('film.'.$this->type.'.duration', ['year'=>$year, 'film'=>$film, 'step'=>$step]);
            case 3:
                $countries = Country::selectRaw("id,  name_" . app()->getLocale() . " as name")->orderBy('rank')
                    ->orderByRaw('convert(name_' . app()->getLocale() . ' using gbk) ASC')
                    ->pluck('name','id');

                $languages = Language::selectRaw("id, rank, name as original, name_" . app()->getLocale() . " as name, false as chosen")
                    ->orderBy('rank')
                    ->pluck('name','id');

                $dialogs = DB::table('film_languages')->join('languages', 'language_id', '=', 'languages.id')
                    ->where('film_id', $film->id)
                    ->get(['name_' . app()->getLocale().' as name', 'language_id as id']);

                $productions = DB::table('film_productions')->join('countries', 'country_id', '=', 'countries.id')
                    ->where('film_id', $film->id)
                    ->get(['name_' . app()->getLocale().' as name', 'country_id as id']);

                $shootings = DB::table('film_shootings')->join('countries', 'country_id', '=', 'countries.id')
                    ->where('film_id', $film->id)
                    ->get(['name_' . app()->getLocale().' as name', 'country_id as id']);

                return view('film.'.$this->type.'.language', ['countries'=>$countries, 'languages'=>$languages,
                    'productions'=>$productions, 'dialogs'=>$dialogs, 'shootings'=>$shootings, 'film'=>$film, 'step'=>$step]);
            case 4:
                $vformats = DB::table('cameras')
                    ->leftJoin(DB::raw("(select camera_id, id, film_id from film_cameras where film_id = '".$film->id."') a"), function ($join) {
                        $join->on('cameras.id', '=', 'a.camera_id');
                    })
                    ->selectRaw('cameras.id, label_'.app()->getLocale().' as label, IFNULL(a.id, 0) as chosen, film_id')
                    ->orderBy('mark')
                    ->orderByRaw('convert(label_'.app()->getLocale().' using gbk) ASC')
                    ->get();
                $fformats = DB::table('format_cines')
                    ->leftJoin(DB::raw("(select format_cine_id, id, film_id from film_format_cines where film_id = '".$film->id."') a"), function ($join) {
                        $join->on('format_cines.id', '=', 'a.format_cine_id');
                    })
                    ->selectRaw('format_cines.id, label, IFNULL(a.id, 0) as chosen')
                    ->orderBy('id')->get();
                $animations = DB::table('animations')
                    ->leftJoin(DB::raw("(select animation_id, id, film_id from film_animations where film_id = '".$film->id."') a"), function ($join) {
                        $join->on('animations.id', '=', 'a.animation_id');
                    })
                    ->selectRaw('animations.id, label_'.App::getLocale().' as label, IFNULL(a.id, 0) as chosen')
                    ->orderByRaw('convert(label_'.App::getLocale().' using gbk) ASC')
                    ->get();
                $softwares = DB::table('film_softwares')->where('film_id', $film->id)->orderBy('order')->pluck('name');
                return view('film.'.$this->type.'.shooting', ['vformats'=>$vformats, 'fformats'=>$fformats, 'animations'=>$animations, 'softwares'=>$softwares, 'film'=>$film, 'step'=>$step]);
            case 5:
                $pscreens = DB::table('screen_format_digitals')
                    ->where('film_id', $film->id)
                    ->join('format_digitals', 'format_digital_id', '=', 'format_digitals.id')
                    ->select('screen_format_digitals.id', 'format_digital_id', 'ratio', 'resolution_x', 'resolution_y', 'english_dubbed','english_subbed', 'size', 'label')
                    ->get()
                    ->map(function ($item){
                        $item->subtitles = DB::table("screen_subtitles")->where(['screen_id'=>$item->id, 'screen_type'=>'d'])
                            ->join("languages", "language_id", "=", "languages.id")
                            ->selectRaw("language_id, languages.name_".app()->getLocale()." as name, subbed, dubbed")
                            ->get();

                        return $item;
                    });
                $vscreens = DB::table('screen_format_videos')
                    ->where('film_id', $film->id)
                    ->join('sounds', 'sounds.id', '=', 'sound_id')
                    ->join('format_videos', 'format_video_id', '=', 'format_videos.id')
                    ->select('screen_format_videos.id', 'format_video_id', 'sound_id', 'ratio', 'standard',  'english_dubbed',
                        'english_subbed', 'sounds.label_'.app()->getLocale().' as sound', 'format_videos.label_'.app()->getLocale().' as label')
                    ->get()
                    ->map(function ($item){
                        $item->subtitles = DB::table("screen_subtitles")->where(['screen_id'=>$item->id, 'screen_type'=>'v'])
                            ->join("languages", "language_id", "=", "languages.id")
                            ->selectRaw("language_id, languages.name_".app()->getLocale()." as name, subbed, dubbed")
                            ->get();

                        return $item;
                    });

                $cscreens = DB::table('screen_format_cines')
                    ->where('film_id', $film->id)
                    ->join('sounds', 'sounds.id', '=', 'sound_id')
                    ->join('format_cines', 'format_cine_id', '=', 'format_cines.id')
                    ->select('screen_format_cines.id', 'format_cine_id', 'sound_id', 'ratio', 'speed',  'english_dubbed', 'english_subbed', 'reel_count', 'reel_length',
                        'sounds.label_'.app()->getLocale().' as sound', 'format_cines.label')
                    ->orderBy('format_cines.label')
                    ->get()
                    ->map(function ($item){
                        $item->subtitles = DB::table("screen_subtitles")->where(['screen_id'=>$item->id, 'screen_type'=>'c'])
                            ->join("languages", "language_id", "=", "languages.id")
                            ->selectRaw("language_id, languages.name_".app()->getLocale()." as name, subbed, dubbed")
                            ->get();

                        return $item;
                    });
                $pformats = DB::table('format_digitals')->orderBy('label')->get();
                $vformats = DB::table('format_videos')
                    ->orderByRaw('convert(label_'.app()->getLocale().' using gbk) ASC')
                    ->get(['id','label_'.app()->getLocale().' as label']);
                $cformats = DB::table('format_cines')->where('screen', 1)->orderBy('label')->get();
                $sounds = DB::table('sounds')
                    ->orderBy('order')
                    ->get(['id', 'label_'.app()->getLocale().' as label', 'digital']);
                $languages = Language::selectRaw("id, rank, name as original, name_" . app()->getLocale() . " as name, false as chosen")
                    ->orderBy('rank')
                    ->get();

                return view('film.'.$this->type.'.screen', ['pformats'=>$pformats, 'vformats'=>$vformats, 'cformats'=>$cformats, 'sounds'=>$sounds, 'languages'=>$languages,
                    'pscreens' => $pscreens, 'vscreens' => $vscreens, 'cscreens'=>$cscreens, 'film'=>$film, 'step'=>$step]);
            case 6:
                $styles = DB::table('styles')
                    ->leftJoin(DB::raw("(select id, style_id from film_styles where film_id = '".$film->id."') a"), function ($join) {
                        $join->on('styles.id', '=', 'a.style_id');
                    })
                    ->selectRaw('styles.id, name_'.app()->getLocale().' as name, IFNULL(a.id,0) as chosen')
                    ->get();
                $subjects = DB::table('subjects')
                    ->leftJoin(DB::raw("(select id, subject_id from film_subjects where film_id = '".$film->id."') a"), function ($join) {
                        $join->on('subjects.id', '=', 'a.subject_id');
                    })
                    ->selectRaw('subjects.id, name_'.app()->getLocale().' as name, IFNULL(a.id,0) as chosen')
                    ->orderBy('id')
                    ->get();
                $genres = Genre::where('film', '>', 0)
                    ->leftJoin(DB::raw("(select id, genre_id from film_genres where film_id = '".$film->id."') a"), function ($join) {
                        $join->on('genres.id', '=', 'a.genre_id');
                    })
                    ->selectRaw('genres.id, name_'.app()->getLocale().' as name, IFNULL(a.id,0) as chosen')
                    ->orderBy('sequence')
                    ->get();
                return view('film.genre', ['genres'=>$genres, 'styles'=>$styles, 'subjects'=>$subjects, 'film'=>$film, 'step'=>$step]);
            case 7:
                $lang = $this->getLanguage();

                $synopsis = DB::table('film_synopsis')
                    ->where('film_id', $film->id)
                    ->where('language_id', $lang->id)
                    ->first(['content']);

                $list = DB::table('film_synopsis')
                    ->where('film_id', $film->id)
                    ->where('language_id', '<>', $lang->id)
                    ->join('languages', 'language_id', '=', 'languages.id')
                    ->get(['film_synopsis.id', 'content', 'language_id', 'languages.name_'.app()->getLocale().' as language']);

                $languages = Language::where('id', '<>', $lang->id)
                    ->selectRaw("id, rank, name_" . app()->getLocale() . " as name")
                    ->orderBy('rank')
                    ->pluck('name', 'id');

                return view('film.synopsis', ['lang'=>$lang, 'languages'=>$languages,'list'=>$list, 'synopsis'=>$synopsis, 'film'=>$film, 'step'=>$step]);
            case 8:
                $year = date("Y");

                $countries = Country::where('region', '<>', 1)->selectRaw("id,  name_" . app()->getLocale() . " as name, sortname")->orderBy('rank')
                    ->orderByRaw('convert(name_' . app()->getLocale() .' using gbk) ASC')
                    ->get();

                return view('film.'.$this->type.'.director', [ 'year'=>$year, 'countries'=>$countries, 'film'=>$film, 'step'=>$step]);
            case 9:
                $year = date("Y");
                $credits = Credit::orderBy('id')
                    ->get(['id', 'label_'.app()->getLocale().' as label'])
                    ->map(function($item) use ($film){
                        $item->makers = FilmCastCredit::where('credit_id', $item->id)
                            ->join('film_casts', 'film_cast_id', 'film_casts.id')
                            ->join('filmakers', 'filmaker_id', '=', 'filmakers.id')
                            ->leftJoin('countries', 'country_id', '=', 'countries.id')
                            ->leftJoin('users', 'related_id', '=', 'users.id')
                            ->selectRaw('film_cast_id, credit_id, film_casts.id, filmaker_id, first_name, last_name, tel, mobile, born, country_id, prefix, filmakers.email, countries.name_'.app()->getLocale().' as country, related_id, username')
                            ->where('film_casts.film_id', $film->id)
                            ->get();

                        return $item;
                    });
                $countries = Country::where('region', '<>', 1)->selectRaw("id,  name_" . app()->getLocale() . " as name, sortname")->orderBy('rank')
                    ->orderByRaw('convert(name_' . app()->getLocale() .' using gbk) ASC')
                    ->get();

                return view('film.'.$this->type.'.credit', ['year'=>$year, 'countries'=>$countries, 'movie'=>$movie, 'credits'=>$credits, 'film'=>$film, 'step'=>$step]);
            case 10:
                $year = date("Y");

                $countries = Country::selectRaw("id,  name_" . app()->getLocale() . " as name, sortname")->orderBy('rank')
                    ->orderByRaw('convert(name_' . app()->getLocale() .' using gbk) ASC')
                    ->get();
                return view('film.producer', ['year'=>$year, 'countries'=>$countries, 'film'=>$film, 'step'=>$step]);
            case 11:
                $year = date("Y");

                $countries = Country::selectRaw("id, region,  name_" . app()->getLocale() . " as name, sortname")->orderBy('rank')
                    ->orderByRaw('convert(name_'. app()->getLocale() .' using gbk) ASC')
                    ->get();

                $festivals  = FilmFestival::where('film_id', $film->id)
                    ->join('cities', 'city_id', '=', 'cities.id')
                    ->join('departments', 'department_id', '=', 'departments.id')
                    ->join('countries', 'departments.country_id', '=', 'countries.id')
                    ->selectRaw('film_festivals.id, city_id, department_id, departments.country_id, event, year, countries.name_'.app()->getLocale().' as country, cities.name_'.app()->getLocale().' as city')
                    ->get()
                    ->map(function($item){
                        $item->rewards = DB::table('film_festival_rewards')->where('film_festival_id', $item->id)
                            ->get(['id', 'name', 'competition']);

                        return $item;
                    });

                $diffusion  = DB::table('film_diffusion')->where('film_id', $film->id)
                    ->leftJoin('countries', 'film_diffusion.country_id', '=', 'countries.id')
                    ->selectRaw('film_diffusion.id, country_id, film_diffusion.name, year, channel, countries.name_'.App::getLocale().' as country')
                    ->get();
                $theaters  = DB::table('film_theaters')->where('film_id', $film->id)
                    ->leftJoin('countries', 'film_theaters.country_id', '=', 'countries.id')
                    ->selectRaw('film_theaters.id, country_id, film_theaters.title, year, program, contact, distribution, countries.name_'.App::getLocale().' as country')
                    ->get();

                $film->music_rights = $movie->music_rights;
                return view('film.'.$this->type.'.seller', ['year'=>$year, 'countries'=>$countries, 'film'=>$film, 'festivals'=>$festivals ,'diffusion'=>$diffusion, 'theaters'=>$theaters, 'step'=>$step]);
            case 12:
                $folderName = 'film/'.$film->id. '/pictures';
                $completed = $film->completed;
                if(Storage::disk('public')->exists($folderName)) {
                    $files = Storage::disk('public')->files($folderName);
                    $this->setStatus(sizeof($files) < 1, $step, $completed, $this->type);
                }
                else{
                    $this->setStatus(true, $step, $completed, $this->type);
                }
                if($completed != $film->completed){
                    DB::table('films')->where('id', $film->id)->update(['completed' => $completed]);
                    $film->completed = $completed;
                }

                return view('film.'.$this->type.'.media', ['film'=>$film, 'step'=>$step]);
            default:
                return $this->showUpload($film, 'movie');
        }
    }

    public function store(Request $request){
        $id = $request['id'];
        $film = Film::find($id);
        if(!$film || $film->user_id != auth()->id()){
            return redirect('/archives');
        }

        if(!$request->has('step')){
            return $this->showDetail($film,'movie');
        }
        $values = [];
        $invalid = false;
        $step = $request['step'];
        switch ($step){
            case 2:
                $request->validate([
                    'title' => 'required|max:80',
                    'title_latin' => 'max:80',
                    'title_inter' => 'max:80'
                ]);
                if($request->input('language_id', null) && $request->input('title_trans', null)){
                    DB::table('film_titles')->insert([
                        'film_id' => $film->id,
                        'language_id' => $request->input('language_id'),
                        'title' => $request->input('title_trans'),
                        'created_at' => date('Y-m-d h:i:s')
                    ]);
                    //  $this->updateTitle($film, $request['titles']);
                }
                $values = $request->only('title','title_latin','title_inter');
                break;
            case 3:
                foreach(['year', 'month', 'hour','second', 'minute'] as $key){
                    $values[$key] = $request->input($key, null);
                    $invalid |= !is_numeric($values[$key]);
                }
                $values['day']  = $request->input('day', null);
                if(!$values['day']){
                    $values['day'] = 1;
                }
                break;
            case 4:
               if($film->completed < 4 || decbin($film->completed)[2] < 1 || is_null($film->country_id)){
                   $values['country_id'] = $request->input('country_id', null);
                   $invalid = is_null($values['country_id']);
               }
               else{
                   $invalid = is_null($film->country_id);
               }
                foreach(['silent', 'mute'] as $key){
                    $values[$key] = $request->input($key, null);
                    $invalid |= is_null($values[$key]);
                }

                if($values['mute']){
                    DB::table('film_languages')->where('film_id', $film->id)->delete();
                    $values['conlange'] = null;
                }
                break;
            case 5:
                $this->updateShooting($film, $request);
                break;
            case 6:
                foreach(['color', 'special'] as $key){
                    $values[$key] = $request->input($key, null);
                    $invalid |= is_null($values[$key]);
                }
                break;
            case 7:
                if($request->has('genre')){
                    $this->setParameters(array_unique($request['genre']), 'film_genres', $film->id, 'genre_id');
                }
                else{
                    $invalid = true;
                }

                if($request->has('style')){
                    $this->setParameters(array_unique($request['style']), 'film_styles', $film->id, 'style_id');
                }
                if($request->has('subject')){
                    $this->setParameters(array_unique($request['subject']), 'film_subjects', $film->id, 'subject_id');
                }
                break;
            case 8:
                $lang = $this->getLanguage();
                if(!DB::table('film_synopsis')->where(['film_id' => $film->id, 'language_id'=>$lang->id])->first()){
                    $invalid = true;
                }
                break;
            case 9:
                $values['virgin'] = $request->input('virgin', null);
                $invalid = !DB::table('film_directors')->where('film_id', $film->id)->exists();
                break;
            case 10:
                foreach (['music_original', 'screenplay_original'] as $key){
                    $invalid |= !$request->has($key);
                }

             //   $invalid |= !DB::table('film_casts')->where('film_id', $film->id)->exists();
                DB::table('movies')->where('film_id', $film->id)->update([
                    'music_original' => $request->input('music_original', null),
                    'screenplay_original' => $request->input('screenplay_original', null)
                ]);
                break;
            case 11:
                foreach(['school', 'school_name'] as $key){
                    $values[$key] = $request->input($key, null);
                }

                $invalid = is_null($values['school']) || ($values['school'] == 1 && is_null($values['school_name']));
                $invalid |= !DB::table('film_producers')->where('film_id', $film->id)->exists();
                break;

            case 12:
                if($request->has('music_rights')){
                    DB::table("movies")->where('film_id', $film->id)->update($request->only('music_rights'));
                }
                else{
                    $invalid = true;
                }

                foreach (['inter_rights', 'festivals', 'diffusion', 'theaters'] as $key){
                    if($request->has($key)){
                        $values[$key] = $request[$key]?1:0;
                    }
                    else{
                        $values[$key] = null;
                    }
                }

                $invalid |= is_null($values['inter_rights']) || !DB::table('film_sellers')->where('film_id', $film->id)->exists();
                $values['festivals'] = $request->has('festivals');
                if($values['festivals']){
                    $values['festivals'] = FilmFestival::where('film_id', $film->id)->exists();
                }
                else{
                    DB::table('film_festivals')->where('film_id', $film->id)->delete();
                }

                $values['diffusion'] = $request->has('diffusion');
                if($values['diffusion']){
                    $values['diffusion'] = DB::table('film_diffusion')->where('film_id', $film->id)->exists();
                }
                else{
                    DB::table('film_diffusion')->where('film_id', $film->id)->delete();
                }

                $values['theaters'] = $request->has('theaters');

                if($values['diffusion']){
                    $values['theaters'] = DB::table('film_theaters')->where('film_id', $film->id)->exists();
                }
                else{
                    DB::table('film_theaters')->where('film_id', $film->id)->delete();
                }
                break;
            case 13:
                $size = 0;
                $folderName = 'film/' . $film->id . '/pictures';
                if (Storage::disk('public')->exists($folderName)) {
                    $size = sizeof(Storage::disk('public')->files($folderName));
                }
                $invalid = $size < 1;
                break;
            default:
                return $step;
        }

        $this->updateStatus($film, $invalid, $step - 2, $values, 'movie');

        return $this->step($film, $step);
    }

    public function complete($id, Request $request){
        $valid = $request->has('final') && $request->has('fullvision');
        $film = Film::find($id);
        DB::table('movies')->where('film_id', $id)
            ->update([
                'final' => $request->input('final', null),
                'fullvision' => $request->input('fullvision', null),
                'subtitle_zh' => $request->has('subtitle_zh')?1:0,
                'subtitle_en' => $request->has('subtitle_en')?1:0,
                'subtitle_other' => $request->input('subtitle_other', null),
            ]);

        $url = $request->input('url', null);
        $link = DB::table('film_copies')->where('film_id', $id)->first();
        if($url){
            if(!str_start($url, 'http')){
                $url = 'https://'.$url;
            }
            if($link){
                DB::table('film_copies')->where('film_id', $id)->update([
                    'url' => $url,
                    'name' => $request->input('name', null),
                    'code' => $request->input('code', null),
                ]);
            }
            else{
                $link =  [
                    'film_id' => $id,
                    'url' => $url,
                    'name' => $request->input('name', null),
                    'code' => $request->input('code', null),
                ];
                DB::table('film_copies')->insert($link);
            }
        }

        if($valid && (sizeof(Storage::disk('public')->files('film/'.$id.'/preview'))>0 || $link)){
            $film->update(['uploaded'=>1]);
            return redirect('archives');
        }
        else{
            return redirect('movies/'.$id);
        }
    }

    public function saveShootings($id, Request$request){
        if($request->has('country_id')){
            $country_id = $request->input('country_id');
            $count = DB::table('film_shootings')->where('film_id', $id);
            if(sizeof($count) < 9){
                if($count->where('country_id', $country_id)->count()){
                    return ['shooting'=> trans('film.errors.shooting_existing')];
                }
                return DB::table('film_shootings')->insertGetId(['film_id'=>$id, 'country_id'=>$country_id, 'order'=>sizeof($count) + 1]);
            }

            return ['shooting'=> trans('film.errors.production_maxlength')];
        }

        return null;
    }

    public function removeShooting($id, $country_id){
        DB::table('film_shootings')->where(['film_id'=>$id, 'country_id'=>$country_id])->delete();
    }

    public function createCredits(Request $request){
        $this->validate($request, ['film_id'=>'required', 'credits'=>'required', 'maker'=>'required']);

        $maker = Filmaker::create(array_merge(array_except($request['maker'], 'contact'), ['id'=>$this->uuid('m'), 'user_id'=>auth()->id()]));
        $result = [];
        $cast = FilmCast::create(['film_id'=>$request['film_id'], 'filmaker_id'=>$maker->id]);
        foreach ($request['credits'] as $key){
            $credit = FilmCastCredit::create(['film_cast_id' => $cast->id, 'credit_id' => $key]);
            array_push($result, ['credit_id'=>$key, 'cast_id'=>$cast->id, 'film_credit_id'=>$credit->id]);
        }
        return ['maker_id'=>$maker->id, 'credits'=>$result];
    }

    public function saveScreen($id, $format, Request $request){

        $values = $request->except( 'subtitles', 'id', 'sound', 'label', 'newlang');

        $validator = [
            'format_'.$format.'_id' => 'required',
            'ratio' => 'required'
        ];

        $type = substr($format,0,1);

       if($type == 'c'){
            array_merge($validator,['sound_id' => 'required',
                'speed' => 'required']);
        }
        else{
            array_merge($validator,['sound_id' => 'required']);
        }

        $this->validate($request, $validator);

        $values = array_add($values, 'film_id', $id);

        $values['english_dubbed'] = $values['english_dubbed'] ? 1 :0;
        $values['english_subbed'] = $values['english_subbed'] ? 1 :0;

        //  $screen = DB::table('screen_format_'.$format.'s')->where($values)->first();
        $screen_id = $request->input('id', null);

        if(!$screen_id){
            $screen = DB::table('screen_format_'.$format.'s')->where($values)->first();

            if($screen){
                $screen_id = $screen->id;
                DB::table('screen_format_'.$format.'s')->where('id', $screen_id)->update($values);
            }
            else{
                $screen_id = DB::table('screen_format_'.$format.'s')->insertGetId($values);
            }
        }
        else{
            DB::table('screen_format_'.$format.'s')->where('id', $screen_id)->update($values);
        }

        $existing = [];

        foreach ($request->input('subtitles', []) as $subtitle){
            $dubbed = array_key_exists('dubbed', $subtitle) ? ($subtitle['dubbed'] ? 1 : 0) : 0;
            $subbed = array_key_exists('subbed', $subtitle) ? ($subtitle['subbed'] ? 1 : 0) : 0;
            if(!array_key_exists('id', $subtitle)){
                $id = DB::table('screen_subtitles')->insertGetId([
                    'screen_id'=> $screen_id,
                    'screen_type' => substr($format,0,1),
                    'language_id' => $subtitle['language_id'],
                    'dubbed' => $dubbed,
                    'subbed' => $subbed
                ]);

                array_push($existing, $id);
            }
            elseif($subtitle['dubbed'] || $subtitle['subbed']){
                DB::table('screen_subtitles')->where('id', $subtitle['id'])
                    ->update(['language_id'=>$subtitle['language_id'], 'subbed'=>$subbed, 'dubbed'=>$dubbed]);
                array_push($existing, $subtitle['id']);
            }
        }

        if($existing){
            DB::table('screen_subtitles')->where(['screen_id' => $screen_id, 'screen_type' => $type])->whereNotIn('id', $existing)->delete();
        }
        else{
            DB::table('screen_subtitles')->where(['screen_id' => $screen_id, 'screen_type' => $type])->delete();
        }

        return $screen_id;
    }

    public function removeScreen($id, $format){
        DB::table('screen_format_'.$format.'s')->delete(['id'=>$id]);
        DB::table('screen_subtitles')->where('screen_id',$id)->where('screen_type', substr($format, 0, 1))->delete();
        return $id;
    }

    public function saveCredits($id, Request$request){
        $this->validate($request, ['credits'=>'required', 'makers'=>'required']);

        $result = [];
        foreach ($request['makers'] as $maker){
            $cast = FilmCast::where(['film_id'=>$id, 'filmaker_id'=>$maker['id']])->first();
            if(!$cast){
                $cast = FilmCast::create(['film_id'=>$id, 'filmaker_id'=>$maker['id']]);
                foreach ($request['credits'] as $key){
                    $credit = FilmCastCredit::create(['film_cast_id' => $cast->id, 'credit_id' => $key]);
                    if (!array_key_exists($key, $result)) {
                        $result = array_add($result, $key, [['maker_id' => $maker['id'], 'cast_id' => $cast->id, 'id' => $credit->id]]);
                    } else {
                        array_push($result[$key], ['maker_id' => $maker['id'], 'cast_id' => $cast->id, 'id' => $credit->id]);
                    }
                }
            }
            else{
                foreach ($request['credits'] as $key){
                    $credit = FilmCastCredit::where(['film_cast_id' => $cast->id, 'credit_id' => $key])->first();
                    if(!$credit){
                        $credit = FilmCastCredit::create(['film_cast_id' => $cast->id, 'credit_id' => $key]);
                        if(!array_key_exists($key, $result)){
                            $result = array_add($result, $key, [['maker_id'=>$maker['id'], 'cast_id'=>$cast->id, 'id'=>$credit->id]]);
                        }
                        else{
                            array_push($result[$key], ['maker_id'=>$maker['id'], 'cast_id'=>$cast->id, 'id'=>$credit->id]);
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function deleteCredits($id, Request $request){
        $cast = FilmCast::where(['film_id'=>$id, 'filmaker_id'=>$request['maker_id']])->first();
        if($cast){
            $credit = FilmCastCredit::where(['film_cast_id'=>$cast->id, 'credit_id'=>$request['credit_id']])->first();

            if($credit){
                $credit->delete();

                if(!FilmCastCredit::where(['film_cast_id'=>$cast->id])->exists()){
                    $cast->delete();
                }

                return $credit->id;
            }

            return 0;
        }

        return -1;
    }

    public function savePreview($id, Request $request){
        $time_start = microtime(true);

        $blobNum =  $request['blob_num'];
        $totalNum = $request['total_blob_num'];
        $filename = 'uploading'.$request['ext'];
        $file = $request->file('preview');
        $size = $file->getClientSize();
        $ext = $request['ext'];
        $content = file_get_contents($file);
        if($blobNum == 1) {
            $folderName = 'film/'.$id.'/preview';
            if (Storage::disk('public')->exists($folderName)) {
                foreach(Storage::disk('public')->files($folderName) as $file){
                    if(!str_start(basename($file),'film')){
                        Storage::disk('public')->delete($file);
                    }
                }
            }

            Storage::disk('public')->put($folderName.'/'.'preview'.$request['ext'], $content);
            /* if($totalNum == 1){
                 $folderName = 'film/'.$id.'/preview';
                 foreach(Storage::disk('public')->files($folderName) as $file){
                     Storage::disk('public')->delete($file);
                 }
                 $request->file('preview')->storeAs('/public/'.$folderName, 'preview'.$request['ext']);
                 $request->file('preview')->storeAs('/public/'.$folderName, $filename);
                 return ['result'=>$folderName.'/'.$filename,'completed'=>1];
             }
             else{
                 $folderName = $folderName . '/temp';
                 if (Storage::disk('public')->exists($folderName)) {
                     foreach ( Storage::disk('public')->files($folderName) as $old){
                         Storage::disk('public')->delete($old);
                     }
                 }
                 else{
                     Storage::makeDirectory($folderName);
                 }
             }*/
        }
        else{
            $folderName = 'film/' . $id . '/preview';
        }


        if ($fp = fopen ( storage_path('app/public/'.$folderName.'/'.$filename), 'ab' )) {
            $startTime = microtime ();
            do {
                $canWrite = flock ( $fp, LOCK_EX );
                if (! $canWrite)
                    usleep ( round ( rand ( 0, 100 ) * 1000 ) );
            } while ( (! $canWrite) && ((microtime () - $startTime) < 1000) );
            if ($canWrite) {
                fwrite ( $fp, $content, $size);
            }
            fclose ( $fp );
        }

        if($blobNum ==$totalNum){
            /*  $folderName = 'film/'.$id.'/preview';

              foreach(Storage::disk('public')->files($folderName) as $file){
                  Storage::disk('public')->delete($file);
              }

              Storage::disk('public')->copy( 'film/' . $id . '/temp/'.$filename.'__1', $folderName.'/preview'.$request['ext']);

              $fp = fopen(storage_path('app/public/'.$folderName.'/'.$filename), "ab");

              foreach(Storage::disk('public')->files('film/' . $id . '/temp') as $file){
                  $handle = fopen(storage_path('app/public/'.$file),"rb");
                  fwrite($fp, Storage::disk('public')->get($file));
                  fclose($handle);
                  unset($handle);
                  Storage::disk('public')->delete($file);
              }

              fclose($fp);*/
          //  rename(storage_path('app/public/'.$folderName.'/'.$filename), storage_path('app/public/'.$folderName.'/'.uuid().$ext));
            if(Storage::disk('public')->exists($folderName.'/film'.$ext)){
                Storage::disk('public')->delete($folderName.'/film'.$ext);
            }
            Storage::disk('public')->move($folderName.'/'.$filename, $folderName.'/film'.$ext);
            $size = $this->countSize(Storage::disk('public')->size($folderName.'/film'.$ext));
            return ['message'=>trans('film.progress.copy_uploaded', ['cnt'=>1, 'size'=>$size, 'ext'=>$ext]), 'completed'=>1];
        }

        return ['result'=>microtime(true) - $time_start, 'completed'=>0];
    }

    public function saveDiffusion($id, Request $request)
    {
        $validator = [
            'channel' => 'required',
            'name' => 'required',
        ];

        $this->validate($request, $validator);

        if($request->has('id') && $request['id']){
            DB::table('film_diffusion')->where('id', $request['id'])
                ->update(['year' => $request['year'],
                    'name' => $request['name'],
                    'country_id' => $request['country_id'],
                    'channel' => $request['channel']]);
        }
        else{
            $id = DB::table('film_diffusion')->insertGetId( ['film_id'=>$id,
                'year' => $request['year'],
                'name' => $request['name'],
                'country_id' => $request['country_id'],
                'channel' => $request['channel'],
                'created_at' => gmdate("Y-m-d H:i:s", time())
            ]);

            return ['id'=>$id];
        }
    }

    public function removeDiffusion($id, $diffusion_id){
        DB::table('film_diffusion')->where('id', $diffusion_id)->delete();
    }

    protected function showUpload($film, $type){
        $film->type = $type;
        $file = null;
        $size = '';
        $name = '';
        $ext = '';
        $movie = DB::table('movies')->where('film_id', $film->id)->first();
        if(Storage::disk('public')->exists("film/".$film->id."/preview")){
            $files = Storage::disk('public')->files("film/".$film->id."/preview");

            foreach ($files as $f){
                if(str_start(basename($f),'film')){
                    $file = $f;

                    $size = $this->countSize(Storage::disk('public')->size($file));


                    $name = basename($file);
                    $ext = substr($name, strrpos($name,'.')+1);
                    break;
                }
            }
        }

        $link = DB::table('film_copies')->where('film_id', $film->id)->select('url', 'name', 'code')->first();
        $languages = Language::selectRaw("id, rank, name as original, name_" . app()->getLocale() . " as name, false as chosen")
            ->orderBy('rank')
            ->get();

        $file = null;
        return view('film.'.$type.'.upload', ['step'=>0, 'languages'=>$languages, 'film'=>$film, 'movie'=>$movie, 'file'=>$file, 'size'=>$size, 'name'=>$name, 'ext'=>$ext, 'link'=>$link]);
    }

    private function countSize($size){
        if($size > 1024){
            $result = $size/1024;
            if($result > 1024){
                $result = $result/1024;
                if($result > 1024){
                    return round($result/1024, 2).'G';
                }
                else{
                    return round($result, 2).'M';
                }
            }
            else{
                return  round($result, 2).'KB';
            }
        }
        else{
            return $size .'B';
        }
    }

    private function updateShooting($film, Request $request)
    {
        foreach (['camera', 'format_cine', 'animation'] as $table) {
            if($request->has($table)){
                $this->setFormat($request[$table], $table, $film->id);
            }
        }

        if($request->has('software')){
            $this->setParameters(array_unique(array_filter($request['software'])), 'film_softwares', $film->id, $col='name');
        }
    }

    private function setFormat($formats, $table, $film)
    {
        $old = DB::table('film_'.$table.'s')->where('film_id', $film)->pluck($table.'_id')->toArray();

        $toRemove = is_null($formats) ? $old : array_diff($old, $formats);

        DB::table('film_'.$table.'s')->where('film_id', $film)->whereIn($table.'_id', $toRemove)->delete();

        $toAdd = is_null($formats) ? [] : array_diff($formats, $old);

        foreach ($toAdd as $v) {
            DB::table('film_' . $table . 's')->insert([
                'film_id' => $film,
                $table . '_id' => $v
            ]);
        }
    }
}
