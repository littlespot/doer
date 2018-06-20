<?php

namespace Zoomov;
use Illuminate\Database\Eloquent\Model;
class Contact extends Model
{
    public $incrementing = false;
    protected $fillable = ['id', 'postal', 'name', 'city_id', 'company', 'address', 'user_id', 'fix', 'mobile'];
}
