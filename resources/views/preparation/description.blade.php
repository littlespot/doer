@extends('preparation.top')

@section('tabcontent')
<div class="content-margin" ng-controller="preparationCtrl" ng-init="loaded()">
    <br>
    <form id="descriptionForm" onsubmit="mySubmit()"
          action="/admin/preparation" method="POST" name="descriptionForm" role="form">
        {{ csrf_field() }}
        <input type="hidden" value="{{$project->id}}" name="id">
        <input type="hidden" value="0" name="sendFlag" id="sendFlag">
        @include('templates.editor', ['content'=>$project->description, 'picture'=>'projects', 'parent_id'=>$project->id])
        @if ($errors->has('content'))
            <div class="text-danger small">
                <span>{{ $errors->first('content') }}</span>
            </div>
        @else
            <div class="error" role="alert" ng-class="{'visible':questionForm.content.$touched}">
                <span ng-show="error.required">{{trans('project.ERRORS.require.question_content ')}}</span>
                <span ng-show="error.maxlength">{{trans('project.ERRORS.maxlength.question_content', ['cnt'=>100])}}</span>
                <span ng-show="error.minlength">{{trans('project.ERRORS.minlength.question_content', ['cnt'=>4])}}</span>
            </div>
        @endif
        <div class="flex-rows">
            <div class="small text-danger">{{trans("project.NOTE")}}</div>
            <div>
                <a class="btn btn-default" href="/admin/preparations/{{$project->id}}?step=0">
                    <span class="fa fa-arrow-left"></span>
                </a>
                <button class="btn btn-primary" type="submit">
                    {{trans('layout.BUTTONS.continue')}}
                </button>
            </div>
        </div>
    </form>
    <script>
        function mySubmit() {
            $('#editor').find('div[data-role=image] img').each(function () {
                var input = $('<input type="hidden" name="images[]">');
                var src = $(this).attr('src');
                input.val(src.substring(src.lastIndexOf('/')+1));
                $('#descriptionForm').append(input);
            });
            $('#editor-content').text($('#editor').html());
        }
    </script>
</div>
@endsection
@section('tabscript')
    <script src="/js/directives/editor.js"></script>
    <script src="/js/controllers/admin/description.js"></script>
@endsection