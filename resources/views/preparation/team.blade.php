 @extends('preparation.top')

@section('tabcontent')
    <div class="content content-margin" ng-controller="preparationCtrl" ng-init="init('{{$project}}','{{$users}}')">
        <div class="alert alert-warning">
            {!! trans("project.ALERTS.member") !!}
        </div>
        <div team-content>
            @include('templates.team')
        </div>
    </div>
    <div class="text-right">
        <a class="btn btn-default" href="/admin/preparations/{{$project->id}}?step=2">
            <span class="fa fa-arrow-left"></span>
        </a>
        <a class="btn btn-primary" href="/admin/preparations/{{$project->id}}?step=4">
            <span class="fa fa-arrow-right"></span>
        </a>
    </div>
    @endsection
@section('tabscript')
    <script src="/js/directives/team.js"></script>
    <script src="/js/controllers/admin/team.js"></script>
@endsection