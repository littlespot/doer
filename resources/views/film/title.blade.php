@extends('layouts.film')

@section('filmForm')
    <form id="title_form" name="filmForm" action="/{{$film->type}}s" method="POST" ng-controller="filmCtrl" ng-init="init('{{$titles}}')">
        {{csrf_field()}}
        <input type="hidden" name="id" value="{{$film->id}}" />
        <input type="hidden" name="step" value="{{$step+1}}" />
        <div class="modal fade" id="deleteTitleModal" tabindex="-1" role="dialog" aria-labelledby="deleteTitleModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center" id="modal-body">
                        <div>{{trans('film.alert.delete_title')}}<span class="pl-1 text-primary" ng-bind="titleToDelete.language"></span> </div>
                    </div>
                    <div class="modal-footer d-flex px-5">
                        <button class="btn btn-danger mr-auto" type="button" data-dismiss="modal" >
                            {{trans("project.BUTTONS.cancel")}}
                        </button>
                        <button class="btn btn-primary" type="button" ng-click="titleDeleted('{{$film->id}}')" >
                            {{trans("project.BUTTONS.confirm")}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <ul class="alert alert-dark text-primary small px-5" role="alert">
            <li>{!! trans('film.alert.title') !!}</li>
            <li>{!! trans('layout.ALERTS.compulsive') !!}</li>
        </ul>
        <div class="py-5">
            <div class="form-group row">
                <label class="col-lg-2 col-md-4 col-sm-12 label-justified required">
                    {{trans('film.label.title_original')}}
                </label>
                <div class="col-lg-10 col-md-8 col-sm-12 input input--isao">
                    <input class="input__field input__field--isao" type="text" id="title_original" name="title"
                           placeholder="{{old('title')?:$film->title}}"
                           value="{{old('title')?:$film->title}}"
                           ng-maxlength="80" required/>
                    <label class="input__label input__label--isao" for="title_original"
                           ng-class="{'isao_error':filmForm.title_original.$error}"
                           data-error="{{trans('film.error.require_title')}}"
                           data-content="{{trans('film.placeholder.title_original')}}">
                        <span class="input__label-content input__label-content--isao">{{trans('film.placeholder.title_original')}}</span>
                    </label>
                    @if ($errors->has('title'))
                        <div class="text-danger small">
                            {{trans("film.error.maxlength_title", ['cnt'=>80])}}
                        </div>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-2 col-md-4 col-sm-12 label-justified">
                    {{trans('film.label.title_latin')}}
                </label>
                <div class="col-lg-10 col-md-8 col-sm-12 input input--isao">
                    <input class="input__field input__field--isao" type="text" id="title_latin" name="title_latin"
                           placeholder="{{old('title_latin')?:$film->title_latin}}"
                           value="{{old('title_latin')?:$film->title_latin}}"
                           ng-maxlength="80"/>
                    <label class="input__label input__label--isao" for="title_latin"
                           ng-class="{'isao_error':filmForm.title_latin.$error}"
                           data-error="{{trans('film.error.maxlength_title', ['cnt'=>80])}}"
                           data-content="{{trans('film.placeholder.title_latin')}}">
                        <span class="input__label-content input__label-content--isao">{{trans('film.placeholder.title_latin')}}</span>
                    </label>
                    <div class="col-2">
                        @if ($errors->has('title_latin'))
                            <div class="text-danger small">
                                {{trans("film.error.maxlength_title", ['cnt'=>80])}}
                            </div>
                        @endif
                    </div>
                </div>

            </div>
            <div class="form-group row">
                <label class="col-lg-2 col-md-4 col-sm-12 label-justified">
                    {{trans('film.label.title_inter')}}
                </label>
                <div class="col-lg-10 col-md-8 col-sm-12 input input--isao">
                    <input class="input__field input__field--isao" type="text" id="title_inter" name="title_inter"
                           placeholder="{{old('title_inter')?:$film->title_inter}}"
                           value="{{old('title_inter')?:$film->title_inter}}"
                           ng-maxlength="80"/>
                    <label class="input__label input__label--isao" for="title_inter"
                           ng-class="{'isao_error':filmForm.title_inter.$error}"
                           data-error="{{trans('film.error.maxlength_title', ['cnt'=>80])}}"
                           data-content="{{trans('film.placeholder.title_inter')}}">
                        <span class="input__label-content input__label-content--isao">{{trans('film.placeholder.title_inter')}}</span>
                    </label>
                    <div class="col-10">
                        @if ($errors->has('title_latin'))
                            <div class="text-danger small">
                                {{trans("film.error.maxlength_title", ['cnt'=>80])}}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="language_id" ng-model="selectedLang" />
        <div class="form-group row" ng-init="newTitle={language_id:'{{old('lang_trans')}}', title:'{{old("title_trans")}}'}">
            <label class="col-lg-2 col-md-8 col-sm-12 label-justified">{{trans('film.label.title_trans')}}</label>
            <div class="col-lg-3 col-md-12 input input--isao">
                <select class="input__field input__field--isao" name="lang_trans" id="lang_trans" ng-model="newTitle.language_id">
                    @foreach($languages as $language)
                        <option value="{{$language->id}}"
                                ng-disabled="(titles|filter:{language_id:{{$language->id}}}).length"
                                ng-selected="newTitle.language_id == {{$language->id}}">
                            {{$language->name}}
                        </option>
                    @endforeach
                </select>
                <label class="input__label input__label--isao" for="lang_trans"
                       ng-class="{'isao_error':!newTitle.language_id}"
                       data-error="{{trans('film.error.require_language_trans')}}"
                       data-content="{{trans("film.placeholder.language")}}">
                    <span class="input__label-content input__label-content--isao">{{trans("film.placeholder.language")}}</span>
                </label>
                <div role="alert" class="error text-right" ng-class="{'visible':error.lang}">
                    <span ng-show="error.lang < 2">
                        {{trans("film.error.require_title")}}
                    </span>
                    <span ng-show="error.lang > 1">
                        {{trans("film.error.maxlength_title", ['cnt'=>80])}}
                    </span>
                </div>
            </div>
            <div class="col-lg-6 col-md-10 input input--isao">
                <input class="input__field input__field--isao" type="text" id="title_trans" name="title_trans"
                       ng-model="newTitle.title" ng-maxlength="80"/>
                <label class="input__label input__label--isao" for="title_trans"
                       ng-class="{'isao_error':!newTitle.title || newTitle.title.length>80}"
                       data-error="{{trans('film.error.maxlength_title', ['cnt'=>80])}}"
                       data-content="{{trans("film.placeholder.title_trans_ex")}}">
                    <span class="input__label-content input__label-content--isao">{{trans("layout.ALERTS.plusToInput")}}</span>
                </label>
            </div>
            <div class="col-lg-1 col-md-2">
                <span class="btn text-primary fa fa-plus" ng-disabled="!newTitle.language_id || !newTitle.title || newTitle.title.length>80" ng-click="addTitle('{{$film->id}}')"></span>
            </div>
        </div>
        <div class="text-primary row"  ng-repeat="t in titles">
            <label class="col-lg-2 col-md-8 col-sm-12 label-justified"></label>
            <label class="col-lg-3 col-md-12"><span ng-bind="t.language">:</span></label>
            <div class="col-lg-6 col-md-10 col-sm-12">
                <span  ng-if="editTitle.language_id != t.language_id" ng-bind="t.title"></span>
                <input ng-if="editTitle.language_id == t.language_id" type="text" ng-model="editTitle.title" class="form-control">
            </div>
            <div class="col-lg-1 col-md-2 col-sm-12 text-right btn-group">
                <span class="btn"  ng-click="changeTitle(t)" ng-if="editTitle.language_id != t.language_id">
                    <span class="fa fa-edit"></span>
                </span>
                <span class="btn text-danger"  ng-click="deleteTitle(t)" ng-if="editTitle.language_id != t.language_id">
                    <span class="fa fa-trash-o"></span>
                </span>
                <span class="btn text-danger" ng-click="cancelTitle()" ng-if="editTitle.language_id == t.language_id">
                    <span class="fa fa-undo"></span>
                </span>
                <span class="btn btn-primary" ng-click="saveTitle('{{$film->id}}', t)" ng-if="editTitle.language_id == t.language_id">
                    <span class="fa fa-save"></span>
                </span>
            </div>
        </div>
        <hr/>
        <div class="d-flex justify-content-between">
            <div class="btn btn-outline-danger" data-toggle="modal" data-target="#deleteFilmModal">{{trans('film.buttons.delete')}}</div>
            <button class="btn btn-primary" ng-click="save()">{{trans('layout.BUTTONS.continue')}}</button>
        </div>
    </form>
@endsection
@section('script')
    <script src="/js/controllers/film/title.js"></script>
@endsection