@extends('film.card')

@section('filmForm')
    <form id="time_form" name="timeForm" action="/film/synopsis" method="post" ng-controller="filmCtrl" ng-init="init('{{$film->id}}')">
        @include('film.form')
    <h4 class="header-slogan">{{trans('film.card.synopsis')}}</h4>
    <div class="alert alert-info" role="alert">
        <div>{!! trans('film.alert.synopsis') !!}</div>
        <div>{!! trans('layout.ALERTS.compulsive') !!}</div>
    </div>
    <label>{{trans('film.label.summary')}}</label>
    <textarea class="form-control" name="synopsis" rows="4" autofocus>{{is_null($synopsis)?trans('film.place.synopsis', ['cnt'=>400]):$synopsis->content}}</textarea>
    <hr/>
    <h6 class="header-slogan">{{trans('film.label.summary_trans')}}</h6>
    <div>
        @foreach($list as $s)
        <div class="row" id="content_{{$s->language_id}}">
            <label class="col-xs-3">{{$s->language}}</label>
            <div class="col-xs-8">{{$s->content}}</div>
            <div class="btn btn-text-important" ng-click="remove('{{$s->language_id}}')"><span class="fa fa-times"></span></div>
        </div>
        @endforeach
    </div>
    <div>
        <div class="row" ng-repeat="s in list">
            <label class="col-xs-3" ng-bind="s.language"></label>
            <div class="col-xs-8" ng-bind="s.content"></div>
            <div class="btn btn-text-important" ng-click="remove(s.language_id)"><span class="fa fa-times"></span></div>
        </div>
    </div>
        <div class="text-right"><span ng-click="edit=1;" class="fa fa-plus"></span></div>
    <div ng-if="edit">
        <select name="lang" ng-model="synopsis.language_id" id="synopsis_language">
            <option value="" disabled>{{trans('film.placeholder.language')}}</option>
            @foreach($languages as $key=>$language)
                <option value="{{$key}}">{{$language}}</option>
            @endforeach
        </select>
        <textarea class="form-control" name="other" rows="4" ng-model="synopsis.content">{{trans('film.place.synopsis', ['cnt'=>400])}}</textarea>
        <div class="text-right">
            <div class="btn btn-default" ng-click="edit = 0;"><span class="fa fa-undo"></span></div> <div class="btn btn-success" ng-click="save()"><span class="fa fa-save"></span></div>
        </div>
    </div>

    <hr/>
    <div class="text-right">
        <button class="btn btn-primary" type="submit">{{trans('layout.BUTTONS.continue')}}</button>
    </div>
    </form>
@endsection
@section('script')
    <script src="/js/controllers/film/synopsis.js"></script>
@endsection