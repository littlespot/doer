<?php

namespace Zoomov;
use DB;
use Zoomov\City;
use Illuminate\Database\Eloquent\Model;

class FestivalEntry extends Model
{
    public $incrementing = false;
    protected $fillable = ['id','film_id', 'festival_unit_id', 'fee','currency', 'title','title_latin','title_inter','payed','accepted', 'user_id', 'sent_at'];
    public function honors(){
        $this->hasMany('Zoomov\FestivalEntryHonor', 'festival_entry_id', 'id');
    }

    public function contact(){
        $contact = DB::table('festival_entry_contacts')->where('festival_entry_id', $this->id)->first();
        $location = City::join('departments', 'department_id','=', 'departments.id')
            ->join('countries', 'country_id', '=', 'countries.id')
            ->select(['cities.name_'.app()->getLocale().' as city, departments.name_'.app()->getLocale().' as department, countries.name_'.app()->getLocale().' as country'])
            ->find($contact->city_id);

        $contact->city = $location->city;
        $contact->department = $location->department;
        $contact->country = $location->country;
        return $contact;
    }
}
