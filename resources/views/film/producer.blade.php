@extends('film.card')

@section('filmForm')
    <form id="time_form" name="timeForm" action="/film/producer" method="post" ng-controller="filmCtrl" ng-init="init('{{$film->id}}', '{{$producers}}', '{{$film->school}}')">
        @include('film.form')
    <h4 class="header-slogan">{{trans('film.card.producer')}}</h4>
    <div class="alert alert-info" role="alert">
        <div>{!! trans('layout.ALERTS.compulsive') !!}</div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <label class="col-xs-4">
                {!! trans('film.label.school') !!}
            </label>
            <div class="col-xs-8 input-group">
               <span class="input-group-addon">
                    <input type="radio" name="school" value="1" ng-click="changeSchool(1)" {{$film->school == 1 ? "checked" : "" }}>
                </span>
                {{trans('layout.LABELS.yes')}}
                <span class="input-group-addon">
                    <input type="radio" name="school" value="0" ng-click="changeSchool(0)" {{!is_null($film->school) && $film->school == 0 ? "checked" : "" }}>
                </span>
                {{trans('layout.LABELS.no')}}
            </div>
        </div>
        <div class="col-sm-6 col-xs-12">
            <div class="input-group">
                <span class="input-group-addon">
                    {{trans('film.label.school_name')}}
                </span>
                <input type="text" name="school_name" id="school_name" class="form-text" value="{{is_null($film->school_name) ? "" : $film->school_name}}">
            </div>
        </div>
    </div>
        <div>
            <div class="row" ng-repeat="d in producers">
                <label><span ng-bind="d.prefix"></span>&nbsp;<span ng-bind="d.last_name"></span>&nbsp;<span ng-bind="d.first_name"></span></label>
                <div class="row">
                    <label class="col-xs-4">{{trans('personal.LABELS.company')}}</label>
                    <div class="col-xs-8"><span ng-bind="d.company"></span></div>
                </div>
                <div class="row">
                    <label class="col-xs-4">{{trans('personal.LABELS.address')}}</label>
                    <div class="col-xs-8"><span ng-bind="d.address"></span>&nbsp;<span ng-bind="d.zip"></span>&<span ng-bind="d.city"></span></div>
                </div>
                <div class="row">
                    <label class="col-xs-4">{{trans('personal.LABELS.tel')}}</label>
                    <div class="col-xs-8"><span ng-bind="d.tel"></span></div>
                </div>
                <div class="row">
                    <label class="col-xs-4">{{trans('personal.LABELS.mobile')}}</label>
                    <div class="col-xs-8"><span ng-bind="d.mobile"></span></div>
                </div>
                <div class="row">
                    <label class="col-xs-4">{{trans('personal.LABELS.email')}}</label>
                    <div class="col-xs-8"><span ng-bind="d.email"></span></div>
                </div>
                <div class="text-right">
                    <div ng-click="delete(d.id)" class="f fa-trash"></div>
                </div>
            </div>
        </div>
        <div maker="producer">
        <div class="fa fa-plus" ng-click="edit = 1"></div>
    <hr>
        <div ng-if="edit" >
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="form-group row">
                        <label for="director_name" class="col-sm-4 col-xs-6">{{trans('film.label.name_book')}}</label>
                        <div class="col-sm-8 col-xs-6">
                            <angucomplete-alt id="searchmaker" input-name="maker"
                                              placeholder="{{trans('film.placeholder.search')}}"
                                              pause="100"
                                              selected-object="makerSelected"
                                              local-data="makers"
                                              focus-in="makerFocus()"
                                              search-fields="first_name,last_name"
                                              title-field="last_name"
                                              description-field="first_name"
                                              minlength="1"
                                              clear-selected = "false"
                                              input-class="form-text"
                                              match-class="highlight"
                                              text-no-results="{{trans('layout.MENU.none')}}"
                                              text-searching="{{trans('layout.MENU.searching')}}"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="prefix" class="col-sm-4 col-xs-6">{{trans('film.label.prefix')}}<sup>*</sup></label>
                        <div class="col-sm-8 col-xs-6">
                            <select name="prefix" ng-model="maker.prefix">
                                @foreach(trans('personal.TITLES') as $key=>$title)
                                    <option value="{{$key}}">{{$title}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="first_name" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.first_name')}}<sup>*</sup></label>
                        <div class="col-sm-8 col-xs-6">
                            <input type="text" name="first_name" class="form-text"  ng-model="maker.first_name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="last_name" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.last_name')}}<sup>*</sup></label>
                        <div class="col-sm-8 col-xs-6">
                            <input type="text" name="last_name" class="form-text" ng-model="maker.last_name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="birthday" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.birthday')}}</label>
                        <div class="col-sm-8 col-xs-6">
                            <select name="birthday" ng-model="maker.born">
                                @for($y = $year - 14; $y > $year-130; $y--)
                                    <option value="{{$y}}">{{$y}}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tel" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.tel')}}</label>
                        <div class="col-sm-8 col-xs-6">
                            <input type="text" name="tel" class="form-text"  ng-model="maker.tel">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="mobile" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.mobile')}}</label>
                        <div class="col-sm-8 col-xs-6">
                            <input type="text" name="mobile" class="form-text"   ng-model="maker.mobile" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="email" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.email')}}</label>
                        <div class="col-sm-8 col-xs-6">
                            <input type="text" name="email" class="form-text"   ng-model="maker.email">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group row">
                        <label for="address_book" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.address_book')}}</label>
                        <div class="col-sm-8 col-xs-6">
                            <angucomplete-alt id="searchcontact" input-name="contact"
                                              placeholder="{{trans('film.placeholder.book')}}"
                                              pause="100"
                                              selected-object="contactSelected"
                                              local-data="contacts"
                                              focus-in="contactFocus()"
                                              search-fields="company,address"
                                              title-field="company"
                                              description-field="address"
                                              minlength="1"
                                              clear-selected = "false"
                                              input-class="form-text"
                                              match-class="highlight"
                                              text-no-results="{{trans('layout.MENU.none')}}"
                                              text-searching="{{trans('layout.MENU.searching')}}"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="address" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.company')}}</label>
                        <div class="col-sm-8 col-xs-6">
                            <input type="text" name="address" class="form-text" ng-model="maker.contact.company">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="address" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.address')}}<sup>*</sup></label>
                        <div class="col-sm-8 col-xs-6">
                            <input type="text" name="address" class="form-text" ng-model="maker.contact.address">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="code" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.code')}}<sup>*</sup></label>
                        <div class="col-sm-8 col-xs-6">
                            <input type="text" name="code" class="form-text" ng-model="maker.contact.zip">
                        </div>
                    </div>
                    <div location="film">
                        <div class="form-group row">
                            <label for="country" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.country')}}<sup>*</sup></label>
                            <div class="col-sm-8 col-xs-6">
                                <select name="country" ng-model="country_id" ng-change="loadDepart(country_id)">
                                    <option value="" disabled translate="location.country"></option>
                                    @foreach($countries as $country)
                                        <option value="{{$country->id}}">{{$country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="state" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.state')}}<sup>*</sup></label>
                            <div class="col-sm-8 col-xs-6">
                                <select class="form-control" ng-model="department_id" name="department_id"
                                        ng-options="d.id as d.name for d in departments"
                                        ng-change="loadCity(department_id)"
                                        ng-disabled="disabled.depart || disabled.city">
                                    <option value="" disabled translate="location.department"></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="city" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.city')}}<sup>*</sup></label>
                            <div class="col-sm-8 col-xs-6">
                                <select class="form-control" ng-model="maker.contact.city_id" name="city_id"
                                        ng-options="c.id as c.name for c in cities">
                                    <option value="" disabled translate="location.city"></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="web" class="col-sm-4 col-xs-6">{{trans('personal.LABELS.web')}}</label>
                        <div class="col-sm-8 col-xs-6">
                            <input type="text" name="web" class="form-text" ng-model="maker.web">
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <div class="btn btn-text-default" ng-click="cancel()"><span class="fa fa-undo"></span></div>
                &nbsp;
                <div class="btn btn-text-danger" ng-click="save()"><span class="fa fa-save"></span></div>
            </div>
        </div>
        </div>
    <hr/>
    <div class="text-right">
        <button class="btn btn-primary">{{trans('layout.BUTTONS.continue')}}</button>
    </div>
    </form>
@endsection
@section('script')
    <script src="/js/directives/location.js"></script>
    <script src="/js/directives/filmaker.js"></script>
    <script src="/js/controllers/film/producer.js"></script>
@endsection