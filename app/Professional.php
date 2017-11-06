<?php

namespace Zoomov;
use Auth;
use DB;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Professional extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public $incrementing = false;
    protected $fillable = ['id', 'username', 'email', 'active', 'password', 'presentation', 'city_id', 'postal', 'address', 'locale', 'phone', 'mobile', 'admin_id'];
    protected $guarded = [
        'password', 'remember_token'
    ];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function city()
    {
        return $this->belongsTo('Zoomov\City', 'city_id');
    }

    public function roles()
    {
        return $this->hasMany('Zoomov\ProfessionalRole')->join('roles', 'role_id', '=', 'roles.id')
            ->select('role_id', 'roles.name_en as name');
    }
}
