<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class InvitationPlaceholder extends Model
{
    protected $fillable = ['checked', 'placeholder_id', 'user_id','invitation_id'];
    public function invitation()
    {
        return $this->belongsTo('Zoomov\Invitation');
    }
}
