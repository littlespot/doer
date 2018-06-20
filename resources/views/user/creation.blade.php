@extends('layouts.zoomov')

@section('content')

<link href="/bower_components/crop-master/cropper.min.css" rel="stylesheet" />
<link href="/css/project.css" rel="stylesheet" />
<link href="/css/picture.css" rel="stylesheet" />

<div class="modal fade" id="alertPosterModal" tabindex="-1" role="dialog" aria-labelledby="alertPosterModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="modal-body">
                <div>{{trans('project.ERRORS.picture.poster')}}</div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" data-dismiss="modal" >
                    {{trans("project.BUTTONS.confirm")}}
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="alertPreparationModal" tabindex="-1" role="dialog" aria-labelledby="alertPreparationModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="modal-body">
                <div>{{trans('project.ALERTS.finish')}}</div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" data-dismiss="modal" >
                    {{trans("project.BUTTONS.confirm")}}
                </button>
            </div>
        </div>
    </div>
</div>
<div class="container " ng-controller="creationCtrl" ng-init="init()">
    <div class="text-right py-4">
        <a class="badge" href="/discover">{{trans("layout.MENU.project_list")}}</a>
        <span class="px-1">/</span>
        <a class="badge" href="/projects">{{trans("layout.MENU.my_projects")}}</a>
        <span class="px-1">/</span>
        <a class="badge" href="/profile?anchor=follower">{{trans("layout.MENU.favorites")}}</a>
    </div>
    <div class="content ">
        <div class="row">
            <div class="col-lg-2"></div>
            <div class="col-lg-8 col-md-12">
                @include('templates.'.app()->getLocale().'.creation')
            </div>
            <div class="col-lg-2"></div>
        </div>
        <br/>
        <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="baic-tab" data-toggle="tab" href="#bacic" role="tab" aria-controls="all" aria-selected="true">
                    {{trans("project.CREATION.pitch")}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" aria-selected="false">
                    {{trans("project.CREATION.description")}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" aria-controls="outdated" aria-selected="false">
                    {{trans("project.CREATION.container")}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" aria-selected="false" ng-click="alert()">
                    {{trans("project.CREATION.recruitment")}}
                </a>
            </li>
        </ul>
        <div class="tab-content bg-white" id="myTabContent">
            <div class="tab-pane fade show active p-3" id="all" role="tabpanel" aria-labelledby="basic-tab">
                @include('templates.picture', ['uploadUrl'=>'storage/uploads', 'pictureFolder'=>'projects', 'pictureId'=>auth()->id()])
                <form id="basicinfo" action="/admin/preparations" method="POST" name="basicinfo" class="p-5">
                    {{ csrf_field() }}
                    <h6>
                        {{trans('project.LABELS.basicInfo')}}
                    </h6>
                    <br/>
                    <div class="form-group px-3">
                        <div class="input input--isao">
                            <input class="input__field input__field--isao" type="text" id="input-title"   ng-model="title" ng-init="title = '{{old("title")}}'" name="title"
                                   placeholder="{{trans('project.PLACES.title')}}"
                                   ng-maxlength="40" required/>
                            <label class="input__label input__label--isao" for="input-title" data-content="{{trans('project.PLACES.title')}}">
                                <span class="input__label-content input__label-content--isao">
                                    <sup class="text-danger pr-1">*</sup>
                                     @if ($errors->has('title'))
                                        <span class="text-danger">
                                              {{trans("project.PLACES.title")}}
                                        </span>
                                    @else
                                        <span class="text-danger" ng-show="basicinfo.title.$error.required">
                                                {{trans("project.ERRORS.require.title")}}
                                        </span>
                                        <span class="text-danger" ng-show="basicinfo.title.$error.maxlength">
                                                {{trans("project.ERRORS.maxlength.title")}}
                                        </span>
                                        <span ng-show="!basicinfo.title.$error.required && !basicinfo.title.$error.maxlength">{{trans('project.LABELS.title')}}</span>
                                    @endif
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="row px-3">
                        <div class="col-md-4 col-xs-12 form-group">
                            <div class="input input--isao">
                                <select class="input__field input__field--isao" name="genre_id" ng-model="genre_id" ng-init="genre_id = '{{old("genre_id")}}'" id="genre" required>
                                    <option disabled value="">{{trans('project.PLACES.genre')}}</option>
                                    @foreach($genres as $genre)
                                        <option value="{{$genre->id}}">{{$genre->name}}</option>
                                    @endforeach
                                </select>
                                <label class="input__label input__label--isao" for="input-title" data-content="{{trans('project.PLACES.genre')}}">
                                    <span class="input__label-content input__label-content--isao">
                                        <sup class="text-danger pr-1">*</sup>
                                        @if ($errors->has('genre_id'))
                                            <span class="text-danger">
                                                  {{trans("project.ERRORS.require.genre")}}
                                            </span>
                                        @else
                                            <span class="text-danger" ng-show="basicinfo.genre_id.$error.required">
                                                {{trans("project.ERRORS.require.genre")}}
                                            </span>
                                            <span ng-show="!basicinfo.genre_id.$error.required">{{trans('project.LABELS.genre')}}</span>
                                        @endif
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4 col-xs-12 form-group">
                            <div class="input input--isao">
                                <input type="text" class="input__field input__field--isao" uib-datepicker-popup="yyyy-MM-dd"
                                       ng-model="project.finish_at" ng-init="finish_at = '{{old("finish_at")}}'"
                                       name="finish_at"
                                       is-open="calendar.opened"
                                       show-button-bar="false"
                                       popup-placement="left"
                                       ng-required="true"
                                       placeholder="{{trans("project.PLACES.finish")}}"
                                       alt-input-formats="['M!/d!/yyyy']" />
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-outline-secondary" ng-click="openCalendar()"><i class="fa fa-calendar"></i></button>
                                </span>
                                <label class="input__label input__label--isao" for="input-title" data-content="{{trans('project.PLACES.finish')}}">
                                    <span class="input__label-content input__label-content--isao">
                                        <sup class="text-danger pr-1">*</sup>
                                        @if ($errors->has('finish_at'))
                                            <span class="text-danger">
                                                  {{trans("project.ERRORS.require.finish")}}
                                            </span>
                                        @else
                                            <span class="text-danger" ng-show="basicinfo.finish_at.$error.required">
                                                {{trans("project.ERRORS.require.finish")}}
                                            </span>
                                            <span ng-show="!basicinfo.finish_at.$error.required">{{trans('project.LABELS.finish')}}</span>
                                        @endif
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4 col-xs-12 form-group">
                            <div>
                                <div class="input input--isao">
                                    <input type="number"  ng-model="project.duration" ng-init="project.duration = {{old("duration")?:1}}"
                                           name="duration" class="input__field input__field--isao"
                                           placeholder="{{trans('project.PLACES.duration')}}"
                                           required />
                                    <label class="input__label input__label--isao" for="input-title"
                                           data-content="{{trans('project.PLACES.duration')}}">
                                        <span class="input__label-content input__label-content--isao">
                                            <sup class="text-danger pr-1">*</sup>
                                             @if ($errors->has('duration'))
                                                <span class="text-danger">
                                                   {{trans('project.PLACES.duration')}}
                                                </span>
                                            @else
                                                <span class="text-danger" ng-show="basicinfo.duration.$error.required">{{trans("project.ERRORS.require.duration")}}</span>
                                                <span class="text-danger" ng-show="project.duration < 1"> {{trans("project.ERRORS.minlength.duration", ['cnt'=>0])}}</span>
                                                <span class="text-danger" ng-show="project.duration >1200">  {{trans("project.ERRORS.maxlength.duration", ['cnt'=>1200])}}</span>
                                                <span ng-show="!basicinfo.duration.$error.required && project.duration>=1 && project.duration<=1200">{{trans('project.LABELS.duration')}}</span>
                                            @endif
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger">{{trans('project.ALERTS.finish')}}</div>
                    <hr/>
                    <div class="form-group" location="project" country="{{old('country_id')}}" department="{{old('department_id')}}">
                        <h6>
                            {{trans('project.LABELS.location')}}
                        </h6>
                        <br/>
                        <div class="row px-3">
                            <div class="col-md-4 col-xs-12 input input--isao">
                                <select class="input__field input__field--isao" name="country_id" ng-model="project.country_id" id="country_id"
                                        required ng-change="loadDepart(project.country_id)">
                                    <option value="">{{trans('project.LABELS.country')}}</option>
                                    @foreach($countries as $country)
                                        <option value="{{$country->id}}" ng-selected="project.country_id == {{$country->id}}">{{$country->name}}</option>
                                    @endforeach
                                </select>
                                <label class="input__label input__label--isao" for="country_id" data-content="{{trans('project.LABELS.country')}}">
                                    <span class="input__label-content input__label-content--isao">
                                       <sup class="text-danger pr-1">*</sup> <span class="text-danger" ng-show="basicinfo.country_id.$error.required">{{trans("project.ERRORS.require.country")}}</span>
                                        <span ng-show="!basicinfo.country_id.$error.required"> {{trans("project.LABELS.country")}}</span>
                                    </span>
                                </label>
                            </div>
                            <div class="col-sm-4 col-xs-12 input input--isao">
                                <select class="input__field input__field--isao" ng-model="project.department_id" name="department_id" id="department_id"
                                        ng-options="d.id as d.name for d in project.departments" ng-change="loadCity(project.department_id)"
                                        required>
                                    <option value="" disabled>{{trans("project.PLACES.department")}}</option>
                                </select>
                                <label class="input__label input__label--isao" for="department_id" data-content="{{trans('project.LABELS.department')}}">
                                    <span class="input__label-content input__label-content--isao">
                                        <sup class="text-danger pr-1">*</sup><span class="text-danger" ng-show="basicinfo.department_id.$error.required">{{trans("project.ERRORS.require.department")}}</span>
                                        <span ng-show="!basicinfo.department_id.$error.required"> {{trans("project.LABELS.department")}}</span>
                                    </span>
                                </label>
                            </div>
                            <div class="col-sm-4 col-xs-12  input input--isao">
                                <select class="input__field input__field--isao" ng-model="project.city_id" name="city_id" id="city_id"
                                        ng-options="c.id as c.name for c in project.cities" required >
                                    <option value="" disabled>{{trans("project.PLACES.city")}}</option>
                                </select>
                                <label class="input__label input__label--isao" for="city_id" data-content="{{trans('project.LABELS.city')}}">
                                    <span class="input__label-content input__label-content--isao">
                                        <sup class="text-danger pr-1">*</sup>
                                         @if ($errors->has('city_id'))
                                            <span class="text-danger">
                                               {{trans("project.ERRORS.require.city")}}
                                            </span>
                                        @else
                                            <span class="text-danger" ng-show="basicinfo.city_id.$error.required">{{trans("project.ERRORS.require.city")}}</span>
                                            <span ng-show="!basicinfo.city_id.$error.required">{{trans("project.LABELS.city")}}</span>
                                        @endif
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <h6>{{trans("project.LABELS.lang")}}</h6>
                        <div class="row ">
                            <div class="col-md-8 col-xs-12 pl-5">
                                <span ng-repeat="l in project.langs" class="mr-2">
                                    <input name="lang[]" value="<%l.language_id%>" type="hidden" />
                                    <span ng-bind="l.name" id="lang_name_<%l.language_id%>" rank="<%l.rank%>"></span>
                                    <sup class="btn btn-sm text-danger fa fa-times-circle" ng-click="removeLang(l.language_id)"></sup>
                                </span>
                            </div>
                            <div class="col-md-4 col-xs-12">
                                <div class="input input--isao">
                                    <select class="input__field input__field--isao" name="newLang" ng-model="newLang" id="newLang" ng-change="addLang(newLang)">
                                        @foreach($languages as $lang)
                                            <option id="opt_lang_{{$lang->id}}" {{$lang->chosen ?'disabled':''}} value="{{$lang->id}}" rank="{{$lang->rank}}">{{$lang->name}}</option>
                                        @endforeach
                                    </select>
                                    <label class="input__label input__label--isao" for="newLang" data-content="{{trans('project.PLACES.lang')}}">
                                        <span class="input__label-content input__label-content--isao">{{trans("project.PLACES.lang")}}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div>
                        <h6><sup class="text-danger pr-1">*</sup>{{trans('project.LABELS.synopsis')}}</h6>
                        <br/>
                        <div class="input input--isao">
                            <textarea rows="3" name="synopsis" id="synopsis"
                                      class="input__field input__field--isao" ng-model="project.synopsis"
                                      ng-model="synopsis"  ng-init="synopsis = '{{old("synopsis")}}'"
                                      placeholder="{{trans('project.PLACES.synopsis')}}"
                                      ng-minlength="40" ng-maxlength="256" required>
                            </textarea>
                            <label class="input__label input__label--isao" for="synopsis" data-content="{{trans('project.PLACES.synopsis')}}">
                                <span class="input__label-content input__label-content--isao">
                                     @if ($errors->has('synopsis'))
                                        <span class="text-danger">
                                            <i class="pr-1">*</i>{{trans('project.PLACES.synopsis')}}
                                        </span>
                                    @else
                                        <span  ng-show="basicinfo.synopsis.$valid">{{trans('project.PLACES.synopsis')}}</span>
                                        <span class="text-danger" ng-show="basicinfo.synopsis.$error.required || basicinfo.synopsis.$error.minlength">{{trans("project.ERRORS.minlength.synopsis")}}</span>
                                        <span class="text-danger" ng-show="basicinfo.synopsis.$error.maxlength">{{trans("project.ERRORS.maxlength.synopsis")}}</span>
                                    @endif
                                </span>
                            </label>
                        </div>
                    </div>

                    <br/>
                    <div ng-if="basicinfo.$invalid" class="alert alert-danger">{{trans("project.NOTE")}}</div>

                </form>
                <div class="text-right">
                    <button class="btn btn-primary" ng-disabled="basicinfo.$invalid || duration < 1 || duration >1200" id="submit"
                            ng-click="save(basicinfo.$invalid || duration < 1 || duration >1200)">
                        {{trans('layout.BUTTONS.continue')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
    @endsection
@section('script')
    <script src="/bower_components/crop-master/cropper.js"></script>
    <script src="/js/modules/preparation.js"></script>
    <script src="/js/directives/picture.js"></script>
    <script src="/js/directives/location.js"></script>
    <script src="/js/directives/script.js"></script>
    <script src="/js/directives/budget.js"></script>
    <script src="/js/directives/team.js"></script>
    <script src="/js/directives/recruit.js"></script>
    <script src="/js/controllers/admin/creation.js"></script>
@endsection