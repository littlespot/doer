<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class Filmaker extends Model
{
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['id', 'user_id', 'prefix', 'last_name', 'first_name', 'born', 'country_id', 'web', 'tel', 'mobile', 'email', 'related_id'];

    public function contact(){
        return $this->has('Zoomov\FilmakerContact')->join(DB::raw("(select id address, postal, company, city_id, department_id, country_id, cities.name_".app()->getLocale()." as city from contacts
                        inner join cities on city_id = cities.id 
                        inner join departments on department_id = departments.id 
                        inner join countries on country_id = countries.id
                        where user_id = '".auth()->id()."') contact"), function ($join) {
            $join->on('contact.id', '=', 'filmaker_contacts.contact_id');
        });
    }
}
