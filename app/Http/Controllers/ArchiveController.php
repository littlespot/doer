<?php

namespace Zoomov\Http\Controllers;
use App;
use Auth;
use Config;
use DB;
use Storage;
use Illuminate\Http\Request;

use Zoomov\Contact;
use Zoomov\FestivalEntry;
use Zoomov\FestivalEntryHonor;
use Zoomov\Film;
use Zoomov\Filmaker;
use Zoomov\FilmakerContact;
use Zoomov\FilmCast;
use Zoomov\FilmFestival;
use Zoomov\Helpers\Uploader;
use Zoomov\Language;

class ArchiveController extends Controller
{
    public function index()
    {
        $id = auth()->id();
        $movie_cnt = 0;
        $play_cnt = 0;
        $films = Film::where('user_id', $id)
            ->leftJoin('countries', 'country_id', '=', 'countries.id')
            ->where('films.completed', '>', 0)
            ->get(['films.id', 'title', 'countries.name_'.app()->getLocale().' as country', 'films.screenplay', 'year', 'month', 'hour', 'minute', 'second', 'completed', 'uploaded', 'silent', 'mute'])
            ->map(function ($item) use(&$movie_cnt, &$play_cnt){
               $folderName = 'film/'.$item->id;

               $item->inscriptions = FestivalEntry::where('film_id', $item->id)
                   ->join('festival_units', 'festival_unit_id', 'festival_units.id')
                   ->join('festival_years', 'festival_year_id', 'festival_years.id')
                   ->join('festivals', 'festival_id', 'festivals.id')
                   ->leftJoin('festival_entry_receipts', 'festival_entry_id', '=', 'festival_entries.id')
                   ->orderBy('payed')
                   ->orderBy('festival_units.due_at', 'desc')
                   ->get(['festival_entries.id', 'payed', 'festivals.name as festival', 'festival_entry_receipts.mch_id as receipt',
                       'festivals.name_'.app()->getLocale().' as festival_locale',
                       'festival_units.name as unit', 'festival_units.name_'.app()->getLocale().' as unit_locale', 'festival_units.due_at']);

              if(sizeof($item->inscriptions)){
                  $item->honors =  FestivalEntryHonor::whereIn('festival_entry_id', $item->inscriptions->pluck('id'))
                      ->join('festival_honors', 'festival_honor_id', '=', 'festival_honors.id')
                      ->join('festival_units', 'festival_unit_id', 'festival_units.id')
                      ->join('festival_years', 'festival_year_id', 'festival_years.id')
                      ->join('festivals', 'festival_id', 'festivals.id')
                      ->selectRaw('festival_years.session, rewarded, IFNULL(festivals.name_'.app()->getLocale().', festivals.name) as festival, 
                                    IFNULL(festival_units.name_'.app()->getLocale().', festival_units.name) as unit, honored_at')
                      ->orderBy('honored_at')
                      ->get();
              }
              else{
                  $item->honors = [];
              }

              if($item->screenplay){
                  $play_cnt += 1;
                  $item->type = 'play';
                  $item->genres = DB::table('film_script_types')->where('film_id', $item->id)
                      ->join('script_types', 'script_type_id', '=', 'script_types.id')->pluck('script_types.name_'.app()->getLocale());
                  $item->attributes = [];
                  $item->workers = DB::table('film_writers')->where('film_id', $item->id)->leftJoin('filmakers', 'filmaker_id', 'filmakers.id')
                      ->get(['first_name', 'last_name']);

                  $coverFolderName = $folderName. '/cover';
                  $files = Storage::disk('public')->files($coverFolderName);
                  if(sizeof($files) > 0) {
                      $item->poster = '/storage/'.$files[0];
                  }
                  else if(Storage::disk('public')->exists($folderName.'/cover.jpg')){
                      $item->poster = '/storage/'.$folderName.'/cover.jpg';
                  }
                  else{
                      $item->poster = '/images/icons/waiting.svg';
                  }
              }
              else{
                  $movie_cnt += 1;
                  $item->type = 'movie';
                  $item->genres = DB::table('film_genres')->where('film_id', $item->id)->join('genres', 'genre_id', '=', 'genres.id')->pluck('genres.name_'.app()->getLocale());
                  $item->attributes = DB::table('movies')->where('film_id', $item->id)->select('music_original')->first();

                  $item->workers = DB::table('film_directors')->where('film_id', $item->id)->leftJoin('filmakers', 'filmaker_id', 'filmakers.id')
                      ->get(['first_name', 'last_name']);

                  $folderName .= '/posters';
                  if(Storage::disk('public')->exists($folderName)) {
                      $files = Storage::disk('public')->files($folderName);
                      if(sizeof($files) > 0){
                          $item->poster = '/storage/'.$files[0];
                      }
                      else{
                          $item->poster = '/images/icons/waiting.svg';
                      }
                  }
                  else{
                      $item->poster = '/images/icons/waiting.svg';
                  }
              }
                $index = $this->getStatus($item, $item->type);
                $item->submitted = $index == 0 && $item->uploaded;
              return $item;
           })
            ->all();

        return view('film.index', ['films'=>$films, 'movie_cnt'=>$movie_cnt, 'play_cnt'=>$play_cnt]);
    }

    public function show($id, Request $request)
    {
        $film = Film::with('country')->find($id);
        if($film->user_id != auth()->id()){
            return redirect('/archives');
        }
        if($film->completed < 1){
            return redirect('/archive/creation');
        }
        if ($film) {
            if($film->screenplay){
                $type = 'play';
                $controler = new PlayController();
            }
            else{
                $type = 'movie';
                $controler = new MovieController();
            }
            $step = $this->getStatus($film, $type);
            if ($request->has('step')) {
                return $controler->showStep($film, $request['step']);

            } else {

                if($step > 0){
                    return $controler->showStep($film, $step);
                }
                else{
                    return $controler->showUpload($film, $type);
                }
            }

        } else {
            return redirect($request->has('type') ? $request['type'] : '/archives');
        }
    }

    public function creation ()
    {
        $contact =  DB::table('user_contacts')->where('user_contacts.user_id',auth()->id())
            ->leftJoin('cities', 'city_id', '=', 'cities.id')
            ->leftJoin('departments', 'department_id', '=', 'departments.id')
            ->leftJoin('countries', 'departments.country_id', '=', 'countries.id')
            ->selectRaw('user_id, company, prefix, first_name, last_name, address, user_contacts.postal, 
                fix_code, fix_number, mobile_code, mobile_number,
                countries.name_'.app()->getLocale().' as country, departments.country_id,
                department_id, departments.name_'.app()->getLocale().' as department, city_id, cities.name_'.app()->getLocale().' as city')
            ->first();


        return view('film.creation', ['contact'=>$contact]);
    }

    public function getSynopsis($id){
        $lang = $this-> getLanguage();
        return DB::table('film_synopsis')->where('film_id', $id)
            ->where('language_id', '<>', $lang->id)
            ->join('languages', 'language_id', '=', 'languages.id')
            ->select('film_synopsis.id','language_id','languages.name_'.App::getLocale().' as language', 'content')
            ->get();
    }

    public function getPosition($id, $position){
        return DB::table('film_'.$position.'s')->where('film_id', $id)
            ->join('filmakers', 'filmaker_id', '=', 'filmakers.id')
            ->leftJoin('countries', 'filmakers.country_id', '=', 'countries.id')
            ->leftJoin('users', 'related_id', '=', 'users.id')
            ->orderByRaw('convert(filmakers.last_name using gbk) ASC')
            ->get(['filmakers.id', 'filmakers.id as filmaker_id', 'first_name', 'last_name', 'prefix', 'born', 'tel', 'mobile', 'web', 'filmakers.email',
                'filmakers.country_id', 'countries.name_'.app()->getLocale().' as country', 'username', 'related_id'])
            ->map(function ($item) {
                $item->contact = FilmakerContact::where('filmaker_id', $item->id)
                    ->join(DB::raw("(select contacts.id, contacts.name, address, contacts.postal, company, city_id, department_id, country_id, cities.name_".app()->getLocale()." as city,
                                countries.name_".app()->getLocale()." as country, departments.name_".app()->getLocale()." as department from contacts
                                inner join cities on city_id = cities.id 
                                inner join departments on department_id = departments.id 
                                inner join countries on country_id = countries.id
                                where user_id = '".auth()->id()."') contact"), function ($join) {
                        $join->on('contact.id', '=', 'filmaker_contacts.contact_id');
                    })
                    ->select('contact_id', 'contact.*')
                    ->first();
                return $item;
            });
    }

    public function getMakers(){
        return  Filmaker::where('filmakers.user_id', auth()->id())
            ->leftJoin('countries', 'filmakers.country_id', '=', 'countries.id')
            ->leftJoin('users', 'related_id', '=', 'users.id')
            ->orderByRaw('convert(filmakers.last_name using gbk) ASC')
            ->get(['filmakers.id', 'filmakers.id as filmaker_id', 'first_name', 'last_name', 'prefix', 'born', 'tel', 'mobile', 'web', 'filmakers.email',
                'filmakers.country_id', 'countries.name_'.app()->getLocale().' as country', 'username', 'related_id'])
            ->map(function ($item) {
                $item->contact = FilmakerContact::where('filmaker_id', $item->id)
                    ->join(DB::raw("(select contacts.id, contacts.name, address, contacts.postal, company, city_id, department_id, country_id, cities.name_".app()->getLocale()." as city,
                                countries.name_".app()->getLocale()." as country, departments.name_".app()->getLocale()." as department from contacts
                                inner join cities on city_id = cities.id 
                                inner join departments on department_id = departments.id 
                                inner join countries on country_id = countries.id
                                where user_id = '".auth()->id()."') contact"), function ($join) {
                        $join->on('contact.id', '=', 'filmaker_contacts.contact_id');
                    })
                    ->select('contact_id', 'contact.*')
                    ->first();
                return $item;
            });
    }

