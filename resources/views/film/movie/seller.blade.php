@extends('layouts.film')

<style>
    .table-bordered thead{
        border-bottom: #293a4f 3px solid;
    }
</style>
@section('filmForm')
    <form name="filmForm" action="/{{$film->type}}s" method="POST" id="filmForm"
          ng-controller="filmCtrl" ng-init="init()">
        {{csrf_field()}}
        <input type="hidden" name="id" value="{{$film->id}}" />
        <input type="hidden" name="step" value="{{$step+1}}" />

        <div class="modal fade bd-example-modal-lg" id="sellerConfirmModal" tabindex="-1" role="dialog" aria-labelledby="sellerConfirmModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <h5 class="modal-header">
                        {{trans('film.header.remove_history')}}
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </h5>
                    <div class="modal-body px-5 py-3">
                        <div>{{trans('film.alert.remove_history')}}</div>
                        <ul>
                            <li class="py-1" ng-if="confirmation.festivals">{{trans('film.header.delete_festival')}}</li>
                            <li ng-if="confirmation.diffusion">{{trans('film.header.delete_diffusion')}}</li>
                            <li class="py-1" ng-if="confirmation.theaters">{{trans('film.header.delete_theater')}}</li>
                        </ul>
                        <div class="alert alert-danger">{{trans('film.alert.delete_history')}}</div>
                    </div>
                    <div class="modal-footer d-flex px-5">
                        <button class="btn btn-outline-danger mr-auto" type="button" data-dismiss="modal" >
                            {{trans("project.BUTTONS.cancel")}}
                        </button>
                        <button class="btn btn-primary" type="button" ng-click="submit()">
                            {{trans("project.BUTTONS.confirm")}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade bd-example-modal-lg" id="festivalNewModal" tabindex="-1" role="dialog" aria-labelledby="festivalNewModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        {{trans('film.header.add_festival')}}
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body px-5 py-3" id="modal-body">
                        <div class="form-group row">
                            <div class="col-lg-4 col-md-12 input input--isao">
                                <select class="input__field input__field--isao" id="festival_year" ng-model="history.year">
                                    @for($y = $year; $y > $year -100; $y--)
                                        <option value="{{$y}}" ng-selected="history.year == {{$y}}">{{$y}}</option>
                                    @endfor
                                </select>
                                <label class="input__label input__label--isao" for="festival_year"
                                       data-content="{{trans('film.placeholder.year')}}">
                                    <span class="input__label-content input__label-content--isao">
                                        <i class="text-danger">*</i>
                                        <span ng-if="history.year"> {{trans('film.label.year')}}</span>
                                        <span ng-if="!history.year" class="text-danger">{{trans('film.error.require_festival_year')}}</span>
                                    </span>
                                </label>
                            </div>
                            <div class="col-lg-8 col-md-12 input input--isao">
                                <input type="text" id="festival_event" name="event" class="input__field input__field--isao" ng-model="history.event">
                                <label class="input__label input__label--isao" for="festival_event"
                                       data-content="{{trans('film.placeholder.event')}}">
                                    <span class="input__label-content input__label-content--isao">
                                         <i class="text-danger">*</i>
                                        <span ng-if="history.event && history.event.length <= 40"> {{trans('film.label.event')}}</span>
                                        <span ng-if="!history.event || history.event.length > 40" class="text-danger">{{trans('film.error.require_festival_event')}}</span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-4 col-md-12 input input--isao">
                                <select class="input__field input__field--isao" id="festival_country" ng-model="history.country_id"
                                        ng-change="loadDepartmet(history)">
                                    @foreach($countries->where('region', '<>', 1) as $country)
                                        <option id="f_country_{{$country->id}}" value="{{$country->id}}" ng-selected="history.country_id == {{$country->id}}">
                                            {{$country->name}}
                                        </option>
                                    @endforeach
                                </select>
                                <label class="input__label input__label--isao" for="festival_country" data-content="{{trans('film.placeholder.country')}}">
                                    <span class="input__label-content input__label-content--isao">
                                         <i class="text-danger">*</i>
                                        <span ng-if="history.country_id">{{trans('personal.LABELS.country')}}</span>
                                        <span ng-if="!history.country_id" class="text-danger">{{trans('film.error.require_festival_country')}}</span>
                                    </span>
                                </label>
                            </div>
                            <div class="col-lg-4 col-md-12 input input--isao">
                                <select id="festival_depart" class="input__field input__field--isao" ng-model="history.department_id"
                                        ng-options="x.id as x.name for x in contactDepartments"
                                        ng-change="loadCity(history)"
                                        ng-disabled="disabled.depart || disabled.city">
                                </select>
                                <label class="input__label input__label--isao" for="festival_depart" data-content="{{trans('personal.LABELS.state')}}">
                                    <span class="input__label-content input__label-content--isao">
                                         <i class="text-danger">*</i>
                                        <span ng-if="history.department_id">{{trans('personal.LABELS.state')}}</span>
                                        <span ng-if="!history.department_id" class="text-danger">{{trans('film.error.require_festival_department')}}</span>
                                    </span>
                                </label>
                            </div>
                            <div class="col-lg-4 col-md-12 input input--isao">
                                <select id="festival_city" class="input__field input__field--isao" ng-model="history.city_id" name="history_city"
                                        ng-options="c.id as c.name for c in contactCities">
                                </select>
                                <label class="input__label input__label--isao" for="history_city"
                                       data-content="{{trans('personal.LABELS.city')}}">
                                    <span class="input__label-content input__label-content--isao">
                                        <i class="text-danger">*</i>
                                        <span ng-if="history.city_id">{{trans('personal.LABELS.city')}}</span>
                                        <span ng-if="!history.city_id" class="text-danger">{{trans('film.error.require_festival_city')}}</span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-md-4 col-sm-12">
                                {{trans('film.label.award')}}
                            </label>
                            <div class="col-lg-10 col-md-8 col-sm-12">
                                <div class="btn btn-block btn-outline-primary mb-3" ng-click="addReward()">
                                    {{trans('film.buttons.add_reward')}}
                                </div>
                                <div ng-repeat="r in history.rewards">
                                    <div class="row" ng-if="!r.edited">
                                        <div class="col-lg-6 col-md-12" ng-bind="r.name"></div>
                                        <div class="col-lg-4 col-md-12"><span ng-if="r.competition" class="badge bage-info">{{trans('film.label.competition')}}</span></div>
                                        <div class="col-lg-2 col-md-12 text-right  btn-group">
                                            <span class="btn text-primary fa fa-edit" ng-click="editReward(r)"></span>
                                            <span class="btn text-danger fa fa-trash" ng-click="removeValue(history.rewards, r.id)"></span>
                                        </div>
                                    </div>
                                    <div class="row" ng-if="r.edited">
                                        <div class="col-lg-6 col-md-12">
                                            <input class="input__field input__field--isao" id="festival_reward_name" name="festival_reward_name" ng-model="r.name" />
                                            <label class="input__label input__label--isao" for="festival_reward_name"
                                                   ng-class="{'isao_error':errors.reward || r.name.length == 0 || r.name.length > 80}"
                                                   data-content="{{trans('film.placeholder.reward')}}"
                                                   data-error="{{trans('film.error.reward')}}">
                                                <span class="input__label-content input__label-content--isao">{{trans('film.label.reward')}}</span>
                                            </label>
                                        </div>
                                        <div class="col-lg-4 col-md-12 checkbox-inline checkbox-primary">
                                            <input type="checkbox" value="1" name="festival_reward_competition" ng-model="r.competition">
                                            {{trans('film.label.competition')}}
                                        </div>
                                        <div class="col-lg-2 col-md-12 text-right btn-group">
                                            <span class="btn text-danger fa fa-undo" ng-click="cancelReward(r)"></span>
                                            <span class="btn text-primary fa fa-check" ng-click="saveReward(r)"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-danger" ng-if="errors.festival" ng-bind="errors.festival"></div>
                    </div>
                    <div class="modal-footer d-flex px-5">
                        <button class="btn btn-outline-danger mr-auto" type="button" data-dismiss="modal" >
                            {{trans("project.BUTTONS.cancel")}}
                        </button>
                        <button class="btn btn-primary" type="button" ng-click="historySaved('{{$film->id}}')" ng-disabled="!history.year || !history.city_id || !history.event || history.event.length > 40">
                            {{trans("project.BUTTONS.confirm")}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade bd-example-modal-lg" id="diffusionNewModal" tabindex="-1" role="dialog" aria-labelledby="diffusionNewModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        {{trans('film.header.add_diffusion')}}
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body px-5 py-3" id="modal-body">
                        <div class="form-group row">
                            <div class="col-lg-4 col-md-12 input input--isao">
                                <select class="input__field input__field--isao" id="diffusion_channel" ng-model="history.channel">
                                    @foreach(trans('film.channel') as $key=>$channel)
                                        <option value="{{$key}}" ng-selected="history.channel == '{{$key}}'">{{$channel}}</option>
                                    @endforeach
                                </select>
                                <label class="input__label input__label--isao" for="festival_year"
                                       data-content="{{trans('film.placeholder.channel')}}">
                                    <span class="input__label-content input__label-content--isao">
                                        <i class="text-danger">*</i>
                                        <span ng-if="history.channel">{{trans('film.label.channel')}}</span>
                                        <span ng-if="!history.channel" class="text-danger">{{trans('film.error.require_diffusion_channel')}}</span>
                                    </span>
                                </label>
                            </div>
                            <div class="col-lg-4 col-md-12 input input--isao">
                                <select class="input__field input__field--isao" id="diffusion_year" ng-model="history.year">
                                    @for($y = $year; $y > $year -100; $y--)
                                        <option value="{{$y}}" ng-selected="history.year == {{$y}}">{{$y}}</option>
                                    @endfor
                                </select>
                                <label class="input__label input__label--isao required" for="diffusion_year"
                                       data-content="{{trans('film.placeholder.year')}}">
                                    <span class="input__label-content input__label-content--isao">
                                        {{trans('film.label.year')}}
                                    </span>
                                </label>
                            </div>
                            <div class="col-lg-4 col-md-12 input input--isao">
                                <select class="input__field input__field--isao" id="diffusion_country" ng-model="history.country_id">
                                    @foreach($countries as $country)
                                        <option id="f_country_{{$country->id}}" value="{{$country->id}}" ng-selected="history.country_id == {{$country->id}}">
                                            {{$country->name}}
                                        </option>
                                    @endforeach
                                </select>
                                <label class="input__label input__label--isao" for="diffusion_country"
                                       data-content="{{trans('film.placeholder.country')}}">
                                      <span class="input__label-content input__label-content--isao">
                                        <i class="text-danger">*</i>
                                        <span ng-if="history.country_id">{{trans('personal.LABELS.country')}}</span>
                                        <span ng-if="!history.country_id" class="text-danger">{{trans('film.error.require_diffusion_country')}}</span>
                                      </span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input input--isao">
                                <input type="text" id="diffusion_name" name="name" class="input__field input__field--isao" ng-model="history.name">
                                <label class="input__label input__label--isao required" for="diffusion_year"
                                       data-content="{{trans('film.placeholder.diffusion_name')}}">
                                    <span class="input__label-content input__label-content--isao">
                                        <i class="text-danger">*</i>
                                        <span ng-if="history.name && history.name.length <= 40">{{trans('film.label.diffusion_name')}}</span>
                                        <span ng-if="!history || !history.name || history.name.length > 40" class="text-danger">{{trans('film.error.require_diffusion_name')}}</span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="alert alert-danger" ng-if="errors.diffusion" ng-bind="errors.diffusion"></div>
                    </div>
                    <div class="modal-footer d-flex px-5">
                        <button class="btn btn-outline-danger mr-auto" type="button" data-dismiss="modal" >
                            {{trans("project.BUTTONS.cancel")}}
                        </button>
                        <button class="btn btn-primary" type="button" ng-click="historySaved('{{$film->id}}')" ng-disabled="!history.country_id || !history.channel || !history.name || history.name.length > 40">
                            {{trans("project.BUTTONS.confirm")}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade bd-example-modal-lg" id="theaterNewModal" tabindex="-1" role="dialog" aria-labelledby="theaterNewModalModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        {{trans('film.header.add_theater')}}
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body px-5 py-3" id="modal-body">
                        <div class="form-group row">
                            <div class="col-lg-4 col-md-12 input input--isao">
                                <select class="input__field input__field--isao" id="theater_program" ng-model="history.program">
                                    @foreach(trans('film.program') as $key=>$program)
                                        <option value="{{$key}}" ng-selected="history.program == '{{$key}}'">{{$program}}</option>
                                    @endforeach
                                </select>
                                <label class="input__label input__label--isao" for="theater_program"
                                       data-content="{{trans('film.placeholder.theater_program')}}">
                                    <span class="input__label-content input__label-content--isao">
                                        <i class="text-danger">*</i>
                                        <span ng-if="history.program">{{trans('film.label.theater_program')}}</span>
                                        <span ng-if="!history.program" class="text-danger">{{trans('film.error.require_theater_program')}}</span>
                                    </span>
                                </label>
                            </div>
                            <div class="col-lg-4 col-md-12 input input--isao">
                                <select class="input__field input__field--isao" id="theater_year" ng-model="history.year">
                                    @for($y = $year; $y > $year -100; $y--)
                                        <option value="{{$y}}" ng-selected="history.year == {{$y}}">{{$y}}</option>
                                    @endfor
                                </select>
                                <label class="input__label input__label--isao" for="theater_year"
                                       data-content="{{trans('film.placeholder.year')}}">
                                    <span class="input__label-content input__label-content--isao">{{trans('film.label.year')}}</span>
                                </label>
                            </div>
                            <div class="col-lg-4 col-md-12 input input--isao">
                                <select class="input__field input__field--isao" id="theater_country" ng-model="history.country_id">
                                    @foreach($countries as $country)
                                        <option id="f_country_{{$country->id}}" value="{{$country->id}}" ng-selected="history.country_id == {{$country->id}}">
                                            {{$country->name}}
                                        </option>
                                    @endforeach
                                </select>
                                <label class="input__label input__label--isao" for="theater_country"
                                       data-content="{{trans('film.placeholder.country')}}">
                                      <span class="input__label-content input__label-content--isao">
                                        <i class="text-danger">*</i>
                                        <span ng-if="history.country_id">{{trans('personal.LABELS.country')}}</span>
                                        <span ng-if="!history.country_id" class="text-danger">{{trans('film.error.require_theater_country')}}</span>
                                      </span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input input--isao">
                                <input type="text" id="theater_distribution" name="distribution" class="input__field input__field--isao" ng-model="history.distribution">
                                <label class="input__label input__label--isao" for="theater_distribution"
                                       data-content="{{trans('film.placeholder.theater_distribution')}}">
                                    <span class="input__label-content input__label-content--isao">
                                        <i class="text-danger">*</i>
                                        <span ng-if="history.distribution && history.distribution.length <= 40">{{trans('film.label.theater_distribution')}}</span>
                                        <span ng-if="!history.distribution || history.distribution.length > 40" class="text-danger">{{trans('film.error.require_theater_distribution')}}</span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input input--isao">
                                <textarea type="text" id="theater_contact" name="theater_contact" class="input__field input__field--isao" ng-model="history.contact">
                                </textarea>
                                <label class="input__label input__label--isao" for="theater_contact"
                                       data-content="{{trans('film.placeholder.theater_contact')}}">
                                    <span class="input__label-content input__label-content--isao">
                                        <span ng-if="history.theater_contact.length <= 200">{{trans('film.label.theater_contact')}}</span>
                                        <span class="text-danger" ng-if="!history.theater_contact || history.theater_contact.length > 200">{{trans('film.error.require_theater_contact')}}</span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="alert alert-danger" ng-if="errors.theater" ng-bind="errors.theater"></div>
                    </div>
                    <div class="modal-footer d-flex px-5">
                        <button class="btn btn-outline-danger mr-auto" type="button" data-dismiss="modal" >
                            {{trans("project.BUTTONS.cancel")}}
                        </button>
                        <button class="btn btn-primary" type="button" ng-click="historySaved('{{$film->id}}')" ng-disabled="!history.country_id || !history.program || !history.distribution  || history.distribution.length > 40 || history.contact.length > 200">
                            {{trans("project.BUTTONS.confirm")}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade bd-example-modal-lg" id="historyDeleteModal" tabindex="-1" role="dialog" aria-labelledby="historyDeleteModal" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <span ng-switch="historyToDelete.name">
                            <span ng-switch-when="festival"> {{trans('film.header.delete_festival')}}</span>
                            <span ng-switch-when="diffusion"> {{trans('film.header.delete_diffusion')}}</span>
                             <span ng-switch-when="theater"> {{trans('film.header.delete_theater')}}</span>
                        </span>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body px-5 py-3" id="modal-body">
                        <div>{{trans('film.alert.delete_history')}}</div>
                    </div>
                    <div class="modal-footer d-flex px-5">
                        <button class="btn btn-outline-danger mr-auto" type="button" data-dismiss="modal" >
                            {{trans("project.BUTTONS.cancel")}}
                        </button>
                        <button class="btn btn-primary" type="button" ng-click="historyDeleted('{{$film->id}}')">
                            {{trans("project.BUTTONS.confirm")}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <ul class="alert alert-dark text-primary small px-5" role="alert">
            <li>{!! trans('film.alert.history') !!}</li>
            <li class="pt-1">{!! trans('layout.ALERTS.compulsive') !!}</li>
        </ul>
        <br/>
        <div class="form-group row">
            <label class="{{app()->getLocale() == 'zh'? 'col-lg-3 label-justified' : 'col-lg-6' }} col-md-6 col-sm-12 required">
                {{trans('film.label.music_rights')}}
            </label>
            <div class="col-lg-3 col-md-3 col-sm-12 radio-inline">
                <input type="radio" name="music_rights" value="1" {{$film->music_rights  === 1 ? "checked" : ""}} />
                {{trans('layout.LABELS.yes')}}
            </div>
            <div class="{{app()->getLocale() == 'zh'? 'col-lg-6' : 'col-lg-3' }} col-md-3 col-sm-12 radio-inline">
                <input type="radio" name="music_rights" value="0" {{$film->music_rights  === 0 ? "checked" : ""}} />
                {{trans('layout.LABELS.no')}}
            </div>
        </div>
        <div class="form-group row">
            <label class="{{app()->getLocale() == 'zh'? 'col-lg-3 label-justified' : 'col-lg-6' }} col-md-6 col-sm-12 required">
                {{trans('film.label.film_rights')}}
            </label>
            <div class="col-lg-3 col-md-3 col-sm-12 radio-inline">
                <input type="radio" name="inter_rights" value="1" {{$film->inter_rights  === 1 ? "checked" : ""}} />
                {{trans('layout.LABELS.yes')}}
            </div>
            <div class="{{app()->getLocale() == 'zh'? 'col-lg-6' : 'col-lg-3' }} col-md-3 col-sm-12 radio-inline">
                <input type="radio" name="inter_rights" value="0" {{$film->inter_rights  === 0 ? "checked" : ""}} />
                {{trans('layout.LABELS.no')}}
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-3 col-md-4 col-sm-12 label-justified required">
                {{trans('film.label.seller')}}
            </label>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="btn btn-block btn-outline-primary" ng-click="chooseMaker()">{{trans('film.label.another_maker')}}</div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="btn btn-block btn-outline-primary" ng-click="createMaker()">{{trans('film.label.add_maker')}}</div>
            </div>
        </div>
        <div class="row">
            <label class="col-lg-3"></label>
            <div class="col-lg-9 col-md-12">
                @include('film.templates.maker', ['position'=>'seller'])
            </div>
        </div>
        <br/>
        <div class="checkbox-primary checkbox-inline" ng-init="in_festivals = {{sizeof($festivals)}} > 0?true:false" >
            <input type="checkbox" ng-model="in_festivals" id="in_festivals" name="festivals"/>
            <label>{{trans('film.label.history_festival')}}</label>
        </div>

        <table id="tb_festivals" class="table table-bordered" ng-if="in_festivals">
            <thead>
                <tr>
                    <th width="80px">{{trans('film.label.year')}}<sup class="text-danger">*</sup></th>
                    <th >{{trans('film.label.event')}}<sup class="text-danger">*</sup></th>
                    <th width="150px">{{trans('personal.LABELS.city')}}<sup class="text-danger">*</sup></th>
                    <th width="200px">{{trans('film.label.award')}}</th>
                    <th width="20px" class="text-right"><div class="btn text-info fa fa-plus" ng-click="editHistory('festival')"></div></th>
                </tr>
            </thead>
            <tbody>
            @foreach($festivals as $f)
                <tr id="festivals_{{$f->id}}">
                    <td>{{$f->year}}</td>
                    <td>{{$f->event}}</td>
                    <td>{{$f->city}}<br/>({{$f->country}})</td>
                    <td>
                        @foreach($f->rewards as $reward)
                            <div>@if($reward->competition)<span class="mr-2 badge badge-info">{{trans('film.label.competition')}}</span>@endif{{$reward->name}}</div>
                        @endforeach
                    </td>
                    <td valign="middle" class="text-right">
                        <span class="btn fa fa-edit text-primary" ng-click="editHistory('festival', '{{$f}}')"></span>
                        <span class="btn fa fa-trash text-danger" ng-click="deleteHistory('festival', '{{$f->id}}')"></span>
                    </td>
                </tr>
            @endforeach
            <tr ng-repeat="f in festivals" id="festival_<%$f.id%>">
                <td><span ng-bind="f.year"></span></td>
                <td><span ng-bind="f.event"></span></td>
                <td><span ng-bind="f.city"></span><br/>(<span ng-bind="f.country"></span>)</td>
                <td>
                    <div ng-repeat="r in f.rewards">
                        <span ng-if="r.competition" class="mr-2 badge badge-info">{{trans('film.label.competition')}}</span>
                        <span ng-bind="r.name"></span>
                    </div>
                </td>
                <td class="text-right">
                    <span class="btn fa fa-edit text-primary" ng-click="editHistory('festival', f, true)"></span>
                    <span class="btn fa fa-trash text-danger" ng-click="deleteHistory('festival', f.id)"></span>
                </td>
            </tr>

            </tbody>
        </table>
        <br/>

        <div class="checkbox-primary checkbox-inline" ng-init="in_diffusion = {{sizeof($diffusion)}} > 0 ? true:false" >
            <input type="checkbox" ng-model="in_diffusion" id="in_diffusion" name="diffusion"/>
            <label>{{trans('film.label.history_tv')}}</label>
        </div>
        <table id="tb_diffusion" class="table table-bordered" ng-if="in_diffusion">
            <thead>
                <tr>
                    <th>{{trans('film.label.channel')}}<sup class="text-danger">*</sup></th>
                    <th>{{trans('film.label.name_tv')}}<sup class="text-danger">*</sup></th>
                    <th>{{trans('film.label.country')}}</th>
                    <th>{{trans('film.label.year')}}</th>
                    <th width="20px" class="text-right"><div class="btn text-info fa fa-plus" ng-click="editHistory('diffusion')"></div></th>
                </tr>
            </thead>
            <tbody>
            @foreach($diffusion as $d)
                <tr id="diffusions_{{$d->id}}">
                    <td>{{trans('film.channel.'.$d->channel)}}</td>
                    <td>{{$d->name}}</td>
                    <td>{{$d->country}}</td>
                    <td>{{$d->year}}</td>
                    <td class="text-right">
                        <span class="btn fa fa-edit text-primary" ng-click="editHistory('diffusion', '{{json_encode($d)}}')"></span>
                        <span class="btn fa fa-trash text-danger" ng-click="deleteHistory('diffusion', '{{$d->id}}')"></span>
                    </td>
                </tr>
            @endforeach
                <tr ng-repeat="d in diffusions" id="diffusion_<%d.id%>">
                    <td><span ng-bind="d.channel_name"></span></td>
                    <td><span ng-bind="d.name"></span></td>
                    <td><span ng-bind="d.country"></span></td>
                    <td><span ng-bind="d.year"></span></td>
                    <td class="text-right">
                        <span class="btn fa fa-edit text-primary" ng-click="editHistory('diffusion', d, true)"></span>
                        <span class="btn fa fa-trash text-danger" ng-click="deleteHistory('diffusion', d.id)"></span>
                    </td>
                </tr>
            </tbody>
        </table>
        <br/>

        <div class="checkbox-primary checkbox-inline" ng-init="in_theater = {{sizeof($theaters)}} > 0? true:false" >
            <input type="checkbox" ng-model="in_theater" id="in_theater" name="theaters"/>
            <label>{{trans('film.label.history_theatre')}}</label>
        </div>
        <table id="tb_theaters" class="table table-bordered" ng-if="in_theater">
            <thead>
                <tr>
                    <th width="100px">{{trans('film.label.theater_program')}}<sup class="text-danger">*</sup></th>
                    <th width="200px">{{trans('film.label.theater_distribution')}}<sup class="text-danger">*</sup></th>
                    <th width="100px">{{trans('film.label.country')}}</th>
                    <th>{{trans('film.label.contact')}}</th>
                    <th width="80px">{{trans('film.label.year')}}</th>
                    <th width="20px" class="text-right "><div class="btn text-info fa fa-plus" ng-click="editHistory('theater')"></div> </th>
                </tr>
            </thead>
            <tbody>
            @foreach($theaters as $t)
                <tr id="theaters_{{$t->id}}">
                    <td>{{trans('film.program.'.$t->program)}}</td>
                    <td style="word-break: break-all">{{$t->distribution}}</td>
                    <td>{{$t->country}}</td>
                    <td style="word-break: break-all">{{$t->contact}}</td>
                    <td>{{$t->year}}</td>
                    <td valign="middle" class="text-right">
                        <span class="btn fa fa-edit text-primary" ng-click="editHistory('theater', '{{json_encode($t)}}')"></span>
                        <span class="btn fa fa-trash text-danger" ng-click="deleteHistory('theater', '{{$t->id}}')"></span>
                    </td>
                </tr>
            @endforeach
                <tr ng-repeat="t in theaters" id="theater_<%t.id%>">
                    <td><span ng-bind="t.program_name"></span></td>
                    <td><span ng-bind="t.distribution"></span></td>
                    <td><span ng-bind="t.country"></span></td>
                    <td><span ng-bind="t.contact"></span></td>
                    <td><span ng-bind="t.year"></span></td>
                    <td valign="middle" class="text-right ">
                        <span class="btn fa fa-edit text-primary" ng-click="editHistory('theater', t, true)"></span>
                        <span class="btn fa fa-trash text-danger" ng-click="deleteHistory('theater',t.id)"></span>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr/>
        <div class="d-flex justify-content-between">
            <div class="btn btn-outline-danger" data-toggle="modal" data-target="#deleteFilmModal">{{trans('film.buttons.delete')}}</div>
            <div class="btn btn-primary" ng-click="save({{sizeof($festivals)}}, {{sizeof($diffusion)}}, {{sizeof($theaters)}})">{{trans('layout.BUTTONS.continue')}}</div>
        </div>
    </form>
@endsection
@section('script')
    <script src="/js/directives/filmaker.js"></script>
    <script src="/js/controllers/film/seller.js"></script>
@endsection