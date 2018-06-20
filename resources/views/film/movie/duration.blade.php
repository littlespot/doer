@extends('layouts.film')
@section('filmForm')
    <form name="filmForm" name="filmForm" action="/{{$film->type}}s" method="POST"
          ng-controller="filmCtrl" ng-init="loaded()">
        {{csrf_field()}}
        <input type="hidden" name="id" value="{{$film->id}}" />
        <input type="hidden" name="step" value="{{$step+1}}" />
        <ul class="alert alert-dark text-primary small px-5" role="alert">
            @foreach(trans('film.alert.duration') as $key=>$label)
                <li class="py-1">{!! $label !!}</li>
            @endforeach
            <li>{!! trans('layout.ALERTS.compulsive') !!}</li>
        </ul>
        <div class="form mt-5 mb-auto">
            <div class="form-group row">
                <label class="col-md-2 col-sm-3 col-xs-4 text-primary label-justified required">{!! trans('film.label.date_complete') !!}</label>
                <div class="col-md-8 col-sm-8 col-xs-8">
                    <select id="year" name="year" class="form-control-sm">
                        <option value="">{{trans('layout.LABELS.year')}}</option>
                        @for($y = $year+1; $y > $year - 10 ; $y--)
                            <option id="opt_year_{{$y}}" value="{{$y}}" {{$film->year == $y ? 'selected' : ''}}>{{$y}}</option>
                        @endfor
                    </select>
                    &nbsp;-&nbsp;
                    <select id="month" name="month" class="form-control-sm">
                        <option value="">{{trans('layout.LABELS.month')}}</option>
                        @for($m = 1; $m < 13 ; $m++)
                            <option id="opt_month_{{$m}}" value="{{$m}}" {{$film->month == $m ? 'selected' : ''}}>{{str_pad($m, 2, '0', STR_PAD_LEFT)}}</option>
                        @endfor
                    </select>
                    -
                    <select id="day" name="day" class="form-control-sm">
                        <option value="">{{trans('layout.LABELS.day')}}</option>
                        @for($d = 1; $d < 31 ; $d++)
                            <option id="opt_day_{{$d}}" value="{{$d}}" {{$film->day == $d ? 'selected' : ''}}>{{str_pad($d, 2, '0', STR_PAD_LEFT)}}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2 col-sm-1"></div>
            </div>
            <div class="form-group row">
                <label for="title_original" class="col-md-2 col-sm-3 col-xs-4 text-primary label-justified required">
                    {!! trans('film.label.duration') !!}
                </label>
                <div class="col-md-8 col-sm-8 col-xs-8" >
                    <select id="hour" name="hour" class="form-control-sm">
                        @for($h = 0; $h < 3; $h++)
                            <option id="opt_hour_{{$h}}" value="{{$h}}" {{$film->hour == $h ? 'selected' : ''}}>{{'0'.$h}}</option>
                        @endfor
                    </select>
                    <sup>{{trans('layout.TIME.h')}}</sup>
                    &nbsp;
                    <select id="minute" name="minute" class="form-control-sm">
                        @for($m = 0; $m < 60; $m++)
                            <option id="opt_minute_{{$m}}" value="{{$m}}" {{$film->minute == $m ? 'selected' : ''}}>{{str_pad($m,2,'0',STR_PAD_LEFT)}}</option>
                        @endfor
                    </select>
                    <sup>{{trans('layout.TIME.m')}}</sup>
                    &nbsp;
                    <select id="second" name="second" class="form-control-sm">
                        @for($s = 0; $s < 60; $s++)
                            <option id="opt_second_{{$s}}" value="{{$s}}" {{$film->second == $s ? 'selected' : ''}}>{{str_pad($s,2,'0',STR_PAD_LEFT)}}</option>
                        @endfor
                    </select>
                    <sup>{{trans('layout.TIME.s')}}</sup>
                </div>
                <div class="col-md-2 col-sm-1"></div>
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