@extends('preparation.top')

@section('tabcontent')
<link href="/css/form.css" rel="stylesheet" />
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
</style>
<div class="content-margin" ng-controller="preparationCtrl" ng-init="init('{{$project->id}}','{{$authors}}', '{{$types}}', '{{$budgets}}', '{{$sponsors}}', '{{$scripts}}')">
    <div class="alert alert-warning">
        {!! trans("project.ALERTS.member") !!}
    </div>
    <div script-content>
        @include("templates.script")
    </div>
    <div budget-content>
        @include("templates.budget")
    </div>
</div>
<div class="text-right">
    <a class="btn btn-default" href="/admin/preparations/{{$project->id}}?step=1">
        <span class="fa fa-arrow-left"></span>
    </a>
    <a class="btn btn-primary" href="/admin/preparations/{{$project->id}}?step=3">
        <span class="fa fa-arrow-right"></span>
    </a>
</div>
@endsection
@section('tabscript')
    <script src="/js/directives/script.js"></script>
    <script src="/js/directives/budget.js"></script>
    <script src="/js/controllers/admin/container.js"></script>
@endsection