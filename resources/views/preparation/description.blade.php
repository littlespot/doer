@extends('preparation.top')

@section('tabcontent')
<div class="p-5 bg-white" ng-controller="preparationCtrl" ng-init="init({{strlen(strip_tags($project->description))}})">
    @if ($errors && count($errors) > 0)
        <ul class="alert alert-danger small px-5" role="alert">
            @foreach ($errors->all() as $error)
                <li class="py-1">{{ $error }}</li>
            @endforeach
        </ul>
    @endif
    <form id="descriptionForm"
          action="/admin/preparation" method="POST" name="descriptionForm" role="form">
        {{ csrf_field() }}
        <input type="hidden" value="{{$project->id}}" name="id">
        <input type="hidden" value="0" name="sendFlag" id="sendFlag">
        <div ng-show="project.description">
            @include('templates.editor', ['content'=>$project->description, 'picture'=>'projects', 'parent_id'=>$project->id])
            <div class="error" role="alert" ng-class="{'visible':errors}">
                {{trans('project.ERRORS.minlength.description', ['cnt'=>200])}}
            </div>
        </div>
        <div ng-show="!project.description" style="word-wrap: break-word">
            {!! $project->description !!}
        </div>
    </form>
    <div class="text-right py-5">
        <a class="btn btn-outline-danger" href="/admin/preparations/{{$project->id}}?step=0" ng-show="!project.description" >
            <span class="fa fa-arrow-left"></span>
        </a>
        <div class="btn btn-primary" ng-click="project.description = true" ng-show="!project.description" >
            {{trans('layout.BUTTONS.edit')}}
        </div>
        @if(strlen(strip_tags($project->description)) > 200 && !$errors->has('description'))
        <div class="btn btn-outline-danger" ng-click="project.description = false" ng-show="project.description" >
            {{trans('layout.BUTTONS.cancel')}}
        </div>
        @endif
        <button class="btn btn-primary" type="submit" ng-show="project.description" ng-disabled="descriptionForm.$invalid" ng-click="save()">
            {{trans('layout.BUTTONS.continue')}}
        </button>
    </div>
</div>
@endsection
@section('tabscript')
    <script src="/js/directives/editor.js"></script>
    <script src="/js/controllers/admin/description.js"></script>
@endsection