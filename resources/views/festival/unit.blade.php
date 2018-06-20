@extends('layouts.zoomov')

@section('content')
    <div class="container" ng-controller="festivalCtrl" ng-init="init('{{$related_id}}', '{{$films[0]->id}}')">
        <div class="text-right pt-5">
            <a class="badge" href="/festivals">{{trans("layout.MENU.festival_list")}}</a>
            <span class="px-1">/</span>
            <a class="badge" href="/entries">{{trans("layout.MENU.festival_inscription")}}</a>
            <span class="px-1">/</span>
            <a class="badge" href="/archives">{{trans("layout.MENU.films")}}</a>
            <span class="px-1">/</span>
            <a class="badge" href="/myfestivals">{{trans("layout.MENU.favorites")}}</a>
        </div>
        <div>
            <h1 ><a href="/festivals/{{$festival->short}}">{{$festival->name_locale?:$festival->name}}</a></h1>
            <div class="card">
                <div class="card-header bg-primary" id="heading1">
                    <h5 class="mb-0 text-white d-flex justify-content-between">
                        {{$unit->name_locale?:$unit->name}}[{{$unit->competition?trans('festival.COMPETITIONS.'.$unit->rank):trans('festival.SHOWS.'.$unit->rank)}}]
                    </h5>
                </div>
                <div class="card-body">
                    <div>{{$unit->presentation}}</div>
                </div>
                <div class="card-footer d-flex">
                    <div class="mr-5"><label>{{trans('festival.LABELS.entry_start')}}</label>: {{$unit->open_at}}</div>
                    <div class="mr-auto"><label>{{trans('festival.LABELS.entry_end')}}</label>:  {{$unit->due_at}}</div>
                    <a href="/storage/festivals/{{$year->festival_id}}/{{$year->session}}/{{app()->getLocale()}}.pdf" target="_blank" class="btn btn-outline-primary">{{trans('festival.BUTTONS.pdf')}}</a>
                </div>
            </div>
        </div>
        @if($rules && !is_null($rules->script))
            <div class="alert alert-info">{{$rules->script == 1 ? trans('film.rule.script'):trans('film.rule.movie')}}</div>
        @endif
        <div class="py-5">
            <h5 class="text-primary">{{trans('festival.LABELS.choose_film')}}</h5>
            <div class="text-center bg-white">
                <input id="filmChosen" type="hidden" name="film_id" />
                <div class="list-group">
                    <div class="btn-link border-0" ng-class="{'disabled':film_index==0}" ng-click="downFilmIndex()"><span class="fa fa-caret-up"></span></div>
                    @foreach($films as $index=>$film)
                        <button type="button" class="list-group-item list-group-item-action" ng-click="chooseFilm('{{$film->id}}')"
                                ng-class="{active:film_id == '{{$film->id}}'}"
                           id="film_{{$film->id}}" ng-hide="{{$index}} >= film_index + film_shown_count || {{$index}} < film_index">{{$film->title}}</button>
                    @endforeach
                    <div class="btn-link border-0" ng-class="{'disabled':film_index + film_shown_count>={{sizeof($films)}}}" ng-click="upFilmIndex({{sizeof($films)}})"><span class="fa fa-caret-down"></span></div>
                </div>
            </div>
            <div class="p-2" ng-if="rules.length == null || rules.length>0">
                <div class="alert alert-danger">
                    {{trans('festival.MESSAGES.rules_invalid')}}
                    <a href="/archives/<%film_id%>">{{trans('festival.MESSAGES.change_film')}}</a>
                </div>
                <div class="row border-bottom border-secondary">
                    <div class="col-lg-3 col-md-4 col-sm-6 border-right border-secondary"></div>
                    <div class="col-lg-5 col-md-4 col-sm-12 px-2 border-right border-secondary">
                        <h6 ng-bind="film_title"></h6>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 ">
                        <h6>{{trans('festival.LABELS.official_rules')}}</h6>
                    </div>
                </div>
                <div ng-repeat="(key, val) in rules">
                    <div class="row border-bottom border-secondary" ng-repeat="rule in val">
                        <div class="col-lg-3 col-md-4 col-sm-12 d-flex justify-content-between py-3 border-right border-secondary">
                            <a href="/archives/<%film_id%>?step=<%key%>" translate="film.movie.<%key%>"></a> <span translate="film.rule.<%rule['key']%>"></span>
                        </div>
                        <div class="col-lg-5 col-md-4 col-sm-12 py-3 px-2 border-right border-secondary">
                            <ol class="breadcrumb" ng-if="rule['condition']" style="padding:0">
                                <li class="breadcrumb-item" ng-repeat="(k, your) in rule['yours']">
                                    <span ng-if="findInArray(rule['diff'], k)>=0" class="text-danger" ng-bind="your"></span>
                                    <span ng-if="findInArray(rule['diff'], k)<0" ng-bind="your"></span>
                                </li>
                            </ol>
                            <span class="text-danger" ng-if="!rule['condition']" ng-switch="rule['yours']">
                                <span ng-switch-when="-1">{{trans('film.rule.null')}}</span>
                                 <span ng-switch-when="0">{{trans('film.rule.no')}}</span>
                                 <span ng-switch-when="1">{{trans('film.rule.yes')}}</span>
                                <span ng-switch-default ng-bind="rule['yours']"></span>
                            </span>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12 py-3 d-flex">
                            <label ng-if="rule['condition']"><span translate="film.rule.<%rule['condition']%>"></span>: </label>
                            <ol class="breadcrumb" style="padding:0">
                                <li class="breadcrumb-item"  ng-if="rule['condition']" ng-repeat="(k, their) in rule['theirs'] track by k">
                                    <span ng-if="findInArray(rule['diff'], k)>=0" class="text-danger" ng-bind="their"></span>
                                    <span ng-if="findInArray(rule['diff'], k)<0" ng-bind="their"></span>
                                </li>
                            </ol>
                            <span ng-switch="rule['theirs']" ng-if="!rule['condition']">
                                 <span ng-switch-when="0">{{trans('film.rule.false')}}</span>
                                 <span ng-switch-when="1">{{trans('film.rule.true')}}</span>
                                <span ng-switch-default ng-bind="rule['theirs']"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end pt-5">
                    <a href="/festivals/{{$festival->short}}" class="btn btn-outline-secondary mr-3">{{trans('layout.BUTTONS.back')}}</a>
                    <div class="btn btn-secondary">{{trans('layout.BUTTONS.next')}}</div>
                </div>
            </div>
            @if(session('contact', null))
                <div class="alert alert-danger">
                    <div>{{trans('film.alert.entry_contact')}}</div>
                    <div><a href="/personal?anchor=contact" class="text-danger">{{trans('festival.BUTTONS.complete_contact')}}</a></div>
                </div>
            @endif
            <form class="pt-5" ng-if="rules.length == 0" action="/festivals" method="POST">
                <div class="text-danger checkbox-inline checkbox-primary">
                    <input type="checkbox" name="term" ng-model="term" checked>
                    <label><i class="text-danger pr-1">*</i><span ng-class="{'text-danger':!term}">{{trans('festival.MESSAGES.entry_term')}}</span></label>
                </div>
                @if($errors->has('term'))
                    <div class="alert alert-danger">{{trans('festival.ERRORS.require_entry_term')}}</div>
                @endif
                <div class="d-flex justify-content-end">
                    <a href="/festivals/{{$festival->short}}" class="btn btn-outline-secondary mr-3">{{trans('layout.BUTTONS.back')}}</a>

                    <input type="hidden" ng-value="film_id" name="film_id">
                    <input type="hidden" value="{{$unit->id}}" name="unit_id">
                    <button type="submit" class="btn btn-primary" ng-disabled="!term">{{trans('layout.BUTTONS.next')}}</button>

                </div>
            </form>
        </div>
    </div>
@endsection
@section('script')
    <script src="/js/controllers/festival/unit.js"></script>
@endsection