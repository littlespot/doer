@extends('layouts.film')
@section('filmForm')
    <form name="filmForm" action="/plays" method="POST"
          ng-controller="filmCtrl" ng-init="loaded()">
        {{csrf_field()}}
        <input type="hidden" name="id" value="{{$film->id}}" />
        <input type="hidden" name="step" value="{{$step+1}}" />
        <input type="hidden" name="writers" value="{{$originals}}" />
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
        <div class="form-group row pt-4 pb-3" >
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
        <div class="form-group row pb-5">
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified required">
                {!!trans('film.label.virgin') !!}
            </label>
            <div class="col-lg-4 col-md-4 col-sm-12 radio-inline">
                <input type="radio" name="virgin" value="1" {{$film->virgin == 1 ? "checked":"" }}>
                {{trans('layout.LABELS.yes')}}
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 radio-inline">
                <input type="radio" name="virgin" value="0" {{$film->virgin == 0 ? "checked":"" }}>
                {{trans('layout.LABELS.no')}}
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified">
                {!!trans('film.label.adapted') !!}
            </label>
            <div class="col-lg-10 col-md-8 col-sm-12 input input--isao">
                <input id="adapted" class="input__field input__field--isao"  name="adapted"
                       value="{{is_null($film->adapted) ? "" : $film->adapted}}" />
                <label class="input__label input__label--isao" for="searchmaker" data-content="{{trans('film.placeholder.adapted')}}">
                    <span class="input__label-content input__label-content--isao">{{trans('film.placeholder.adapted')}}</span>
                </label>
            </div>
        </div>
        <div class="form-group row py-5">
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified required">
                {{trans('film.label.writers')}}
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

                @include('film.templates.maker', ['position'=>'writer'])
            </div>
        </div>
        <hr/>
        <div class="d-flex justify-content-between">
            <div class="btn btn-outline-danger" data-toggle="modal" data-target="#deleteFilmModal">{{trans('film.buttons.delete')}}</div>
            <button class="btn btn-primary">{{trans('layout.BUTTONS.continue')}}</button>
        </div>
    </form>
@endsection
@section('script')
    <script src="/js/directives/filmaker.js"></script>
    <script src="/js/controllers/film/producer.js"></script>
@endsection