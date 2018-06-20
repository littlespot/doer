@extends('layouts.film')

@section('filmForm')
    <form name="filmForm" action="/{{$film->type}}s" method="POST"
          ng-controller="filmCtrl" ng-init="loaded()">
        {{csrf_field()}}
        <input type="hidden" name="id" value="{{$film->id}}" />
        <input type="hidden" name="step" value="{{$step+1}}" />
        <ul class="alert alert-dark text-primary small px-5" role="alert">
            <li class="py-1">{!! trans('layout.ALERTS.compulsive') !!}</li>
        </ul>
        <div class="form-group row text-primary my-5">
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified">
                {{trans('film.label.film_format')}}
            </label>
            <div class="col-lg-10 col-md-8 col-sm-12 row">
                @foreach($fformats as $format)
                    <div class="checkbox-inline col-4 pb-3">
                        <input type="checkbox"  name="format_cine[]" value="{{$format->id}}" {{$format->chosen >0 ? "checked" : ""}}>
                        {{$format->label}}
                    </div>
                @endforeach
            </div>
        </div>
        <div class="form-group row text-primary ">
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified">
                {{trans('film.label.video_format')}}
            </label>
            <div  class="col-lg-10 col-md-8 col-sm-12 row">
                @foreach($vformats as $format)
                    <div class="col-4 pb-3">
                        <div class="checkbox-inline">
                            <input type="checkbox"  name="camera[]" value="{{$format->id}}" {{$format->chosen >0 ? "checked" : ""}}>
                            {{$format->label}}
                            @if($format->id == 'other' || $format->id == 'TAPE')
                                <span class="fa fa-question" title="{{trans('film.tip.'.$format->id)}}"></span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="form-group row text-primary my-5">
            <label class="col-lg-2 col-md-4 col-xs-12 ">
                {{trans('film.label.animation')}}
                <span class="fa fa-question-circle"  title="{{trans('film.tip.animation')}}"></span>
            </label>
            <div  class="col-lg-10 col-md-8 col-xs-8 row">
                @foreach($animations as $animation)
                    <div class="checkbox-inline col-4 pb-3">
                        <input type="checkbox" name="animation[]" value="{{$animation->id}}" {{$animation->chosen >0 ? "checked" : ""}}>
                        {{$animation->label}}
                    </div>
                @endforeach
            </div>
        </div>
        <div class="form-group row text-primary">
            <label class="col-lg-2 col-md-4 col-xs-12">
                {{trans('film.label.software')}}
                <span class="fa fa-question-circle"  title="{{trans('film.tip.software')}}"></span>
            </label>
            <div  class="col-lg-10 col-md-8 col-sm-8 col-xs-8">
                <input type="text" class="form-control mb-1" name="software[0]" value="{{sizeof($softwares) > 0 ? $softwares[0] : ''}}">
                <input type="text" class="form-control mb-1" name="software[1]" value="{{sizeof($softwares) > 1 ? $softwares[1] : ''}}">
                <input type="text" class="form-control" name="software[2]" value="{{sizeof($softwares) > 2 ? $softwares[2] : ''}}">
            </div>
        </div>
    <hr/>
        <div class="d-flex justify-content-between">
            <div class="btn btn-outline-danger" data-toggle="modal" data-target="#deleteFilmModal">{{trans('film.buttons.delete')}}</div>
            <button class="btn btn-primary" type="submit">{{trans('layout.BUTTONS.continue')}}</button>
        </div>
    </form>
@endsection
@section('script')
    <script src="/js/controllers/film/general.js"></script>
@endsection