@extends('preparation.top')

@section('tabcontent')
    <div class="p-5 bg-white">
        <div  ng-controller="preparationCtrl" ng-init="init('{{$project->id}}','{{$recruitment}}')">
            <div recruit-content class="">
                @include('templates.recruit')
            </div>
        </div>
        <div class="text-right">
            <a class="btn btn-outline-danger" href="/admin/preparations/{{$project->id}}?step=3">
                <span class="fa fa-arrow-left"></span>
            </a>
            @if($project->active==1)
                <a class="btn btn-primary"  href="/project/{{$project->id}}">
                    <span>{{trans("layout.BUTTONS.complete")}}</span>
                </a>
            @else
                <div class="btn btn-primary"  onclick="$('#btnSubmit').click()">
                    <span>{{trans("project.BUTTONS.submit")}}</span>
                </div>
            @endif
        </div>
    </div>

@endsection
@section('tabscript')
    <script src="/js/directives/recruit.js"></script>
    <script src="/js/controllers/admin/recruitment.js"></script>
@endsection