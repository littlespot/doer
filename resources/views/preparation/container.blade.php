@extends('preparation.top')

@section('tabcontent')
<link href="/css/form.css" rel="stylesheet" />

<div class="p-5 bg-white">
    <div class="content " ng-controller="preparationCtrl" ng-init="init('{{$project->id}}','{{$authors}}', '{{$types}}', '{{$budgets}}', '{{$sponsors}}', '{{$scripts}}')">
        <div class="my-2 alert alert-warning">
            {!! trans("project.ALERTS.member") !!}
        </div>
        <div script-content="preparation">
            @include("templates.script")
        </div>
        <div budget-content>
            @include("templates.budget")
        </div>
    </div>
    <div class="text-right">
        <a class="btn btn-outline-danger" href="/admin/preparations/{{$project->id}}?step=1">
            <span class="fa fa-arrow-left"></span>
        </a>
        <a class="btn btn-primary" href="/admin/preparations/{{$project->id}}?step=3">
            {{trans('layout.BUTTONS.next')}}
        </a>
    </div>
</div>

@endsection
@section('tabscript')
    <script src="/js/directives/script.js"></script>
    <script src="/js/directives/budget.js"></script>
    <script src="/js/controllers/admin/container.js"></script>
@endsection