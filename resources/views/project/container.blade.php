@extends('project.top')

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
<div class="content-margin" ng-controller="preparationCtrl" ng-init="init('{{$project->id}}', '{{$authors}}', '{{$types}}', '{{$budgets}}', '{{$sponsors}}', '{{$scripts}}')">
    <div script-content>
        @include("templates.script")
    </div>
    <div budget-content>
        @include("templates.budget")
    </div>
</div>
@endsection
@section('tabscript')
    <script src="/js/directives/script.js"></script>
    <script src="/js/directives/budget.js"></script>
    <script src="/js/controllers/admin/container.js"></script>
@endsection