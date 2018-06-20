<?php

namespace Zoomov\Http\Controllers;
use App;
use Auth;
use Config;
use DB;
use Illuminate\Support\Facades\Lang;
use Storage;
use Illuminate\Http\Request;
use Zoomov\City;
use Zoomov\Country;
use Zoomov\Festival;
use Zoomov\FestivalEntry;
use Zoomov\FestivalEntryHonor;
use Zoomov\FestivalInscriptionHonor;
use Zoomov\FestivalUnit;
use Zoomov\FestivalYear;
use Zoomov\Film;
use Zoomov\Filmaker;
use Zoomov\Language;
use Zoomov\User;
class FestivalController extends Controller
{
    public function index(Request $request)
    {
        if(!auth()->check()){
            session(['imherefor' => 'festivals']);
        }

        return view('festival.index', ['festivals'=>$this->display($request), 'information'=>$this->getInformation(), 'film_id'=>$request->input('film_id', null)]);
    }

    public function show($id, Request $request)
    {
        $festival = Festival::where('short', $id)
            ->select('id', 'web','email','name', 'name_'.app()->getLocale().' as name_locale', 'presentation_'.app()->getLocale().' as presentation','created_year', 'company', 'city_fixed',
                'city_id', 'short', 'address')
            ->first();

        $year = $this->getYear(['festival_id'=>$festival->id]);
        $this->getFestival($festival, $year);

        if($request->has('unit')){
            return $this->unit($festival, $year, $request);

        }
        else{
            $rules = null;
            $units = DB::table("festival_units")
                ->where("festival_year_id", $year->id)
                ->orderBy('rank', 'decs')
                ->get(['id', 'same_rule', 'name', 'name_'.app()->getLocale().' as name_locale', 'presentation_'.app()->getLocale().' as presentation', 'competition', 'rank', 'open_at', 'due_at'])
                ->map(function ($item) use(&$rules, $year){
                    if($item->same_rule){
                        if(!$rules){
                            $rules = $this->getRelatedRules($item,$year->id);
                        }

                        $item->rules = $rules;
                    }
                    else{
                        $item->rules = $this->getRelatedRules($item,$item->id);
                   }
                    return $item;
                })
                ->all();
            $films = Film::where('user_id', auth()->id())->groupBy('uploaded')->selectRaw('count(id) as cnt, uploaded')->pluck('cnt', 'uploaded')->all();
            return view('festival.detail', ['year'=>$year, 'festival'=>$festival, 'units'=>$units, 'films'=>$films, 'film_id'=>$request->input('film_id', null)]);
        }
     }

    public function display(Request $request){
        $sessions = $this->getBase($request);

        if(auth()->check()){
            if($request->input('favorite', -1) > 0){
                $sessions = $sessions->join(DB::raw("(select id, festival_id from festival_favorites where user_id = '".auth()->id()."') festival_favorites"), function ($join){
                    $join->on('festival_favorites.festival_id', '=', 'festivals.id');
                });
            }
            else{
                $sessions = $sessions->leftJoin(DB::raw("(select id, festival_id from festival_favorites where user_id = '".auth()->id()."') festival_favorites"), function ($join){
                    $join->on('festival_favorites.festival_id', '=', 'festivals.id');
                });
            }
        }

        return $this->getResult($sessions, $request->input('status', -1));
    }

    private function getBase(Request $request)
    {
        $genre = $request->input('genre', -1);
        $script = $request->input('script', -1);
        $sessions = Festival::with('genres')
            ->when($genre > 0, function ($query) use ($genre) {
                return $query->whereRaw('not exists (select 1 from festival_genres where festival_genres.festival_id = festivals.id) or exists (select 1 from festival_genres where festival_genres.festival_id = festivals.id and genre_id =' . $genre . ')');
            })
            ->when($script > 0, function ($query) use ($script) {
                return $query->where('screenplay', $script);
            })->join(DB::raw("(select id as year, festival_years.festival_id, session, start_at, city_id, datediff(due_at, CURDATE()) as datediff from festival_years) festival_years"), function ($join) {
                $join->on('festival_years.festival_id', '=', 'festivals.id');
            })
            ->join(DB::raw("(select festival_years.festival_id, max(session) as cnt from festival_years group by festival_id) year"), function ($join) {
                $join->on('year.festival_id', '=', 'festival_years.festival_id')
                    ->on('year.cnt', '=', 'festival_years.session');
            })
            ->join(DB::raw("(select festival_year_id, min(due_at) as deadline from festival_units group by festival_year_id) due"), function ($join) {
                $join->on('due.festival_year_id', '=', 'festival_years.year');
            })
            ->join(DB::raw("(select cities.id as city_id, cities.name_" . app()->getLocale() . " as city, countries.name_" . app()->getLocale() . " as country from cities inner join departments on department_id = departments.id inner join countries on country_id = countries.id) cities"), function ($join) {
                $join->on(DB::raw("CASE city_fixed when 1 then festivals.city_id else festival_years.city_id end"), '=', 'cities.city_id');
            });

        return $sessions;
    }

