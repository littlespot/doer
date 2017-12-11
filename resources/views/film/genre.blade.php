@extends('film.card')

@section('filmForm')
    <form id="time_form" name="timeForm" action="/film/genre" method="post" ng-controller="filmCtrl">
        @include('film.form')
    <h4 class="header-slogan">{{trans('film.card.screen')}}</h4>
    <div class="alert alert-info" role="alert">
        <ul>
            <li>{!! trans('film.alert.genre1') !!}</li>
            <li>{!! trans('film.alert.genre2') !!}</li>
        </ul>
        <div>{!! trans('layout.ALERTS.compulsive') !!}</div>
    </div>
    <h6 class="header-slogan">{!! trans('film.label.genre') !!}</h6>
    <div class="row" id="block_genre">
        @foreach($genres as $genre)
            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                <div class="input-group">
                        <span class="input-group-addon">
                            <input type="checkbox"  name="genre[]" ng-click="change('genre')" value="{{$genre->id}}" {{$genre->chosen ? "checked" : ''}}>
                        </span>
                    {{$genre->name}}
                </div>
            </div>
        @endforeach
    </div>
    <hr>
    <h6 class="header-slogan">{{trans('film.label.style')}}</h6>
    <div class="row" id="block_style">
        @foreach($styles as $style)
        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
            <div class="input-group">
                <span class="input-group-addon">
                    <input type="checkbox" name="style[]" ng-click="change('style')" value="{{$style->id}}" {{$style->chosen ? "checked" : ''}}>
                </span>
                {{$style->label}}
            </div>
        </div>
        @endforeach
    </div>
    <hr/>
    <h6 class="header-slogan">{{trans('film.label.subject')}}</h6>
    <div class="row" id="block_subject">
        @foreach($subjects as $subject)
            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                <div class="input-group">
                <span class="input-group-addon">
                    <input type="checkbox" name="subject[]" ng-click="change('subject')" value="{{$subject->id}}" {{$subject->chosen ? "checked" : ''}}>
                </span>
                    {{$subject->label}}
                </div>
            </div>
        @endforeach
    </div>
    <hr/>
    <div class="text-right">
        <button class="btn btn-primary" type="submit">{{trans('layout.BUTTONS.continue')}}</button>
    </div>
    </form>
@endsection
@section('script')
    <script src="/js/controllers/film/genre.js"></script>
@endsection