    public function getContacts(){
        return DB::table('contacts')->where('contacts.user_id',auth()->id())
            ->leftJoin('cities', 'city_id', '=', 'cities.id')
            ->leftJoin('departments', 'department_id', '=', 'departments.id')
            ->leftJoin('countries', 'departments.country_id', '=', 'countries.id')
            ->selectRaw('contacts.id as contact_id, company, contacts.name, address, IFNULL(contacts.postal, cities.postal) as postal, 
                countries.name_'.app()->getLocale().' as country, departments.country_id, company,
                department_id, departments.name_'.app()->getLocale().' as department, city_id, cities.name_'.app()->getLocale().' as city')
            ->get();
    }

    public function updateCountry($id, Request $request){
        if($request->has('country_id')){
            $country_id = $request->input('country_id');
            DB::table('films')->where('id', $id)->update(['country_id'=>$country_id]);
            return 1;
        }
        return 0;
    }

    public function saveProductions($id, Request$request){
        if($request->has('country_id')){
            $country_id = $request->input('country_id');
            $count = DB::table('film_productions')->where('film_id', $id);
            if(sizeof($count) < 5){
                if($count->where('country_id', $country_id)->count()){
                    return ['production'=> trans('film.errors.production_existing')];
                }
                return DB::table('film_productions')->insertGetId(['film_id'=>$id, 'country_id'=>$country_id, 'order'=>sizeof($count) + 1]);
            }

            return ['production'=> trans('film.errors.production_maxlength')];
        }

        return null;
    }

    public function saveLanguages($id, Request $request){
        if($request->has('language_id')){
            $language_id = $request->input('language_id');
            $count = DB::table('film_languages')->where(['film_id' => $id, 'language_id' => $language_id])->get();
            if($count){
                DB::table('film_languages')->insertGetId(['film_id'=>$id, 'language_id'=>$language_id, 'order'=>sizeof($count) + 1]);
                return sizeof($count) + 1;
            }

            return ['language'=> trans('film.errors.double_value')];
        }

        return null;
    }

    public function saveTitle($id, Request$request){
        $this->validate($request, ['language_id'=>'required', 'title'=>'required|max:80']);
        $title = DB::table('film_titles')->where(['film_id'=>$id, 'language_id'=>$request->input('language_id')])->first();
        if(!$title){
            return DB::table('film_titles')->insertGetId(['film_id'=>$id, 'language_id'=>$request->input('language_id'), 'title'=>$request->input('title'), 'created_at'=>gmdate("Y-m-d H:i:s", time())]);;
        }
        else if($title->title != $request->input('title')){
            DB::table('film_titles')->where(['film_id'=>$id, 'language_id'=>$request->input('language_id')])->update(['title'=>$request->input('title')]);
            return $title->id;
        }
        else{
            return 0;
        }
    }

    public function updateConlange($id, Request $request){
        $conlange = $request->input('conlange');
        $film = Film::find($id);
        $film->update(['conlange'=>$conlange]);
        return $conlange;
    }

    public function removeTitle($id, $lang_id){
        DB::table('film_titles')->where(['film_id'=>$id, 'language_id'=>$lang_id])->delete();
    }

    public function removeProduction($id, $country_id){
        $production = DB::table('film_productions')->where(['film_id'=>$id, 'country_id'=>$country_id])->first();
        if($production){
            $order = $production->order;
            DB::table('film_productions')->where('id', $production->id)->delete();
            DB::table('film_productions')->where(['film_id'=>$id, 'country_id'=>$country_id])
                ->where('order', '>', $order)
                ->decrement('order', 1);
        }
    }


    public function removeLanguage($id, $language_id){
        DB::table('film_languages')->where(['film_id'=>$id, 'language_id'=>$language_id])->delete();
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
                'contact.postal' => 'required|max:12',
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

        $user = auth()->id();
        $values = array_merge($request->only('prefix', 'last_name', 'first_name', 'tel', 'mobile', 'email', 'born'), ['country_id'=>$request['nation_id'], 'user_id'=>$user]);

        if($request->has('id')){
            $maker = Filmaker::find($request->id);
        }
        else{
            $maker =Filmaker::where($values)->first();
        }

        if(is_null($maker)) {
            $maker_id = $this->uuid('m', 10, '13');
            $maker = Filmaker::create(array_merge($values, ['id' => $maker_id]));
            DB::table('film_'.$format.'s')->insert([
                'film_id' => $id,
                'filmaker_id' => $maker->id
            ]);
        }
        else{
            $maker->update($values);
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
        }

        return $maker;
    }

    public function saveMaker($format, Request $request){
        $this->validate($request, ['film_id'=>'required',  'makers' => 'required']);

        foreach ($request['makers'] as $maker){
            $old = DB::table('film_'.$format.'s')->where([
                'film_id' => $request['film_id'],
                'filmaker_id' => $maker
            ])->first();

            if(!$old){
                DB::table('film_'.$format.'s')->insertGetId([
                    'film_id' => $request['film_id'],
                    'filmaker_id' => $maker['id']
                ]);
            }
        }
    }

    public function deleteMakers($id, Request $request){
        $film = Film::find($id);

        if($film->user_id == auth()->id()){
            $format = $request['position'];

            DB::table('film_'.$format.'s')->where([
                'film_id' => $id,
                'filmaker_id' =>  $request['maker_id']
            ])->delete();

            return 1;
        }

        return 0;
    }

    public function saveFestival($id, Request $request)
    {
        $this->validate($request, [
            'year' => 'required',
            'event' => 'required',
            'city_id' => 'required',
            'country_id' => 'required'
        ]);

        if($request->has('id') && $request['id']){
            $festival = FilmFestival::find($request['id']);
            $festival->update($request->only('year', 'event', 'city_id', 'country_id'));
            if($request->has('rewards')){
                $rewards = [];
                foreach ($request['rewards'] as $reward){
                    if($reward['name'] && sizeof($reward['name']) < 80){
                        if($reward['id'] == 0){
                            $reward['id'] = DB::table('film_festival_rewards')->insertGetId(['film_festival_id'=>$festival->id, 'name'=>$reward['name'], 'competition'=>$reward['competition']?1:0]);
                        }
                        else{
                            DB::table('film_festival_rewards')->where('id', $reward['id'])
                                ->update(['name'=>$reward['name'], 'competition'=>$reward['competition']?1:0]);
                        }
                        array_push($rewards, $reward['id']);
                    }
                }
                DB::table('film_festival_rewards')->where('film_festival_id', $festival->id)->whereNotIn('id', $rewards)->delete();

                return ['id'=>$festival->id, 'rewards'=>DB::table('film_festival_rewards')->where('film_festival_id', $festival->id)->get(['id', 'name', 'competition'])];
            }
            else{
                DB::table('film_festival_rewards')->where('film_festival_id', $festival->id)->delete();
                return [];
            }
        }
        else{
            $festival = FilmFestival::create(['film_id' => $id,
                'year' => $request['year'],
                'event' => $request['event'],
                'city_id' => $request['city_id'],
                'country_id' => $request['country_id'],
                'created_at' => time()
            ]);
            if($request->has('rewards')) {
                foreach ($request['rewards'] as $reward) {
                    DB::table('film_festival_rewards')->insert(['film_festival_id'=>$festival->id, 'name'=>$reward['name'], 'competition'=>$reward['competition']?1:0]);
                }
            }

            return ['id'=>$festival->id, 'rewards'=>DB::table('film_festival_rewards')->where('film_festival_id', $festival->id)->get(['id', 'name', 'competition'])];
        }
    }

    public  function removeFestival($id, $festival_id){
        $festival = FilmFestival::find($festival_id);

        if($festival){
            $festival->delete();
            return 1;
        }

        return 0;
    }
    public function saveTheater($id, Request $request)
    {
        $validator = [
            'program' => 'required',
            'distribution' => 'required'
        ];

        $this->validate($request, $validator);
        if($request->has('id') && $request['id']){
            DB::table('film_theaters')->where('id', $request['id'])
                ->update(['year' => $request['year'],
                    'program' => $request['program'],
                    'country_id' => $request['country_id'],
                    'distribution' => $request['distribution'],
                    'contact' => $request['contact']
                ]);
        }
        else{
            $id = DB::table('film_theaters')->insertGetId( ['film_id'=>$id,
                'year' => $request['year'],
                'program' => $request['program'],
                'country_id' => $request['country_id'],
                'distribution' => $request['distribution'],
                'contact' => $request['contact'],
                'created_at' => gmdate("Y-m-d H:i:s", time())
            ]);

            return ['id' => $id];
        }
    }

    public  function removeTheater($id, $theater_id){
        DB::table('film_theaters')->where('id', $theater_id)->delete();
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
            return DB::table('film_synopsis')->insertGetId([
                'film_id' => $id,
                'language_id' => $language,
                'content' => $content,
                'created_at' => date('Y-m-d h:i:s')
            ]);
        }
        else if($content != $synopsis->content){
            DB::table('film_synopsis')->where(['film_id'=>$id, 'language_id' => $language])->update(['content'=>$content]);

        }

        return $synopsis->id;
    }