    public function unit($festival, $year, Request $request){
        if(!auth()->check()){
            return redirect('/login');
        }


        $unit = DB::table("festival_units")
            ->select(['id', 'festival_year_id',  'same_rule', 'name', 'name_'.app()->getLocale().' as name_locale', 'presentation_'.app()->getLocale().' as presentation',
                'competition', 'rank',
                'open_at', 'due_at', 'fee', 'currency'])
            ->where(['festival_year_id'=>$year->id, 'name'=>$request['unit']])
            ->first();

        if($unit->same_rule)
        {
            $related_id = $year->id;
        }
        else{
            $related_id = $unit->id;

        }

        $rules = DB::table('festival_rules')->where('related_id', $related_id)->first();
        if($rules && !is_null($rules->script))
        {
            if( $rules->script === 1){
                $films = Film::where('user_id', auth()->id())->where('screenplay', 1)->where('uploaded',1)->orderBy('updated_at','desc')->get();
            }
            elseif($rules->script  === 0){
                $films = Film::where('user_id', auth()->id())->where('screenplay', 0)->where('uploaded',1)->orderBy('updated_at','desc')->get();
            }
        }
        else{
            $films = Film::where('user_id', auth()->id())->where('uploaded',1)->orderBy('updated_at','desc')->get();
        }

        if(!$films){
            return redirect('/archives');
        }

        $user = User::find(auth()->id());
        $contact = $user->getContact();

        if(!$contact){
            $request->session()->flash('contact', trans('film.error.entry_contact'));
        }

        return view('festival.unit', ['year'=>$year, 'festival'=>$festival, 'unit' =>$unit, 'rules'=>$rules, 'films'=>$films,  'related_id'=>$related_id]);
    }

    public function favorites(Request $request){
        $sessions = $this->mine($request);
        return view('festival.favorites', ['festivals'=>$sessions, 'information'=>$this->getInformation()]);
    }

    public function mine(Request $request){
        $sessions = $this->getBase($request);

        $sessions = $sessions->join(DB::raw("(select id, festival_id from festival_favorites where user_id = '".auth()->id()."') festival_favorites"), function ($join){
            $join->on('festival_favorites.festival_id', '=', 'festivals.id');
        });

        return $this->getResult($sessions, $request->input('status', -1));
    }
    public function update($id){
        $favorite = DB::table('festival_favorites')->where('festival_id', $id)->where('user_id', auth()->id())->get();

        if(sizeof($favorite)){
            DB::table('festival_favorites')->where('festival_id', $id)->where('user_id', auth()->id())->delete();
            return 0;
        }
        else{
            return DB::table('festival_favorites')->insertGetId(['festival_id'=>$id, 'user_id'=>auth()->id()]);
        }
    }

