@extends('layouts.film')

@section('filmForm')
    <form name="filmForm" action="/{{$film->type}}s" method="POST"
          ng-controller="filmCtrl" ng-init="loaded()">
        {{csrf_field()}}
        <input type="hidden" name="id" value="{{$film->id}}" />
        <input type="hidden" name="step" value="{{$step+1}}" />
        <ul class="alert alert-dark text-primary small px-5" role="alert">
            <li>{!! trans('film.alert.directors') !!}</li>
            <li class="pt-1">{!! trans('layout.ALERTS.compulsive') !!}</li>
        </ul>
        <div class="form-group row py-5">
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified required">
                {!!trans('film.label.virgin') !!}
            </label>
            <div class="col-lg-4 col-md-4 col-sm-12 pl-4 radio-inline">
                <input type="radio" name="virgin" value="1" {{$film->virgin == 1 ? "checked":"" }}>
                {{trans('layout.LABELS.yes')}}
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 radio-inline">
                <input type="radio" name="virgin" value="0" {{$film->virgin == 0 ? "checked":"" }}>
                {{trans('layout.LABELS.no')}}
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified required">
                {{trans('film.label.directors')}}
            </label>
            <div class="col-lg-10 col-md-8 col-sm-12">
                <div class="row px-3">
                    <div class="col-md-6 col-sm-12">
                        <div class="btn btn-block btn-outline-primary"  ng-click="chooseMaker()">{{trans('film.label.another_maker')}}</div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="btn btn-block btn-outline-primary" ng-click="createMaker()">{{trans('film.label.add_maker')}}</div>
                    </div>
                </div>

                @include('film.templates.maker', ['position'=>'director'])
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
    <script src="/js/controllers/film/director.js"></script>
@endsection