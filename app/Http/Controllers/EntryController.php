<?php

namespace Zoomov\Http\Controllers;
use App;
use Auth;
use Config;
use DB;
use Storage;
use Illuminate\Http\Request;
use Zoomov\City;
use Zoomov\Country;
use Zoomov\Festival;
use Zoomov\FestivalEntry;
use Zoomov\FestivalInscriptionHonor;
use Zoomov\FestivalUnit;
use Zoomov\FestivalYear;
use Zoomov\Film;
use Zoomov\Filmaker;
use Zoomov\Language;
use Zoomov\User;

class EntryController extends Controller
{
    public function index(Request $request)
    {
        $inpayed = 0;
        $outdated = 0;
        $time = time();
        $entries = DB::table('festival_entries')->where('festival_entries.user_id', auth()->id())
            ->join('festival_units', 'festival_entries.festival_unit_id', 'festival_units.id')
            ->join('festival_years', 'festival_year_id', 'festival_years.id')
            ->join('festivals', 'festival_id', 'festivals.id')
            ->leftJoin('festival_entry_honors','festival_entry_id', 'festival_entries.id')
            ->leftJoin('festival_honors','festival_honor_id', 'festival_honors.id')
            ->selectRaw('festival_entries.id, festival_entries.title, film_id, festival_entries.screenplay, festival_entries.fee, festival_entries.currency, payed, 
                festival_years.session, festival_entries.festival_unit_id, festival_year_id, festival_id, receipt_number, accepted, received_at,
                sent_at, .festival_entries.updated_at,festival_entries.created_at, festival_units.due_at, festival_units.open_at, festival_units.end_at,
                IFNULL(festivals.name_'.app()->getLocale().', festivals.name) as festival, festivals.short,
                IFNULL(festival_units.name_'.app()->getLocale().', festival_units.name) as unit,
                IFNULL(festival_honors.name_'.app()->getLocale().', festival_honors.name) as honor, rewarded')
            ->orderBy('payed')
            ->get()
            ->mapToGroups(function ($item) use ($time, &$inpayed, &$outdated){
                if(!$item->payed){
                    if($item->due_at < $time){
                        $item->payed = 2;
                        $outdated += 1;
                    }
                    else{
                        $inpayed += 1;
                    }
                }

                if($item->sent_at && $item->sent_at > $item->created_at){
                    return [date("Y", strtotime($item->sent_at))=>$item];
                }
                else{
                    return [date("Y", strtotime($item->updated_at))=>$item];
                }
            });

        return view('festival.entries', ['entries'=>$entries, 'inpayed'=>$inpayed, 'outdated'=>$outdated]);
    }

    public function show($id)
    {
        $entry = FestivalEntry::find($id);
        if(!$entry){
            return redirect('/entry')->withErrors(trans('festival.errors.entry_404'));
        }

        $unit = FestivalUnit::select('fee', 'currency', 'name_'.app()->getLocale().' as name_locale', 'name', 'id', 'festival_year_id')->find($entry->festival_unit_id);
        $controller = new FestivalController();
        $year = $controller->getYear(['id'=>$unit->festival_year_id], true);
        $festival = Festival::select('id', 'web','email','name', 'name_'.app()->getLocale().' as name_locale', 'created_year', 'company', 'city_fixed', 'city_id', 'short', 'address')
            ->find($year->festival_id);
        $controller->getFestival($festival, $year);

        $units= FestivalUnit::where('festival_year_id', $year->id)->count();
        $contact = DB::table('festival_entry_contacts')
                ->join('cities', 'city_id','=', 'cities.id')
                ->join('departments', 'department_id','=', 'departments.id')
                ->join('countries', 'country_id', '=', 'countries.id')
                ->where('festival_entry_id', $entry->id)
                ->select('cities.name_'.app()->getLocale().' as city', 'departments.name_'.app()->getLocale().' as department', 'countries.name_'.app()->getLocale().' as country', 'festival_entry_contacts.*')
                ->first();
        if(!$entry->payed){
            return view('festival.payment',  ['film'=>Film::find($entry->film_id), 'contact'=>$contact, 'unit'=>$unit, 'entry'=>$entry]);
        }
        else{

            $receipt = DB::table('festival_entry_receipts')->where('festival_entry_id', $id)->first();
            if(!$receipt){
                $receipt = ['festival_entry_id'=>$id, 'app_id'=>$this->uuid('', 12, 'app'), 'app_id'=>$this->uuid('mch', 12, 'mch'),'user_id'=>auth()->id(),'created_at'=>gmdate("Y-m-d H:i:s", time())];
                DB::table('festival_entry_receipts')->insert($receipt);
                $receipt = DB::table('festival_entry_receipts')->where('festival_entry_id', $id)->first();
            }

            return view('festival.receipt', ['contact'=>$contact, 'year'=>$year, 'entry'=>$entry, 'units'=>$units, 'unit'=>$unit, 'festival'=>$festival, 'receipt'=>$receipt]);

        }
    }

    public function store(Request $request)
    {
        $this->validate($request, ['film_id' => 'required',
            'festival_unit_id' => 'required',
            'term' => 'required:on']);

        $values = $request->only('film_id', 'festival_unit_id');
        $entry = FestivalEntry::where($values)->first();

        $unit = FestivalUnit::find($values['festival_unit_id']);
        $year = FestivalYear::selectRaw('id,festival_id, session, presentation_'.app()->getLocale().' as presentation, start_at, end_at, due_at, datediff(due_at, CURDATE()) as datediff')->find($unit->festival_year_id);
        $festival = Festival::select('web','email','name', 'name_'.app()->getLocale().' as name_locale', 'created_year', 'short', 'city_fixed', 'city_id')->find($year->festival_id);
        if($entry && $entry->payed){
            $receipt = DB::table('festival_entry_receipts')->where('festival_entry_id', $entry->id)->first();
            if($receipt){
                return view('festival.receipt', ['film'=>Film::find($entry->film_id), 'unit'=>$unit, 'festival'=>$festival, 'year'=>$year,  'receipt'=>$receipt]);
            }
        }

        $user = User::find(auth()->id());
        $contact = $user->getContact();


        if(!$contact){
            return redirect('/festivals/'.$festival->short.'?unit='.$unit->name)->with('contact', trans('festival.errors.inscription_contact'));
        }

        $film = Film::find($request['film_id']);
        $rule = DB::table('festival_rules')->where('related_id', $unit->same_rule?$unit->festival_year_id:$unit->id)->first();
        $invalids = (new FestivalController())->validFilmRule($film, $rule);
        if(sizeof($invalids) > 0){
            $films =  $user->getFilms(['uploaded'=>1]);
            return view('festival.unit',  ['unit'=>$unit, 'rules'=>$invalids, 'films'=>$films, 'film_id'=>$film->id]);
        }

        if(!$entry){
            $basicInformation = $this->createEntry($film);
            $contact_id = $this->createEntryContact($contact);
            $localizations = $this->createEntryLocalization($film->id);
            $entry = FestivalEntry::create(array_merge($basicInformation, [
                'id' => $this->uuid('e'),
                'user_id' => auth()->id(),
                'contact_id' => $contact_id,
                'localizations' => $localizations,
                'fee' => $unit->fee,
                'currency' => $unit->currency,
                'payed' => $unit->fee == 0 ? 1 :0,
                'sent_at' => gmdate("Y-m-d H:i:s", time()),
                'created_at'=>gmdate("Y-m-d H:i:s", time())
            ]));
        }

        return redirect('/entry/'.$entry->id);
    }

    private function createEntry($film){
        $subjects = DB::table('film_subjects')->where('film_id', $film->id)->implode('subject_id', ',');
        $styles = DB::table('film_styles')->where('film_id', $film->id)->implode('style_id', ',');
        $genres = DB::table('film_genres')->where('film_id', $film->id)->implode('genre_id', ',');
        $languages = DB::table('film_languages')->where('film_id', $film->id)->implode('language_id', ',');
        $productions = DB::table('film_productions')->where('film_id', $film->id)->implode('country_id', ',');

        $values = [
            'film_id' => $film->id,
            'title' => $film->title,
            'title_latin' => $film->title_latin,
            'title_inter' => $film->title_inter,
            'country_id' => $film->country_id,
            'year' => $film->year,
            'month' => $film->month,
            'day' => $film->day,
            'hour' => $film->hour,
            'minute' => $film->month,
            'second' => $film->day,
            'mute' => $film->mute,
            'silent' => $film->silent,
            'color' => $film->color,
            'school' => $film->school,
            'school_name' => $film->school_name,
            'screenplay_original' => $film->screenplay_original,
            'music_original' => $film->music_original,
            'virgin' => $film->virgin,
            'special' => $film->special,
            'conlange' => $film->conlange,
            'inter_rights' => $film->inter_rights,
            'festivals' => $film->festivals,
            'diffusion' => $film->diffusion,
            'theaters' => $film->theaters,
            'subjects' => $subjects,
            'styles' => $styles,
            'genres' => $genres,
            'languages' => $languages,
            'production_countries' => $productions];
            return $values;
      }

    private function createEntryContact($contact){
        $values = [
            'first_name' => $contact->first_name,
            'last_name' => $contact->last_name,
            'prefix' => $contact->prefix,
            'email' => auth()->user()->email,
            'fix' => '+'.$contact->fix_code.' '.$contact->fix_number,
            'mobile' =>  '+'.$contact->mobile_code.' '.$contact->mobile_number,
            'address' => $contact->address,
            'company' => $contact->company,
            'postal' => $contact->postal,
            'city_id' => $contact->city_id
        ];

        $result = DB::table('entry_contacts')->where($values)->first();
        if($result){
            return $result->id;
        }

        return DB::table('entry_contacts')->insertGetId($values);
    }

    private function createEntryLocalization($film_id){
        $titles = DB::table('film_titles')->where('film_id', $film_id)->get(['language_id', 'title']);
        $result = [];
        foreach($titles as $title){
            $synopsis = DB::table('film_synopsis')->where(['film_id'=>$film_id, 'language_id'=>$title->language_id])->first();
            $values = [
                'language_id' => $title->language_id,
                'title' => $title->title,
                'synopsis' => $synopsis?$synopsis->content:null
            ];

            $loca = DB::table('entry_localizations')->where($values)->first();
            if($loca){
                array_push($result, $loca->id);
            }
            else{
                $id = DB::table('entry_localizations')->insertGetId($values);
                array_push($result, $id);
            }
        }

        $synopsis = DB::table('film_synopsis')->where('film_id', $film_id)->whereNotIn('language_id', $titles->pluck('language_id'))->get(['language_id', 'content']);

        foreach($synopsis as $syno){
            $values = [
                'language_id' => $syno->language_id,
                'synopsis' =>  $syno->content
            ];
            $loca = DB::table('entry_localizations')->where($values)->first();
            if($loca){
                array_push($result, $loca->id);
            }
            else{
                $id = DB::table('entry_localizations')->insertGetId($values);
                array_push($result, $id);
            }
        }

        return implode(',', $result);
    }

    private function setScreenplay($basic, $film_id, $credits, $directors){
        $play = DB::table('plays')->where('film_id', $film_id)->first();
        $audiences = DB::table('film_audience_types')->where('film_id', $film_id)->implode('audience_type_id', ',');
        $values = array_merge($basic, [
            'screenplay' => $play->type,
            'pages' => $play->pages,
            'adapted' => $play->adapted,
            'audiences' => $audiences
        ]);
        $entry = DB::table('entries')->where($values)->first();
        if($entry){
            $entry_id = $entry->id;
        }
        else{
            $entry_id = DB::table('entries')->inserGetId(array_merge($values, ['created_at'=>gmdate("Y-m-d H:i:s", time()), 'updated_at' => gmdate("Y-m-d H:i:s", time())]));
        }

        $writers = DB::table('film_writers')->where('film_id', $film_id)->pluck('filmaker_id');

        foreach($credits as $credit){
            $filmaker = DB::table('filmakers')
                ->join('filmakers', 'filmaker_id', '=', 'filmakers.id')
                ->leftJoin('filmaker_contacts', 'contact_id', '=', 'contacts.id')
                ->where('id', $credit->filmaker_id)
                ->selectRaw('film_id, first_name, last_name, prefix, filmakers.country_id as nationality, born, email, web, related_id, address, company, city_id, postal, fix, mobile')
                ->toArray();

            $eFilmaker = DB::table('entry_filmakers')->where($filmaker)->first();
            if($eFilmaker){
                $eFilmaker_id = $eFilmaker->id;
            }
            else{
                $eFilmaker_id = DB::table('entry_filmakers')->insertGetId($filmaker);
            }

            $eCredits = ['credits' => $credit->roles, 'producer' => 0, 'seller' => 0, 'director' => 0, 'writer' => 0];
            if($directors->search($credit->filmaker_id, true)){
                $directors->reject(function($value, $key) use ($credit){
                    return $value == $credit->filmaker_id;
                });
                $eCredits['director'] = 1;
            }

            if($writers->search($credit->filmaker_id, true)){
                $writers->reject(function($value, $key) use ($credit){
                    return $value == $credit->filmaker_id;
                });
                $eCredits['writer'] = 1;
            }

            DB::table('festival_entry_filmaker')->insert(array_merge($eCredits, [
                'entry_id' => $entry_id,
                'entry_filmaker_id' => $eFilmaker_id,
            ]));
        }

        foreach($directors as $filmaker_id){
            $filmaker = DB::table('filmakers')
                ->join('filmakers', 'filmaker_id', '=', 'filmakers.id')
                ->leftJoin('filmaker_contacts', 'contact_id', '=', 'contacts.id')
                ->where('id', $filmaker_id)
                ->selectRaw('film_id, first_name, last_name, prefix, filmakers.country_id as nationality, born, email, web, related_id, address, company, city_id, postal, fix, mobile')
                ->get();

            $eFilmaker = DB::table('entry_filmakers')->where($filmaker)->first();
            if($eFilmaker){
                $eFilmaker_id = $eFilmaker->id;
            }
            else{
                $eFilmaker_id = DB::table('entry_filmakers')->insertGetId($filmaker);
            }

            $eCredits = ['director' => 1, 'producer' => 0, 'seller' => 0, 'writer' => 0];

            if($writers->search($filmaker_id, true)){
                $writers->reject(function($v, $k) use ($filmaker_id){
                    return $v == $filmaker_id;
                });

                $eCredits['writer'] = 1;
            }

            DB::table('festival_entry_filmaker')->insert(array_merge($eCredits, [
                'entry_id' => $entry_id,
                'entry_filmaker_id' => $eFilmaker_id,
            ]));

            return ['entry_id'=>$entry_id];
        }

        foreach($writers as $filmaker_id){
            $filmaker = DB::table('filmakers')
                ->join('filmakers', 'filmaker_id', '=', 'filmakers.id')
                ->leftJoin('filmaker_contacts', 'contact_id', '=', 'contacts.id')
                ->where('id', $filmaker_id)
                ->selectRaw('film_id, first_name, last_name, prefix, filmakers.country_id as nationality, born, email, web, related_id, address, company, city_id, postal, fix, mobile')
                ->get();
            $eFilmaker = DB::table('entry_filmakers')->where($filmaker)->first();
            if($eFilmaker){
                $eFilmaker_id = $eFilmaker->id;
            }
            else{
                $eFilmaker_id = DB::table('entry_filmakers')->insertGetId($filmaker);
            }

            DB::table('festival_entry_filmaker')->insert(['entry_id' => $entry_id, 'entry_filmaker_id' => $eFilmaker_id, 'director' => 0, 'producer' => 0, 'seller' => 0, 'writer' => 1]);
        }
    }

    private function setMovie($basic, $film_id, $credits, $directors){
        $movie = DB::table('movies')->where('film_id', $film_id)->first();
        $shootings = DB::table('film_shootings')->where('film_id', $film_id)->implode('country_id', ',');
        $cameras = DB::table('film_cameras')->where('film_id', $film_id)->implode('camera_id', ',');
        $cines = DB::table('film_format_cines')->where('film_id', $film_id)->implode('format_cine_id', ',');
        $animations = DB::table('film_animations')->where('film_id', $film_id)->implode('animation_id', ',');

        $values = array_merge($basic, [
            'animations' => $animations,
            'shooting_countries' => $shootings,
            'shooting_cameras' => $cameras,
            'shooting_cines' => $cines,
            'music_original' => $movie->music_original,
            'final' => $movie->final,
            'fullvision' => $movie->fullvision
        ]);
        $entry = DB::table('entries')->where($values)->first();

        if($entry){
            $entry_id = $entry->id;
        }
        else{
            $entry_id = DB::table('entries')->inserGetId(array_merge($values, ['created_at'=>gmdate("Y-m-d H:i:s", time()), 'updated_at' => gmdate("Y-m-d H:i:s", time())]));
        }

        $producers = DB::table('film_producers')->where('film_id', $film_id)->pluck('filmaker_id');
        $sellers = DB::table('film_sellers')->where('film_id', $film_id)->pluck('filmaker_id');

        foreach($credits as $credit){
            $filmaker = DB::table('filmakers')
                ->join('filmakers', 'filmaker_id', '=', 'filmakers.id')
                ->leftJoin('filmaker_contacts', 'contact_id', '=', 'contacts.id')
                ->where('id', $credit->filmaker_id)
                ->selectRaw('film_id, first_name, last_name, prefix, filmakers.country_id as nationality, born, email, web, related_id, address, company, city_id, postal, fix, mobile')
                ->toArray();

            $eFilmaker = DB::table('entry_filmakers')->where($filmaker)->first();
            if($eFilmaker){
                $eFilmaker_id = $eFilmaker->id;
            }
            else{
                $eFilmaker_id = DB::table('entry_filmakers')->insertGetId($filmaker);
            }

            $eCredits = ['credits' => $credit->roles, 'director' => 0, 'producer' => 0, 'seller' => 0, 'writer' => 0];
            if($directors->search($credit->filmaker_id, true)){
                $directors->reject(function($value, $key) use ($credit){
                    return $value == $credit->filmaker_id;
                });
                $eCredits['director'] = 1;
            }

            if($producers->search($credit->filmaker_id, true)){
                $producers->reject(function($value, $key) use ($credit){
                    return $value == $credit->filmaker_id;
                });
                $eCredits['producer'] = 1;
            }

            if($sellers->search($credit->filmaker_id, true)){
                $sellers->reject(function($value, $key) use ($credit){
                    return $value == $credit->filmaker_id;
                });
                $eCredits['seller'] = 1;
            }

            DB::table('festival_entry_filmaker')->insert(array_merge($eCredits, [
                'entry_id' => $entry_id,
                'entry_filmaker_id' => $eFilmaker_id,
            ]));
        }

        foreach($directors as $filmaker_id){
            $filmaker = DB::table('filmakers')
                ->join('filmakers', 'filmaker_id', '=', 'filmakers.id')
                ->leftJoin('filmaker_contacts', 'contact_id', '=', 'contacts.id')
                ->where('id', $filmaker_id)
                ->selectRaw('film_id, first_name, last_name, prefix, filmakers.country_id as nationality, born, email, web, related_id, address, company, city_id, postal, fix, mobile')
                ->get();

            $eFilmaker = DB::table('entry_filmakers')->where($filmaker)->first();
            if($eFilmaker){
                $eFilmaker_id = $eFilmaker->id;
            }
            else{
                $eFilmaker_id = DB::table('entry_filmakers')->insertGetId($filmaker);
            }

            $eCredits = ['director' => 1, 'producer' => 0, 'seller' => 0, 'writer' => 0];

            if($producers->search($filmaker_id, true)){
                $producers->reject(function($v, $k) use ($filmaker_id){
                    return $v == $filmaker_id;
                });

                $eCredits['producer'] = 1;
            }

            if($sellers->search($filmaker_id, true)){
                $sellers->reject(function($v, $k) use ($filmaker_id){
                    return $v == $filmaker_id;
                });

                $eCredits['seller'] = 1;
            }

            DB::table('festival_entry_filmaker')->insert(array_merge($eCredits, [
                'entry_id' => $entry_id,
                'entry_filmaker_id' => $eFilmaker_id,
            ]));
        }

        foreach($producers as $filmaker_id){
            $filmaker = DB::table('filmakers')
                ->join('filmakers', 'filmaker_id', '=', 'filmakers.id')
                ->leftJoin('filmaker_contacts', 'contact_id', '=', 'contacts.id')
                ->where('id', $filmaker_id)
                ->selectRaw('film_id, first_name, last_name, prefix, filmakers.country_id as nationality, born, email, web, related_id, address, company, city_id, postal, fix, mobile')
                ->get();
            $eFilmaker = DB::table('entry_filmakers')->where($filmaker)->first();
            if($eFilmaker){
                $eFilmaker_id = $eFilmaker->id;
            }
            else{
                $eFilmaker_id = DB::table('entry_filmakers')->insertGetId($filmaker);
            }

            $eCredits = ['director' => 0, 'producer' => 1, 'seller' => 0, 'writer' => 0];

            if($sellers->search($filmaker_id, true)) {
                $sellers->reject(function($v, $k) use ($filmaker_id){
                    return $v == $filmaker_id;
                });

                $eCredits['seller'] = 1;
            }

            DB::table('festival_entry_filmaker')->insert(array_merge($eCredits, [
                'entry_id' => $entry_id,
                'entry_filmaker_id' => $eFilmaker_id,
            ]));
        }

        foreach($sellers as $filmaker_id){
            $filmaker = DB::table('filmakers')
                ->join('filmakers', 'filmaker_id', '=', 'filmakers.id')
                ->leftJoin('filmaker_contacts', 'contact_id', '=', 'contacts.id')
                ->where('id', $filmaker_id)
                ->selectRaw('film_id, first_name, last_name, prefix, filmakers.country_id as nationality, born, email, web, related_id, address, company, city_id, postal, fix, mobile')
                ->get();
            $eFilmaker = DB::table('entry_filmakers')->where($filmaker)->first();
            if($eFilmaker){
                $eFilmaker_id = $eFilmaker->id;
            }
            else{
                $eFilmaker_id = DB::table('entry_filmakers')->insertGetId($filmaker);
            }

            DB::table('festival_entry_filmaker')->insert(['entry_id' => $entry_id, 'entry_filmaker_id' => $eFilmaker_id, 'director' => 0, 'producer' => 0, 'seller' => 1, 'writer' => 0]);
        }

        foreach(['cines', 'digitals', 'videos'] as $format){
            $sines = DB::table('screen_format_'.$format)->where('film_id', $film_id)->get();
            foreach($sines as $sine){
                $sine['dubbed'] = DB::table('screen_subtitles')->where(['screen_id' => $sines->id, 'screen_type'=>'c', 'dubbed'=>1])
                    ->implode('language_id', ',');
                $sine['subbed'] = DB::table('screen_subtitles')->where(['screen_id' => $sines->id, 'screen_type'=>'c', 'subbed'=>1])
                    ->implode('language_id', ',');
                $screen = $sine->forget('id')->toArray();
                $screens = [];

                $screen = DB::table('entry_screens')->where($screen)->first();
                if($screen){
                    array_push($screens, $screen->id);
                }
                else{
                    array_push($screens, DB::table('entry_screens')->insertGetId($sines->toArray()));
                }
            }
        }

        return ['entry_id'=>$entry_id, 'screens'=>$screens];
    }

    public function pay(){
        require_once("AopSdk.php");
        $aop = new AopClient ();
        $aop->gatewayUrl = '[https://openapi.alipay.com/gateway.do](https://openapi.alipay.com/gateway.do)';
        $aop->appId = '请填写APPID';
        $aop->rsaPrivateKey = '请填写商户私钥';
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset= 'utf-8';
        $aop->format='json';
        $request = new AlipayTradePagePayRequest ();
        $request->setReturnUrl('请填写您的页面同步跳转地址');
        $request->setNotifyUrl('请填写您的异步通知地址');
        $request->setBizContent('{"product_code":"FAST_INSTANT_TRADE_PAY","out_trade_no":"20150320010101001","subject":"Iphone6 16G","total_amount":"88.88","body":"Iphone6 16G"}');

        $result = $aop->pageExecute ($request);

        echo $result;
    }

    private function display(Request $request)
    {
        $entries = FestivalEntry::join(DB::raw('(select id, title, title_inter, title_latin from films where user_id="'.auth()->id().'") films'), function ($join) {
            $join->on('film_id', '=', 'films.id');
        })
            ->join('festival_units', 'festival_unit_id', '=', 'festival_units.id')
            ->join('festival_years', 'festival_year_id', '=','festival_years.id')
            ->join('festivals', 'festival_id', '=', 'festivals.id');

        if($request->has('year')){
            $year = $request['year'];
            $entries = $entries->whereRaw('festival_entries.created_at BETWEEN "'.$year.'-01" AND "'.$year.'-12"');
        }
        else if($request->has('pay')){
            $entries = $entries->where('festival_entries.payed', '= ', 0);
        }

        $entries = $entries->selectRaw('festival_entries.id, IFNULL(festivals.name_'.app()->getLocale().', festivals.name) as festival, session, IFNULL(festival_units.name_'.app()->getLocale().', festival_units.name) as unit,
                festival_units.fee, festival_units.currency, festival_units.start_at, festival_units.due_at, payed, accepted, received_at, sent_at')
            ->orderBy('payed')
            ->orderBy('festival_units.start_at', 'desc')
            ->paginate(24);

        return $entries;
    }
}
