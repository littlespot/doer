@extends('film.card')

@section('filmForm')
    <form id="time_form" name="timeForm" action="/film/time" method="post">
       @include('film.form')
    <h4 class="header-slogan">{{trans('film.card.duration')}}</h4>
    <div class="alert alert-info" role="alert">
        <div>{!! trans('film.alert.duration') !!}</div>
        <div>{!! trans('layout.ALERTS.compulsive') !!}</div>
    </div>
    <div class="form">
        <div class="form-group row">
            <label class="col-md-2 col-sm-3 col-xs-4 col-form-label text-right">{!! trans('film.label.date_complete') !!}</label>
            <div  class="col-md-8 col-sm-8 col-xs-8">
                <select id="month" name="month">
                    <option value="">{{trans('layout.LABELS.month')}}</option>
                    @for($m = 1; $m < 12 ; $m++)
                        <option id="opt_month_{{$m}}" value="{{$m}}" {{$film->month == $m ? 'selected' : ''}}>{{$m.trans('layout.LABELS.month')}}</option>
                    @endfor
                </select>
                &nbsp;/&nbsp;
                <select id="year" name="year">
                    <option value="">{{trans('layout.LABELS.year')}}</option>
                    @for($y = $year+1; $y > $year - 10 ; $y--)
                        <option id="opt_year_{{$y}}" value="{{$y}}" {{$film->year == $y ? 'selected' : ''}}>{{$y}}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2 col-sm-1"></div>
        </div>
        <div class="form-group row">
            <label for="title_original" class="col-md-2 col-sm-3 col-xs-4 col-form-label text-right">{!! trans('film.label.duration') !!}</label>
            <div class="col-md-8 col-sm-8 col-xs-8">
                <select id="hour" name="hour">
                    @for($h = 0; $h < 3; $h++)
                        <option id="opt_hour_{{$h}}" value="{{$h}}" {{$film->hour == $h ? 'selected' : ''}}>{{'0'.$h}}</option>
                    @endfor
                </select>
                <sup>{{trans('layout.TIME.h')}}</sup>
                &nbsp;
                <select id="minute" name="minute">
                    @for($m = 0; $m < 60; $m++)
                        <option id="opt_minute_{{$m}}" value="{{$m}}" {{$film->minute == $m ? 'selected' : ''}}>{{str_pad($m,2,'0',STR_PAD_LEFT)}}</option>
                    @endfor
                </select>
                <sup>{{trans('layout.TIME.m')}}</sup>
                &nbsp;
                <select id="second" name="second">
                    @for($s = 0; $s < 60; $s++)
                        <option id="opt_second_{{$s}}" value="{{$s}}" {{$film->second == $s ? 'selected' : ''}}>{{str_pad($s,2,'0',STR_PAD_LEFT)}}</option>
                    @endfor
                </select>
                <sup>{{trans('layout.TIME.s')}}</sup>
            </div>
            <div class="col-md-2 col-sm-1"></div>
        </div>
    </div>
    <div class="text-right">
        <button class="btn btn-primary" type="submit">{{trans('layout.BUTTONS.continue')}}</button>
    </div>
    </form>
@endsection