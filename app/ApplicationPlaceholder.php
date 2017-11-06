<?php

namespace Zoomov;

use Illuminate\Database\Eloquent\Model;

class ApplicationPlaceholder extends Model
{
    protected $fillable = ['application_id', 'checked', 'user_id', 'placeholder_id'];

    public function application()
    {
        return $this->belongsTo('Zoomov\Application');
    }
}
