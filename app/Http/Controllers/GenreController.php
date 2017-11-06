<?php

namespace Zoomov\Http\Controllers;

use Auth;
use DB;
use Zoomov\Genre;

class GenreController extends Controller
{
    public function index(){
        return Genre::select('id', DB::raw('name_'.Auth::user()->locale.' as name'), 'ordre')->orderBy('ordre')->get();
    }
}