    public function removeSynopsis($id, $lang_id){
        DB::table('film_synopsis')->where(['film_id'=>$id, 'language_id' => $lang_id])->delete();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:80',
            'title_latin' => 'max:80',
            'title_inter' => 'max:80',
            'agreement' => 'required'
        ]);
        $user_id = auth()->id();
        $id = $this->uuid('f', 10, 'faker');
        $screenplay = $request->input('screenplay', 0);
        $film = Film::create([
            'id' => $id,
            'user_id' => $user_id,
            'title' => $request['title'],
            'title_latin' => $request['title_latin'],
            'title_inter' => $request['title_inter'],
            'screenplay' => $screenplay,
            'created_at' => time(),
            'completed' => 1,
            'uploaded' => 0
        ]);

        $maker = Filmaker::where(['user_id' => $user_id, 'related_id' => $user_id])->first();

        if (!$maker) {
            $contact = DB::table('user_contacts')->where('user_id', $user_id)->first();
            $user = auth()->user();
            $email = $user->email;
            $values = ['id' => $this->uuid('m'), 'email' => $email, 'user_id' => $user_id, 'related_id' => $user_id];
            if (!$contact) {
                $result = preg_split('/\W+/', $email);
                if (sizeof($result) > 3) {
                    $last_name = $result[1];
                    $first_name = $result[0];
                } else {
                    $first_name = '';
                    $last_name = auth()->user()->username;
                }

                Filmaker::create(array_merge($values, ['last_name' => $last_name, 'first_name' => $first_name]));
            } else {
                Filmaker::create(array_merge($values, ['last_name' => $contact->last_name, 'first_name' => $contact->first_name]));
                $values = ['city_id'=>$contact->city_id, 'postal'=>$contact->postal, 'company'=>$contact->company, 'address'=>$contact->address];
                if ($contact->fix_code && $contact->fix_number) {
                    array_add($values, 'tel', $contact->fix_code . '' . $contact->fix_number);
                }
                if ($contact->mobile_code && $contact->mobile_number) {
                    array_add($values, 'mobile', $contact->mobile_code . '' . $contact->mobile_number);
                }

                $contact = Contact::create(array_merge($values, ['id'=>$this->uuid('a', 10, $email), 'user_id'=>auth()->id()]));
                FilmakerContact::create(['filmaker_id' => $contact->id, 'contact_id' => $contact->id]);
            }
        }

        if ($screenplay) {
            DB::table('plays')->insert(['film_id' => $film->id]);
            return redirect('/plays/' . $film->id . '?step=1');
        } else {
            DB::table('movies')->insert(['film_id' => $film->id]);
            return redirect('/movies/' . $film->id . '?step=1');
        }
    }

    public function saveFile($id, $format, Request $request){

        $folderName = 'film/'.$id.'/'.$format;
        $size = sizeof(Storage::disk('public')->files($folderName));
        if($size >= $request->input('max', 9)){
            return false;
        }
        $files = $request->file($format);
        foreach ($files as $file) {
            $ext = $file->getClientOriginalExtension();
            $name = time().'.'.$ext;
            $file->storeAs(
                '/public/'.$folderName, $name
            );

            $size++;

            if($size > $request->input('max', 9)){
                return false;
            }
        }

        if($request->has('completed')){
            $film = Film::find($id);

            $type = $film->screenplay?'play':'movie';
            $completed = $film->completed;
            $this->setStatus(false, config('constants.film.'.$type.'_step'), $completed, $type);
            if($completed != $film->completed){
                DB::table('films')->where('id',$id)->update(['completed' => $completed]);
                return ['result'=>$name, 'completed'=>decbin($completed)];
            }
        }
        elseif($request->has('uploaded')){
            $film = Film::find($id);
            if(!$film->uploade){
                DB::table('films')->where('id',$id)->update(['uploaded' => 1]);
            }

            return ['result'=>$name, 'uploaded'=>1];
        }

        return ['result'=>''];
    }

    public function removeFile($id, $format, Request $request){
        $folderName = 'film/'.$id;
        if(!Storage::disk('public')->exists($folderName)) {
            return $request->all();
        }

        $folderName = $folderName.'/'.$format;
        if(!Storage::disk('public')->exists($folderName)) {
            return $request->all();
        }

        Storage::disk('public')->delete($folderName.'/'.$request['key']);
        if($request->has('completed')){
            $film = Film::find($id);
            $files = Storage::disk('public')->files($folderName);
            $completed = $this->setStatus(sizeof($files) < 1, config('constants.film.'.($film->screenplay?'play':'movie').'_step'), $film->completed);
            if($completed != $film->completed){
                $film->update(['completed' => $completed]);
                return ['result'=>$request['key'], 'completed'=>$completed];
            }
        }
        elseif($request->has('uploaded')){
            $film = Film::find($id);
            $files = Storage::disk('public')->files($folderName);
            if($film->uploaded && sizeof($files) < 1){
                $film->update(['uploaded' => 0]);
            }

            return ['result'=>$request['key'], 'uploaded'=>$film->uploaded];
        }

        return $request->all();
    }

    public function destroy($id){
        $film = Film::find($id);

        if($film->user_id != auth()->id()){
            return back()->with('errors', trans('auth.authorization'));
        }

        $film->update(['completed'=>0]);

        Storage::disk('public')->deleteDirectory('film/'.$id);

        foreach (['titles', 'synopsis', 'genres', 'styles', 'subjects', 'languages', 'productions', 'directors', 'producers'] as $col){
            DB::table('film_'.$col)->where('film_id', $id)->delete();
        }

        $casts = FilmCast::where('film_id', $id)->pluck('id')->toArray();

        if(sizeof($casts) > 0){
            foreach ($casts as $cast){
                DB::table("film_cast_credits")->where('film_cast_id', $cast)->delete();
            }

            DB::table('film_casts')->where('film_id', $id)->delete();
        }

        if($film->screenplay){
            foreach (['adapters', 'script_types', 'writers', 'audience_types'] as $col){
                DB::table('film_'.$col)->where('film_id', $id)->delete();
            }

            DB::table('plays')->where('film_id', $id)->delete();
        }
        else{
            foreach (['animations', 'softwares', 'shootings', 'sellers', 'theaters', 'diffusion', 'format_cines', 'cameras'] as $col){
                DB::table('film_'.$col)->where('film_id', $id)->delete();
            }

            foreach (['format_cines', 'format_digitals', 'format_videos'] as $col){
                DB::table('screen_'.$col)->where('film_id', $id)->delete();
            }

            $festivals = FilmFestival::where('film_id', $id)->pluck('id')->toArray();

            if(sizeof($festivals)>0){
                foreach ($festivals as $festival){
                    DB::table('film_festival_rewards')->where("film_festival_id", $festival)->delete();
                }
                FilmFestival::where('film_id', $id)->delete();
            }

            DB::table('movies')->where('film_id', $id)->delete();
        }

        $film->delete();

        return redirect('/archives');
   }

    protected function getLanguage()
    {
        switch (app()->getLocale()) {
            case 'zh':
                $lang = Language::where('name_en', 'Mandarin')->first(['id', 'name_' . app()->getLocale() . ' as name']);
                break;
            case 'en':
                $lang = Language::where('name_en', 'English')->first(['id', 'name_' . app()->getLocale() . ' as name']);
                break;
            case 'fr':
                $lang = Language::where('name_en', 'French')->first(['id', 'name_' . app()->getLocale() . ' as name']);
                break;
            default:
                $lang = Language::where('name_en', 'English')->first(['id', 'name_' . app()->getLocale() . ' as name']);
                break;
        }

        return $lang;
    }

    protected function updateStatus(&$film, $invalid, $step, $values, $type = null){
        if(!$type)
        {
            $type = $film->screenplay ? 'play' : 'movie';
        }

        $completed = $film->completed;
        $status = $this->setStatus($invalid, $step, $completed, $type);
        $film->update(array_add($values, 'completed',$completed));
        $film->status = $status;
        return $status;
    }

    protected function getStatus(&$film, $type){
        $bin = decbin($film->completed);
        $film->status = str_pad($bin, config('constants.film.'.$type.'_step'), '0');
        $index = strpos($film->status, '0');
        return is_numeric($index) ? $index + 1 : 0;
    }

    protected function setStatus($invalid, $step, &$completed, $type){
        $bin = decbin($completed);
        $status = str_pad($bin, config('constants.film.'.$type.'_step'), '0');
        $status[$step] = $invalid ? 0 : 1;
        $completed = bindec(substr($status, 0, strlen($bin) > $step ? strlen($bin) : $step+1));

        return $status;
    }

    protected function setParameters($parameters, $table, $film, $col='country_id', $params=[]){
        DB::table($table)->where('film_id', $film)->whereNotIn($col, $parameters)->delete();
        $old = DB::table($table)->where('film_id', $film)->pluck($col)->toArray();
        $toAdd = array_diff($parameters, $old);
        $values = array_merge($params, ['film_id'=>$film]);
        foreach ($toAdd as $new_id){
            DB::table($table)->insert(array_add($values, $col, $new_id));
        }
    }

    public function uploadForm($id){
        $film = Film::find($id);
        $type = $film->screenplay?'play':'movie';
        $this->getStatus($film, $type);
        return $this->stepUpload($film, $type);
    }

    protected function stepUpload($film, $type){
        $index = $this->getStatus($film, $type);

        if($index > 0){
            return redirect('/'.$type.'s/'.$film->id.'?step='.$index);
        }
        else{
            return $this->showUpload($film, $type);
        }
    }

    protected function showUpload($film, $type){
       if($film->screenplay){
           $film->type= 'play';
           $play = DB::table('plays')->where('film_id', $film->id)->first();
           if(Storage::disk('public')->exists("film/".$film->id."/preview")){
               $files = Storage::disk('public')->files("film/".$film->id."/preview");
               foreach ($files as $f){

                   if(str_start(basename($f),'preview')){
                       $file = $f;
                       break;
                   }
               }
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
           $languages = Language::selectRaw("id, rank, name as original, name_" . app()->getLocale() . " as name, false as chosen")
               ->orderBy('rank')
               ->get();

           $file = null;
           return view('film.'.$type.'.upload', ['step'=>0, 'languages'=>$languages, 'film'=>$film, 'play'=>$play, 'file'=>$file, 'size'=>$size, 'name'=>$name, 'ext'=>$ext]);
       }
       else{
           $film->type= 'movie';
           $movie = DB::table('movies')->where('film_id', $film->id)->first();
           if(Storage::disk('public')->exists("film/".$film->id."/preview")){
               $files = Storage::disk('public')->files("film/".$film->id."/preview");
               foreach ($files as $f){

                   if(str_start(basename($f),'preview')){
                       $file = $f;
                       break;
                   }
               }
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
           $languages = Language::selectRaw("id, rank, name as original, name_" . app()->getLocale() . " as name, false as chosen")
               ->orderBy('rank')
               ->get();

           $file = null;
           return view('film.'.$type.'.upload', ['step'=>0, 'languages'=>$languages, 'film'=>$film, 'movie'=>$movie, 'file'=>$file, 'size'=>$size, 'name'=>$name, 'ext'=>$ext]);
       }
    }
}
