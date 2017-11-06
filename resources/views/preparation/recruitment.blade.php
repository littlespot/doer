@extends('preparation.top')

@section('tabcontent')
    <div class="content content-margin" ng-controller="preparationCtrl" ng-init="init('{{$project->id}}','{{$recruitment}}')">
        <div recruit-content class="content margin-top-md">
            @include('templates.recruit')
        </div>
    </div>
    <div class="text-right">
        <a class="btn btn-default" href="/admin/preparations/{{$project->id}}?step=3">
            <span class="fa fa-arrow-left"></span>
        </a>
        <div class="btn btn-info" onclick="$('#btnSubmit').click()">
            <span>{{trans("project.BUTTONS.submit")}}</span>
        </div>
    </div>
@endsection
@section('tabscript')
    <script src="/js/directives/recruit.js"></script>
    <script src="/js/controllers/admin/recruitment.js"></script>
@endsection