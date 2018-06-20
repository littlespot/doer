<?php

namespace Zoomov;
use DB;
use Illuminate\Database\Eloquent\Model;

class FestivalUnit extends Model
{
    public $incrementing = false;

    public function info(){
        $year = FestivalYear::find($this->festival_year_id);
        $festival = Festival::select('web','email','name', 'name_'.app()->getLocale().' as name_locale', 'created_year', 'company', 'city_fixed', 'city_id')->find($year->festival_id);

        $this->year = $year;
        $this->festival = $festival;
    }

}
