@extends('layouts.zoomov')

@section('content')
<link href="/css/form.css" rel="stylesheet" />
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
<script type="text/ng-template" id="complete.html">
    <div class="text-right">
        <div class="btn btn-sm text-danger" type="button" ng-click="$close(0)" data-toggle="tooltip" title="{{trans('layout.BUTTONS.cancel')}}">
            <span class="fa fa-times"></span>
        </div>
    </div>
    <div class="modal-body" id="modal-body">
        <h3>{{trans('project.MESSAGES.complete')}}</h3>
    </div>
    <div class="modal-footer">
        <button class="btn btn-info" type="button" ng-click="$close(1)">{{trans("layout.BUTTONS.continue")}}</button>
        <button class="btn btn-success" type="button" ng-click="$close(2)">{{trans("layout.BUTTONS.save_return")}}</button>
    </div>
</script>
<div class="container margin-bottom-lg">
    <div class="affix fixed-top fixed-right margin-top-lg padding-top-lg" ng-controller="menuCtrl">
        <div class="btn btn-text-danger " ng-click="cancel('{{$project->id}}')">
            <span class="text-uppercase">{{trans("layout.BUTTONS.cancel")}}</span>
        </div>
        <br/>
        <div class="margin-top-md btn btn-info" id="btnSubmit" ng-click="send('{{$step}}','{{$project->id}}')">
            <span class="text-uppercase">{{trans("layout.BUTTONS.complete")}}</span>
        </div>
    </div>
    <div class="jumbotron">
        <h3>
            <a href="/project/{{$project->id}}">{{$project->title}}</a>
        </h3>
        <div class="text-center"> {!!trans('project.agreement')!!}</div>
    </div>
    <ul class="nav nav-tabs nav-justified nav-top-menu">
        <li role="presentation" class="{{$step==0 ? 'active':''}}">
            <a href="/admin/projects/{{$project->id}}?step=0">
                <span>{{trans("project.CREATION.pitch")}}</span>
            </a>
        </li>
        <li role="presentation" class="{{$step==1 ? 'active':''}}">
            <a href="/admin/projects/{{$project->id}}?step=1">
                <span>{{trans("project.CREATION.description")}}</span>
            </a>
        </li>
        @if($step>1 || $project->count > 199 || (!is_null($project->description) && strlen($project->description)> 199))
            <li role="presentation" class="{{$step==2 ? 'active':''}}">
                <a href="/admin/projects/{{$project->id}}?step=2">
                    <span>{{trans("project.CREATION.container")}}</span>
                </a>
            </li>
            <li role="presentation" class="{{$step==3 ? 'active':''}}">
                <a href="/admin/projects/{{$project->id}}?step=3">
                    <span>{{trans("project.CREATION.team")}}</span>
                </a>
            </li>
            <li role="presentation" class="{{$step==4 ? 'active':''}}">
                <a href="/admin/projects/{{$project->id}}?step=4">
                    <span>{{trans("project.CREATION.recruitment")}}</span>
                </a>
            </li>
        @else
            <li class="disabled">
                <a href="javascript:void(0)" ng-click="alert()">
                    <span>{{trans("project.CREATION.container")}}</span>
                </a>
            </li>
            <li class="disabled">
                <a href="javascript:void(0)" ng-click="alert()">
                    <span>{{trans("project.CREATION.team")}}</span>
                </a>
            </li>
            <li class="disabled">
                <a href="javascript:void(0)" ng-click="alert()">
                    <span>{{trans("project.CREATION.recruitment")}}</span>
                </a>
            </li>
        @endif
    </ul>
    @yield('tabcontent')
</div>

    @endsection
@section('script')
    <script src="/js/controllers/admin/project.js"></script>
    @yield('tabscript')

@endsection
