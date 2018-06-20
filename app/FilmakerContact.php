<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class FilmakerContact extends Model
{
    public $timestamps = false;
    protected $fillable = ['filmaker_id', 'contact_id'];
}
