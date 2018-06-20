@extends('project.top')

@section('tabcontent')
    <link href="/bower_components/crop-master/cropper.min.css" rel="stylesheet" />
    <link href="/css/project.css" rel="stylesheet" />
    <link href="/css/picture.css" rel="stylesheet" />
    <link href="/bower_components/crop-master/cropper.min.css" rel="stylesheet" />
    <link href="/css/project.css" rel="stylesheet" />
    <link href="/css/picture.css" rel="stylesheet" />
    <div class="p-5 bg-white" ng-controller="preparationCtrl" ng-init="init('{{$project->finish_at}}', {{$project->city_id}})">
        @include('templates.picture', ['uploadUrl'=>'storage', 'pictureFolder'=>'projects', 'pictureId'=>$project->id])
        <div id="basicinfo" class="form-horizontal">
            {{ csrf_field() }}
            <input type="hidden" value="{{$project->id}}" name="id">
            <input type="hidden" value="0" name="sendFlag" id="sendFlag">
            <div class="form-group row">
                <div class="col-md-10 col-sm-8">
                    <div class="input input--isao">
                        <input class="input__field input__field--isao" id="project_title" name="title" ng-maxlength="40" value="{{$project->title}}" alt="{{$project->title}}"
                               ng-disabled="!project.title"/>
                        <label class="input__label input__label--isao" for="project_title" data-content="{{trans('project.PLACES.title')}}">
                            <span class="input__label-content input__label-content--isao">
                                <i class="text-danger pr-1">*</i>
                                <span ng-show="!errors.title">{{trans('project.LABELS.title')}}</span>
                                <span class="text-danger" ng-show="errors.title == 1">{{trans("project.ERRORS.require.title")}}</span>
                                <span class="text-danger" ng-show="errors.title == 2">{{trans("project.ERRORS.maxlength.title")}}</span>
                            </span>
                        </label>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4">
                    <span class="btn text-info fa fa-edit" ng-show="!project.title" ng-click="project.title=true"></span>
                    <button class="btn btn-success fa fa-check" ng-show="project.title"  ng-click="save('{{$project->id}}', 'title', 1, 40)"></button>
                    <span class="btn text-secondary fa fa-undo" ng-show="project.title" ng-click="cancel('title')"></span>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-4 col-xs-8 ">
                    <div class="input input--isao">
                        <select class="input__field input__field--isao" name="genre_id" id="project_genre"  ng-disabled="!project.genre" alt="{{$project->genre_id}}">
                            @foreach($genres as $genre)
                                <option value="{{$genre->id}}" {{$genre->id == $project->genre_id ? 'selected':''}}>{{$genre->name}}</option>
                            @endforeach
                        </select>
                        <label class="input__label input__label--isao" for="input-title" data-content="{{trans('project.PLACES.genre')}}">
                            <span class="input__label-content input__label-content--isao">
                                <i class="text-danger pr-1">*</i>
                                <span ng-show="!errors.genre"> {{trans('project.LABELS.genre')}}</span>
                                <span class="text-danger" ng-show="errors.genre">{{trans("project.ERRORS.require.genre")}}</span>
                            </span>
                        </label>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4">
                    <span class="btn text-info fa fa-edit" ng-show="!project.genre" ng-click="project.genre=true"></span>
                    <button class="btn btn-success fa fa-check" ng-show="project.genre"  ng-click="save('{{$project->id}}', 'genre', 1)"></button>
                    <span class="btn text-secondary fa fa-undo" ng-show="project.genre" ng-click="cancel('genre')"></span>
                </div>
                <div class="col-md-4 col-xs-8 form-group">
                    <div class="input input--isao">
                        <input type="number" id="project_duration" name="duration" class="input__field input__field--isao" alt="{{$project->duration}}"
                               value="{{$project->duration}}" ng-disabled="!project.duration"/>
                        <label class="input__label input__label--isao" for="input-title" data-content="{{trans('project.PLACES.duration')}}">
                            <span class="input__label-content input__label-content--isao">
                                <i class="text-danger pr-1">*</i>
                                <span ng-show="!errors.duration">{{trans('project.LABELS.duration')}}</span>
                                <span class="text-danger" ng-show="errors.duration == 1">{{trans("project.ERRORS.require.duration")}}</span>
                                <span class="text-danger" ng-show="errors.duration == 2">{{trans("project.ERRORS.maxlength.duration", ['cnt'=>1200])}}</span>
                            </span>
                        </label>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4">
                    <span class="btn text-info fa fa-edit" ng-show="!project.duration" ng-click="project.duration=true"></span>
                    <button class="btn btn-success fa fa-check" ng-show="project.duration"  ng-click="save('{{$project->id}}', 'duration', 1, 1200)"></button>
                    <span class="btn text-secondary fa fa-undo" ng-show="project.duration" ng-click="cancel('duration')"></span>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-4 col-xs-12">
                    <div class="input input--isao">
                        <input class="input__field input__field--isao" uib-datepicker-popup="yyyy-MM-dd" id="project_finish" alt="{{$project->finish_at}}"
                               ng-model="finish_at"
                               name="finish_at"
                               is-open="calendar.opened"
                               show-button-bar="false"
                               popup-placement="left"
                               ng-required="true"
                               ng-disabled="!project.finish"
                               placeholder="{{trans("project.PLACES.finish")}}"
                               alt-input-formats="['M!/d!/yyyy']"
                        />
                        <span class="input-group-btn">
                            <button ng-show="project.finish" type="button" class="btn btn-outline-secondary" ng-click="openCalendar()"><i class="fa fa-calendar"></i></button>
                        </span>
                        <label class="input__label input__label--isao" for="input-title" data-content="{{trans('project.PLACES.finish')}}">
                            <span class="input__label-content input__label-content--isao">
                                <i class="text-danger pr-1">*</i>
                                <span ng-show="!errors.finish">{{trans('project.LABELS.finish')}}</span>
                                <span class="text-danger" ng-show="errors.finish==1">{{trans("project.ERRORS.require.finish")}}</span>
                                <span class="text-danger" ng-show="errors.finish==2">{{trans("project.ALERTS.finish")}}</span>
                            </span>
                        </label>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4">
                    <span class="btn text-info fa fa-edit" ng-show="!project.finish" ng-click="project.finish=true"></span>
                    <button class="btn btn-success fa fa-check" ng-show="project.finish"  ng-click="saveDate('{{$project->id}}')"></button>
                    <span class="btn text-secondary fa fa-undo" ng-show="project.finish" ng-click="cancelDate()"></span>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <label class="pl-1 small text-primary">
                    <sup class="text-danger pr-1">*</sup>{{trans('project.LABELS.location')}}
                </label>
                <div class="row">
                    <div class="col-md-4 col-xs-12 input input--isao">
                        <select class="input__field input__field--isao" name="country_id" id="project_country" ng-disabled="!project.city"
                                ng-model="project.country_id"
                                ng-change="changeCountry(project.country_id, 'project')">
                            <option value="">{{trans('project.LABELS.country')}}</option>
                            @foreach($countries as $country)
                                <option value="{{$country->id}}">{{$country->name}}</option>
                            @endforeach
                        </select>
                        <label class="input__label input__label--isao" for="country_id" data-content="{{trans('project.LABELS.country')}}">
                            <span class="input__label-content input__label-content--isao"  ng-show="project.city">
                                 <i class="text-danger pr-1">*</i>
                                {{trans("project.LABELS.country")}}
                            </span>
                        </label>
                    </div>
                    <div class="col-sm-4 col-xs-12 input input--isao">
                        <select class="input__field input__field--isao" name="department_id" id="project_department" ng-disabled="!project.city"
                                ng-model="project.department_id"
                                ng-options="d.id as d.name for d in project.departments"
                                ng-change="changeDepartment(project.department_id, 'project')">
                            <option value="" disabled>{{trans("project.PLACES.department")}}</option>
                        </select>
                        <label class="input__label input__label--isao" for="department_id" data-content="{{trans('project.LABELS.department')}}">
                            <span class="input__label-content input__label-content--isao"  ng-show="project.city">
                                <i class="text-danger pr-1">*</i>{{trans("project.LABELS.department")}}
                            </span>
                        </label>
                    </div>
                    <div class="col-sm-4 col-xs-12  input input--isao">
                        <select class="input__field input__field--isao" name="city_id" id="project_city" ng-disabled="!project.city"
                                ng-model="project.city_id"
                                ng-options="c.id as c.name for c in project.cities">
                            <option value="" disabled>{{trans("project.PLACES.city")}}</option>
                        </select>
                        <label class="input__label input__label--isao" for="city_id" data-content="{{trans('project.LABELS.city')}}">
                            <span class="input__label-content input__label-content--isao"  ng-show="project.city">
                                <i class="text-danger pr-1">*</i>
                                <span class="text-danger" ng-show="errors.city">{{trans("project.ERRORS.require.city")}}</span>
                                <span ng-show="!errors.city">{{trans("project.LABELS.city")}}</span>
                            </span>
                        </label>
                    </div>
                </div>
                <div class="text-right">
                    <span class="btn text-info fa fa-edit" ng-show="!project.city" ng-click="project.city=true;"></span>
                    <button class="btn btn-success fa fa-check" ng-show="project.city"  ng-click="save('{{$project->id}}', 'city', 1)"></button>
                    <span class="btn text-secondary fa fa-undo" ng-show="project.city" ng-click="cancel('city')"></span>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <label class="small text-primary">{{trans("project.LABELS.lang")}}</label>
                <div class="row ">
                    <div class="col-md-8 col-xs-12 pl-5">
                        @foreach($languages->where('chosen', 1) as $lang)
                            <span class="pr-3" id="lang_{{$lang->id}}">
                               <span >{{$lang->name}}</span>
                            <span class="btn text-danger fa fa-times-circle" ng-click="removeLang('{{$project->id}}','{{$lang->id}}')"></span>
                          </span>
                        @endforeach
                        <span class="pr-3" ng-repeat="lang in languages">
                              <span ng-bind="lang.name"></span>
                            <span class="btn text-danger fa fa-times-circle" ng-click="removeLang('{{$project->id}}', lang.id)"></span>
                          </span>
                    </div>
                    <div class="col-md-4 col-xs-12">
                        <div class="input input--isao">
                            <select class="input__field input__field--isao" name="newLang" id="newLang" ng-model="newLang" ng-change="addLang('{{$project->id}}')">
                                @foreach($languages as $lang)
                                    <option id="opt_lang_{{$lang->id}}"  value="{{$lang->id}}" rank="{{$lang->rank}}" {{$lang->chosen?'disabled':''}}>{{$lang->name}}</option>
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
            <label class="small text-primary">
                <sup class="text-danger pr-1">*</sup>{{trans('project.LABELS.synopsis')}}
            </label>
            <div class="input input--isao">
                <textarea rows="3" name="synopsis" id="project_synopsis"
                          class="input__field input__field--isao" ng-disabled="!project.synopsis">{{$project->synopsis}}</textarea>
                <label class="input__label input__label--isao" for="synopsis" data-content="{{trans('project.PLACES.synopsis')}}">
                    <span class="input__label-content input__label-content--isao" ng-show="project.synopsis">
                        <i class="text-danger pr-1">*</i>
                        <span class="text-danger" ng-show="!errors.synopsis"> {{trans('project.LABELS.synopsis')}}</span>
                        <span ng-show="errors.synopsis == 1">{{trans("project.ERRORS.minlength.synopsis")}}</span>
                         <span ng-show="errors.synopsis == 2">{{trans("project.ERRORS.maxlength.synopsis")}}</span>
                    </span>
                </label>
            </div>
            <div class="text-right">
                <span class="btn text-info fa fa-edit" ng-show="!project.synopsis" ng-click="project.synopsis=true;"></span>
                <button class="btn btn-success fa fa-check" ng-show="project.synopsis"  ng-click="save('{{$project->id}}', 'synopsis', 40, 256)"></button>
                <span class="btn text-secondary fa fa-undo" ng-show="project.synopsis" ng-click="cancel('synopsis')"></span>
            </div>
        </div>
        <br/>
        <div class="alert alert-danger">{{trans("project.NOTE")}}</div>
        <div class="text-right">
            <a href="/admin/preparations/{{$project->id}}?step=1">
                {{trans('layout.BUTTONS.next')}}
            </a>
        </div>
    </div>
@endsection
@section('tabscript')
    <script src="/bower_components/crop-master/cropper.js"></script>
    <script src="/js/directives/picture.js"></script>
    <script src="/js/controllers/project/basic.js"></script>
@endsection