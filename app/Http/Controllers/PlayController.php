<?php

namespace Zoomov\Http\Controllers;
use App;
use Auth;
use Config;
use DB;
use Storage;
use Illuminate\Http\Request;
use Zoomov\FestivalInscriptionHonor;
use Zoomov\Film;
use Zoomov\FilmakerContact;
use Zoomov\Helpers\Uploader;
use Zoomov\Language;
use Zoomov\Country;

class PlayController extends ArchiveController
{
    public function store(Request $request){
        $id = $request['id'];
        $film = Film::find($id);
        if(!$film || $film->user_id != auth()->id()){
            return redirect('/archives');
        }
        if(!$request->has('step')){
            return $this->showDetail($film,'play');
        }
        $values = [];
        $invalid = false;
        $step = $request['step'];
        switch ($step) {
            case 2:
                $request->validate([
                    'title' => 'required|max:80',
                    'title_latin' => 'max:80',
                    'title_inter' => 'max:80'
                ]);
                $values = $request->only('title', 'title_latin', 'title_inter');
                if($film->title != $request['title']){
                    $this->createCover($film->id, $request['title']);
                }
                break;
            case 3:
                $lang = $this->getLanguage();
                if(!DB::table('film_synopsis')->where(['film_id' => $film->id, 'language_id'=>$lang->id])->first()){
                    $invalid = true;
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
                foreach (['silent', 'mute'] as $key) {
                    $values[$key] = $request->input($key, null);
                    $invalid |= is_null($values[$key]);
                }

                if ($values['mute']) {
                    DB::table('film_languages')->where('film_id', $film->id)->delete();
                    $values['conlange'] = null;
                }

                break;
            case 5:

                if($request->has('genre')){
                    $this->setParameters(array_unique($request['genre']), 'film_script_types', $film->id, 'script_type_id');
                }
                else{
                    $invalid = true;
                }

                if($request->has('style')){
                    $this->setParameters(array_unique($request['style']), 'film_styles', $film->id, 'style_id');
                }
                if($request->has('subject')){
                    $this->setParameters(array_unique($request['subject']), 'film_audience_types', $film->id, 'audience_type_id');
                }
                break;
            case 6:
                foreach(['school', 'school_name'] as $key){
                    $values[$key] = $request->input($key, null);
                }

                $invalid |= is_null($values['school']) || ($values['school'] == 1 && is_null($values['school_name']));

                $writers = DB::table('film_writers')->where('film_id', $film->id)->pluck('filmaker_id')->toArray();

                if($writers){
                    $this->createCover($film->id, $film->title);
                }
                else{
                    $invalid |= true;
                    if(Storage::disk('public')->exists('film/'.$id.'/cover.jpg')){
                        Storage::disk('public')->delete('film/'.$id.'/cover.jpg');
                    }
                }

                DB::table('plays')->where('film_id', $film->id)->update($request->only('adapted'));
                break;
            case 7:
                break;
        }

        $this->updateStatus($film, $invalid, $step - 2, $values, 'play');
        return $this->step($film, $step);
    }

    public function uploadForm($id){
        $film = Film::find($id);
        $this->getStatus($film, 'play');
        if(!Storage::disk('public')->exists('film/'.$id.'/cover.jpg')){
            $this->createCover($film->id, $film->title);
        }
        return $this->showUpload($film, 'play');
    }

    public function saveCredits($id, Request$request){
        $this->validate($request, ['credits'=>'required', 'makers'=>'required']);
        $result = [];
        foreach ($request['credits'] as $key=>$val){
            foreach ($request['makers'] as $filmaker_id){
                $credit = DB::table('film_'.$key)->where(['film_id'=>$id, 'filmaker_id'=>$filmaker_id])->first();
                if($credit) {
                    $result = array_add($result, $filmaker_id, 0);
                }
                else{
                    DB::table('film_'.$key)->insert(['film_id'=>$id, 'filmaker_id'=>$filmaker_id]);
                    $result = array_add($result, $filmaker_id, 1);
                }
            }
        }

        return $result;
    }

    public function deleteCredits($id, Request $request){
        $this->validate($request, ['type'=>'required', 'filmaker_id'=>'required']);
        DB::table('film_'.$request['type'])->where(['film_id'=>$id, 'filmaker_id'=>$request['filmaker_id']])->delete();
    }

    public function complete($id){
        if(sizeof(Storage::disk('public')->files('film/'.$id.'/preview'))>0){
            $film = Film::find($id);
            $film->update(['uploaded'=>1]);
            return redirect('archives');
        }
        else{
            return redirect('plays/'.$id);
        }
    }

    protected function step($film, $step){
        $film->status = str_pad(decbin($film->completed), config('constants.film.play_step'), '0');
        return $this->showStep($film, $step);
    }

    protected function showStep($film, $step){
        $film->type = 'play';
        $play = DB::table('plays')->where('film_id', $film->id)->first();
        if(!$play){
            return redirect('/archives');
        }
        switch ($step) {
            case 1:
                $languages = Language::selectRaw("id, rank, name as original, name_" . app()->getLocale() . " as name, false as chosen")
                    ->orderByRaw('convert(name_' . app()->getLocale() . ' using gbk) ASC')
                    ->get();

                $titles = DB::table('film_titles')->join('languages', 'language_id', 'languages.id')
                    ->selectRaw('title, name_' . app()->getLocale() . ' as language, language_id')
                    ->where('film_id', $film->id)
                    ->get();

                return view('film.title', ['languages' => $languages, 'film' => $film, 'titles' => $titles, 'step' => $step]);
            case 2:
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
            case 3:
                $countries = Country::selectRaw("id,  name_" . app()->getLocale() . " as name")->orderBy('rank')
                    ->orderByRaw('convert(name_' . app()->getLocale() .' using gbk) ASC')
                    ->pluck('name','id');

                $languages = Language::selectRaw("id, rank, name as original, name_" . app()->getLocale() . " as name, false as chosen")
                    ->orderBy('rank')
                    ->pluck('name','id');

                $dialogs =  Language::join('film_languages', 'languages.id', '=', 'language_id')
                    ->where('film_id', $film->id)
                    ->get(['languages.name_'.app()->getLocale().' as name', 'languages.id', 'order']);

                $productions = Country::join('film_productions', 'country_id', '=', 'countries.id')
                    ->where('film_id', $film->id)
                    ->get(['name_'.app()->getLocale().' as name', 'countries.id', 'order']);

                return view('film.play.language', ['countries'=>$countries, 'productions'=>$productions, 'languages'=>$languages, 'film'=>$film, 'dialogs'=>$dialogs, 'step'=>$step]);
            case 4:
                $styles = DB::table('styles')->where('play', '<>', '0')
                    ->leftJoin(DB::raw("(select id, style_id from film_styles where film_id = '".$film->id."') a"), function ($join) {
                        $join->on('styles.id', '=', 'a.style_id');
                    })
                    ->selectRaw('styles.id, name_'.app()->getLocale().' as name, IFNULL(a.id,0) as chosen')
                    ->get();

                $subjects = DB::table('audience_types')
                    ->leftJoin(DB::raw("(select id, audience_type_id from film_audience_types where film_id = '".$film->id."') a"), function ($join) {
                        $join->on('audience_types.id', '=', 'a.audience_type_id');
                    })
                    ->selectRaw('audience_types.id, name_'.app()->getLocale().' as name, IFNULL(a.id,0) as chosen')
                    ->get();

                $genres = DB::table('script_types')
                    ->leftJoin(DB::raw("(select id, script_type_id from film_script_types where film_id = '".$film->id."') a"), function ($join) {
                        $join->on('script_types.id', '=', 'a.script_type_id');
                    })
                    ->selectRaw('script_types.id, name_'.app()->getLocale().' as name, IFNULL(a.id,0) as chosen')
                    ->get();
                return view('film.genre', ['genres'=>$genres, 'styles'=>$styles, 'subjects'=>$subjects, 'film'=>$film, 'step'=>$step]);
            case 5:
                $year = date("Y");

                $countries = Country::where('region', '<>', 1)->selectRaw("id,  name_" . app()->getLocale() . " as name, sortname")->orderBy('rank')
                    ->orderByRaw('convert(name_' . app()->getLocale() .' using gbk) ASC')
                    ->get();

                $originals =  DB::table('film_writers')->where('film_id', $film->id)->implode('filmaker_id', ',');
                $film->adapted = is_null($play) ? '' : $play->adapted;
                return view('film.play.writer', ['year'=>$year, 'countries'=>$countries, 'originals'=>$originals, 'film'=>$film, 'step'=>$step]);
            case 6:
                $year = date("Y");
                $countries = Country::where('region', '<>', 1)->orderByRaw('convert(name_' . app()->getLocale() .' using gbk) ASC')->orderBy('rank')
                    ->pluck("name_" . app()->getLocale(), "id");

                $adapters = DB::table('film_adapters')->where('film_id', $film->id)
                    ->join('filmakers', 'filmaker_id','=', 'filmakers.id')
                    ->leftJoin('users', 'filmakers.related_id', 'users.id')
                    ->leftJoin('countries', 'filmakers.country_id', 'countries.id')
                    ->selectRaw('film_adapters.id, filmaker_id, first_name, last_name, username, related_id, CONVERT(filmakers.country_id, CHAR(3)) as country_id, 
                        countries.name_'.app()->getLocale().' as country, filmakers.tel, filmakers.mobile,  filmakers.web,
                        CONVERT(born,CHAR(4)) as born, prefix, filmakers.email')
                    ->get()
                    ->map(function ($item) {
                        $item->contact = FilmakerContact::where('filmaker_id', $item->filmaker_id)
                            ->join(DB::raw("(select contacts.id, address, IFNULL(contacts.postal, cities.postal) as postal, company, city_id, department_id, country_id, cities.name_".app()->getLocale()." as city,
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
                $producers = DB::table('film_producers')->where(['film_id'=>$film->id])
                    ->join('filmakers', 'filmaker_id','=', 'filmakers.id')
                    ->leftJoin('users', 'filmakers.related_id', 'users.id')
                    ->leftJoin('countries', 'filmakers.country_id', 'countries.id')
                    ->selectRaw('film_producers.id, filmaker_id, first_name, last_name, username, related_id, CONVERT(filmakers.country_id, CHAR(3)) as country_id, 
                        countries.name_'.app()->getLocale().' as country, filmakers.tel, filmakers.mobile,  filmakers.web,
                        CONVERT(born,CHAR(4)) as born, prefix, filmakers.email')
                    ->get()
                    ->map(function ($item) {
                        $item->contact = FilmakerContact::where('filmaker_id', $item->filmaker_id)
                            ->join(DB::raw("(select contacts.id, address, IFNULL(contacts.postal, cities.postal) as postal, company, city_id, department_id, country_id, cities.name_".app()->getLocale()." as city,
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
                $directors = DB::table('film_directors')->where(['film_id'=>$film->id])
                    ->join('filmakers', 'filmaker_id','=', 'filmakers.id')
                    ->leftJoin('users', 'filmakers.related_id', 'users.id')
                    ->leftJoin('countries', 'filmakers.country_id', 'countries.id')
                    ->selectRaw('film_directors.id, filmaker_id, first_name, last_name, username, related_id, CONVERT(filmakers.country_id, CHAR(3)) as country_id, 
                        countries.name_'.app()->getLocale().' as country, filmakers.tel, filmakers.mobile,  filmakers.web,
                        CONVERT(born,CHAR(4)) as born, prefix, filmakers.email')
                    ->get()
                    ->map(function ($item) {
                        $item->contact = FilmakerContact::where('filmaker_id', $item->filmaker_id)
                            ->join(DB::raw("(select contacts.id, address, IFNULL(contacts.postal, cities.postal) as postal, company, city_id, department_id, country_id, cities.name_".app()->getLocale()." as city,
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
                return view('film.play.credit', ['year'=>$year, 'countries'=>$countries, 'adapters'=>$adapters, 'directors'=>$directors, 'producers'=>$producers,
                        'film'=>$film, 'step'=>$step]);
            default:
                return $this->stepUpload($film, 'play');
        }
    }

    private function createCover($id, $title){
        $width = 595;
        $height = 842;
        $img = imagecreatetruecolor($width, $height);
        $white = imagecolorAllocate($img,255,255,255);
        $black = ImageColorAllocate($img, 0, 0, 0);
        imagefill($img,0,0,$white);

        $fontSize1 = 50;

        $x = 42;

        $y = 162;
        $chars = explode(' ', $title);

        $fontHeight = imagefontheight ($fontSize1);

        if(sizeof($chars) > 1){
            $fontSize1 = 39;
            $fontHeight = imagefontheight ($fontSize1);
            $max_length = '';
            foreach ($chars as $char){
                $length = strlen($char);
                if(strlen($max_length) < $length){
                    $max_length = $char;
                }
            }

            while(imagefontwidth ( $fontSize1 ) * mb_strwidth($max_length) > $width - 2 * $x){
                $fontSize1 -= 1;
            }

            $titles = $chars[0].' ';
            $textWidth = mb_strwidth($titles);
            $flagEnd = false;
            for ($i = 1; $i < sizeof($chars); $i++){
                $char = $chars[$i].' ';
                if($textWidth + mb_strwidth($char) > 24){
                    $flagEnd = true;
                    imagettftext ($img, $fontSize1, 0, $x, $y, $black, "font/NotoSansCJKsc-Medium.otf", $titles);
                    $y += 45 + $fontHeight;
                    $textWidth = mb_strwidth($char);
                    $titles = $char;
                }
                else{
                    $flagEnd = false;
                    $textWidth += mb_strwidth($char);
                    $titles .= $char;
                }
            }
            if(!$flagEnd){
                imagettftext ($img, $fontSize1, 0, $x, $y, $black, "font/NotoSansCJKsc-Medium.otf", $titles);
            }
        }
        elseif(strlen($title)> 7){
            $index = 0;
            while (strlen($title)> $index){
                imagettftext ($img, $fontSize1, 0, $x, $y, $black, "font/NotoSansCJKsc-Medium.otf", mb_strimwidth($title, $index, 14, '', 'utf8'));
                $index += 7;
                $y += 76 + $fontHeight;
            }
        }
        else{
            imagettftext ($img, $fontSize1, 0, $x, $y, $black, "font/NotoSansCJKsc-Medium.otf", $title);
        }

        $writers = DB::table('film_writers')->where('film_id', $id)
            ->join('filmakers', 'filmaker_id', '=', 'filmakers.id')
            ->select("last_name", "first_name")
            ->limit(5)
            ->get();

        $fontSize = 18;
        $fontHeight = imagefontheight ($fontSize1);
        $y = $height - 176  - $fontHeight;

        for($index = sizeof($writers) - 1; $index >= 0; $index--){
            $writer = $writers[$index];
            if($this->checkChinese($writer->last_name) > 0 && $this->checkChinese($writer->first_name)>0){
                $name = $writer->last_name.$writer->first_name;
            }
            else{
                $name = strtoupper($writer->last_name).' '.$writer->first_name;
            }

            if($index == 5 && DB::table('film_writers')->where('film_id', $id)->count() > 5){
                $name .= ',etc';
            }

            imagettftext ($img, $fontSize, 0, $x, $y, $black, "font/NotoSansCJKsc-Light.otf", $name);

            $y = $y - 25 - $fontHeight;
        }

        header('content-type:image/jpeg');
        $folderName = 'film/' . $id;
        if (!Storage::disk('public')->exists($folderName)) {
            Storage::disk('public')->makeDirectory($folderName);
        }

        imagejpeg($img,  storage_path('app/public/film/'.$id.'/cover.jpg'), 100);
        imagedestroy($img);
        return '/storage/film/'.$id.'/cover.jpg';
    }
}
