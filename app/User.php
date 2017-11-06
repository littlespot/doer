<?php

namespace Zoomov;
use Auth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zoomov\Helpers\CustomPassword;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $incrementing = false;

    protected $fillable = ['id', 'username', 'email', 'password', 'presentation', 'city_id', 'address',
        'locale', 'phone', 'mobile', 'created_at','username'];

    protected $guarded = [
        'password', 'remember_token', 'email', 'usernamed_at', 'created_at', 'updated_at'
    ];
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

    public function projects()
    {
        return $this->hasMany('Zoomov\Project')
            ->select('id', 'title', 'synopsis', 'active', 'genre_id', 'city_id', 'user_id', 'updated_at', 'start_at', 'finish_at',DB::raw('FLOOR((unix_timestamp(finish_at) - unix_timestamp(now()))/60/60/24) as daterest'), DB::raw('datediff(finish_at, created_at) as datediff'));
    }

    /*  public function idols()
      {
          return $this->hasMany('Zoomov\Relation', 'fan_id')
              ->join('users', 'idol_id', '=', 'users.id')
              ->select(['users.username', 'idol_id', 'users.presentation', 'fan_id']);
      }

      public function fans()
      {
          return $this->hasMany('Zoomov\Relation', 'idol_id')
              ->join('users', 'fan_id', '=', 'users.id')
              ->select(['users.username', 'fan_id', 'users.presentation', 'idol_id']);
      }*/

    public function preparations()
    {
        return $this->hasMany('Zoomov\Preparation');
    }

    public function participation()
    {
        return $this->hasMany('Zoomov\ProjectTeam', 'user_id')
            ->join('projects', 'projects.id', '=', 'project_teams.project_id')
            ->select('projects.*', 'project_teams.id as team_id');
    }

    public function sns(){
        return $this->hasMany('Zoomov\SnsUser')
            ->join('sns', 'sns.id', '=', 'sns_users.sns_id')
            ->select('sns_id', 'sns_name', 'user_id', 'sns.type');
    }

    public function getCityAttribute()
    {
        return City::find($this->city_id);
    }

    public function getFansCntAttribute()
    {
        if(!Auth::Check()){
            return 0;
        }

        return Relation::where('idol_id', $this->id)->count();
    }

    public function getIdolsCntAttribute()
    {
        if(!Auth::Check()){
            return 0;
        }

        return Relation::where('fan_id', $this->id)->count();
    }

    public function getRelationAttribute()
    {
        if(!Auth::Check()){
            return '';
        }

        $user = Auth::User()->id;

        if($this->id == $user){
            return 'Self';
        }
        else{
            $myFan = Relation::where('fan_id', $this->id)->where('idol_id', $user)->first();

            if($myFan != null){
                return $myFan->love ? 'Friend' : 'Fan';
            }
            else{
                $myIdol = Relation::where('idol_id', $this->id)->where('fan_id', $user)->first();
                return $myIdol == null ? '' : ($myIdol->love ?  'Friend' : 'Idol');
            }
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomPassword($token));
    }
}
