@extends('layouts.zoomov')

@section('content')
<script type="text/ng-template" id="confirm.html">
    <div class="modal-body" id="modal-body">
        <h3 translate="project.MESSAGES.<%confirm%>"></h3>
    </div>
    <div class="modal-footer">
        <button class="btn btn-default" type="button" ng-click="$close(false)">{{trans("project.BUTTONS.cancel")}}</button>
        <button class="btn btn-danger" type="button" ng-click="$close(true)">{{trans("project.BUTTONS.confirm")}}</button>
    </div>
</script>
<div class="container content py-5" ng-controller="reportNewCtrl" ng-init="init()">
    <script type="text/ng-template" id="error.html">
        <div class="modal-body" id="modal-body">
            <h3 translate="FAIL"></h3>
        </div>
        <div class="modal-footer">
            <button class="btn btn-link" type="button" ng-click="$dismiss(false)">OK</button>
        </div>
    </script>
    <div class="row">
        <div class="col-sm-9">
            <form name="reportForm" id="reportForm" action="/admin/reports" method="POST" onsubmit="mySubmit()">
                {{ csrf_field() }}
                <input type="hidden" name="project_id" id="project_id" value="{{$id}}" />
                <div class="margin-bottom-md">
                    <input type="text" name="title" class="form-text" ng-model="report.title"
                        placeholder="{{trans('project.PLACES.report_title')}}"
                        required ng-maxlength="40" ng-minlength="2"/>
                </div>
                @if ($errors->has('title'))
                    <div class="text-danger small">
                        <span>{{ $errors->first('title') }}</span>
                    </div>
                @else
                    <div class="text-danger small" role="alert" ng-show="reportForm.title.$touched || reportForm.submitted">
                        <span ng-show="reportForm.title.$error.required">{{trans('project.ERRORS.require.report_title')}}</span>
                        <span ng-show="reportForm.title.$error.minlength">{{trans('project.ERRORS.minlength.report_title', ['cnt'=>2])}}</span>
                        <span ng-show="reportForm.title.$error.maxlength">{{trans('project.ERRORS.maxlength.report_title', ['cnt'=>40])}}</span>
                    </div>
                @endif
                <textarea name="synopsis" ng-model="report.synopsis" class="form-control" name="synopsis"
                          ng-maxlength="400" ng-minlength="10"  required >
                </textarea>
                @if ($errors->has('synopsis'))
                    <div class="text-danger small">
                        <span>{{ $errors->first('synopsis') }}</span>
                    </div>
                @else
                    <div class="text-danger small" role="alert" ng-show="reportForm.synopsis.$touched || reportForm.submitted">
                        <span ng-show="reportForm.synopsis.$error.required">{{trans('project.ERRORS.require.report_synopsis')}}</span>
                        <span ng-show="reportForm.synopsis.$error.minlength">{{trans('project.ERRORS.minlength.report_synopsis', ['cnt'=>10])}}</span>
                        <span ng-show="reportForm.synopsis.$error.maxlength">{{trans('project.ERRORS.maxlength.report_synopsis', ['cnt'=>400])}}</span>
                    </div>
                @endif
                <div class="margin-top-md">
                    @include('templates.editor', ['content'=>'', 'picture'=>'reports', 'parent_id'=>$id])
                    @if ($errors->has('editor'))
                        <div class="text-danger small">
                            <span>{{ $errors->first('editor') }}</span>
                        </div>
                    @else
                        <div class="text-danger small" role="alert" ng-show="error">
                            <span>{{trans('project.ERRORS.require.report_editor')}}</span>
                        </div>
                    @endif
                    <div>
                        <span ng-repeat="t in report.tags">
                            <input type="hidden" name="tags[<%t.id%>]" ng-value="t.label" />
                            <span class="tag">
                                <span ng-bind="t.label"></span>
                            </span>
                             <span class="btn btn-link text-danger fa fa-times"
                                   ng-click="removeTag(t, $index)"></span>&nbsp;
                        </span>
                        <span ng-if="report.tags.length == 0" class="text-danger">{{trans('project.ERRORS.require.report_tag')}}</span>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-sm-11">
                            <input ng-model="newTags" type="text" class="form-text" name="newTags" id="newTags"
                                   placeholder="{{trans('project.PLACES.tags')}}"/>
                        </div>
                        <div class="col-sm-1">
                            <span class="btn text-danger fa fa-plus" ng-click="storeTags()"></span>
                        </div>
                    </div>
                    <br/>
                    <div class="flex-rows">
                        <div>
                            <div class="btn btn-default" ng-click="cancel()"><span class="fa fa-undo"></span></div>
                            <div class="btn btn-primary" ng-click="save()" ng-disabled="reportForm.$invalid || report.tags.length == 0" >
                                <span class="fa fa-check"></span>
                            </div>
                        </div>
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
        <div class="col-sm-3">
            <div>
                <h5 class="text-primary">
                    <a href="/project/{{$id}}" target="_blank">{{$title}}</a>
                </h5>
                <div>
                    <img class="img-fluid" src="/storage/projects/{{$id}}.thumb.jpg" />
                </div>
                <div class="blockquote-reverse margin-top-sm">
                    <a class="inner" href="/profile/{{$user_id}}">
                        <img class="img-circle img-fluid" src="/storage/avatars/{{$user_id}}.small.jpg" />
                    </a>
                    <a id="user" class="inner" href="/profile/{{$user_id}}">
                        {{$username}}
                    </a>
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-sm-offset-9 col-sm-3">
            <div style="margin-top: -100%">
                <span ng-repeat="t in tags">
                    <span class="btn btn-sm" ng-click="addTag(t)"
                          ng-class="{'btn-default':!t.chosen, 'btn-primary':t.chosen}">
                        #<span ng-bind="t.label"></span>#
                    </span>&nbsp;
                </span>
            </div>
        </div>
    </div>

</div>
    @endsection
@section('script')
    <script src="/js/directives/editor.js"></script>
    <script src="/js/controllers/admin/report.js"></script>
@endsection