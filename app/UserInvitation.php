<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class UserInvitation extends Model
{
    protected $fillable = ['user_id','invitation_code', 'created_at'];
}
