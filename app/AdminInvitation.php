<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class AdminInvitation extends Model
{
    protected $fillable = [
        'email', 'invitation_code', 'user_id', 'created_at'
    ];
}
