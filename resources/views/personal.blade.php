@extends('layouts.zoomov')

@section('content')
    <link href="/bower_components/crop-master/cropper.min.css" rel="stylesheet" />
    <link href="/css/form.css" rel="stylesheet" />
<style>
    .profile{
        display: flex;
        justify-content: space-around;
    }

    .profile-image{
        position: relative;
        width: 150px;
        cursor: pointer;
    }

    .profile-image>div{
        position: absolute;
        top:0px;
        border-radius: 100px;
        border: 2px solid #999;
        width: 100%;
        height:150px;;
        text-align: center;
        padding-top: 60px;
        background: #999;
        color: #fff;
        opacity: 0.9;
        visibility: hidden;
    }

    .profile-image img{
        width: 100%;
        border-radius: 100px;
        border: 2px solid #999;
    }

    .poster{
        width: 100%;
        height: 100%;

    }

    .poster img{
        width: 150px;
        height:150px;
    }

    .img-preview {
        display:inline-block;
        margin-right: 1rem;
        margin-bottom: 1rem;
        overflow: hidden;
    }

    .picture-upload{
        display: none;
    }

    .img-container{
        display:none;
        text-align:center;
    }

    .preview-lg{
        width:150px;
        height:150px;
    }

    .preview-md{
        width:75px;
        height:75px;
    }

    .preview-sm{
        width:30px;
        height:30px;
    }

    #picture_wrapper{
        width:150px;
        height:150px;
    }



    .textarea:before{
        content: '*';
        color:rgb(215,116,89);
        position: absolute;
        left:0;
        width:20px;
        text-wrap: none;
    }


    .profile-image:hover>div{
        visibility: visible;
    }
    #inform-content>div{
        width: 640px;
    }

    .sns-gallery{
        display: flex;
        justify-content: flex-start;
    }

    .sns-gallery>div{
        padding:6px auto;
        width: 150px;
        text-align: center;
        position: relative;
    }

    .sns-gallery>div>.close{
        visibility: hidden;
    }

    .sns-gallery>div:hover .close{
        visibility: visible;
    }

    .sns-gallery>div>.close.top{
        position: absolute;
        top:20px;
        right:20px;
    }

    .sns-gallery>div>.close.bottom{
        position: absolute;
        top:20px;
        left:20px;
    }

    .sns-gallery>div>img{
        width:48px;
        cursor: pointer;
        -webkit-transition:all 0.5s;
        transition: all 0.5s;
    }
    .sns-gallery>div:hover{
        color: #808080;
    }
    .sns-gallery>div:hover img:not(.active), .sns-gallery>div>img.active{
        width: 36px;
        padding-top: 12px;
        opacity: 0.5;
        filter: alpha(opacity=50); /* For IE8 and earlier */
    }

    .sns-add-bar{
        position: relative;
    }

    .sns-add-bar>div{
        position: absolute;
    }

    .sns-add-bar .sns-add-buttons{
        right: 0;
        bottom: 8px;
        z-index:100;
    }

    .sns-add-buttons{
        display: flex;
        justify-content: flex-end;
    }

    .sns-add-bar .dropdown-menu{
        border: none;
    }
    .sns-add-bar .dropdown-menu li>a{
        border: none;
        padding: 3px;
    }
    .sns-new-bar{
        bottom: 0;
        right: 0px;
        z-index: -1;
        -webkit-transition: right 0.2s ease;
        -moz-transition: right 0.2s ease;
        -o-transition: right 0.2s ease;
        -ms-transition: right 0.2s ease;
        transition: right 0.2s ease;
    }

    .sns-new-bar input{
        width: 150px;
    }

    .sns-new-bar{
        display: flex;
        justify-content: flex-start;
    }

    .sns-new-bar.active{
        right: 92px;
        z-index: 99;
    }

    input:-moz-read-only { /* For Firefox */
        border-color: transparent;
    }

    input:read-only {
        border-color: transparent;
    }
    .tab-pane {
        border-right: 1px solid #eee;
        border-left: 1px solid #eee;
        padding: 12px 36px;
    }
</style>
<script type="text/ng-template" id="alert.html">
    <div class="modal-body" id="modal-body">
        <h3 translate="personal.message.<%alert%>"></h3>
        <ul>
            <li ng-repeat="e in errors" ng-bind="e"></li>
        </ul>
    </div>
    <div class="modal-footer">
        <button class="btn btn-default" type="button" ng-click="$close()">OK</button>
    </div>
