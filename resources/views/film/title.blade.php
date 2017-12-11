@extends('film.card')

@section('filmForm')

    <form id="title_form" name="titleForm" action="/film/title" method="post" ng-controller="filmCtrl" ng-init="init('{{$titles}}')">
        @include('film.form')
    <h4 class="header-slogan">{{trans('film.card.title')}}</h4>
    <div class="alert alert-info" role="alert">
        <div>{!! trans('film.alert.title') !!}</div>
        <div>{!! trans('layout.ALERTS.compulsive') !!}</div>
    </div>
    <div class="form">
        <div class="form-group row">
            <label for="title_original" class="col-md-2 col-sm-3 col-xs-4 col-form-label text-right">{!! trans('film.label.title_original') !!}</label>
            <div class="col-md-8 col-sm-8 col-xs-8">
                <input class="form-text" type="text" value="{{$film->title}}"
                       placeholder="{{trans('film.placeholder.title_original')}}" id="title_original" name="title">
                @if ($errors->has('title'))
                    <div class="text-danger small">
                        {{trans("film.error.maxlength_title", ['cnt'=>80])}}
                    </div>
                @endif
            </div>
            <div class="col-md-2 col-sm-1"></div>
        </div>
        <div class="form-group row">
            <label for="title_latin" class="col-md-2 col-sm-3 col-xs-4 col-form-label text-right">{{trans('film.label.title_latin')}}</label>
            <div class="col-md-8 col-sm-8 col-xs-8">
                <input class="form-text" type="text" value="{{$film->title_latin}}"
                       placeholder="{{trans('film.placeholder.title_latin')}}" id="title_latin" name="title_latin">
                @if ($errors->has('title_latin'))
                    <div class="text-danger small">
                        {{trans("film.error.maxlength_title", ['cnt'=>80])}}
                    </div>
                @endif
            </div>
            <div class="col-md-2 col-sm-1"></div>
        </div>
        <div class="form-group row">
            <label for="title_international" class="col-md-2 col-sm-3 col-xs-4 col-form-label text-right">{{trans('film.label.title_inter')}}</label>
            <div class="col-md-8 col-sm-8 col-xs-8">
                <input class="form-text" type="text" value="{{$film->title_inter}}"
                       placeholder="{{trans('film.placeholder.title_inter')}}" id="title_iner" name="title_iner">
                @if ($errors->has('title_iner'))
                    <div class="text-danger small">
                        {{trans("film.error.maxlength_title", ['cnt'=>80])}}
                    </div>
                @endif
            </div>
            <div class="col-md-2 col-sm-1"></div>
        </div>
    </div>
    <hr>
    <div>
        <h5 class="header-slogan">{{trans('film.label.title_trans')}}</h5>
        <div class="form" id="trans_form" name="transForm">
            <div>
                <div class="row" ng-repeat="t in titles">
                    <input type="hidden" name="titles[<%t.language_id%>]" value="<%t.title%>" />
                    <label class="col-md-2 col-sm-12 col-xs-12"><span ng-bind="t.language">:</span></label>
                    <div class="col-md-8 col-sm-8 col-xs-12">
                        <span  ng-if="editTitle.language_id != t.language_id" ng-bind="t.title"></span>
                        <input ng-if="editTitle.language_id == t.language_id" type="text" ng-model="editTitle.title" class="form-text">
                    </div>
                    <div class="col-md-2 col-sm-2 col-xs-6">
                        <button class="btn btn-default"  ng-click="changeTitle(t)" ng-if="editTitle.language_id != t.language_id">
                            <span class="fa fa-edit"></span>
                        </button>
                        <button class="btn text-danger"  ng-click="deleteTitle(t)" ng-if="editTitle.language_id != t.language_id">
                            <span class="fa fa-trash-o"></span>
                        </button>
                        <button class="btn btn-default" ng-click="cancelTitle()" ng-if="editTitle.language_id == t.language_id">
                            <span class="fa fa-undo"></span>
                        </button>
                        <button class="btn btn-primary" ng-click="saveTitle(t)" ng-if="editTitle.language_id == t.language_id">
                            <span class="fa fa-save"></span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-xs-6">
                    <input class="form-text" type="text" value="" ng-model="newTitle" ng-maxlength="80"
                           PLACEHOLDER="{{trans('film.placeholder.title_trans')}}"
                           id="title_trans" name="title_trans">
                    <div role="alert" class="error text-right" ng-class="{'visible':title_form.title_iner.$dirty || error.title}">
                        <span ng-show="error.title < 2">
                            {{trans("film.error.require_title")}}
                        </span>
                        <span ng-show="title_form.title_trans.$error.maxlength || error.title > 1">
                            {{trans("film.error.maxlength_title", ['cnt'=>80])}}
                        </span>
                    </div>
                </div>
                <div class="col-xs-4">
                    <angucomplete-alt id="searchinput" input-name="search"
                                      placeholder="{{trans("film.place.lang")}}"
                                      pause="100"
                                      selected-object="langSelected"
                                      local-data="{{$languages}}"
                                      search-fields="name,original"
                                      title-field="name"
                                      description-field="original"
                                      minlength="1"
                                      input-class="form-text"
                                      match-class="highlight"
                                      text-no-results="{{trans('layout.MENU.none')}}"
                                      text-searching="{{trans('layout.MENU.searching')}}" />
                    <div role="alert" class="error text-right" ng-class="{'visible':error.lang}">
                        <span ng-show="error.lang < 2">
                            {{trans("film.error.require_title")}}
                        </span>
                        <span ng-show="error.lang > 1">
                            {{trans("film.error.maxlength_title", ['cnt'=>80])}}
                        </span>
                    </div>

                </div>
                <div class="col-xs-2"><div class="btn text-important fa fa-plus" ng-click="addTitle()"></div> </div>
            </div>
        </div>
    </div>
    <hr/>
    <div class="text-right">
        <button class="btn btn-primary" ng-click="save()">{{trans('layout.BUTTONS.continue')}}</button>
    </div>

    </form>
@endsection
@section('script')
    <script src="/js/controllers/film/title.js"></script>
@endsection