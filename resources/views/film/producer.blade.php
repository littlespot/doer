@extends('layouts.film')
@section('filmForm')
    <form name="filmForm" action="/{{$film->type}}s" method="POST" id="producerForm"
          ng-controller="filmCtrl" ng-init="loaded()">
        <input type="hidden" name="id" id="film_id" value="{{$film->id}}" />
        {{csrf_field()}}
        <input type="hidden" name="id" value="{{$film->id}}" />
        <input type="hidden" name="step" value="{{$step+1}}" />
        <ul class="alert alert-dark text-primary small px-5" role="alert">
            <li>{!! trans('film.alert.school') !!}</li>
            <li class="py-1">{!! trans('film.alert.producer') !!}</li>
            <li>{!! trans('layout.ALERTS.compulsive') !!}</li>
        </ul>
        <br/>
        <div class="form-group row" ng-init="schoolProduced = {{$film->school ? 1 : 0}}">
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified required">
                {{trans('film.label.school')}}
            </label>
            <div class="col-lg-3 col-md-4 col-sm-12 radio-inline">
                <input type="radio" name="school" value="1" ng-model="schoolProduced" />
                {{trans('layout.LABELS.yes')}}
            </div>
            <div class="col-lg-7 col-md-4 col-sm-12 radio-inline">
                <input type="radio" name="school" value="0" ng-model="schoolProduced" />
                {{trans('layout.LABELS.no')}}
            </div>
        </div>
        <div class="form-group row py-3">
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified">
                {{trans('film.label.school_name')}}
            </label>
            <div class="col-lg-10 col-md-8 col-sm-12 input input--isao">
                <input id="school_name" class="input__field input__field--isao"  name="school_name"
                       value="{{is_null($film->school_name) ? "" : $film->school_name}}"
                       ng-disabled="schoolProduced==0"/>
                <label class="input__label input__label--isao" for="searchmaker" data-content="{{trans('film.placeholder.school_name')}}">
                    <span class="input__label-content input__label-content--isao">{{trans('film.placeholder.school_name')}}</span>
                </label>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified required">
                {{trans('film.label.producer')}}
            </label>
            <div class="col-lg-10 col-md-8 col-sm-12">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="btn btn-block btn-outline-primary" ng-click="chooseMaker()">{{trans('film.label.another_maker')}}</div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="btn btn-block btn-outline-primary" ng-click="createMaker()">{{trans('film.label.add_maker')}}</div>
                    </div>
                </div>

                @include('film.templates.maker', ['position'=>'producer'])
            </div>
        </div>
    </form>
    <hr/>
    <div class="d-flex justify-content-between">
        <div class="btn btn-outline-danger" data-toggle="modal" data-target="#deleteFilmModal">{{trans('film.buttons.delete')}}</div>
        <button class="btn btn-primary" onclick="$('#producerForm').submit()">{{trans('layout.BUTTONS.continue')}}</button>
    </div>
@endsection
@section('script')
    <script src="/js/directives/location.js"></script>
    <script src="/js/directives/filmaker.js"></script>
    <script src="/js/controllers/film/producer.js"></script>
@endsection