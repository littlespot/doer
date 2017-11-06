@extends('project.top')

@section('tabcontent')
    <div class="content content-margin" ng-controller="preparationCtrl" ng-init="init('{{$project->id}}','{{$recruitment}}')">
        <div recruit-content class="content margin-top-md">
            @include('templates.recruit')
        </div>
    </div>
@endsection
@section('tabscript')
    <script src="/js/directives/recruit.js"></script>
    <script src="/js/controllers/admin/recruitment.js"></script>
@endsection