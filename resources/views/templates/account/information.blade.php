<div class="modal fade" id="informationChangedModal" tabindex="-1" role="dialog" aria-labelledby="informationChangedModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="modal-body">
                <div>{{trans('personal.ALERTS.info_changed')}}</div>
                <div class="alert alert-warning">{{trans('personal.ALERTS.page_jump')}}</div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" ng-click="cancelPageJump()" >
                    {{trans("project.BUTTONS.cancel")}}
                </button>
                <button class="btn btn-success" type="button" ng-click="confirmPageJump()" >
                    {{trans("project.BUTTONS.confirm")}}
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="informationErrorModal" tabindex="-1" role="dialog" aria-labelledby="informationErrorModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="modal-body">
                <div ng-bind="errors"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-info" type="button" data-dismiss="modal">
                    {{trans("project.BUTTONS.confirm")}}
                </button>
            </div>
        </div>
    </div>
</div>
@include('templates.picture', ['uploadUrl'=>'storage', 'pictureFolder'=>'avatars', 'pictureId'=>auth()->id()])

<form name="usrform" id="usrform">
    {{ csrf_field() }}
    <div class="card">
        <div class="card-header">
            <h6 class="card-title">{{trans('personal.LABELS.personal')}}<span ng-if="!user.edit.info" class="btn text-info pl-1 fa fa-edit" ng-click="editInfo()"></span></h6>
        </div>
        <div class="card-body">
            <div class="row" ng-show="!user.edit.info">
                <div class="col-lg-7 col-md-12 col-xs-12 form-group">
                    <label for="input-title">
                        <i class="text-danger pr-1">*</i>{{trans('personal.LABELS.username')}}:
                    </label>
                    <span ng-bind="user.username"></span>
                </div>
                <div class="col-lg-3 col-md-6 col-xs-12 form-group">
                    <label for="input-title">
                        {{trans('personal.LABELS.birthday')}}:
                    </label>
                    <span ng-bind="user.birthdayFormat"></span>
                </div>
                <div class="col-lg-2 col-md-6 col-xs-12 form-group">
                    <label for="input-title">
                        {{trans('personal.LABELS.sex')}}:
                    </label>
                    <span id="userSexe">
                        @if($user->sex)
                        {{trans('personal.SEX.'.strtolower(auth()->user()->sex))}}
                        @endif
                    </span>
                </div>
            </div>
            <div ng-if="user.edit.info" >
                <div class="row">
                @if($user->username_datediff >= 30)
                <div class="col-lg-7 col-md-12 col-xs-12 form-group" >
                    <div class="input input--isao">
                        <input class="input__field input__field--isao" type="text"
                               id="username" name="username" ng-model="user.username"
                               ng-maxlength="16" required/>
                        <label class="input__label input__label--isao" for="input-title"
                               data-content="{{trans('personal.PLACES.username')}}">
                            <span class="input__label-content input__label-content--isao">
                                <i class="text-danger pr-1">*</i>
                                @if($errors->has('username'))
                                    <span class="text-danger">{{trans('personal.ERRORS.require_username', ['cnt'=>40])}}</span>
                                @else
                                    <span ng-if="!usrform.username.$error.required && !usrform.username.$error.maxlength">{{trans('personal.LABELS.username')}}</span>
                                    <span class="text-danger" ng-if="usrform.username.$error.required || usrform.username.$error.maxlength">{{trans('personal.ERRORS.require_username', ['cnt'=>40])}}</span>
                                @endif
                            </span>
                        </label>
                    </div>
                </div>
                @else
                <div class="col-lg-7 col-md-12 col-xs-12 form-group">
                    <div class="input input--isao">
                        <input class="input__field input__field--isao" type="text"
                               id="username" value="{{$user->username}}"
                               readonly />
                        <label class="input__label input__label--isao {{$errors->has('username')?'isao_error':''}}" for="username"
                               data-content="{{trans('personal.PLACES.username')}}">
                            <span class="input__label-content input__label-content--isao text-danger">{{trans('personal.ERRORS.require_days', ['cnt'=>30-$user->username_datediff])}}</span>
                        </label>
                    </div>
                </div>
                @endif
                <div class="col-lg-3 col-md-6 col-xs-12 form-group">
                    <div class="input input--isao">
                        <input type="text" class="input__field input__field--isao" uib-datepicker-popup="yyyy-MM-dd"
                               name="birthday"
                               ng-model="user.birthday"
                               is-open="user.opened"
                               show-button-bar="false"
                               popup-placement="left"
                               ng-required="true"
                               placeholder="yyyy-MM-dd"
                               alt-input-formats="['M!/d!/yyyy']" />
                        <span class="input-group-btn">
                                <button type="button" class="btn btn-outline-secondary" ng-click="openCalendar()"><i class="fa fa-calendar"></i></button>
                            </span>
                        <label class="input__label input__label--isao" for="input-title" data-content="{{trans('personal.PLACES.birthday')}}">
                            <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.birthday')}}</span>
                        </label>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-xs-12 form-group">
                    <div class="input input--isao">
                        <select class="input__field input__field--isao" name="sex" id="sexOption">
                            @foreach(trans('personal.SEX') as $key=>$val)
                                <option value="{{$key}}" ng-selected="user.sex == '{{$key}}'" id="sexOption_{{$key}}">{{$val}}</option>
                            @endforeach
                        </select>
                        <label class="input__label input__label--isao" for="input-title" data-content="{{trans('personal.PLACES.sex')}}">
                            <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.sex')}}</span>
                        </label>
                    </div>
                </div>
            </div>
                <div class="text-right">
                    <span class="btn btn-outline-secondary fa fa-undo" ng-click="cancelInfo()"></span>
                    <span class="btn btn-primary fa fa-check" ng-click="saveInfo()"></span>
                </div>
            </div>
        </div>
    </div>
    <hr/>
    <div class="card">
        <div class="card-header">
            <h6 class="card-title"><sup class="text-danger">*</sup>{{trans('personal.PLACES.location')}}<span ng-if="!user.edit.city" class="btn text-info pl-1 fa fa-edit" ng-click="user.edit.city=true"></span></h6>
        </div>
        <div class="card-body">
            <div ng-if="user.city_id && !user.edit.city" id="user_location">
                @if($location)
                    {{$location->city_name}}({{$location->country_name}})
                @endif
            </div>
            <div ng-if="!user.city_id || user.edit.city">
                <div class="row">
                    <div class="col-md-4 col-xs-12 input input--isao" ng-init="loadLocation(user.city_id, 'location')">
                        <select class="input__field input__field--isao"  ng-model="location.country_id" ng-change="changeCountry(location.country_id, 'location')"
                                required
                                ng-disabled="disabled.depart">
                            @foreach($countries->where('region', 0) as $country)
                                <option  value="{{$country->id}}">{{$country->name}}</option>
                            @endforeach
                        </select>
                        <label class="input__label input__label--isao" for="country_id"
                               ng-class="{'isao_error':usrform.country_id.$error.required && (usrform.country_id.$touched || usrform.submitted)}"
                               data-error="{{trans('personal.ERRORS.require_location')}}"
                               data-content="{{trans('project.LABELS.country')}}">
                            <span class="input__label-content input__label-content--isao"><i class="text-danger pr-1">*</i>{{trans("project.LABELS.country")}}</span>
                        </label>
                    </div>
                    <div class="col-md-4 col-xs-12 input input--isao">
                        <select class="input__field input__field--isao" ng-model="location.department_id"
                                ng-options="d.id as d.name for d in location.departments"
                                required
                                ng-change="changeDepartment(location.department_id, 'location')"
                                ng-disabled="disabled.depart || disabled.city">
                            <option value="" disabled>{{trans('personal.LABELS.state')}}</option>
                        </select>
                        <label class="input__label input__label--isao" for="department_id"
                               ng-class="{'isao_error':usrform.department_id.$error.required && (usrform.department_id.$touched || usrform.submitted)}"
                               data-error="{{trans('personal.ERRORS.require_location')}}"
                               data-content="{{trans('project.LABELS.department')}}">
                            <span class="input__label-content input__label-content--isao"><i class="text-danger pr-1">*</i>{{trans("project.LABELS.department")}}</span>
                        </label>
                    </div>
                    <div class="col-md-4 col-xs-12 input input--isao">
                        <select class="input__field input__field--isao" ng-model="location.city_id" name="city_id"
                                ng-options="c.id as c.name for c in location.cities" required >
                            <option value="" disabled>{{trans('personal.LABELS.city')}}</option>
                        </select>
                        <label class="input__label input__label--isao" for="city_id"
                               ng-class="{'isao_error':usrform.city_id.$error.required && (usrform.city_id.$touched || usrform.submitted)}"
                               data-error="{{trans('personal.ERRORS.require_location')}}"
                               data-content="{{trans('project.LABELS.city')}}">
                            <span class="input__label-content input__label-content--isao"><i class="text-danger pr-1">*</i>{{trans("project.LABELS.city")}}</span>
                        </label>
                    </div>
                </div>
                <div class="text-right">
                    <span class="btn btn-outline-secondary fa fa-undo" ng-click="user.edit.city = false;"></span>
                    <span class="btn btn-primary fa fa-check" ng-click="saveLocation(location.city_id, 'user', 'users');user.edit.city=false;"></span>
                </div>
            </div>
        </div>
    </div>
    <hr/>
    <div class="card">
        <div class="card-header d-flex">
            <h6 class="card-title"><sup class="text-danger">*</sup>{{trans('personal.PLACES.talents')}}<span ng-if="!user.edit.occupation" class="btn text-info pl-1 fa fa-edit" ng-click="user.edit.occupation=true"></span></h6>
        </div>
        <div class="card-body pl-3">
            <div id="roles">
                <span ng-repeat="o in occupations|filter:{old: 1}" >
                    <input name="role[<%o.id%>]" value="<%o.id%>" type="hidden" />
                    <span ng-bind="o.name" class="badge badge-info badge-pill"></span>
                    <span ng-if="!user.edit.occupation" class="btn"></span>
                    <span ng-if="user.edit.occupation" class="btn text-danger fa fa-times" ng-click="o.old=0"></span>
                </span>
            </div>
            <br/>
            <div class="ml-3" ng-if="user.edit.occupation">
                <div class="input input--isao">
                    <select class="input__field input__field--isao" id="newOccupation" ng-model="newTalent"
                            ng-change="newTalent.old = 1"
                            ng-options="o as o.name for o in occupations|filter:{old: 0}|orderBy:name">
                        <option value="" disabled>{{trans("personal.PLACES.occupation")}}</option>
                    </select>
                    <label class="input__label input__label--isao" for="newOccupation" data-content="{{trans('personal.PLACES.occupation')}}">
                        <span class="input__label-content input__label-content--isao">{{trans('personal.LABELS.occupation')}}</span>
                    </label>
                </div>
            </div>
            <div class="alert alert-danger" ng-if="!(occupations|filter:{old: 1}).length">{{trans('personal.ERRORS.require_occupation')}}</div>
            <div class="text-right" ng-if="user.edit.occupation">
                <span class="btn btn-outline-secondary fa fa-undo" ng-click="user.edit.occupation=false"></span>
                <span class="btn btn-primary fa fa-check" ng-click="saveTalents()"></span>
            </div>
        </div>
    </div>
    <hr/>
    <div class="card">
        <div class="card-header">
            <h6 class="card-title">{{trans('personal.LABELS.description')}}<span ng-if="!presentationCopy" class="btn text-info pl-1 fa fa-edit" ng-click="editPresentation()"></span></h6>
        </div>
        <div class="card-body">
            <div ng-show="!user.edit.presentation" id="presentationDiv">{{$user->presentation}}</div>
            <div ng-show="user.edit.presentation">
                <div class="input input--isao">
                    <textarea class="input__field input__field--isao" id="presentation" name="presentation" ng-model="presentation"
                              placeholder="{{trans('personal.PLACES.description')}}"
                              rows="4"
                              ng-maxlength="800"></textarea>
                    <label class="input__label input__label--isao" for="presentation"
                           data-content="{{trans('personal.LABELS.description')}}">
                        <span class="input__label-content input__label-content--isao">
                            <span ng-if="!usrform.presentation.$error.maxlength">{{trans('personal.LABELS.description')}}</span>
                            <span ng-if="usrform.presentation.$error.maxlength" class="text-danger">{{trans('personal.ERRORS.maxlength_presentation', ['cnt'=>800])}}</span>
                        </span>
                    </label>
                    <div class="text-right">
                        <span class="btn btn-outline-secondary fa fa-undo" ng-click="cancelPresentation()"></span>
                        <span class="btn btn-primary fa fa-check" ng-click="savePresentation(usrform.presentation.$error.maxlength)"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <br>
    <div class="text-right">
        <a href="{{$previous}}" class="btn btn-outline-danger">{{trans('layout.BUTTONS.back')}}</a>
    </div>
</form>