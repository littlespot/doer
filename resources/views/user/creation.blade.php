@extends('layouts.zoomov')

@section('content')

<link href="/bower_components/crop-master/cropper.min.css" rel="stylesheet" />
<link href="/css/form.css" rel="stylesheet" />
<link href="/css/project.css" rel="stylesheet" />
<link href="/css/picture.css" rel="stylesheet" />
<style>
    .btn-header{
        position: fixed;
        top:300px;
        right:25px;
    }
    .btn-header>div{
        display: block;
        margin-bottom: 10px;
    }
</style>
<script type="text/ng-template" id="alert.html">
    <div class="modal-body" id="modal-body">
        <h3 translate="project.MESSAGES.<%alert%>"></h3>
    </div>
    <div class="modal-footer">
        <button class="btn btn-default" type="button" ng-click="$dismiss()">OK</button>
    </div>
</script>
<script type="text/ng-template" id="confirm.html">
    <div class="modal-body" id="modal-body">
        <h3 translate="project.MESSAGES.<%confirm%>"></h3>
    </div>
    <div class="modal-footer">
        <button class="btn btn-default" type="button" ng-click="$close(false)">
            {{trans("project.BUTTONS.cancel")}}
        </button>
        <button class="btn btn-danger" type="button" ng-click="$close(true)">
            {{trans("project.BUTTONS.confirm")}}
        </button>
    </div>
</script>

