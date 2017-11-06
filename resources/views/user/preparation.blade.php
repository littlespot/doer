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


    .tab-menu-bar{
        display: flex;
        justify-content: space-around;
    }

    .tab-menu-item{
        width: 100%;
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
        <button class="btn btn-default" type="button" ng-click="$close(false)">{{trans("project.BUTTONS.cancel")}}</button>
        <button class="btn btn-danger" type="button" ng-click="$close(true)">{{trans("project.BUTTONS.confirm")}}</button>
    </div>
</script>
<script type="text/ng-template" id="script.html">
    <div class="modal-header">
        <div class="font-md" translate="script.info"></div>
    </div>
    <div class="modal-body">
        <div ng-if="script" class="alert alert-warning" translate="script.author"></div>
        <form name="authorForm" class="margin-top-sm margin-right-md margin-left-md" novalidate>
            <div>
                <input class="form-text" type="text" name="authorname"
                       ng-model="author.name" ng-minlength="2" ng-maxlength="40" required>
                <div role="alert" class="error" ng-class="{'visible':authorForm.authorname.$touched || authorForm.$submitted}">
                    <span ng-show="authorForm.authorname.$error.required" translate="script.ERRORS.require.Name"></span>
                    <span ng-show="authorForm.authorname.$error.minlength" translate="script.ERRORS.minlength.Name"></span>
                    <span ng-show="authorForm.authorname.$error.maxlength" translate="script.ERRORS.maxlength.Name"></span>
                </div>
            </div>
            <div>
                <input class="form-text" type="email" name="email" placeholder="<%'script.PLACES.email' | translate%>"
                       ng-model="author.email" ng-maxlength="100" />
                <div role="alert" class="error" ng-class="{'visible':authorForm.email.$touched || authorForm.link.$touched || authorForm.$submitted}">
                    <span ng-show="authorForm.email.$error.required" translate="login.ERRORS.minlength.Email"></span>
                    <span ng-show="authorForm.email.$error.email" translate="login.ERRORS.require.Email"></span>
                    <span ng-show="authorForm.email.$error.maxlength" translate="login.ERRORS.maxlength.Email"></span>
                </div>
            </div>
            <div>
                <input class="form-text" type="text" name ="link" placeholder="<%'script.PLACES.site' | translate%>"
                       ng-model="author.link" ng-maxlength="200" />
                <div role="alert" class="error" ng-class="{'visible':authorForm.link.$touched || authorForm.$submitted}">
                    <span ng-show="authorForm.link.$error.maxlength" translate="script.ERRORS.maxlength.Link"></span>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-default" type="button" ng-click="$close(null)">{{trans("project.BUTTONS.cancel")}}</button>
        <button class="btn btn-success text-uppercase" type="button" ng-disabled="authorForm.$invalid"
                ng-click="$close(author)">{{trans("project.BUTTONS.confirm")}}</button>
    </div>
</script>
<div class="container margin-bottom-lg" ng-controller="preparationCtrl" ng-init="init('{{$project}}','{{$step}}')">
    <div>
        <div class="affix fixed-top fixed-right margin-top-lg padding-top-md">
            @if(is_null($project->user_id))
            <div class="btn btn-text-danger " ng-click="delete()">
                <span class="text-uppercase">{{trans("project.BUTTONS.delete")}}</span>
            </div>
            @endif
            <br/>
            <div class="btn btn-default" ng-if="false">
                <span translate="project.BUTTONS.preview"></span>
            </div>
            <div class="margin-top-md btn btn-info"
                 ng-disabled="basicinfo.$invalid" ng-if="step > 1" ng-click="send()">
                <span class="text-uppercase" translate="project.BUTTONS.submit"></span>
            </div>
        </div>
        <div class="jumbotron">
            <h3>
                {{$project->title}}
            </h3>
            <div class="text-center"> {!!trans('project.agreement')!!}</div>
        </div>
        <div class="row">
            <div class="tab-menu-bar" style="margin:auto 15px">
                <div class="tab-menu-item"
                     ng-click="selectTab(0)"
                     ng-class="{'active':0==selectedTab}">
                    <span>{{trans("project.CREATION.pitch")}}</span>
                </div>
                <div class="tab-menu-item"
                     ng-click="selectTab(1)"
                     ng-class="{'active':1==selectedTab}">
                    <span>{{trans("project.CREATION.description")}}</span>
                </div>
                <div class="tab-menu-item"
                     ng-click="selectTab(2)"
                     ng-class="{'active':2==selectedTab, 'disabled':step < 2}">
                    <span>{{trans("project.CREATION.container")}}</span>
                </div>
                <div class="tab-menu-item"
                     ng-click="selectTab(3)"
                     ng-class="{'active':3==selectedTab, 'disabled':step < 2}">
                    <span>{{trans("project.CREATION.team")}}</span>
                </div>
                <div class="tab-menu-item"
                     ng-click="selectTab(4)"
                     ng-class="{'active':4==selectedTab, 'disabled':step < 2}">
                    <span>{{trans("project.CREATION.container")}}</span>
                </div>
            </div>
        </div>
        <div class="content-margin" ng-switch="selectedTab">
            <div name="basicForm" ng-switch-default>
                @include('templates.preparation')
            </div>
            <div ng-switch-when="1">
                <br/>
                <form id="descriptionForm" name="descriptionForm" role="form">
                    <textarea ck-editor name="editor" id="editor"
                              class="editor" cols="100"
                              placeholder="<%project.CREATION.description%>"
                              ng-model="project.description"
                              required>
                    </textarea>
                    <div role="alert" class="error" ng-class="{'visible':project.description.length < 200}">
                        <span translate="project.ERRORS.minlength.description"></span>
                    </div>
                    <br/>
                    <div class="flex-rows">
                        <div class="small text-danger" translate="project.CREATION.creation"></div>
                        <div>
                            <div class="btn btn-default" ng-click="cancel()">
                                <span class="fa fa-undo"></span>
                            </div>
                            <div class="btn btn-primary" ng-disabled="project.description.length < 200" id="submit"
                                 ng-click="save()"><span class="fa fa-arrow-right"></span></div>
                        </div>
                    </div>
                </form>
            </div>

            <div ng-switch-when="2">
                <div script-content>
                    @include("templates.script")
                </div>
                <div budget-content>
                    @include("templates.budget")
                </div>
            </div>

            <div ng-switch-when="3">
                <div team-content class="content margin-top-lg">
                    @include('templates.team')
                </div>
            </div>

            <div ng-switch-when="4">
                <div recruit-content class="content margin-top-md">
                    @include('templates.recruit')
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
    <script src="/js/controllers/admin/preparation.js"></script>
@endsection