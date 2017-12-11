@extends('film.card')

@section('filmForm')
    <form id="time_form" name="timeForm" action="/film/format" method="post">
        @include('film.form')
    <h4 class="header-slogan">{{trans('film.card.shooting')}}</h4>
    <div class="alert alert-info" role="alert">
        <div>{!! trans('film.alert.shooting') !!}</div>
        <div>{!! trans('layout.ALERTS.compulsive') !!}</div>
    </div>
    <div class="form">
        <div class="form-group row">
            <label class="col-md-2 col-sm-3 col-xs-4 col-form-label text-right">
                {{trans('film.label.film_format')}}
            </label>
            <div  class="col-md-8 col-sm-8 col-xs-8">
                @foreach($fformats as $format)
                <div class="col-sm-4 col-xs-6">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <input type="checkbox"  name="format_cine[]" value="{{$format->id}}" {{$format->chosen >0 ? "checked" : ""}}>
                        </span>
                        {{$format->label}}
                    </div>
                </div>
                @endforeach
            </div>
            <div class="col-md-2 col-sm-1"></div>
        </div>
        <hr/>
        <div class="form-group row">
            <label class="col-md-2 col-sm-3 col-xs-4 col-form-label text-right">
                {{trans('film.label.video_format')}}
            </label>
            <div  class="col-md-8 col-sm-8 col-xs-8">
                @foreach($vformats as $format)
                    <div class="col-sm-4 col-xs-6">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox"  name="camera[]" value="{{$format->id}}" {{$format->chosen >0 ? "checked" : ""}}>
                            </span>
                            {{$format->label}}
                            @if($format->id == 'other' || $format->id == 'TAPE')
                            <span class="input-group-addon" title="{{trans('film.tip.'.$format->id)}}">
                                <img src="/images/icons/help.svg" style="height: 16px;width: 16px">
                            </span>
                                @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="col-md-2 col-sm-1"></div>
        </div>
    </div>
    <hr/>
    <div class="form">
        <div class="form-group row">
            <label class="col-md-2 col-sm-3 col-xs-4 col-form-label text-right">
                <div class="input-group" title="{{trans('film.tip.software')}}">
                    <span class="input-group-addon">
                        <img src="/images/icons/help.svg" style="height: 16px;width: 16px">
                    </span>
                    {{trans('film.label.software')}}
                </div>
            </label>
            <div  class="col-md-8 col-sm-8 col-xs-8">
                <input type="text" class="form-text" name="software[0]" value="{{sizeof($softwares) > 0 ? $softwares[0] : ''}}">
                <br/>
                <input type="text" class="form-text" name="software[1]" value="{{sizeof($softwares) > 1 ? $softwares[1] : ''}}">
                <br/>
                <input type="text" class="form-text" name="software[2]" value="{{sizeof($softwares) > 2 ? $softwares[2] : ''}}">
            </div>
            <div class="col-md-2 col-sm-1"></div>
        </div>
    </div>
    <hr/>
    <div class="form">
        <div class="form-group row">
            <label class="col-md-2 col-sm-3 col-xs-4 col-form-label text-right">
                <div class="input-group" title="{{trans('film.tip.animation')}}">
                    <span class="input-group-addon">
                        <img src="/images/icons/help.svg" style="height: 16px;width: 16px">
                    </span>
                    {{trans('film.label.animation')}}
                </div>
            </label>
            <div  class="col-md-8 col-sm-8 col-xs-8">
                @foreach($animations as $animation)
                    <div class="input-group">
                        <span class="input-group-addon">
                            <input type="checkbox" name="animation[]" value="{{$animation->id}}" {{$animation->chosen >0 ? "checked" : ""}}>
                        </span>
                        {{$animation->label}}
                    </div>
                @endforeach
            </div>
            <div class="col-md-2 col-sm-1"></div>
        </div>
    </div>
    <div class="text-right margin-bottom-md">
        <button class="btn btn-primary" type="submit">{{trans('layout.BUTTONS.continue')}}</button>
    </div>
    </form>
@endsection