    public function store(Request $request){
        $this->validate($request, [
            'film_id'=>'required',
            'unit_id'=>'required',
            'term' => 'required:on'
        ]);

        $entry = FestivalEntry::where(['film_id'=> $request['film_id'], 'festival_unit_id' =>  $request['unit_id']])->first();

        if($entry){
            return redirect('/entries/'.$entry->id);
        }

        $film = Film::find($request['film_id']);
        $unit = FestivalUnit::select('fee', 'currency', 'name_'.app()->getLocale().' as name_locale', 'name', 'id', 'festival_year_id')->find($request['unit_id']);
        $year = $this->getYear(['id'=>$unit->festival_year_id], true);
        $festival = Festival::select('id', 'web','email','name', 'name_'.app()->getLocale().' as name_locale', 'created_year', 'company', 'city_fixed', 'city_id', 'short', 'address')
            ->find($year->festival_id);
        $this->getFestival($festival, $year);
        $rule = DB::table('festival_rules')->where('related_id', $unit->same_rule ? $unit->festival_year_id : $unit->id)->first();
        $result = $this->validFilmRule($film, $rule);

        if(sizeof($result) > 0){
            return redirect('/festivals/'.$festival->short.'?unit='.$unit->name)->withInput($request->only('film_id'));
        }


        $user = User::find(auth()->id());
        $contact = $user->getContact();

        if(!$contact){
            return back()->with('contact', trans('film.errors.entry_contact'));
        }

        $location = City::join('departments', 'department_id','=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->select(['cities.name_'.app()->getLocale().' as city, departments.name_'.app()->getLocale().' as department, countries.name_'.app()->getLocale().' as country'])
            ->find($contact->city_id);
        $contact->fix = is_null($contact->fix_number)?:'+'.$contact->fix_code.' '.$contact->fix_number;
        $contact->mobile = is_null($contact->mobile_number)?:'+'.$contact->mobile_code.' '.$contact->mobile_number;
        $contact->email = $user->email;
        $contact->city = $location->city;
        $contact->department = $location->department;
        $contact->country = $location->country;

       return view('festival.entry', ['film'=>$film, 'unit'=>$unit, 'contact'=>$contact, 'year'=>$year, 'festival'=>$festival, 'units'=>FestivalUnit::where('festival_year_id', $year->id)->count()]);
    }

    public function addValidResult(&$invalid, $step, $result){
        if(!$result){
            return;
        }
        if(array_key_exists($step, $invalid)){
            array_push($invalid[$step], $result);
        }
        else{
            $invalid = array_add($invalid, $step, [$result]);
        }
    }

    public function validFilm($id, Request $request)
    {
        $rule = DB::table('festival_rules')->where('related_id', $id)->first();

        if (is_null($rule)) {
            return [];
        }

        if($request->has('film_id')){
            $film = Film::find($request['film_id']);
        }
        else{
            $film = Film::where('user_id', auth()->id())[0];
        }

        return $this->validFilmRule($film, $rule);
    }
    public function getYear($params, $strict=false){
        if($strict){
            return FestivalYear::where($params)
                ->selectRaw('id,festival_id, session, presentation_'.app()->getLocale().' as presentation, city_id, start_at, end_at, due_at, datediff(due_at, CURDATE()) as datediff')
                ->first();
        }
        else{
            return FestivalYear::where($params)
                ->selectRaw('id,festival_id, session, presentation_'.app()->getLocale().' as presentation, city_id, start_at, end_at, due_at, datediff(due_at, CURDATE()) as datediff')
                ->orderBy('session', 'desc')
                ->first();
        }
    }

    public function getFestival(&$festival, $year){
        if(auth()->check()){
            $favorite = DB::table('festival_favorites')->where(['festival_id'=>$festival->id, 'user_id'=>auth()->id()])->first();
            $festival->favorite = $favorite ? $favorite->id : 0;
        }
        else{
            $festival->favorite = 0;
        }

        $festival->sns = DB::table('sns_festivals')->where('festival_id', $festival->id)->pluck('link', 'sns_id')->all();

        $festival->phones = DB::table('festival_phones')->where('festival_id', $festival->id)
            ->join('phones', 'phone_id', '=', 'phones.id')
            ->get(['type','code','number']);

        if($festival->city_id){
            $festival->location = City::join('departments','department_id','=', 'departments.id')
                ->join('countries','country_id', '=', 'countries.id')
                ->select('cities.name_'.app()->getLocale().' as name', 'postal', 'departments.name_'.app()->getLocale().' as department', 'countries.name_'.app()->getLocale().' as country')
                ->find($festival->city_id);
        }
        else{
            $festival->location =  City::join('departments','department_id','=', 'departments.id')
                ->join('countries','country_id', '=', 'countries.id')
                ->select('cities.name_'.app()->getLocale().' as name', 'postal', 'departments.name_'.app()->getLocale().' as department', 'countries.name_'.app()->getLocale().' as country')
                ->find($year->city_id);
        }
    }

    public function validFilmRule($film, $rule){
        $movie = null;
        $invalid = [];

        $result  = $this->validDuration($film, $rule);

        if($result){
            $invalid = array_add($invalid, '2', [$result]);
        }


        $this->addValidResult($invalid, '2', $this->validFinish($film, $rule));

        $result = $this->validLanguages($film,$rule);
        if($result){
            $invalid = array_add($invalid,  '3', [$result]);
        }

        if($rule->languages){
            $languages = DB::table('rule_languages')->where('related_id', $rule->related_id)->get();
            if($languages){
                $rel = DB::table('film_languages')->where('film_id', $film->id)->pluck('language_id')->toArray();
                foreach ($languages as $language){
                    $val = explode(',', $language->values);
                    $condition = $language->condition;
                    $diff = $this->validCondition($condition, $val, $rel);
                    if($diff){
                        $arrayDiff = ['key'=>'languages', 'condition'=>$condition, 'yours'=>Language::whereIn('id', $rel)->pluck('name_'.app()->getLocale(), 'id')->toArray(),
                            'theirs'=>Language::whereIn('id', $val)->pluck('name_'.app()->getLocale(), 'id')->toArray(),
                            'diff'=>$diff];
                        $this->addValidResult($invalid, '3', $arrayDiff);
                    }
                }
            }
        }

        if($rule->productions === 1){
            $this->validProductions($invalid, '3', $film,$rule);
        }

        foreach (['shooting_format_cines'=>'label', 'shooting_format_camera'=>'label_' . app()->getLocale()] as $key=>$val){
            $value = object_get($rule, $key, null);
            if($value){
                $this->addValidResult($invalid, '4', $this->validShootings($film, $key, $value, $val));
            }
        }

        if($rule->color && $rule->color < 2 && $rule->color != $film->color){
            array_add($invalid, '5', ['key'=>'color', 'label'=>'color', 'yours'=>is_null($film->color) ? -1 :$film->color,
                'theirs'=>$rule->color,
                'diff'=>is_null($film->color) ? -1 :$film->color]);
        }

        if ($rule->special && $rule->special != $film->special) {
            $this->addValidResult($invalid, '5', ['key'=>'special', 'label'=>'special', 'yours' => is_null($film->special) ? -1 :$film->special,
                'theirs' => $rule->special]);
        }

        if ($rule->screen) {
            if($rule->subtitle){
                $subtitles = DB::table('rule_subtitles')->where('related_id', $rule->related_id)->get();
            }
            else{
                $subtitles = [];
            }

            $this->validScreen($invalid, $rule, $film->id, $subtitles);
        }

        if($rule->preview){
            $previews = DB::table('rule_previews')->where('related_id', $rule->related_id)->get();
            foreach ($previews as $preview){
                $continents =  [];
                $regions = [];

                if($preview->continents){
                    $continents = explode(',', $preview->continents);
                }
                if($preview->regions){
                    $regions = explode(',', $preview->regions);
                }

                $val = array_merge($continents, $regions);
                $channels = explode(',', $preview->channels);

                foreach ($channels as $channel){
                    $rel = DB::table('film_'.$channel)->where('film_id', $film->id)->select('country_id')->distinct()->pluck('country_id')->toArray();
                    if($preview->continents){
                        $rel = array_merge($rel, Country::whereIn('id', $rel)->select('continent_id')->distinct()->pluck('continent_id')->toArray());
                    }

                    $diff = $this->validCondition($preview->condition, $val, $rel);
                    if($diff){
                        $theirs = [];
                        if($continents){
                            $theirs = array_merge($theirs, DB::table('continents')->whereIn('id', $continents)->pluck('name_'.app()->getLocale(), 'id')->toArray());
                        }

                        if($regions){
                            $theirs = array_merge($theirs, Country::whereIn('id', $regions)->pluck("name_".app()->getLocale(), 'id')->toArray());
                        }

                        $this->addValidResult($invalid, '10', ['key'=>$channel, 'condition'=>$preview->condition,
                            'yours'=>Country::whereIn('id', $rel)->pluck("name_".app()->getLocale(), 'id')->toArray(),
                            'theirs'=>$theirs, 'diff'=>$diff]);
                    }
                }
            }
        }

        foreach (['genres', 'styles', 'subjects'] as $key){
            $value = object_get($rule, $key, null);
            if($value){
                $val = explode(',', $value);
                $rel = DB::table('film_' . $key)->where('film_id', $film->id)->pluck(substr($key, 0, strlen($key) - 1) . '_id')->toArray();
                $diff = array_values(array_diff($val, $rel));
                if ($diff) {
                    $this->addValidResult($invalid, '6',  ['key'=>$key, 'condition'=>'has', 'yours' => DB::table($key)->whereIn('id', $rel)->pluck('name_' . app()->getLocale(), 'id')->toArray(),
                        'theirs' => DB::table($key)->whereIn('id', $val)->pluck('name_' . app()->getLocale(), 'id')->toArray(),
                        'diff' => $diff]);
                }
            }
        }

        $result = $this->validAge($film,$rule);
        if($result){
            $invalid = array_add($invalid, '8', $result);
        }

        if (!is_null($rule->virgin) && $film->virgin != $rule->virgin) {
            $this->addValidResult($invalid, '8', ['key'=>'virgin', 'yours' => is_null($film->virgin)?-1:$film->virgin, 'theirs' => $rule->virgin]);
        }

        foreach (['music_original', 'screenplay_original'] as $key){
            $value = object_get($rule, $key, null);
            if($value){
                if (!$movie) {
                    $movie = DB::table('movies')->where('film_id', $film->id)->first();
                }
                if ($movie) {
                    $rel = object_get($movie, $key, null);
                    if ($rel != $value) {
                        $this->addValidResult($invalid, '9', ['key' => $key, 'yours' => is_null($rel)?-1:$rel, 'theirs' => $value]);
                    }
                }
            }
        }

        if($rule->school && $film->school != $rule->school){
            $this->addValidResult($invalid, '10', ['key' => 'school', 'yours' => is_null($film->school)?-1:$film->school, 'theirs' => $rule->school]);
        }

        if($rule->poster === 1){
            $folderName = 'film/' . $film->id . '/poster';
            if (sizeof(Storage::disk('public')->files($folderName))<1) {
                $this->addValidResult($invalid, '12', ['key' => 'poster', 'yours' => 0, 'theirs' => 1]);
            }
        }

        return $invalid;
    }

    private function getResult($sessions, $status){
        if ($status >= 0) {
            if ($status == 2) {
                $sessions = $sessions->whereRaw('festival_years.datediff BETWEEN 0 and 14');
            } else if ($status == 1) {
                $sessions = $sessions->whereRaw('festival_years.datediff > 14');
            } else if ($status == 0) {
                $sessions = $sessions->whereRaw('festival_years.datediff < 0');
            }
            else{
                $sessions = $sessions->whereRaw('festival_years.datediff > 0');
            }

            $sessions = $sessions->selectRaw('festivals.id, festival_years.*, cities.*, created_year, deadline,  festivals.name, festivals.name_' . app()->getLocale() . ' as name_locale, 
                ' . $status . ' as status, festivals.short, screenplay as script,'.(auth()->check() ? 'IFNULL(festival_favorites.id, 0) as favorite':'0 as favorite'))
                ->orderBy('datediff')
                ->paginate(12);
        } else {
            $sessions = $sessions->selectRaw('festivals.id, festival_years.*, cities.*, created_year, deadline, festivals.name, festivals.name_' . app()->getLocale() . ' as name_locale, 
                case when datediff < 0 then 0  when datediff > 14 then 1 else 2 end as status,festivals.short, screenplay as script,'
                .(auth()->check() ? 'IFNULL(festival_favorites.id, 0) as favorite':'0 as favorite'))
                ->orderBy('status', 'desc')
                ->orderBy('datediff')
                ->paginate(12);
        }

        return $sessions;
    }

    private function getRelatedRules(&$unit, $id, $rule=null)
    {
        if (!$rule) {
            $rule = DB::table('festival_rules')->where('related_id', $id)->first();
        }

        if (is_null($rule)) {
            return [];
        }

        $result = [];

        $unit->script = $rule->script;

        $theirs = $this->ruleDuration($rule);

        if($theirs){
            $this->addValidResult($result, '2', ['key'=>'duration', 'theirs'=>$theirs]);
        }

        $theirs = $this->ruleFinish($rule);

        if($theirs){
            $this->addValidResult($result, '2', ['key'=>'finish', 'theirs'=>$theirs]);
        }

        if($rule->silent){
            $this->addValidResult($result, '3', ['key'=>'silent', 'theirs'=>$rule->silent]);
        }
        else if($rule->mute){
            $this->addValidResult($result, '3', ['key'=>'mute', 'theirs'=>$rule->mute]);
        }
        else if($rule->languages === 1){
            $languages = DB::table('rule_languages')->where('related_id', $rule->related_id)->get();
            foreach($languages as $language){
                $this->addValidResult($result, '3', ['key'=>'languages', 'theirs'=>Language::whereIn('id', explode(',', $language->values))->pluck('name_'.app()->getLocale())->toArray(),
                    'condition'=>$language->condition]);
            }
        }

        if($rule->productions === 1){
            $productions = DB::table('rule_productions')->where('related_id', $rule->related_id)->get();
            foreach($productions as $production){
                $val = [];
                if($production->contients){
                    $val = DB::table('continents')->whereIn('id', explode(',', $production->contients))->pluck('name_'.app()->getLocale())->toArray();
                }

                if($production->regions){
                    $val = array_merge($val, Country::whereIn('id', explode(',', $production->regions))->pluck('name_'.app()->getLocale())->toArray());
                }

                if(sizeof($val)){
                    $this->addValidResult($result, '3', ['key'=>'production', 'theirs'=>$val, 'condition'=>$production->condition]);
                }
            }
        }

        foreach (['shooting_format_cines'=>'label', 'shooting_format_camera'=>'label_' . app()->getLocale()] as $key=>$val){
            $value = object_get($rule, $key, null);
            if($value){
                $this->addValidResult($result, '4', ['key'=>$key, 'theirs'=>DB::table(str_replace('shooting_', '', $key).'s')->whereIn('id', explode(',', $value))->pluck($val)->toArray(), 'condition'=>'in']);
            }
        }

        if($rule->color && $rule->color < 2){
            array_add($result, '5', ['key'=>'color', 'theirs'=>trans('film.rule.colors.'.$rule->color)]);
        }

        if ($rule->special) {
            $this->addValidResult($result, '5', ['key'=>'special', 'theirs'=>trans('film.rule.'.$rule->special)]);
        }

        if ($rule->screen) {
            $val = explode(',', $rule->screen);
            if(sizeof($val)){
                $theirs = [];
                foreach ($val as $screen){
                    array_push($theirs, trans('film.rule.'.$screen));
                }

                $this->addValidResult($result, '5', ['key'=>'screen', 'condition'=>'cros', 'theirs'=>$theirs]);

                foreach ($val as $screen){
                    DB::table('rule_screen_format_'.$screen)->where('related_id', $rule->related_id)
                        ->select('values', 'condition')->get()
                        ->map(function ($item) use (&$result, $screen){
                            $this->addValidResult($result, '5', ['key'=>'screen_format_'.$screen,
                                'theirs'=>DB::table('format_'.$screen)->whereIn('id', explode(',', $item->values))->pluck('label', 'id')->all(),
                                'condition'=>$item->condition]);
                        });
                }
            }

        }

        if($rule->subtitle){
            DB::table('rule_subtitles')->where('related_id', $rule->related_id)
                ->select('values', 'condition', 'subbed', 'dubbed')->get()
                ->map(function ($item) use (&$result){
                    $subtitles = Language::whereIn('id', explode(',', $item->values))->pluck('name_'.app()->getLocale())->all();
                    if($item->subbed){
                        if($item->dubbed){
                            $this->addValidResult($result, '5', ['key'=>'subtitle', 'theirs'=>$subtitles, 'condition'=>$item->condition]);
                        }
                        else{
                            $this->addValidResult($result, '5', ['key'=>'subbed', 'theirs'=>$subtitles, 'condition'=>$item->condition]);
                        }
                    }
                    else{
                        $this->addValidResult($result, '5', ['key'=>'dubbed', 'theirs'=>$subtitles, 'condition'=>$item->condition]);
                    }
                });
        }

        foreach (['genres', 'styles', 'subjects'] as $key){
            $value = object_get($rule, $key, null);
            if($value){
                $this->addValidResult($result, '6',
                     ['key'=>$key, 'theirs' => DB::table($key)->whereIn('id', explode(',', $value))->pluck('name_' . app()->getLocale(), 'id')->toArray(), 'condition'=>'in']);
            }
        }

        if (!is_null($rule->virgin)) {
            $this->addValidResult($result, '8', ['key'=>'virgin', 'theirs' => $rule->virgin]);
        }

        $query = null;
        $theirs = $this->ruleAge($query, $rule);
        if($theirs){
            $this->addValidResult($result, '8', ['key'=>'age', 'theirs'=> $theirs]);
        }

        foreach (['music_original', 'screenplay_original'] as $key){
            $value = object_get($rule, $key, null);
            if($value){
                $this->addValidResult($result, '9', ['key' => $key, 'theirs' => $value]);
            }
        }

        if($rule->school){
            $this->addValidResult($result, '10', ['key' => 'school', 'theirs' => $rule->school]);
        }

        if($rule->preview){
            $previews = DB::table('rule_previews')->where('related_id', $rule->related_id)->get();
            foreach ($previews as $preview){
                $continents =  [];
                $regions = [];

                if($preview->continents){
                    $continents = explode(',', $preview->continents);
                }
                if($preview->regions){
                    $regions = explode(',', $preview->regions);
                }

                $channels = explode(',', $preview->channels);

                foreach ($channels as $channel){
                    $theirs = [];
                    if($continents){
                        $theirs = array_merge($theirs, DB::table('continents')->whereIn('id', $continents)->pluck('name_'.app()->getLocale(), 'id')->toArray());
                    }

                    if($regions){
                        $theirs = array_merge($theirs, Country::whereIn('id', $regions)->pluck("name_".app()->getLocale(), 'id')->toArray());
                    }

                    if($theirs){
                        $this->addValidResult($result, '11', ['key'=>$channel, 'theirs'=>$theirs, 'condition'=>$preview->condition]);
                    }
                }
            }
        }

        if($rule->poster === 1){
            $this->addValidResult($result, '12', ['key' => 'poster', 'theirs' => 1]);
        }

        return $result;
    }



    private function getInformation(){
        $counters = ['confirmed' => 0,'rewards' => 0,'honors' => 0,'completed' =>0, 'inscriptions'=>0];
        if(auth()->check()){
            $contact = DB::table('user_contacts')->where('user_id', auth()->id())
                ->join('cities', 'city_id', '=', 'cities.id')
                ->join('departments', 'department_id', '=', 'departments.id')
                ->join('countries', 'country_id', '=', 'countries.id')
                ->selectRaw('first_name, last_name, prefix, company,address, IFNULL(user_contacts.postal, cities.postal) as postal, 
                    fix_code, mobile_code, 0 as fix, 0 as mobile, fix_number, mobile_number, countries.name_'.app()->getLocale().' as country, departments.name_'.app()->getLocale().' as department, cities.name_'.app()->getLocale().' as city')
                ->first();

            $user = auth()->user();

            $invalid = is_null($contact) || is_null($contact->first_name) ||  is_null($contact->last_name);

            if(!$invalid){
                $invalid |= is_null($contact->city) || is_null($contact->address);

                if(!$invalid){
                    if(!is_null($contact->fix_code) && !is_null($contact->fix_number)){
                        $contact->fix = 1;
                    }
                    if(!is_null($contact->mobile_code) && !is_null($contact->mobile_number)){
                        $contact->mobile = 1;
                    }

                    $invalid = !$contact->fix && !$contact->mobile;
                }
            }

            $films = Film::where('user_id', $user->id)->pluck('uploaded', 'id')->toArray();

            $counters['completed'] = sizeof(array_filter(array_values($films)));

            $counters['incompleted'] = sizeof($films) - $counters['completed'];

            FestivalEntry::whereIn('film_id', array_keys($films))->where('payed', 1)->get()
                ->map(function ($item) use (&$counters){
                    $counters['inscriptions']++;
                    if($item->accepted){
                        $counters['confirmed']++;
                    }

                    $honors = FestivalEntryHonor::where('festival_entry_id', $item->id)->pluck('id', 'rewarded')->toArray();
                    $rewards_cnt = array_key_exists(1, $honors) ? sizeof($honors[1]) : 0;
                    $counters['rewards'] += $rewards_cnt;
                    if(array_key_exists(0, $honors)){
                        $counters['honors'] += $rewards_cnt + sizeof($honors[0]);
                    }
                    else{
                        $counters['honors'] += $rewards_cnt;
                    }
                });
        }
        else{
            $contact = null;
            $user = null;
            $invalid = true;
        }

        return ['contact'=>$contact, 'user'=>$user, 'invalid'=>$invalid, 'counters'=>$counters];
    }

    private function validShootings($film, $key, $value, $implode){
        $col = str_replace('shooting_', '', $key);
        $val = explode(',', $value);
        $rel = DB::table('film_'.$col)->where('film_id', $film->id)->pluck(substr($col,0,strlen($col)-1).'_id')->toArray();
        $sect = array_values(array_intersect($val, $rel));
        if(!$sect){
            return ['key'=>$key, 'condition'=>'in', 'yours'=>DB::table($col)->whereIn('id', $rel)->pluck($implode, 'id')->toArray(),
                'theirs'=>DB::table($col)->whereIn('id', $val)->pluck($implode, 'id')->toArray(),
                'diff'=>$rel];
        }

        return [];
    }

    private function validCondition($condition, $val, $rel){
        if($condition == 'cover'){
            return array_values(array_diff($val, $rel));
        }
        if($condition == 'in'){
            return array_values(array_diff($rel, $val));
        }
        if($condition == 'cros'){
            $cros = array_intersect($val, $rel);
            if(!$cros){
                return array_values($rel);
            }
        }

        if($condition == 'diff'){
            return array_values(array_intersect($val, $rel));
        }

        if($condition == '<>'){
            $diff = array_diff($val, $rel);

            if(!$diff){
                return array_values($diff);
            }
        }

        if($condition == '='){
            $diff = array_diff($val, $rel);
            if($diff){
                return array_values($diff);
            }
            else{
                return array_values(array_diff($rel, $val));
            }
        }
    }

    private function validScreen(&$invalid, $rule,$film_id, $subtitles){
        $screens = explode(',', $rule->screen);
        $theirs = [];
        $invalidScreens = [];
        $yours = [];
        $screenFlag = [];
        foreach (['cines', 'digitals', 'videos'] as $screen){
            $col = 'format_'.substr($screen, 0, strlen($screen)-1).'_id';
            $rel = DB::table('screen_format_'.$screen)->where('film_id', $film_id)->pluck($col)->toArray();
            if(array_search($screen, $screens) === false){
                if(sizeof($rel) > 0){
                    $yours = array_merge($yours, DB::table('format_'.$screen)->whereIn('id', $rel)->pluck('label', 'id')->toArray());
                }
            }
            else{
                $format = DB::table('rule_screen_format_'.$screen)->where('related_id', $rule->related_id)->first();
                if($format){
                    $val = explode(',', $format->values);
                    if(sizeof($rel) < 1){
                        array_push($invalidScreens, ['key'=>$screen, 'condition'=>$format->condition, 'yours'=>[],
                            'theirs'=>DB::table('format_'.$screen)->whereIn('id', $val)->pluck('label', 'id')->toArray(), 'diff'=>$val]);
                    }
                    else{
                        $diff = $this->validCondition($format->condition, $val, $rel);
                        if($diff){
                            array_push($invalidScreens, ['key'=>$screen, 'condition'=>$format->condition, 'yours'=>DB::table('format_'.$screen)->whereIn('id', $rel)->pluck('label', 'id')->toArray(),
                                'theirs'=>DB::table('format_'.$screen)->whereIn('id', $val)->pluck('label', 'id')->toArray(), 'diff'=>$diff]);
                        }
                        else{
                            if($subtitles){
                                switch ($format->condition){
                                    case 'cover':
                                    case 'in':
                                    case 'cros':
                                    case '=':
                                        $this->validSubtitle($invalid, $screen, $film_id, $col, array_values(array_intersect($val, $rel)), $subtitles);
                                        break;
                                    case '<>':
                                    case 'diff':
                                        $this->validSubtitle($invalid, $screen, $film_id, $col, $rel, $subtitles);
                                        break;
                                }
                            }
                            $screenFlag = true;
                        }
                    }
                }
                elseif(sizeof($rel)>0){
                    if($subtitles) {
                        $this->validSubtitle($invalid, $screen, $film_id, $col, $rel, $subtitles);
                    }
                    $screenFlag = true;
                }
            }
        }

        if(!$screenFlag){
            if(sizeof($invalidScreens) > 0){
                foreach ($invalidScreens as $arr){
                    $this->addValidResult($invalid, '5', $arr);
                }
            }
            else{
                $this->addValidResult($invalid, '5', ['key'=>'screen', 'condition'=>'cros', 'yours'=>$yours, 'theirs'=>$theirs]);
            }
        }
    }

    private function validSubtitle(&$invalid, $screen, $film_id, $col, $val, $subtitles){
        $subbed = [];
        $dubbed = [];
        $en = Language::where('iso1', 'en')->select('name_'.app()->getLocale().' as name', 'id')->first();
        $yours = DB::table('screen_format_'.$screen)->where('film_id', $film_id)->whereIn($col, $val)
            ->select('id', 'english_subbed', 'english_dubbed')
            ->get();
        foreach ($yours as $your) {
            $subs = DB::table('screen_subtitles')->where(['screen_id'=>$your->id, 'screen_type'=>$screen[0]])
                ->join('languages', 'language_id', '=', 'languages.id')
                ->get(['subbed', 'dubbed', 'language_id', 'languages.name_' . app()->getLocale() . ' as name']);
            foreach ($subs as $sub) {
                if ($sub->subbed && !array_key_exists($sub->language_id, $subbed)) {
                    $subbed = array_add($subbed, $sub->language_id, $sub->name);
                }

                if ($sub->dubbed && !array_key_exists($sub->language_id, $dubbed)) {
                    $dubbed = array_add($dubbed, $sub->language_id, $sub->name);
                }
            }

            if($your->english_subbed && !array_key_exists($en->id, $subbed)){
                $subbed = array_add($subbed, $en->id, $en->name);
            }

            if($your->english_dubbed && !array_key_exists($en->id, $dubbed)){
                $dubbed = array_add($dubbed, $en->id, $en->name);
            }
        }

        $rel_subbed = array_keys($subbed);
        $rel_dubbed = array_keys($dubbed);

        foreach ($subtitles as $subtitle){
            $val = explode(',', $subtitle->values);
            $theirs = Language::whereIn('id', $val)->pluck('name_'.app()->getLocale(), 'id')->toArray();
            if($subtitle->subbed){
                $diff = $this->validCondition($subtitle->condition, $val, $rel_subbed);
                if($diff){
                    $this->addValidResult($invalid, '5', ['key'=>'subbed_'.$screen, 'condition'=>$subtitle->condition,
                        'yours'=>$subbed,
                        'theirs'=>$theirs,
                        'diff'=>$diff]);
                }

            }
            if($subtitle->dubbed){
                $diff = $this->validCondition($subtitle->condition, $theirs, $rel_dubbed);

                if($diff){
                    $this->addValidResult($invalid, '5', ['key'=>'subbed', 'condition'=>$subtitle->condition,
                        'yours'=>$subbed,
                        'theirs'=>$theirs,
                        'diff'=>$diff]);
                }
            }
        }
    }

    private function validProductions(&$invalid, $step, $film, $rule){
        $productions = DB::table('rule_productions')->where('related_id', $rule->related_id)->first();
        if($productions) {
            $rel = DB::table('film_productions')->where('film_id', $film->id)->pluck('country_id')->toArray();
            array_push($rel, $film->country_id);
            $yours = Country::whereIn('id', $rel)->pluck('name_' . app()->getLocale(), 'id')->toArray();
            foreach ($productions as $production) {
                $condition = $production->condition;
                $val = [];
                $theirs = [];

                if ($production->continents) {
                    $rel = array_merge($rel, Country::whereIn('id', $rel)->pluck('contient_id')->distinct()->toArray());
                    $val = explode(',', $production->continents);
                    $theirs = DB::table('contients')->whereIn('id', $val)->pluck('name_' . app()->getLocale(), 'id')->toArray();
                }

                if ($production->regions) {
                    $val = $val = array_merge($val, explode(',', $production->regions));
                }
                if ($val) {
                    $diff = $this->validCondition($condition, $val, $rel);
                    if ($diff) {
                        $this->addValidResult($invalid, $step, ['key' => 'productions', 'condition' => $condition, 'theirs' => $theirs, 'yours' => $yours]);
                    }
                }
            }
        }
    }

    private function validLanguages($film, $rule){
        if($rule->silent && $rule->silent != $film->silent){
            return ['key'=>'silent', 'yours'=>is_null($film->silent)?-1:$film->silent, 'theirs'=>$rule->silent];
        }

        if($rule->mute && $rule->mute != $film->mute){
            return ['key'=>'mute',  'yours'=>is_null($film->mute)?-1:$film->mute, 'theirs'=>$rule->mute];
        }

        return [];
    }

    private function validDuration($film, $rule){
        $theirs = $this->ruleDuration($rule);
        if(!$theirs){
            return;
        }

        if($film->hour || $film->minute || $film->second){
            $duration = trans('film.rule.duration_minute', ['cnt'=>$film->hour?$film->hour*60+$film->minute:($film->minute?:1)]);
        }
        else{
            $duration = null;
        }

        if($rule->duration_max){
            if($rule->duration_min){
                if(!$duration){
                   return ['key'=>'duration', 'yours'=>trans('film.rule.null'), 'theirs'=>$theirs];
                }
                elseif( $duration < $rule->duration_min || $duration > $rule->duration_max){
                    return['key'=>'duration', 'yours'=>$duration, 'theirs'=>$theirs];
                }
            }
            elseif($duration > $rule->duration_max){
                return ['key'=>'duration', 'yours'=>$duration, 'theirs'=>$theirs];
            }
        }
        elseif($rule->duration_min && $duration < $rule->duration_min){
            return ['key'=>'duration','yours'=>$duration, 'theirs'=>$theirs];
        }

        return [];
    }

    private function validFinish($film, $rule){
        $theirs = $this->ruleFinish($rule);
        if(!$theirs){
            return;
        }

        if($film->year && $film->month){
            $rel = $film->year.'-'.str_pad($film->month, 2, '0', STR_PAD_LEFT).'-'.str_pad($film->day, 2, '0', STR_PAD_LEFT);
        }
        else{
            $rel = -1;
        }

        if($rule->finish_after){
            if($rule->finish_before){
                if(!$rel || strtotime($rel) > strtotime($rule->finish_before) || strtotime($rel) < strtotime($rule->finish_after)){
                    return ['key'=>'finish', 'yours'=>$rel, 'theirs'=>$theirs];
                }
            }
            elseif(!$rel || strtotime($rel) < strtotime($rule->finish_after)){
                return ['key'=>'finish', 'yours'=>$rel, 'theirs'=>$theirs];
            }
        }elseif($rule->finish_before && (!$rel || strtotime($rel) > strtotime($rule->finish_before))){
            return ['key'=>'finish', 'yours'=>$rel, 'theirs'=>$theirs];
        }

        return [];
    }

    private function validAge($film, $rule){
        $query = null;
        $theirs = $this->ruleAge($query, $rule);

        if(!$theirs){
            return;
        }

        $directors = Filmaker::join(DB::raw('(select filmaker_id from film_directors where film_id="'.$film->id.'") film_directors'), function ($join) {
            $join->on('filmaker_id', '=', 'filmakers.id');
        })->select(['born', 'first_name', 'last_name', 'filmakers.id'])
        ->get();

        $result = null;

        if(!$directors){
            $result = ['key'=>'age',  'yours'=>trans('film.rule.null'), 'theirs'=>$theirs];
        }

        $diff =  Filmaker::join(DB::raw('(select filmaker_id from film_directors where film_id="'.$film->id.'") film_directors'), function ($join) {
            $join->on('filmaker_id', '=', 'filmakers.id');
        })
            ->whereNull('born')->orWhereRaw($query)
            ->pluck('filmakers.id');

        $yours = $directors->mapWithKeys(function($item){
            return [$item->id=>$item->last_name.' '.$item->first_name.'('.($item->born?:'?').')'];
        })->all();
        if(sizeof($diff) > 0){
            $result = ['key'=>'age', 'yours'=>$yours, 'theirs'=>$theirs, 'diff'=> $diff];
        }

        return $result;
    }

    private function ruleDuration($rule){
        if($rule->duration_max){
            if($rule->duration_min){
                return trans('film.rule.duration_between', ['min' => $rule->duration_min, 'max' => $rule->duration_max]);
            }
            else{
                return trans('film.rule.duration_max', ['max' => $rule->duration_max]);
            }
        }
        elseif($rule->duration_min){
            return trans('film.rule.duration_min', ['min' => $rule->duration_min]);
        }
        else{
            return null;
        }
    }

    private function ruleAge(&$query, $rule){
        if($rule->age_max){
            if($rule->age_min){
                $query = 'born not between '.(date("Y") - $rule->age_max).' and '.(date("Y") - $rule->age_min);
                return trans('film.rule.age_between', ['min' => $rule->age_min, 'max' => $rule->age_max]);
            }
            else{
                $query = 'born < '.(date("Y") - $rule->age_max);
                return trans('film.rule.age_max', ['max' => $rule->age_max]);
            }
        }
        elseif($rule->age_min){
            $query = 'born >'.(date("Y") - $rule->age_min);
            return trans('film.rule.age_min', ['min' => $rule->age_min]);
        }
        else{
            $query = null;
            return null;
        }
    }

    private function ruleFinish($rule)
    {
        if ($rule->finish_after) {
            if ($rule->finish_before) {
                return trans('film.rule.finish_between', ['after' => $rule->finish_after, 'before' => $rule->finish_before]);
            } else {
                return trans('film.rule.finish_after', ['after' => $rule->finish_after]);
            }
        } elseif ($rule->finish_before) {
            return trans('film.rule.finish_before', ['before' => $rule->finish_before]);
        }
        else{
            return null;
        }
    }
}