<div class="container margin-bottom-lg" ng-controller="creationCtrl" ng-init="init()">
    <div class="container margin-bottom-lg">
        <div class="jumbotron">
            <h3>{{trans("project.LABELS.title")}}</h3>
            <div class="text-center"> {!!trans('project.agreement')!!}</div>

        </div>
        <ul class="nav nav-tabs nav-justified nav-top-menu">
            <li role="presentation" class="active">
                <a href="#">
                    <span>{{trans("project.CREATION.pitch")}}</span>
                </a>
            </li>
            <li class="disabled">
                <a href="javascript:void(0)" ng-click="alert()">
                    <span>{{trans("project.CREATION.description")}}</span>
                </a>
            </li>
            <li class="disabled">
                <a href="javascript:void(0)" ng-click="alert()">
                    <span>{{trans("project.CREATION.container")}}</span>
                </a>
            </li>
            <li class="disabled">
                <a href="javascript:void(0)" ng-click="alert()">
                    <span>{{trans("project.CREATION.recruitment")}}</span>
                </a>
            </li>
        </ul>
        @yield('tabcontent')
    </div>
    <div class="content-margin">
        <div>
            <div class="loader-content" style="display: none" id="picture_loader"><div class="loader"></div></div>
            @if ($errors->has('poster'))
                <div class="text-danger">
                    {{ $errors->first('poster') }}
                </div>
            @endif
            <div class="text-center margin-top-sm margin-bottom-sm img-original">
                <a class="poster {{ $errors->has('poster') ? ' has-error' : '' }}"
                   onclick="$('#avatarInput').click()">
                    <img id="poster_image" ng-src="uploads/projects/{{Auth::id()}}.jpg"
                         onError="this.onerror=null;this.src='/images/poster.png';"/>
                </a>
                <div class="text-center text-danger">{{trans("project.PLACES.poster")}}</div>
                <br/>
                <div class="text-center margin-top-sm ">
                    <button class="btn btn-default" onclick="$('#avatarInput').click()">
                        {{trans("project.BUTTONS.upload")}}
                    </button>
                </div>
            </div>
            <div class="img-container col-xs-12 margin-top-sm">
                <div class="img-preview preview-lg"></div>
                <div class="img-preview preview-md"></div>
                <div class="img-preview preview-sm"></div>
            </div>
            <div class="img-container modal-footer">
                <button class="btn btn-default" type="button" ng-click="stopCropper()">{{trans("project.BUTTONS.cancel")}}</button>
                <button class="btn btn-success text-uppercase" type="button"
                        ng-click="submitPicture(this.form)">{{trans("project.BUTTONS.confirm")}}</button>
            </div>

        </div>
        <br>
        <form id="picture-form" picture-content="project" enctype="multipart/form-data">
            <div class="picture-upload">
                <input type="text" class="avatar-src" name="picture_src" ng-model="url">
                <input type="text" class="avatar-data" name="picture_data">
                <input type="text" name="picture_name" value="{{Auth::id()}}">
                <input type="file" id="avatarInput" name="picture_file" accept="image/*"
                       onchange="angular.element(this).scope().pictureChanged()">
                <input type="hidden" name="picture_dst" value="uploads/projects">
            </div>
            <script type="text/ng-template" id="picture.html">
                <div class="modal-body flex-center">
                    <div id="picture_wrapper"><img ng-src="<%url%>" id="picture_cropper"></div>
                </div>
                <div class="modal-footer">
                    <div ng-click="$close(false)" class="btn btn-default">
                        <span class="fa fa-undo"></span>
                    </div>
                    <div ng-click="$close(true)" class="btn btn-primary">
                        <span class="fa fa-check"></span>
                    </div>
                </div>
            </script>
        </form>
        <form id="basicinfo" action="/admin/preparations" method="POST" name="basicinfo" class="form-horizontal">
            {{ csrf_field() }}
            <div class="row">
                <label class="col-md-1 col-sm-2 col-xs-4 require">
                    {{trans('project.LABELS.title')}}
                </label>
                <div class="col-md-6 col-sm-10">
                    <input name="title" id="title" type="text" class="form-text"
                           ng-class="{'error':basicinfo.title.$touched && basicinfo.title.$error}"
                           ng-model="title" ng-init="title = '{{old("title")}}'"
                           placeholder="{{trans('project.PLACES.title')}}"
                           ng-maxlength="40" required autofocus/>
                    <div role="alert" class="error" ng-class="{'visible':basicinfo.title.$touched}">
                        <span ng-show="basicinfo.title.$error.required">{{trans("project.ERRORS.require.title")}}</span>
                        <span ng-show="basicinfo.title.$error.maxlength">{{trans("project.ERRORS.maxlength.title")}}</span>
                    </div>
                </div>
                <label class="col-md-1 col-sm-2 col-xs-4 require">
                    {{trans('project.LABELS.genre')}}
                </label>
                <div class="col-md-4 cols-sm-10">
                    <select class="form-control" name="genre_id" ng-model="genre_id" ng-init="genre_id = '{{old("genre_id")}}'" id="genre" required>
                        <option disabled value="">{{trans('project.PLACES.genre')}}</option>
                        @foreach($genres as $genre)
                            <option value="{{$genre->id}}">{{$genre->name}}</option>
                        @endforeach
                    </select>
                    <div role="alert" class="error" ng-class="{'visible':basicinfo.genre.$touched || basicinfo.$submitted}">
                        <span ng-show="basicinfo.genre.$error.required">{{trans("project.ERRORS.require.genre")}}</span>
                    </div>
                </div>
            </div>
            <br/>
            <div class="row">
                <label class="col-md-1 col-sm-2 col-xs-4 require">
                    {{trans('project.LABELS.finish')}}
                </label>
                <div class="col-md-5 col-sm-6 col-xs-12">
                            <span class="input-group">
                              <input type="text" class="form-text" uib-datepicker-popup="yyyy-MM-dd" name="finish_at"
                                     ng-model="finish_at" ng-init="finish_at = '{{old("finish_at")}}'"
                                     ng-class="{'error':basicinfo.finish_at.$touched && basicinfo.finish_at.$error}"
                                     placeholder="{{trans('project.PLACES.finish')}}"
                                     is-open="calendar.opened"
                                     show-button-bar="false"
                                     datepicker-options="dateOptions"
                                     ng-required="true"
                                     alt-input-formats="['M!/d!/yyyy']" />
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default" ng-click="openCalendar()"><i class="glyphicon glyphicon-calendar"></i></button>
                                </span>
                            </span>
                    <div role="alert" class="error" ng-class="{'visible':basicinfo.finish_at.$touched || basicinfo.$submitted}">
                        <span ng-show="basicinfo.finish_at.$error.required">{{trans("project.ERRORS.require.finish")}}</span>
                    </div>
                </div>
                <label class="col-md-offset-1 col-md-1 col-sm-2 col-xs-4 require">
                    {{trans('project.LABELS.duration')}}
                </label>
                <div class="col-md-4 col-sm-3 col-xs-8">
                    <div class="input-group">
                        <input type="number" ng-model="duration" name="duration" class="form-control"
                               placeholder="{{trans('project.PLACES.duration')}}" ng-init="duration = '{{old("duration")}}'"
                               ng-class="{'error':basicinfo.duration.$touched && basicinfo.duration.$error}"
                               required />
                        <span class="input-group-addon" translate="project.Duration" ></span>
                    </div>
                    @if ($errors->has('duration'))
                        <div class="text-danger">
                            {{ $errors->first('duration') }}
                        </div>
                    @else
                        <div role="alert" class="error" ng-class="{'visible':basicinfo.duration.$touched || basicinfo.$submitted}">
                            <span ng-show="basicinfo.duration.$error.required">
                                {{trans("project.ERRORS.require.duration")}}
                            </span>
                            <span ng-show="duration < 1">
                                {{trans("project.ERRORS.minlength.duration", ['cnt'=>0])}}
                            </span>
                            <span ng-show="duration>120">
                                {{trans("project.ERRORS.maxlength.duration", ['cnt'=>120])}}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
            <br/>
            <div class="row" location="project" country="{{old('country_id')}}" department="{{old('department_id')}}">
                <label class="col-md-1 col-sm-2 require">
                    {{trans('project.LABELS.location')}}
                </label>
                <div class="col-md-3 col-sm-2">
                    <select class="form-control" name="country_id" ng-model="country_id" ng-change="loadDepart(country_id)"
                            required
                            ng-class="{'error':basicinfo.country_id.$touched && basicinfo.country_id.$error}"
                            ng-disabled="disabled.depart">
                        <option value="" disabled translate="location.country"></option>
                        @foreach($countries as $country)
                            <option value="{{$country->id}}">{{$country->name}} ({{$country->sortname}})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4">
                    <select class="form-control" ng-model="department_id" name="department_id"
                            ng-options="d.id as d.name for d in departments"
                            required ng-change="loadCity(department_id)"
                            ng-class="{'error':basicinfo.department_id.$touched && basicinfo.department_id.$error}"
                            ng-disabled="disabled.depart || disabled.city">
                        <option value="" disabled translate="location.department"></option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <select class="form-control" ng-model="city_id" name="city_id" ng-init="city_id = '{{old("city_id")}}'"
                            ng-class="{'error':basicinfo.city_id.$touched && basicinfo.city_id.$error}"
                            ng-options="c.id as c.name for c in cities" required >
                        <option value="" disabled translate="location.city"></option>
                    </select>
                </div>
            </div>
            <div class="row">
                @if ($errors->has('city_id'))
                    <div class="text-danger">
                        {{trans("project.ERRORS.require.location")}}
                    </div>
                @else
                    <div role="alert" class="col-md-offset-1 col-sm-offset-2 error" ng-class="{'visible':basicinfo.city_id.$touched || basicinfo.$submitted}">
                        <span ng-show="basicinfo.city_id.$error.required">{{trans("project.ERRORS.require.location")}}</span>
                    </div>
                @endif
            </div>
            <div class="row">
                <label class="col-md-1 col-sm-2 col-xs-4">
                    <span>{{trans("project.LABELS.lang")}}</span>
                </label>
                <div class="col-md-11 col-sm-10 col-xs-8 flext-left">
                    <span ng-repeat="l in langs">
                        <input name="lang[]" value="<%l.language_id%>" type="hidden" />
                        <span ng-bind="l.name" ></span>
                        <span class="btn text-danger fa fa-times" ng-click="removeLang(l.language_id)"></span>
                    </span>
                    <select id="newLang" ng-model="newLang" ng-change="addLang(newLang)">
                        <option disabled value="">{{trans("project.PLACES.lang")}}</option>
                        @foreach($languages->where('chosen', 0) as $lang)
                            <option id="opt_lang_{{$lang->id}}" value="{{$lang->id}}" rank="{{$lang->rank}}">{{$lang->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <br/>
            <div class="row">
                <label class="col-md-1 col-sm-2 col-xs-4 require">
                    <span>{{trans('project.LABELS.synopsis')}}</span>
                </label>
                <div class="col-md-11 col-sm-10 col-xs-8 flext-left"></div>
            </div>
            <textarea rows="3" name="synopsis" id="synopsis"
                      class="form-control" style="width:100%" ng-model="synopsis"  ng-init="synopsis = '{{old("synopsis")}}'"
                      placeholder="{{trans('project.PLACES.synopsis')}}"
                      ng-minlength="40" ng-maxlength="256" required>
                </textarea>
            @if ($errors->has('synopsis'))
                <div class="text-danger">
                    {{ $errors->first('synopsis') }}
                </div>
            @else
                <div role="alert" class="error" ng-class="{'visible':basicinfo.synopsis.$touched || basicinfo.$submitted}">
                    <span ng-show="basicinfo.synopsis.$error.required">{{trans("project.ERRORS.minlength.synopsis")}}</span>
                    <span ng-show="basicinfo.synopsis.$error.minlength">{{trans("project.ERRORS.minlength.synopsis")}}</span>
                    <span ng-show="basicinfo.synopsis.$error.maxlength">{{trans("project.ERRORS.maxlength.synopsis")}}</span>
                </div>
            @endif
            <br/>
            <div class="flex-rows">
                <div class="small text-danger">{{trans("project.NOTE")}}</div>
                <div>
                    <div class="btn btn-primary" ng-disabled="basicinfo.$invalid || duration < 1 || duration >120"
                         id="submit" ng-click="save(basicinfo.$invalid || duration < 1 || duration >120)">
                        {{trans('layout.BUTTONS.continue')}}
                    </div>
                </div>
            </div>
        </form>
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
    <script src="/js/controllers/user/creation.js"></script>
@endsection