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
<div class="container margin-bottom-lg">
    <div  ng-controller="menuCtrl">


    <div class="affix fixed-top fixed-right margin-top-lg padding-top-md">
        <div class="btn btn-text-danger " ng-click="delete('{{$project->id}}')">
            <span class="text-uppercase">{{trans("project.BUTTONS.delete")}}</span>
        </div>
        <br/>
        @if($step>1 || $project->count > 199 ||(!is_null($project->description) && strlen($project->description)> 199))
            <a class="margin-top-md btn btn-default" href="/admin/preparation/{{$project->id}}" target="_blank">
                {{trans("project.BUTTONS.preview")}}
            </a>
            <br/>
        <div class="margin-top-md btn btn-info" id="btnSubmit" ng-click="send('{{$step}}')">
            <span class="text-uppercase">{{trans("project.BUTTONS.submit")}}</span>
        </div>
            <form name="sendForm" id="sendForm" action="/admin/send" method="POST">
                <input type="hidden" name="id" value="{{$project->id}}">
            </form>
        @endif
    </div>
    <div class="jumbotron">
        <h3>
            {{$project->title}}
        </h3>
        <div class="text-center"> {!!trans('project.agreement')!!}</div>

    </div>
        <div class="tab-menu-bar">
        <a class="tab-menu-item {{$step==0 ? 'active':''}}" ng-click="changeStep('{{$project->id}}', '{{$step}}', 0)"
           href="javascript:void(0)">
            <span>{{trans("project.CREATION.pitch")}}</span>
        </a>
        <a class="tab-menu-item {{$step==1 ? 'active':''}}" ng-click="changeStep('{{$project->id}}', '{{$step}}', 1)"
           href="javascript:void(0)">
            <span>{{trans("project.CREATION.description")}}</span>
        </a>
        @if($step > 1 || $project->count > 199 || (!is_null($project->description) && strlen($project->description)> 199))
            <a class="tab-menu-item {{$step==2 ? 'active':''}}" ng-click="changeStep('{{$project->id}}', '{{$step}}', 2)"
               href="javascript:void(0)">
                <span>{{trans("project.CREATION.container")}}</span>
            </a>
            <a class="tab-menu-item {{$step==3 ? 'active':''}}" ng-click="changeStep('{{$project->id}}', '{{$step}}', 3)"
               href="javascript:void(0)">
                <span>{{trans("project.CREATION.team")}}</span>
            </a>
            <a class="tab-menu-item {{$step==4 ? 'active':''}}" ng-click="changeStep('{{$project->id}}', '{{$step}}', 4)"
               href="javascript:void(0)">
                <span>{{trans("project.CREATION.recruitment")}}</span>
            </a>
        @else
            <div class="tab-menu-item disabled"
                 ng-click="alert()">
                <span>{{trans("project.CREATION.container")}}</span>
            </div>
            <div class="tab-menu-item disabled"
                 ng-click="alert()">
                <span>{{trans("project.CREATION.team")}}</span>
            </div>
            <div class="tab-menu-item disabled"
                 ng-click="alert()">
                <span>{{trans("project.CREATION.recruitment")}}</span>
            </div>
        @endif
    </div>
    </div>
    @yield('tabcontent')
</div>

    @endsection
@section('script')
    <script src="/js/modules/preparation.js"></script>
    <script src="/js/controllers/admin/preparation.js"></script>
    @yield('tabscript')
@endsection
