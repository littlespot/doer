@extends('layouts.zoomov')

@section('content')

    <!--
    <link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.no-icons.min.css" rel="stylesheet">
    <link href="/bootstrap-responsive.min.css" rel="stylesheet">
    -->

    <div class="container content margin-bottom-lg margin-top-lg" ng-controller="questionNewCtrl" ng-init="init()">
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
                <form name="questionForm" id="questionForm" action="/admin/questions" method="POST">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="project_id" id="project_id" value="{{$id}}">
                    <div>
                        <input type="text" name="subject" class="form-text" ng-model="subject"
                               placeholder="{{trans("project.PLACES.question_subject")}}"
                               required ng-maxlength="100" ng-minlength="4"/>
                    </div>
                    @if ($errors->has('subject'))
                        <div class="text-danger small">
                            <span>{{ $errors->first('subject') }}</span>
                        </div>
                    @else
                        <div class="error" role="alert" ng-class="{'visible':questionForm.subject.$touched}">
                            <span ng-show="questionForm.subject.$error.required">{{trans('project.ERRORS.require.question_subject')}}</span>
                            <span ng-show="questionForm.subject.$error.maxlength">{{trans('project.ERRORS.maxlength.question_subject', ['cnt'=>100])}}</span>
                            <span ng-show="questionForm.subject.$error.minlength">{{trans('project.ERRORS.minlength.question_subject', ['cnt'=>4])}}</span>
                        </div>
                    @endif
                    @include('templates.editor', ['content'=>'', 'picture'=>''])
                    <div class="text-danger small">
                    @if ($errors->has('editor'))
                            <span>{{ trans('project.ERRORS.invalid.question_content') }}</span>
                        @else
                            <span ng-if="error.editor">{{ trans('project.ERRORS.invalid.question_content') }}</span>
                    @endif
                    </div>
                    <div>
                        <div>
                            <span ng-repeat="t in qtags">
                                <input type="hidden" name="tags[<%t.id%>]" ng-value="t.label" />
                                <span class="tag">
                                    <span ng-bind="t.label"></span>
                                </span>
                                 <span class="btn btn-link text-danger fa fa-times"
                                       ng-click="removeTag(t, $index)"></span>&nbsp;
                            </span>
                        </div>
                        <br/>
                        <div class="text-danger small">
                            @if ($errors->has('tags'))
                                {{ trans('project.ERRORS.require.question_tag') }}
                            @else
                                <span ng-if="error.tags">{{ trans('project.ERRORS.require.question_tag') }}</span>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-sm-11">
                                <input ng-model="newTags" type="text" class="form-text" name="newTags" id="newTags"
                                       placeholder="{{trans("project.PLACES.tags")}}"/>
                            </div>
                            <div class="col-sm-1">
                                <span class="btn text-danger fa fa-plus" ng-click="storeTags()"></span>
                            </div>
                        </div>
                        <br/>
                        <div class="text-right">
                            <div class="btn btn-default" ng-click="cancel()"><span class="fa fa-undo"></span></div>
                            <div class="btn btn-primary" ng-click="save(questionForm.$invalid)" ng-disabled="questionForm.$invalid || qtags.length == 0" >
                                <span class="fa fa-check"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-sm-3">
                <div>
                    <h5 class="text-primary">
                        <a href="/project/{{$id}}" target="_blank">{{$title}}</a>
                    </h5>
                    <div>
                        <img class="img-responsive" src="/context/projects/{{$id}}.thumb.jpg" />
                    </div>
                    <div class="blockquote-reverse margin-top-sm">
                        <a class="inner" href="/profile/{{$user_id}}">
                            <img class="img-circle img-responsive" src="/context/avatars/{{$user_id}}.small.jpg" />
                        </a>
                        <a id="user" class="inner" href="/profile/{{$user_id}}">
                            {{$username}}
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="/js/controllers/admin/question.js"></script>
    <script src="/js/directives/editor.js"></script>
@endsection