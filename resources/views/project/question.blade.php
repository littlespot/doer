@extends('layouts.zoomov')

@section('content')
<div class="container content margin-bottom-lg margin-top-lg" ng-controller="questionNewCtrl" ng-init="init('{{json_encode($tags)}}')">
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
            <form name="questionForm" id="questionForm" action="/admin/question" method="POST">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{$id}}">
                <div>
                    <input type="text" name="subject"
                           class="form-text" ng-model="subject"
                            placeholder="{{trans("project.PLACES.question_subject")}}"
                            ng-init="subject='{{$subject}}'"
                     required ng-maxlength="100" ng-minlength="4"/>
                </div>
                <div class="error" role="alert">
                    <span ng-show="questionForm.subject.$error.required"
                          translate="project.ERRORS.require.subject"></span>
                    <span ng-show="questionForm.subject.$error.maxlength"
                          translate="project.ERRORS.maxlength.subject" translate-values="{value:100}"></span>
                    <span ng-show="questionForm.subject.$error.minlength"
                          translate="project.ERRORS.minlength.subject" translate-values="{value:4}"></span>
                </div>
                <div class="margin-top-lg">
                    @include('templates.editor', ['content'=>$content, 'picture'=>null, 'parent_id'=>null])
                    <div class="error" role="alert">
                        <span ng-show="error.required"
                              translate="project.ERRORS.require.content"></span>
                        <span ng-show="error.maxlength"
                              translate="project.ERRORS.maxlength.content" translate-values="{value:100}"></span>
                        <span ng-show="error.minlength"
                              translate="project.ERRORS.minlength.content" translate-values="{value:4}"></span>
                    </div>
                    <br/>
                    <div>
                        <span ng-repeat="t in qtags">
                            <input type="hidden" name="tags[<%t.id%>]" ng-value="t.label" />
                            <span class="tag">
                                <span ng-bind="t.label"></span>
                            </span>
                             <span class="btn btn-link text-danger fa fa-times"
                                   ng-click="removeTag(t, $index)"></span>&nbsp;
                        </span>
                        <span ng-if="question.tags.length == 0" class="text-danger"
                              translate="project.ERRORS.require.qtags"></span>
                    </div>
                    <br/>
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
                        <div class="btn btn-primary" ng-click="save()" ng-disabled="questionForm.$invalid || error.required || error.maxlength || error.minlength || qtags.length == 0" >
                            <span class="fa fa-check"></span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-sm-3">
            <div>
                <h5 class="text-primary">
                    <a href="/project/{{$project_id}}" target="_blank">{{$title}}</a>
                </h5>
                <div>
                    <img class="img-responsive" src="/context/projects/{{$project_id}}.thumb.jpg" />
                </div>
                <div class="blockquote-reverse margin-top-sm">
                    <a class="inner" href="/profile/{{$planner_id}}">
                        <img class="img-circle img-responsive" src="/context/avatars/{{$planner_id}}.small.jpg" />
                    </a>
                    <a id="user" class="inner" href="/profile/{{$planner_id}}">
                        {{$planner_name}}
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
<script src="/js/controllers/admin/question.js"></script>
@endsection