@extends('project.top')

@section('tabcontent')
    <link href="/bower_components/crop-master/cropper.min.css" rel="stylesheet" />
    <link href="/css/project.css" rel="stylesheet" />
    <link href="/css/picture.css" rel="stylesheet" />
    <div class="content-margin" ng-controller="projectCtrl" ng-init="init('{{$langs}}')">
        @include('templates.picture')
        <form id="basicinfo" action="/admin/projects" method="POST" name="basicinfo" class="form-horizontal">
            {{ csrf_field() }}
            <input type="hidden" value="{{$project->id}}" name="id">
            <input type="hidden" value="{{$step}}" name="step">
            <input type="hidden" value="0" name="returnFlag">
            <div class="row">
                <label class="col-md-1 col-sm-2 col-xs-4 require">
                    {{trans('project.LABELS.duration')}}
                </label>
                <div class="col-md-4 col-sm-3 col-xs-8">
                    <div class="input-group">
                        <input type="number" ng-model="project.duration" name="duration" class="form-control"
                               ng-init="project.duration = {{$project->duration}}"
                               placeholder="{{trans('project.PLACES.duration')}}"
                               ng-class="{'error':basicinfo.duration.$touched && basicinfo.duration.$error}"
                               required />
                        <span class="input-group-addon" translate="project.Duration" ></span>
                    </div>
                    <div role="alert" class="error" ng-class="{'visible':basicinfo.duration.$touched || basicinfo.$submitted}">
                        <span ng-show="basicinfo.duration.$error.required">
                            {{trans("project.ERRORS.require.duration")}}
                        </span>
                    </div>
                </div>

            </div>
            <br>
            <div class="row">
                <label class="col-md-1 col-sm-2 col-xs-4 require">
                    <span>{{trans("project.LABELS.lang")}}</span>
                </label>
                <div class="col-md-11 col-sm-10 col-xs-8 flext-left">
                    <span ng-repeat="l in project.lang">
                        <input name="lang[]" value="<%l.language_id%>" type="hidden" />
                        <span ng-bind="l.name" ></span>
                        <span class="btn text-danger fa fa-times" ng-click="removeLang(l.language_id)"></span>
                    </span>
                    <select id="newLang" ng-model="newLang" ng-change="addLang(newLang)">
                        <option disabled value="">{{trans("project.PLACES.lang")}}</option>
                        @foreach($languages->where('chosen',0) as $lang)
                            <option id="opt_lang_{{$lang->id}}" value="{{$lang->id}}" rank="{{$lang->rank}}">{{$lang->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
            <br>
            <div class="row">
                <label class="col-md-1 col-sm-2 col-xs-4">
                    {{trans('project.LABELS.genre')}}
                </label>
                <div class="col-md-2 cols-sm-4 col-xs-8">
                    <span class="text-info">{{$project->genre_name}}</span>
                </div>
                <label class="col-md-1 col-sm-2 col-xs-4 text-center">
                    {{trans('project.LABELS.location')}}
                </label>
                <div class="col-md-2 cols-sm-4 col-xs-8">
                    <span class="text-info">{{$project->country}}</span> -
                    <span class="text-info">{{$project->department}}</span> -
                    <span class="text-info">{{$project->city}}</span>
                </div>
                <label class="col-md-1 col-sm-2 col-xs-4">
                    {{trans('project.LABELS.finish')}}
                </label>
                <div class="col-md-2 cols-sm-4 col-xs-8">
                    <span class="text-info">{{$project->finish_at}}</span>
                </div>
            </div>
            <br/>
            <div class="row">
                <label class="col-md-1 col-sm-2 col-xs-4 require">
                    <span>{{trans('project.LABELS.synopsis')}}</span>
                </label>
                <div class="col-md-11 col-sm-10 col-xs-8 flext-left"></div>
            </div>
            {!! $project->synopsis !!}
        <br/>
        <div class="flex-rows">
            <div class="small text-danger">{{trans("project.NOTE")}}</div>
            <div>
                <div id="btnSubmit" class="btn btn-primary" ng-disabled="basicinfo.$invalid" id="submit" ng-click="save(basicinfo.$invalid)">
                    {{trans('layout.BUTTONS.continue')}}
                </div>
            </div>
        </div>
    </div>

@endsection
@section('tabscript')
    <script src="/bower_components/crop-master/cropper.js"></script>
    <script src="/js/directives/picture.js"></script>
    <script src="/js/controllers/project/basic.js"></script>
@endsection