</script>
<script type="text/ng-template" id="confirm.html">
    <div class="modal-body" id="modal-body">
        <h3 translate="personal.message.<%confirm%>"></h3>
    </div>
    <div class="modal-footer">
        <button class="btn btn-default" type="button" ng-click="$close(false)">{{trans("project.BUTTONS.cancel")}}</button>
        <button class="btn btn-danger" type="button" ng-click="$close(true)">{{trans("project.BUTTONS.confirm")}}</button>
    </div>
</script>
<div class="content" ng-controller="personalCtrl" ng-init="init('{{Auth::id()}}','{{Auth::user()->city_id}}','{{Auth::user()->birthday}}', '{{$occupations}}')">
    <div class="jumbotron container" style="margin: 50px auto;">
        <h3 translate="personal.title"></h3>
        <div class="slogan" translate="personal.slogan"></div>
        @if(!Auth::user()->active)<h6 class="text-center text-danger" translate="personal.message.active"></h6>@endif
    </div>
    <div class="container margin-bottom-lg" ng-switch="selectedTopTab">
        <uib-tabset justified="true">
            <uib-tab>
                <uib-tab-heading>
                    <span translate="personal.tabs.information"></span>
                </uib-tab-heading>
                <div class="text-center margin-top-sm margin-bottom-sm img-original">
                    <a class="poster"
                       onclick="$('#avatarInput').click()">
                        <img class="img-circle" ng-src="/context/avatars/{{Auth::id()}}.jpg"
                             onError="this.onerror=null;this.src='/images/avatar.png';"/>
                    </a>
                    <br/>
                    <div class="text-center margin-top-sm ">
                        <button class="btn btn-default" onclick="$('#avatarInput').click()">
                            {{trans('layout.BUTTONS.avatar')}}
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
                <br>
                <form id="picture-form" picture-content="user" enctype="multipart/form-data">
                    <div class="picture-upload">
                        <input type="text" class="avatar-src" name="picture_src">
                        <input type="text" class="avatar-data" name="picture_data">
                        <input type="text" name="picture_name" value="{{Auth::id()}}">
                        <input type="file" id="avatarInput" name="picture_file" accept="image/*"
                               onchange="angular.element(this).scope().pictureChanged()">
                        <input type="test" name="picture_dst" value="context/avatars">
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

                <br>
                @if(!Auth::user()->professioal)
                <form name="usrform" id="usrform">
                    {{ csrf_field() }}
                    <div class="row">
                        <label class="col-md-2 col-sm-4 require" translate="personal.PLACES.username"></label>
                        <div class="ool-md-10 col-sm-8">
                            <input type="text" class="form-text" ng-readonly="differenceInDays('{{Auth::user()->usernamed_at}}')<30"
                                  id="username" name="username" placeholder="<%'login.PLACES.username' | translate%>" value="{{Auth::user()->username}}"/>
                            <div class="error" role="alert" ng-class="{'visible':error.username}">
                                <span ng-show="error.username == 'r'" translate="login.ERRORS.require.Username"></span>
                                <span ng-show="error.username == 'i'" translate="login.ERRORS.minlength.Username"></span>
                                <span ng-show="error.username == 'm'" translate="login.ERRORS.maxlength.Username"></span>
                            </div>
                        </div>
                        <div class="col-md-offset-2 col-sm-offset-4">
                            <span class="text-danger small" ></span>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <label class="col-md-2 col-sm-4 require" translate="personal.PLACES.talents"></label>
                        <div class="ool-md-10 col-sm-8" id="roles">
                            <span ng-repeat="o in occupations|filter:{old: 1}">
                                <input name="role[<%o.id%>]" value="<%o.id%>" type="hidden" />
                                <span class="tag" ng-bind="o.name" ></span>
                                <span class="btn text-danger fa fa-times" ng-click="removeTalent(o)"></span>
                            </span>
                            <select id="newOccupation" class="form-text"
                                    ng-model="newTalent"
                                    ng-options="o as o.name for o in occupations|filter:{old: 0}|orderBy:name"
                                    ng-change="addTalent(newTalent)">
                                <option value="" disabled translate="personal.PLACES.occupation"></option>
                            </select>
                        </div>
                        <div class="error col-md-offset-2 col-sm-offset-4" role="alert"
                             ng-class="{'visible':!(occupations|filter:{old: 1}).length}">
                            <span translate="personal.ERRORS.require.Occupation"></span>
                        </div>
                    </div>
                    <br/>
                    <div class="row" location="user" country="{{$location ? $location->country_id : 0}}" department="{{$location ? $location->department_id : 0}}">
                        <label class="col-md-2 col-sm-4 require">
                            <span ng-bind="'personal.PLACES.location' | translate"></span>
                        </label>
                        <div class="col-md-3 col-sm-2">
                            <select class="form-control" ng-model="country_id" ng-change="loadDepart(country_id)"
                                    required
                                    ng-disabled="disabled.depart">
                                <option value="0" disabled translate="location.country"></option>
                                @foreach($countries as $country)
                                    <option ng-selected="country_id == '{{$country->id}}'" value="{{$country->id}}">{{$country->name}} ({{$country->sortname}})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-3">
                            <select class="form-control" ng-model="department_id"
                                    ng-options="d.id as d.name for d in departments"
                                    required ng-change="loadCity(department_id)"
                                    ng-disabled="disabled.depart || disabled.city">
                                <option value="" disabled translate="location.department"></option>
                            </select>
                        </div>
                        <div class="col-md-4 col-sm-3">
                            <select class="form-control" ng-model="user.city_id" name="city_id"
                                    ng-options="c.id as c.name for c in cities" required >
                                <option value="" disabled translate="location.city"></option>
                            </select>
                        </div>
                        <div class="error col-md-offset-2 col-sm-offset-4" role="alert"
                             ng-class="{'visible':usrform.city_id.$touched || usrform.submitted}">
                            <span ng-show="usrform.city_id.$error.required" translate="personal.ERRORS.require.Location"></span>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <label class="col-md-2 col-sm-4" translate="personal.PLACES.birthday"></label>
                        <div class="col-md-5 col-sm-7">
                            <span class="input-group">
                                <input type="text" class="form-text" uib-datepicker-popup name="birthday"
                                     ng-model="user.birthday"
                                     is-open="user.opened"
                                     show-button-bar="false"
                                     alt-input-formats="['M!/d!/yyyy']" />
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default" ng-click="openCalendar()"><i class="glyphicon glyphicon-calendar"></i></button>
                                </span>
                            </span>
                        </div>
                        <label class="col-md-2 col-sm-4 text-right" translate="personal.PLACES.sex"></label>
                        <div class="col-md-3 col-sm-8">
                            <select class="form-control" name="sex" id="sexOption">
                                <option ng-repeat="s in sexes" value="<%s%>" ng-selected="'{{Auth::user()->sex}}' == s" translate="personal.sex.<%s%>"></option>
                            </select>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <label class="col-md-2 col-sm-4 require"><span translate="personal.PLACES.description"></span></label>
                        <div class="col-md-10 col-sm-8"></div>
                    </div>
                    <div class="row">
                        <textarea class="text-control" style="width: 100%" id="presentation" name="presentation" translate
                                  translate-attr-placeholder="login.ERRORS.minlength.Presentation" rows="4">{{Auth::user()->presentation}}</textarea>
                        <div class="error" role="alert" ng-class="{'visible':error.presentation}">
                            <span ng-show="error.presentation == 'r'" translate="login.ERRORS.require.Presentation"></span>
                            <span ng-show="error.presentation == 'i'"  translate="login.ERRORS.minlength.Presentation"></span>
                            <span ng-show="error.presentation == 'm'" translate="login.ERRORS.maxlength.Presentation"></span>
                        </div>
                    </div>
                    <br>
                    <div class="text-right">
                        <div class="btn btn-primary" id="submit"
                             ng-click="save()"><span class="fa fa-check"></span> </div>
                    </div>
                </form>
                @endif
            </uib-tab>
            <uib-tab>
                <uib-tab-heading>
                    <span translate="personal.tabs.password"></span>
                </uib-tab-heading>
                <form name="pwdform" class="margin-top-lg">
                    <div class="form-group">
                        <input type="password" class="form-text"
                               name="old" placeholder="{{trans('passwords.old_password')}}"
                               ng-model="password.old"
                               required />
                        <div class="error" role="alert" ng-class="{'visible':pwdform.old.$touched || pwdform.$submitted}">
                            <span ng-show="pwdform.old.$error.required" translate="login.ERRORS.require.Pwd"></span>
                            <span ng-show="error.password" ng-bind="error.password"></span>
                        </div>
                    </div>
                    <br/>
                    <div class="form-group">
                        <input type="password" class="form-text" id="newpwd"
                               name="password" placeholder="{{trans('passwords.new_password')}}"
                               ng-model="password.password" ng-pattern="regex"
                               ng-minlength="6" ng-maxlength="16"
                               required />
                        <div class="error" role="alert" ng-class="{'visible':pwdform.password.$touched || pwdform.$submitted}">
                            <span ng-show="pwdform.password.$error.required || pwdform.password.$error.minlength || pwdform.password.$error.maxlength || pwdform.password.$error.pattern">
                                {{trans("passwords.password")}}
                            </span>
                            <span ng-show="error.newpwd">{{trans("passwords.password_same")}}</span>
                        </div>
                    </div>
                    <br/>
                    <div class="form-group">
                        <input type="password" class="form-text"
                               name="password_confirmation" placeholder="{{trans('passwords.password_confirmation')}}"
                               ng-model="password.password_confirmation" pw-check="newpwd"
                               required />
                        <div class="error" role="alert" ng-class="{'visible':pwdform.password_confirmation.$touched || pwdform.$submitted}">
                            <span ng-show="pwdform.password_confirmation.$error.required">{{trans("passwords.password")}}</span>
                            <span ng-show="pwdform.password_confirmation.$error.pwmatch">{{trans("passwords.password_different")}}</span>
                        </div>
                    </div>
                    <br>
                    <div class="text-right">
                        <div class="btn btn-primary" ng-disabled="pwdform.$invalid" ng-click="changePwd(password)"><span class="fa fa-check"></span> </div>
                    </div>
                </form>
            </uib-tab>
            <uib-tab select="selectTab()">
                <uib-tab-heading>
                    <span translate="personal.tabs.social"></span>
                </uib-tab-heading>
                <div ng-repeat="(key, value) in sns">
                    <form name="snsform_<%key%>">
                        <div>
                            <h4 translate="personal.SNS.<%key%>"></h4>
                        </div>
                        <div class="sns-gallery">
                            <div ng-repeat="s in value|filter:{sns_id:'!!'}">
                                <img ng-src="/images/sns/<%s.id%>.png" ng-class="{'active':editedSns.sns_id == s.sns_id}"  />
                                <input type="text" ng-readonly="editedSns.sns_id != s.sns_id"
                                       class="form-text text-center" ng-model="s.sns_name" />
                                <div class="close top fa" ng-class="{'fa-times':editedSns.sns_id != s.sns_id, 'fa-undo':editedSns.sns_id == s.sns_id}"
                                     ng-click="cancelSns(s)"></div>
                                <div class="close bottom fa" ng-class="{'fa-edit':editedSns.sns_id != s.sns_id, 'fa-check':editedSns.sns_id == s.sns_id}"
                                     ng-click="updateSns(s)"></div>
                            </div>
                        </div>
                        <br/>
                        <div class="sns-add-bar" >
                            <div class="sns-new-bar" ng-class="{'active': chosenSns.type == key}">
                                <div class="dropdown" id="new_<%key%>" style="padding: 3px; font-size: 14px;display: inline-block">
                                    <div class="dropdown-toggle" type="button" id="dropdown_<%key%>"
                                         data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        <img ng-src="/images/sns/<%chosenSns.id%>.png" ng-attr-title='<%s.name%>' style="width:36px"  />
                                        <span class="caret"></span>
                                    </div>
                                    <ul class="dropdown-menu">
                                        <li ng-repeat="s in value|filter:{sns_id:'!'}" ng-if="s.id != chosenSns.id">
                                            <a ng-click="choseSns(s)" href="javascript:void(0)">
                                                <img ng-src="/images/sns/<%s.id%>.png" style="width:36px" ng-attr-title='<%s.name%>' />
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div>
                                    <input type="text" class="form-text" name="new_sns_<%key%>" placeholder="<%'personal.PLACES.sns' | translate%>"
                                           ng-model="chosenSns.sns_name" required/>
                                </div>
                            </div>
                            <div class="sns-add-buttons">
                                <div class="btn fa" ng-click="addSns(key)"
                                     ng-class="{'btn-default fa-plus': chosenSns.type != key, 'bt-success fa-check':chosenSns.type == key}">
                                </div>
                                <div class="btn btn-default fa fa-undo" ng-class="hide"
                                     ng-show="chosenSns.type == key" ng-click="choseSns({id:null, type:null, sns_name:null})" >
                                </div>
                            </div>
                        </div>
                        <hr>
                    </form>
                </div>
            </uib-tab>
        </uib-tabset>
    </div>
</div>

    @endsection
@section('script')
    <script src="/bower_components/crop-master/cropper.js"></script>
    <script src="/js/directives/common.js"></script>
    <script src="/js/directives/picture.js"></script>
    <script src="/js/directives/location.js"></script>
    <script src="/js/controllers/admin/personal.js"></script>
@endsection