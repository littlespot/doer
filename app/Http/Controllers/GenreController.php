<?php

namespace Zoomov\Http\Controllers;

use Auth;
use DB;
use Zoomov\Genre;

class GenreController extends Controller
{
    public function index(){
        return Genre::where('film', '<', 2)->select('id', DB::raw('name_'.app()->getLocale().' as name'), 'sequence as order')->orderBy('sequence')->get();
    }
}
