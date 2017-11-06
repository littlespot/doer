

    <div name="basicForm">
        @include('templates.picture')

        <form id="basicinfo" action="/admin/preparations" method="POST" name="basicinfo" class="form-horizontal">
            {{ csrf_field() }}
            <input type="hidden" value="{{$project->id}}" name="id">
            <input type="hidden" value="0" name="sendFlag" id="sendFlag">
            <div class="row">
                <label class="col-md-1 col-sm-2 col-xs-4 require">
                    {{trans('project.LABELS.title')}}
                </label>
                <div class="col-md-6 col-sm-10">
                    <input name="title" id="title" type="text" class="form-text"
                           ng-class="{'error':basicinfo.title.$touched && basicinfo.title.$error}"
                           ng-model="project.title"
                           placeholder="{{trans('project.PLACES.title')}}"
                           ng-maxlength="40" required/>
                    <div role="alert" class="error" ng-class="{'visible':basicinfo.title.$touched}">
                        <span ng-show="basicinfo.title.$error.required">{{trans("project.ERRORS.require.title")}}</span>
                        <span ng-show="basicinfo.title.$error.maxlength">{{trans("project.ERRORS.maxlength.title")}}</span>
                    </div>
                </div>
                <label class="col-md-1 col-sm-2 col-xs-4 require">
                    {{trans('project.LABELS.genre')}}
                </label>
                <div class="col-md-4 cols-sm-10">
                    <select class="form-control" name="genre_id" ng-model="project.genre_id" id="genre" required>
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
                                     ng-model="project.finish_at"
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
                        <input type="number" ng-model="project.duration" name="duration" class="form-control"
                               placeholder="{{trans('project.PLACES.duration')}}"
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
                            <span ng-show="project.duration < 1">
                                {{trans("project.ERRORS.minlength.duration", ['cnt'=>0])}}
                            </span>
                            <span ng-show="project.duration>120">
                                {{trans("project.ERRORS.maxlength.duration", ['cnt'=>120])}}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
            <br/>
            <div class="row" location="project" country="{{$project->country_id}}"
                 department="{{$project->department_id}}">
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
                            <option @if($country->id == $project->country_id) selected @endif
                            value="{{$country->id}}">{{$country->name}} ({{$country->sortname}})</option>
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
                    <select class="form-control" ng-model="project.city_id" name="city_id"
                            ng-class="{'error':basicinfo.city_id.$touched && basicinfo.city_id.$error}"
                            ng-options="c.id as c.name for c in cities" required >
                        <option value="" disabled translate="location.city"></option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div role="alert" class="col-md-offset-1 col-sm-offset-2 error" ng-class="{'visible':basicinfo.city_id.$touched || basicinfo.$submitted}">
                    <span ng-show="basicinfo.city_id.$error.required">{{trans("project.ERRORS.require.location")}}</span>
                </div>
            </div>
            <div class="row">
                <label class="col-md-1 col-sm-2 col-xs-4">
                    <span>{{trans("project.LABELS.lang")}}</span>
                </label>
                <div class="col-md-11 col-sm-10 col-xs-8 flext-left">
                    <span ng-repeat="l in project.lang">
                        <input name="lang[]" value="<%l.language_id%>" type="hidden" />
                        <span ng-bind="l.name" id="lang_name_<%l.language_id%>" rank="<%l.rank%>"></span>
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
                      class="form-control" style="width:100%" ng-model="project.synopsis"
                      placeholder="{{trans('project.PLACES.synopsis')}}"
                      ng-minlength="40" ng-maxlength="256" required>
                </textarea>
            <div role="alert" class="error" ng-class="{'visible':basicinfo.synopsis.$touched || basicinfo.$submitted}">
                <span ng-show="basicinfo.synopsis.$error.required">{{trans("project.ERRORS.minlength.synopsis")}}</span>
                <span ng-show="basicinfo.synopsis.$error.minlength">{{trans("project.ERRORS.minlength.synopsis")}}</span>
                <span ng-show="basicinfo.synopsis.$error.maxlength">{{trans("project.ERRORS.maxlength.synopsis")}}</span>
            </div>
        </form>
        <br/>
        <div class="flex-rows">
            <div class="small text-danger">{{trans("project.NOTE")}}</div>
            <div>
                <div class="btn btn-primary" ng-disabled="basicinfo.$invalid || project.duration < 1 || project.duration>120" id="submit" ng-click="save(basicinfo.$invalid || project.duration < 1 || project.duration>120)">
                    {{trans('layout.BUTTONS.continue')}}
                </div>
            </div>
        </div>
    </div>