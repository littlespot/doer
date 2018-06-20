@extends('layouts.zoomov')
<style>
    .card-header .collapse{
        display: none;
    }
</style>
@section('content')
    <link href="/css/festival.css" rel="stylesheet" />
    <div class="container" ng-controller="festivalCtrl" ng-init="loaded()">
        <div class="text-right py-5">
            <a class="badge" href="/festivals">{{trans("layout.MENU.festival_list")}}</a>
            <span class="px-1">/</span>
            <a class="badge" href="/entries">{{trans("layout.MENU.festival_inscription")}}</a>
            <span class="px-1">/</span>
            <a class="badge" href="/archives">{{trans("layout.MENU.films")}}</a>
            <span class="px-1">/</span>
            <a class="badge" href="/myfestivals">{{trans("layout.MENU.favorites")}}</a>
        </div>
        @include('templates.festival-top')
        <div class="mt-1 p-5 bg-light row">
            <div>{!! $year->presentation?:$festival->presentation !!}</div>
            <section id="units" class="col-12 pt-3">
                @foreach($units as $index=>$unit)
                <div class="card my-3">
                    <div class="card-header bg-primary" id="heading{{$index}}">
                        <h5 class="mb-0 text-white d-flex justify-content-between">
                            {{$unit->name_locale?:$unit->name}}@if($unit->competition)[{{trans('festival.COMPETITIONS.'.$unit->rank)}}]@endif
                            <span class="small" data-toggle="collapse" data-target="#{{$unit->id}}" aria-expanded="{{$index==0}}" aria-controls="{{$unit->id}}">
                                <span class="{{$index == 0? 'ng-hide':''}}">{{trans('festival.BUTTONS.collapse')}} <span class="fa fa-caret-down"></span></span>
                                <span class="{{$index > 0? 'ng-hide':''}}"> {{trans('festival.BUTTONS.fold')}} <span class="fa fa-caret-up"></span></span>
                            </span>
                        </h5>
                    </div>
                    <div id="{{$unit->id}}" class="collapse {{$index==0? 'show':''}}" aria-labelledby="heading{{$index}}" data-parent="#units">
                        <div class="card-body">
                            <h5>{{trans('festival.HEADERS.presentation')}}</h5>
                            <div class="mb-5">{!! $unit->presentation !!}</div>
                            @if(!is_null($unit->script))
                                <div class="alert alert-info">{{$unit->script ? trans('film.rule.script'):trans('film.rule.movie')}}</div>
                            @endif
                            <h5>{{trans('festival.HEADERS.rules')}}</h5>
                            <table class="table table-striped">
                                @foreach($unit->rules as $key=>$val)
                                    @foreach($val as $rule)
                                        <tr>
                                            <td>{{trans('film.rule.'.$rule['key'])}}</td>
                                            <td class="d-flex">
                                                @if(array_key_exists('condition', $rule))
                                                    <label class="mr-1">{{trans('film.rule.'.$rule['condition'])}}: </label>
                                                    <ol class="breadcrumb bg-transparent" style="padding:0">
                                                        @foreach($rule['theirs'] as $k=>$their)
                                                        <li class="breadcrumb-item">
                                                            {{$their}}
                                                        </li>
                                                        @endforeach
                                                    </ol>
                                                @else
                                                    {{$rule['theirs'] == 1 ? trans('film.rule.true'):($rule['theirs']==0 ? trans('film.rule.false'):$rule['theirs'])}}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </table>
                            <h5 class="mt-5">{{trans('festival.HEADERS.entry_time')}}</h5>
                            <div>{{trans('festival.LABELS.entry_start')}}: {{$unit->open_at}}</div>
                            <div class="d-flex justify-content-between">
                                <div>{{trans('festival.LABELS.entry_end')}}:  {{$unit->due_at}}</div>
                                <div>
                                    @if(auth()->guest())
                                        <a href="/login" class="btn btn-outline-primary">{{trans('festival.BUTTONS.entry')}}</a>
                                    @else
                                        @if(time($unit->due_at) > time() || time($unit->open_at) <time())
                                            <button class="btn btn-light" disabled="">{{trans('festival.BUTTONS.entry')}}</button>
                                        @elseif(sizeof($films) === 0)
                                            <a href="/archive/creation" class="btn btn-primary">{{trans('festival.BUTTONS.entry')}}</a>
                                        @elseif(array_key_exists(1, $films))
                                            <a href="/festivals/{{$festival->short}}?unit={{$unit->name}}" class="btn btn-primary">{{trans('festival.BUTTONS.entry')}}</a>
                                        @elseif($films[0] === 1)
                                            <a href="/archives/0" class="btn btn-primary">{{trans('festival.BUTTONS.entry')}}</a>
                                        @else
                                            <a href="/archives/" class="btn btn-primary">{{trans('festival.BUTTONS.entry')}}</a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-floor" style="{{$index==0?'display: none':''}}">
                        <div class="p-3 d-flex justify-content-between ">
                            <div>{{trans('festival.LABELS.entry_end')}}:  {{$unit->due_at}}</div>
                            <div>
                                @if(auth()->guest())
                                    <a href="/login" class="btn">{{trans('festival.BUTTONS.entry')}}</a>
                                @else
                                    @if(time($unit->due_at) > time() || time($unit->open_at) <time())
                                        <span class="text-muted btn">{{trans('festival.BUTTONS.entry')}}</span>
                                    @elseif(sizeof($films) === 0)
                                        <a href="/archive/creation" class="btn">{{trans('festival.BUTTONS.entry')}}</a>
                                    @elseif(array_key_exists(1, $films))
                                        <a href="/festivals/{{$festival->short}}?unit={{$unit->name}}" class="btn">{{trans('festival.BUTTONS.entry')}}</a>
                                    @elseif($films[0] === 1)
                                        <a href="/archives/0" class="btn">{{trans('festival.BUTTONS.entry')}}</a>
                                    @else
                                        <a href="/archives/" class="btn">{{trans('festival.BUTTONS.entry')}}</a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </section>
        </div>
    </div>
@endsection
@section('script')
    <script src="/js/controllers/festival/detail.js"></script>
@endsection