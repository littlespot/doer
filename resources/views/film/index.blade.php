@extends('layouts.zoomov')
<link href="/css/festival.css" rel="stylesheet" />
@section('content')
    <div class="container" ng-controller="filmCtrl" ng-init="loaded()">
        <div class="text-right py-5">
            <a class="badge" href="/festivals" >{{trans("layout.MENU.festival_list")}}</a>
            <span class="px-1">/</span>
            <a class="badge" href="/entries">{{trans("layout.MENU.festival_inscription")}}</a>
            <span class="px-1">/</span>
            <b class="badge text-muted">{{trans("layout.MENU.films")}}</b>
            <span class="px-1">/</span>
            <a class="badge" href="/myfestivals">{{trans("layout.MENU.favorites")}}</a>
        </div>
        <div class="d-flex nav-film">
            <ul class="col-6 nav nav-tabs nav-fill mr-auto" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="all-tab" data-toggle="tab" href="#all" role="tab" aria-controls="all" aria-selected="true">{{trans('film.header.my')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="movie-tab" data-toggle="tab" href="#movie" role="tab" aria-controls="movie" aria-selected="false">{{trans('film.header.movies')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="screenplay-tab" data-toggle="tab" href="#screenplay" role="tab" aria-controls="screenplay" aria-selected="false">{{trans('film.header.screenplays')}}</a>
                </li>
            </ul>
            <form class="form-inline my-2 my-lg-0">
                <a href="archive/creation" class="btn btn-primary my-2 my-sm-0">{{trans('film.buttons.create')}}</a>
            </form>
        </div>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active py-3" id="all" role="tabpanel" aria-labelledby="all-tab">
                @if($movie_cnt > 0 || $play_cnt > 0)
                    @foreach($films as $index=>$movie)
                        @include('film.templates.movies', ['index'=>'a'])
                    @endforeach
                @else
                    <div class="content text-center my-5 py-5">
                        <h1>{{trans('festival.ERRORS.no_archive')}}</h1>
                        <a href="archive/creation" class="btn btn-outline-primary btn-lg mt-5">{{trans('festival.BUTTONS.go_create') }}</a>
                    </div>
                @endif
            </div>
            <div class="tab-pane fade" id="movie" role="tabpanel" aria-labelledby="movie-tab">
                @if($movie_cnt > 0)
                    @foreach($films as $index=>$movie)
                        @if(!$movie->screenplay)
                            @include('film.templates.movies',['index'=>'m'])
                        @endif
                    @endforeach
                @else
                    <div class="content text-center my-5 py-5">
                        <h1>{{trans('festival.ERRORS.no_movie')}}</h1>
                        <a href="archive/creation" class="btn btn-outline-primary btn-lg mt-5">{{trans('festival.BUTTONS.go_create') }}</a>
                    </div>
                @endif
            </div>
            <div class="tab-pane fade" id="screenplay" role="tabpanel" aria-labelledby="screenplay-tab">
                @if($play_cnt > 0)
                    @foreach($films as $index=>$movie)
                        @if($movie->screenplay)
                            @include('film.templates.movies',['index'=>'s'])
                        @endif
                    @endforeach
                @else
                   <div class="content text-center my-5 py-5">
                       <h1>{{trans('festival.ERRORS.no_play')}}</h1>
                       <a href="archive/creation" class="btn btn-outline-primary btn-lg mt-5">{{trans('festival.BUTTONS.go_create') }}</a>
                   </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="/js/controllers/film/list.js"></script>
@endsection