@extends('layouts.film')

@section('filmForm')
    <form name="filmForm"  action="/plays" method="POST"
          ng-controller="filmCtrl" ng-init="init()">
        {{csrf_field()}}
        <input type="hidden" name="id" value="{{$film->id}}" />
        <input type="hidden" name="step" value="{{$step+1}}" />
        <div class="modal fade" id="deleteCreditModal" tabindex="-1" role="dialog" aria-labelledby="deleteCreditModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div><label class="p-1" ng-bind="creditToDelete.label"></label>{{trans('film.header.delete_credits')}}</div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body px-5 py-3" id="modal-body">
                        <div class="alert alert-danger">
                            {{trans('film.alert.delete_maker')}} <span class="pl-3" ng-bind="creditToDelete.maker.last_name"></span><span class="pl-1" ng-bind="creditToDelete.maker.first_name"></span>
                        </div>
                        <div class="row py-2">
                            <div class="col-md-4 col-sm-12 label-justified">
                                {{trans('film.label.user')}}
                            </div>
                            <div class="col-md-8 col-sm-12">
                                <a ng-if="creditToDelete.maker.username" href="/profile/<%creditToDelete.maker.related_id%>" target="_blank" ng-bind="creditToDelete.maker.username"></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 label-justified">
                                {{trans('personal.LABELS.title')}}
                            </div>
                            <div class="col-md-8 col-sm-12">
                                <span ng-switch="creditToDelete.maker.prefix">
                                    <span ng-switch-when="mr">{{trans('personal.TITLES.mr')}}</span>
                                    <span ng-switch-when="ms">{{trans('personal.TITLES.ms')}}</span>
                                </span>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-md-4 label-justified">
                                {{trans('personal.LABELS.email')}}
                            </div>
                            <div class="col-md-8 col-sm-12">
                                <span ng-bind="creditToDelete.maker.email"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 label-justified">
                                {{trans('personal.LABELS.nationality')}}
                            </div>
                            <div class="col-md-8 col-sm-12">
                                <span ng-bind="creditToDelete.maker.country"></span>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-md-4 label-justified">
                                {{trans('personal.LABELS.born')}}
                            </div>
                            <div class="col-md-8 col-sm-12">
                                <span ng-bind="creditToDelete.maker.born"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 label-justified">
                                {{trans('personal.LABELS.mobile')}}
                            </div>
                            <div class="col-md-8 col-sm-12">
                                <span ng-bind="creditToDelete.maker.mobile"></span>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-md-4 label-justified">
                                {{trans('personal.LABELS.fix')}}
                            </div>
                            <div class="col-md-8 col-sm-12">
                                <span ng-bind="creditToDelete.maker.tel"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-sm-12 label-justified">
                                {{trans('personal.LABELS.web')}}
                            </div>
                            <div class="col-md-8 col-sm-12">
                                <a href="creditToDelete.maker.web" target="_blank" ng-bind="creditToDelete.maker.web"></a>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex px-5">
                        <button class="btn btn-danger mr-auto" type="button" data-dismiss="modal" >
                            {{trans("project.BUTTONS.cancel")}}
                        </button>
                        <button class="btn btn-primary" type="button" ng-click="creditDeleted('{{$film->id}}')" >
                            {{trans("project.BUTTONS.confirm")}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade bd-example-modal-lg" id="editMakerModal" tabindex="-1" role="dialog" aria-labelledby="editMakerModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <label>{{trans('film.label.editMaker')}}</label>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body px-5 py-3" id="modal-body">
                        <div class="row">
                            <div class="col-12 input input--isao">
                                <select id="searchUser" name="user" class="input__field input__field--isao"
                                        ng-model="makerCopy.related_id" ng-options="m.id as (m.username + '[' + m.location + ']') for m in users">
                                </select>
                                <label class="input__label input__label--isao" for="searchmaker" data-content="{{trans('film.placeholder.search_user')}}">
                                    <span class="input__label-content input__label-content--isao">{{trans('film.label.users')}}</span>
                                </label>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-md-6 col-sm-12 text-primary">
                                <input id="lastName_<%makerCopy.id%>" name="last_name" class="input__field input__field--isao text-uppercase"
                                       ng-model="makerCopy.last_name"  />
                                <label class="input__label input__label--isao" for="searchmaker" data-content="{{trans('personal.LABELS.last_name')}}">
                                    <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.last_name')}}</span>
                                </label>
                            </div>
                            <div class="col-md-6 col-sm-12 input input--isao">
                                <input id="firstName_<%makerCopy.id%>" name="first_name" class="input__field input__field--isao"
                                       ng-model="makerCopy.first_name"  />
                                <label class="input__label input__label--isao" for="searchmaker" data-content="{{trans('personal.LABELS.first_name')}}">
                                    <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.first_name')}}</span>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-12 input input--isao">
                                <select id="title_<%makerCopy.id%>" name="prefix" class="input__field input__field--isao"
                                        ng-model="makerCopy.prefix">
                                    @foreach(trans('personal.TITLES') as $key=>$title)
                                        <option value="{{$key}}" ng-selected="makerCopy.prefix == '{{$key}}'">{{$title}}</option>
                                    @endforeach
                                </select>
                                <label class="input__label input__label--isao" for="title_<%makerCopy.id%>" data-content="{{trans('personal.LABELS.title')}}">
                                    <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.title')}}</span>
                                </label>
                            </div>
                            <div class="col-md-6 col-sm-12 input input--isao">
                                <input id="email_<%makerCopy.id%>" ng-model="makerCopy.email" class="input__field input__field--isao" />
                                <label class="input__label input__label--isao" for="email_<%makerCopy.id%>" data-content="{{trans('personal.LABELS.email')}}">
                                    <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.email')}}</span>
                                </label>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-lg-6 col-md-6 input input--isao">
                                <select id="nationality_<%makerCopy.id%>" class="input__field input__field--isao" ng-model="makerCopy.country_id">
                                    @foreach($countries as $key=>$country)
                                        <option id="nation_{{$key}}" value="{{$key}}" ng-selected="makerCopy.country_id == {{$key}}">{{$country}}</option>
                                    @endforeach
                                </select>
                                <label class="input__label input__label--isao" for="nationality_<%makerCopy.id%>" data-content="  {{trans('personal.LABELS.nationality')}}">
                                    <span class="input__label-content input__label-content--isao">  {{trans('personal.LABELS.nationality')}}</span>
                                </label>
                            </div>
                            <div class="col-lg-6 col-md-6 input input--isao">
                                <select id="born_<%makerCopy.id%>" ng-model="makerCopy.born" class="input__field input__field--isao">
                                    @for($year = date("Y"); $year > 1900; $year--)
                                        <option value="{{$year}}" ng-selected="makerCopy.born=={{$year}}">{{$year}}</option>
                                    @endfor
                                </select>
                                <label class="input__label input__label--isao" for="born_<%makerCopy.id%>" data-content="{{trans('personal.LABELS.born')}}">
                                    <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.born')}}</span>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 input input--isao">
                                <input id="mobile_<%makerCopy.id%>" ng-model="makerCopy.mobile" class="input__field input__field--isao" />
                                <label class="input__label input__label--isao" for="mobile_<%makerCopy.id%>" data-content="{{trans('personal.LABELS.mobile')}}">
                                    <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.mobile')}}</span>
                                </label>
                            </div>
                            <div class="col-lg-6 col-md-6 input input--isao">
                                <input id="fix_<%makerCopy.id%>" ng-model="makerCopy.tel" class="input__field input__field--isao" />
                                <label class="input__label input__label--isao" for="fix_<%makerCopy.id%>" data-content="{{trans('personal.LABELS.fix')}}">
                                    <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.fix')}}</span>
                                </label>
                            </div>
                        </div>
                        <div class="py-2 input input--isao">
                            <input id="web_<%makerCopy.id%>" ng-model="makerCopy.web" class="input__field input__field--isao" />
                            <label class="input__label input__label--isao" for="web_<%makerCopy.id%>" data-content="{{trans('personal.LABELS.web')}}">
                                <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.web')}}</span>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer d-flex px-5">
                        <button class="btn btn-danger mr-auto" type="button" data-dismiss="modal" >
                            {{trans("project.BUTTONS.cancel")}}
                        </button>
                        <button class="btn btn-primary" type="button" ng-click="updateCredit(makerCopy)" >
                            {{trans("project.BUTTONS.confirm")}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade bd-example-modal-lg" id="makerListModal" tabindex="-1" role="dialog" aria-labelledby="makerListModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <label>{{trans('film.label.another_maker')}}</label>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body small" id="modal-body">
                        <div class="row">
                            <div ng-repeat="d in persons" class="col-lg-6 col-md-12">
                                <div class="card border border-dark m-3"  ng-class="{'bg-secondary':d.selected }">
                                    <div class="card-header d-flex">
                                        <span class="mr-auto checkbox-inline">
                                            <input type="checkbox" ng-model="d.selected">
                                            <span ng-bind="d.last_name"></span><span class="pl-1" ng-bind="d.first_name"></span>
                                        </span>
                                        <span class="btn fa" ng-class="{'fa-caret-down':!d.viewed, 'fa-caret-up':d.viewed}" ng-click="d.viewed = !d.viewed;"></span>
                                    </div>
                                    <div class="card-body" ng-hide="!d.viewed">
                                        <div class="row py-2">
                                            <div class="col-md-4 col-sm-12 label-justified">
                                                {{trans('film.label.user')}}
                                            </div>
                                            <div class="col-md-8 col-sm-12">
                                                <a ng-if="d.username" href="/profile/<%d.related_id%>" target="_blank" ng-bind="d.username"></a>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 label-justified">
                                                {{trans('personal.LABELS.title')}}
                                            </div>
                                            <div class="col-md-8 col-sm-12">
                                            <span ng-switch="d.prefix">
                                                <span ng-switch-when="mr">{{trans('personal.TITLES.mr')}}</span>
                                                <span ng-switch-when="ms">{{trans('personal.TITLES.ms')}}</span>
                                            </span>
                                            </div>
                                        </div>
                                        <div class="row py-2">
                                            <div class="col-md-4 label-justified">
                                                {{trans('personal.LABELS.email')}}
                                            </div>
                                            <div class="col-md-8 col-sm-12">
                                                <span ng-bind="d.email"></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 label-justified">
                                                {{trans('personal.LABELS.nationality')}}
                                            </div>
                                            <div class="col-md-8 col-sm-12">
                                                <span ng-bind="d.country"></span>
                                            </div>
                                        </div>
                                        <div class="row py-2">
                                            <div class="col-md-4 label-justified">
                                                {{trans('personal.LABELS.born')}}
                                            </div>
                                            <div class="col-md-8 col-sm-12">
                                                <span ng-bind="d.born"></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 label-justified">
                                                {{trans('personal.LABELS.mobile')}}
                                            </div>
                                            <div class="col-md-8 col-sm-12">
                                                <span ng-bind="d.mobile"></span>
                                            </div>
                                        </div>
                                        <div class="row py-2">
                                            <div class="col-md-4 label-justified">
                                                {{trans('personal.LABELS.fix')}}
                                            </div>
                                            <div class="col-md-8 col-sm-12">
                                                <span ng-bind="d.tel"></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 col-sm-12 label-justified">
                                                {{trans('personal.LABELS.web')}}
                                            </div>
                                            <div class="col-md-8 col-sm-12">
                                                <a href="<%d.web%>" target="_blank" ng-bind="d.web"></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-danger" ng-bind="errors.maker.another"></div>
                    </div>
                    <div class="modal-footer d-flex px-5">
                        <button class="btn btn-outline-danger mr-auto" type="button" data-dismiss="modal" >
                            {{trans("project.BUTTONS.cancel")}}
                        </button>
                        <button class="btn btn-primary" type="button" ng-click="creditSaved('{{$film->id}}')" ng-disabled="!makerSelected">
                            {{trans("project.BUTTONS.confirm")}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade bd-example-modal-lg" id="newMakerModal" tabindex="-1" role="dialog" aria-labelledby="newMakerModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        {{trans('film.label.add_maker')}}
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body px-5 py-3" id="modal-body">
                        <div class="row">
                            <div class="col-sm-12 input input--isao">
                                <select class="input__field input__field--isao" id="newmaker_related"
                                        ng-model="newMaker.related_id" ng-options="u.id as u.username for u in users"></select>
                                <label class="input__label input__label--isao" for="searchmaker" data-content="{{trans('film.label.user')}}">
                                    <span class="input__label-content input__label-content--isao">{{trans('film.label.user')}}</span>
                                </label>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-lg-6 col-md-6 input input--isao">
                                <input ng-model="newMaker.last_name" autofocus class="input__field input__field--isao text-uppercase" />
                                <label class="input__label input__label--isao" for="title_new" data-content="{{trans('personal.LABELS.last_name')}}">
                                    <span class="input__label-content input__label-content--isao"><sup class="text-danger">*</sup>{{trans('personal.LABELS.last_name')}}</span>
                                </label>
                            </div>
                            <div class="col-lg-6 col-md-6 input input--isao">
                                <input ng-model="newMaker.first_name" class="input__field input__field--isao" />
                                <label class="input__label input__label--isao" for="email" data-content="{{trans('personal.LABELS.first_name')}}">
                                    <span class="input__label-content input__label-content--isao"><sup class="text-danger">*</sup>{{trans('personal.LABELS.first_name')}}</span>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 input input--isao">
                                <select id="title" ng-model="newMaker.prefix" class="input__field input__field--isao" >
                                    @foreach(trans('personal.TITLES') as $key=>$title)
                                        <option value="{{$key}}">{{$title}}</option>
                                    @endforeach
                                </select>
                                <label class="input__label input__label--isao" for="title_new" data-content="{{trans('personal.LABELS.title')}}">
                                    <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.title')}}</span>
                                </label>
                            </div>
                            <div class="col-lg-6 col-md-6 input input--isao">
                                <input id="email" ng-model="newMaker.email" class="input__field input__field--isao" />
                                <label class="input__label input__label--isao" for="email" data-content="{{trans('personal.LABELS.email')}}">
                                    <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.email')}}</span>
                                </label>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-lg-6 col-md-6 input input--isao">
                                <select id="nationality" class="input__field input__field--isao" ng-model="newMaker.country_id">
                                    @foreach($countries as $key=>$country)
                                        <option id="nation_{{$key}}" value="{{$key}}">{{$country}}</option>
                                    @endforeach
                                </select>
                                <label class="input__label input__label--isao" for="nationality" data-content="  {{trans('personal.LABELS.nationality')}}">
                                    <span class="input__label-content input__label-content--isao">  {{trans('personal.LABELS.nationality')}}</span>
                                </label>
                            </div>
                            <div class="col-lg-6 col-md-6 input input--isao">
                                <select id="born" ng-model="newMaker.born" class="input__field input__field--isao">
                                    @for($year = date("Y"); $year > 1900; $year--)
                                        <option value="{{$year}}" ng-selected="newMaker.born=={{$year}}">{{$year}}</option>
                                    @endfor
                                </select>
                                <label class="input__label input__label--isao" for="born" data-content="{{trans('personal.LABELS.born')}}">
                                    <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.born')}}</span>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 input input--isao">
                                <input id="mobile" ng-model="newMaker.mobile" class="input__field input__field--isao" />
                                <label class="input__label input__label--isao" for="mobile_<%d.id%>" data-content="{{trans('personal.LABELS.mobile')}}">
                                    <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.mobile')}}</span>
                                </label>
                            </div>
                            <div class="col-lg-6 col-md-6 input input--isao">
                                <input id="fix" ng-model="newMaker.tel" class="input__field input__field--isao" />
                                <label class="input__label input__label--isao" for="fix" data-content="{{trans('personal.LABELS.fix')}}">
                                    <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.fix')}}</span>
                                </label>
                            </div>
                        </div>
                        <div class="py-2 input input--isao">
                            <input id="web" ng-model="newMaker.web" class="input__field input__field--isao" />
                            <label class="input__label input__label--isao" for="web" data-content="{{trans('personal.LABELS.web')}}">
                                <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.web')}}</span>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer d-flex px-5">
                        <button class="btn btn-outline-danger mr-auto" type="button" data-dismiss="modal" >
                            {{trans("project.BUTTONS.cancel")}}
                        </button>
                        <button class="btn btn-primary" type="button" ng-click="creditCreated('{{$film->id}}')" ng-disabled="!newMaker.last_name || !newMaker.first_name">
                            {{trans("project.BUTTONS.confirm")}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="alert alert-warning">{{trans('film.alert.original_writer')}}</div>
        <div class="row pt-3">
            <label class="col-lg-2 col-md-4 col-sm-12">{{trans('film.label.add_credits')}}</label>
            <div class="col-lg-10 col-md-8 col-sm-12">
                <div class="row  pb-3">
                    <div class="col-lg-4 col-md-12 checkbox-inline">
                        <input type="checkbox" value="adapters" name="credit_type" ng-model="creditSelected['adapters']" />
                        {{trans('film.label.original_writer')}}
                    </div>
                    <div class="col-lg-4 col-md-12 checkbox-inline">
                        <input type="checkbox" value="directors" name="credit_type" ng-model="creditSelected['directors']" />
                        {{trans('film.label.directors')}}
                    </div>
                    <div class="col-lg-4 col-md-12 checkbox-inline">
                        <input type="checkbox" value="producers" name="credit_type" ng-model="creditSelected['producers']" />
                        {{trans('film.label.producers')}}
                    </div>
                </div>
                <div ng-if="errors.credit" class="alert alert-danger">{{trans('film.alert.choose_credit')}}</div>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="btn btn-block btn-outline-primary"
                             ng-click="creditChosen(false)"
                             ng-disabled="!creditSelected['adapters'] && !creditSelected['directors'] && !creditSelected['producers']"
                        >{{trans('film.label.another_maker')}}</div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="btn btn-block btn-outline-primary"
                             ng-disabled="!creditSelected['adapters'] && !creditSelected['directors'] && !creditSelected['producers']"
                             ng-click="creditChosen(true)">{{trans('film.label.add_maker')}}</div>
                    </div>
                </div>
            </div>
        </div>
        <br/>
        <div class="row py-3">
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified">
                {{trans('film.label.original_writer')}}
            </label>
            <div class="col-lg-10 col-md-8 col-sm-12" ng-init="adapters = {{json_encode($adapters)}}">
                <div ng-repeat="d in adapters">
                    <div class="d-flex border-secondary border-bottom"  ng-click="d.viewed = !d.viewed">
                        <div class="mr-auto">
                            @if(app()->getLocale() == 'zh')
                                <span ng-bind="d.last_name"></span>&nbsp;<span ng-bind="d.first_name"></span>
                            @endif                            &nbsp;
                            @if(app()->getLocale() != 'zh')
                                <span ng-bind="d.first_name"></span>&nbsp;<span class="text-uppercase" ng-bind="d.last_name"></span>
                            @endif
                        </div>
                        <div class="btn fa" ng-class="{'fa-caret-down':!d.viewed, 'fa-caret-up':d.viewed}"></div>
                    </div>
                    <div ng-hide="!d.viewed" class="small">
                        <div class="row py-2">
                            <div class="col-lg-2 col-md-4 col-sm-12 label-justified">
                                {{trans('film.label.user')}}
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-12">
                                <a ng-if="d.username" href="/profile/<%d.related_id%>" target="_blank" ng-bind="d.username"></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-2 col-md-3 label-justified">
                                {{trans('personal.LABELS.title')}}
                            </div>
                            <div class="col-lg-4 col-md-3 col-sm-12">
                                <span ng-switch="d.prefix">
                                    <span ng-switch-when="mr">{{trans('personal.TITLES.mr')}}</span>
                                    <span ng-switch-when="ms">{{trans('personal.TITLES.ms')}}</span>
                                </span>
                            </div>
                            <div class="col-lg-2 col-md-3 label-justified">
                                {{trans('personal.LABELS.email')}}
                            </div>
                            <div class="col-lg-4 col-md-3 col-sm-12">
                                <span ng-bind="d.email"></span>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-lg-2 col-md-3 label-justified">
                                {{trans('personal.LABELS.nationality')}}
                            </div>
                            <div class="col-lg-4 col-md-3 col-sm-12">
                                <span ng-bind="d.country"></span>
                            </div>
                            <div class="col-lg-2 col-md-3 label-justified">
                                {{trans('personal.LABELS.born')}}
                            </div>
                            <div class="col-lg-4 col-md-3 col-sm-12">
                                <span ng-bind="d.born"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-2 col-md-3 label-justified">
                                {{trans('personal.LABELS.mobile')}}
                            </div>
                            <div class="col-lg-4 col-md-3 col-sm-12">
                                <span ng-bind="d.mobile"></span>
                            </div>
                            <div class="col-lg-2 col-md-3 label-justified">
                                {{trans('personal.LABELS.fix')}}
                            </div>
                            <div class="col-lg-4 col-md-3 col-sm-12">
                                <span ng-bind="d.tel"></span>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-lg-2 col-md-3 col-sm-12 label-justified">
                                {{trans('personal.LABELS.web')}}
                            </div>
                            <div class="col-lg-10 col-md-9 col-sm-12">
                                <a href="<%d.web%>" target="_blank" ng-bind="d.web"></a>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="btn btn-sm btn-outline-danger mr-auto" ng-click="deleteCredit('adapters', '{{trans("film.label.original_writer")}}', d)">
                                {{trans('film.buttons.delete_writer')}}
                            </div>

                            <div class="btn btn-sm btn-primary" ng-click="editMaker(d);">
                                {{trans('film.buttons.edit_maker')}}
                            </div>
                        </div>
                        <hr/>
                    </div>
                    <br/>
                </div>
            </div>
        </div>
        <br/>
        <div class="row py-3">
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified">
                {{trans('film.label.directors')}}
            </label>
            <div class="col-lg-10 col-md-8 col-sm-12" ng-init="directors = {{json_encode($directors)}}">
                <div ng-repeat="d in directors" >
                    <div class="d-flex border-secondary border-bottom" ng-click="d.viewed = !d.viewed">
                        <div class="mr-auto">
                            @if(app()->getLocale() == 'zh')
                                <span ng-bind="d.last_name"></span>&nbsp;<span ng-bind="d.first_name"></span>
                            @endif                            &nbsp;
                            @if(app()->getLocale() != 'zh')
                                <span ng-bind="d.first_name"></span>&nbsp;<span class="text-uppercase" ng-bind="d.last_name"></span>
                            @endif
                        </div>
                        <div class="btn fa" ng-class="{'fa-caret-down':!d.viewed, 'fa-caret-up':d.viewed}" ></div>
                    </div>
                    <div  ng-hide="!d.viewed" class="small">
                        <div class="row py-2">
                            <div class="col-lg-2 col-md-4 col-sm-12 label-justified">
                                {{trans('film.label.user')}}
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-12">
                                <a ng-if="d.username" href="/profile/<%d.related_id%>" target="_blank" ng-bind="d.username"></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-2 col-md-3 label-justified">
                                {{trans('personal.LABELS.title')}}
                            </div>
                            <div class="col-lg-4 col-md-3 col-sm-12">
                                <span ng-switch="d.prefix">
                                    <span ng-switch-when="mr">{{trans('personal.TITLES.mr')}}</span>
                                    <span ng-switch-when="ms">{{trans('personal.TITLES.ms')}}</span>
                                </span>
                            </div>
                            <div class="col-lg-2 col-md-3 label-justified">
                                {{trans('personal.LABELS.email')}}
                            </div>
                            <div class="col-lg-4 col-md-3 col-sm-12">
                                <span ng-bind="d.email"></span>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-lg-2 col-md-3 label-justified">
                                {{trans('personal.LABELS.nationality')}}
                            </div>
                            <div class="col-lg-4 col-md-3 col-sm-12">
                                <span ng-bind="d.country"></span>
                            </div>
                            <div class="col-lg-2 col-md-3 label-justified">
                                {{trans('personal.LABELS.born')}}
                            </div>
                            <div class="col-lg-4 col-md-3 col-sm-12">
                                <span ng-bind="d.born"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-2 col-md-3 label-justified">
                                {{trans('personal.LABELS.mobile')}}
                            </div>
                            <div class="col-lg-4 col-md-3 col-sm-12">
                                <span ng-bind="d.mobile"></span>
                            </div>
                            <div class="col-lg-2 col-md-3 label-justified">
                                {{trans('personal.LABELS.fix')}}
                            </div>
                            <div class="col-lg-4 col-md-3 col-sm-12">
                                <span ng-bind="d.tel"></span>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-lg-2 col-md-3 col-sm-12 label-justified">
                                {{trans('personal.LABELS.web')}}
                            </div>
                            <div class="col-lg-10 col-md-9 col-sm-12">
                                <a href="<%d.web%>" target="_blank" ng-bind="d.web"></a>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="btn btn-sm btn-outline-danger mr-auto" ng-click="deleteCredit('directors', '{{trans("film.label.directors")}}', d)">
                                {{trans('film.buttons.delete_director')}}
                            </div>

                            <div class="btn btn-sm btn-primary" ng-click="editMaker(d);">
                                {{trans('film.buttons.edit_maker')}}
                            </div>
                        </div>
                        <hr/>
                    </div>
                    <br/>
                </div>
            </div>
        </div>
        <br/>
        <div class="row py-3">
            <label class="col-lg-2 col-md-4 col-sm-12 label-justified">
                {{trans('film.label.producers')}}
            </label>

            <div class="col-lg-10 col-md-8 col-sm-12" ng-init="producers={{json_encode($producers)}};">

                <div ng-repeat="d in producers">
                    <div class="d-flex border-secondary border-bottom"  ng-click="d.viewed = !d.viewed">
                        <div class="mr-auto">
                            @if(app()->getLocale() == 'zh')
                                <span ng-bind="d.last_name"></span>&nbsp;<span ng-bind="d.first_name"></span>
                            @endif                            &nbsp;
                            @if(app()->getLocale() != 'zh')
                                <span ng-bind="d.first_name"></span>&nbsp;<span class="text-uppercase" ng-bind="d.last_name"></span>
                            @endif
                        </div>
                        <div class="btn fa" ng-class="{'fa-caret-down':!d.viewed, 'fa-caret-up':d.viewed}"></div>
                    </div>
                    <div ng-if="d.filmaker_id" ng-hide="!d.viewed" class="small">
                        <div class="row py-2">
                            <div class="col-lg-2 col-md-4 col-sm-12 label-justified">
                                {{trans('film.label.user')}}
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-12">
                                <a ng-if="d.username" href="/profile/<%d.related_id%>" target="_blank" ng-bind="d.username"></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-2 col-md-3 label-justified">
                                {{trans('personal.LABELS.title')}}
                            </div>
                            <div class="col-lg-4 col-md-3 col-sm-12">
                                <span ng-switch="d.prefix">
                                    <span ng-switch-when="mr">{{trans('personal.TITLES.mr')}}</span>
                                    <span ng-switch-when="ms">{{trans('personal.TITLES.ms')}}</span>
                                </span>
                            </div>
                            <div class="col-lg-2 col-md-3 label-justified">
                                {{trans('personal.LABELS.email')}}
                            </div>
                            <div class="col-lg-4 col-md-3 col-sm-12">
                                <span ng-bind="d.email"></span>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-lg-2 col-md-3 label-justified">
                                {{trans('personal.LABELS.nationality')}}
                            </div>
                            <div class="col-lg-4 col-md-3 col-sm-12">
                                <span ng-bind="d.country"></span>
                            </div>
                            <div class="col-lg-2 col-md-3 label-justified">
                                {{trans('personal.LABELS.born')}}
                            </div>
                            <div class="col-lg-4 col-md-3 col-sm-12">
                                <span ng-bind="d.born"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-2 col-md-3 label-justified">
                                {{trans('personal.LABELS.mobile')}}
                            </div>
                            <div class="col-lg-4 col-md-3 col-sm-12">
                                <span ng-bind="d.mobile"></span>
                            </div>
                            <div class="col-lg-2 col-md-3 label-justified">
                                {{trans('personal.LABELS.fix')}}
                            </div>
                            <div class="col-lg-4 col-md-3 col-sm-12">
                                <span ng-bind="d.tel"></span>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-lg-2 col-md-3 col-sm-12 label-justified">
                                {{trans('personal.LABELS.web')}}
                            </div>
                            <div class="col-lg-10 col-md-9 col-sm-12">
                                <a href="<%d.web%>" target="_blank" ng-bind="d.web"></a>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="btn btn-sm btn-outline-danger mr-auto" ng-click="deleteCredit('producers', '{{trans("film.label.producers")}}', d)">
                                {{trans('film.buttons.delete_producer')}}
                            </div>

                            <div class="btn btn-sm btn-primary" ng-click="editMaker(d);">
                                {{trans('film.buttons.edit_maker')}}
                            </div>
                        </div>
                        <hr/>
                    </div>
                    <br/>
                </div>
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
    <script src="/js/directives/filmaker.js"></script>
    <script src="/js/controllers/film/play_credit.js"></script>
@endsection