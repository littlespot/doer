<?php

namespace Zoomov;
use Auth;
use Zoomov\Project;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $appends = ['type'];
    protected $fillable = ['project_id','budget_type_id', 'quantity', 'comment','updated_at','type'];
    protected $visible = ['id', 'budget_type_id', 'quantity', 'comment','updated_at','type'];

    public function project()
    {
        return $this->belongsTo('Zoomov\Project')->select('id', 'title');
    }

    public function getTypeAttribute()
    {
        return BudgetType::where('id', $this->budget_type_id)->select('name_'.Auth::user()->locale.' as name')->first();
    }
}
