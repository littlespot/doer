<?php
/**
 * Created by PhpStorm.
 * User: petit
 * Date: 2018/4/2
 * Time: 11:36
 */

namespace Zoomov\Http\Controllers;

use DB;
use Storage;
use Illuminate\Http\Request;
use Zoomov\Contact;
use Zoomov\Filmaker;
use Zoomov\FilmakerContact;

class FilmakerController extends Controller
{
    public function index(Request $request){
        return Filmaker::where('user_id', auth()->id())
            ->leftJoin('users', 'related_id', '=', 'users.id')
            ->selectRaw('filmakers.id, filmakers.related_id, concat(filmakers.first_name, filmakers.last_name) as name, IFNULL(username, "'.trans('film.label.notuser').'") as username')
            ->orderBy('related_id', 'desc')
            ->orderByRaw('convert(username using gbk) ASC')
            ->get();
    }

    public function show($id){
        return Filmaker::with('contact')->find($id);
    }

    public function update($id, Request $request){
        $maker = Filmaker::find($id);
        if(!$maker){
            return $this->store($request);
        }

        $maker->update($request->except('id', 'filmaker_id', 'user_id'));

        return $maker;
    }

    public function store(Request $request){
        $this->validate($request, ['maker'=>'required', 'film_id'=>'required']);

        $maker = Filmaker::create(array_merge($request['maker'], ['id'=>$this->uuid('m'), 'user_id'=>auth()->id()]));
        $values = null;
        if($request->has('contact')) {
            $values = $request['contact'];
        }
        elseif(array_key_exists('contact', $request['maker'])){
            $values = $request['maker']['contact'];
        }

        if($values){
            if(array_key_exists('contact_id', $values) && $values['contact_id']){
                FilmakerContact::create(['filmaker_id' => $maker->id, 'contact_id'=>$values['contact_id']]);
            }
            else if(array_key_exists('city_id', $values) && $values['city_id'] && array_key_exists('address', $values) && $values['address']){
                $contact = $this->createContact($maker->id, $values);
                $values['contact_id'] = $contact->id;
            }

            $maker->contact = $values;
        }

        if($request->has('positions')){
            foreach ($request['positions'] as $position=>$chosen){
                DB::table('film_'.$position)->insertGetId(['filmaker_id' => $maker->id, 'film_id'=>$request['film_id']]);
            }
        }
        elseif($request->has('position')){
            DB::table('film_'.$request['position'].'s')->insertGetId(['filmaker_id' => $maker->id, 'film_id'=>$request['film_id']]);
        }
        else if($request->has('credits')){
              foreach ($request['credits'] as $key=>$val){
                  $cast_id = DB::table('film_casts')->insertGetId(['film_id'=>$request['film_id'], 'filmaker_id'=>$maker->id]);
                  DB::table('film_cast_credits')->insert(['film_cast_id'=>$cast_id, 'credit_id'=>$key]);
              }
        }

        return $maker;
    }

    public function destroy($id, Request $request){
        $format = $request->input('position');
        DB::table('film_'.$format.'s')->where('filmaker_id', $id)->delete();

        if($request->input('delete_maker', 0)){
            $maker = Filmaker::find($id);
            if($maker && $maker->user_id == auth()->id()){
                $filmakerContact = FilmakerContact::wehere('filmaker_id', $maker->id)->first();;

                if($filmakerContact){
                    $filmakerContact->delete();
                    $this->deleteContact($filmakerContact->id);
                }

                $maker->delete();
            }
        }
    }

    public function changeContact($id, $contact_id){
        $filmcontact = FilmakerContact::where('filmaker_id', $id)->first();
        if($filmcontact){
            $filmcontact->update(['contact_id'=>$contact_id]);
        }
        else{
            $filmcontact = FilmakerContact::create(['filmaker_id' => $id, 'contact_id'=>$contact_id]);
        }

        return $filmcontact->id;
    }

    public function contact($id, Request $request){
        $values = $request->only('city_id', 'postal', 'address', 'name', 'company');

        if($request->input('contact_id', null)){
            $contact =  $this->updateContact($request['contact_id'], $id, $values);
        }
        else{
            $contact =  $this->createContact($id, $values);
        }

        return $contact->id;
    }

    public function deleteContact($id){
        $filmakerContact = FilmakerContact::where('filmaker_id', $id)->first();;

        if($filmakerContact){
            $filmakerContact->delete();

            $count = FilmakerContact::where('contact_id', $filmakerContact->contact_id)->count();

            if(!$count){
                DB::table('contacts')->where('id', $filmakerContact->contact_id)->delete();
            }

        }

        return $filmakerContact->contact_id;
    }

    private function updateContact($id, $filmaker_id, $values){
        $contact = Contact::find($id);
        if($contact){
            $contact->update($values);
            return $contact;
        }
        else{
            return $this->createContact($filmaker_id, $values);
        }
    }

    private function createContact($id, $values){
        $contact = Contact::create(array_merge($values, ['id'=>$this->uuid('a',8), 'user_id'=> auth()->id()]));

        $filmcontact = FilmakerContact::where(['filmaker_id' => $id])->first();

        if($filmcontact){
            $filmcontact->update(['contact_id'=>$contact->id]);
        }
        else{
            FilmakerContact::create(['filmaker_id' => $id, 'contact_id'=>$contact->id]);
        }

        return $contact;
    }


}
