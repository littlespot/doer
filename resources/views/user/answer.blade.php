@extends('layouts.zoomov')

@section('content')
<link href="{{URL::asset('css/comment.css') }}" rel="stylesheet" />
<link href="{{URL::asset('css/message.css') }}" rel="stylesheet" />
<style>
    .answer-container {
        position: relative;
        display: block;
        min-height: 110px;
        margin: 12px auto;
        padding: 12px auto;
        border-bottom: dashed #e0e0e0 1px;
    }
</style>
<div ng-controller="answerNewCtrl" ng-init="init('{{$question->id}}', '{{$question->mineCnt}}')">
    <div>
        <modal-dialog confirm-modal="confirmModal()">
            <p translate="project.MESSAGES.<%result%>"><p>
        </modal-dialog>
    </div>
    <div class="container" style="padding-bottom: 0">
        <div class="tag-groups">
            @foreach($question->tags as $tag)
            <div class="tag">
                {{$tag->label}}
            </div>
            @endforeach
        </div>
        <div style="display: flex; justify-content: space-between">
            <div class="h3">{{$subject}}</div>
            <div>
                @if($user_id == Auth::id())
                <span class="btn btn-default">
                    <span class="glyphicon glyphicon-heart"></span>&nbsp;
                    @if($followers_cnt > 0){{$followers_cnt}}@endif
                </span>
                @elseif($myfollow)
                    <a href="#" class="btn btn-danger" id="followers" ng-click="follow()">
                        <span class="glyphicon glyphicon-heart"></span>&nbsp;
                        @if($followers_cnt > 0)<span id="count">{{$followers_cnt}}</span>@endif
                    </a>
                @else
                    <a href="#" class="btn btn-default danger" id="followers"  tooltip="<%project.FollowQuestion%>" ng-click="follow()">
                        <span class="glyphicon glyphicon-heart" ></span>&nbsp;
                        @if($followers_cnt > 0)<span id="count">{{$followers_cnt}}</span>@endif
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="container content">
        <div class="row">
            <div class="col-md-8">
                <div class="row question-info">
                    <div class="btn">
                        @if($user_id == Auth::id())
                            <a href="{{URL::asset('admin/question')}}/{{$id}}">{{trans("project.BUTTONS.edit")}}</a>
                        @else
                            <span class="text-muted" translate="project.CreatedAt" translate-values="{date:'{{$created_at}}'}"></span>
                            <a class="primary" href="{{URL::asset('profile')}}/{{$user_id}}">{{$username}}</a>
                        @endif
                    </div>
                </div>
                <blockquote>
                    {!! $content !!}
                </blockquote>
                <hr/>
                @if(!$answers_ctn)
                    <div class="btn text-important">{{trans("project.QUESTION.answer_first")}}</div>
                @elseif($mineCnt)
                    <div class="btn text-primary" translate="project.MyAnswersCnt" translate-values="{cnt:'{{$mineCnt}}'}"></div>
                @else
                     <div class="btn text-danger">{{trans("project.QUESTION.answer_wait")}}</div>
                @endif

                <div>
                    @include('templates.editor', ['content'=>$project->description])
                    <textarea ck-editor name="editor" id="editor" ng-ckeditor
                              class="editor" ng-model="answer.content"
                              placeholder="<%'project.ToAnswer'|translate%>">
                    </textarea>
                    <br/>
                    <div class="text-right">
                        <div class="btn btn-default" ng-click="cancel()"><span class="fa fa-undo"></span></div>
                        <div class="btn btn-primary" ng-click="send()"><span class="fa fa-check"></span></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4" style="padding-left: 20px;">
                <div style="padding: 6px 0; font-size: 14px;" class="text-right text-danger">
                    @if($answers_ctn)
                        <span translate="project.AnswerCnt" translate-values="{cnt:'{{$answer_cnt}}'}"></span>
                    @else
                        <span >{{trans('project.QUESTION.answer_none')}}</span>
                    @endif
                </div>
                <h4 class="text-primary">{{trans('project.LABELS.question_related')}}</h4>
                <h5>
                    <a class="light" href="/project/{{$question->project_id}}" target="_blank">{{$question->title}}</a>
                </h5>
                <div>
                    <img ng-src="/storage/projects/{{$question->project_id}}.thumb.jpg" width="100%"/>
                </div>
                <div class="text-right">
                    <div class="btn">
                        <img class="avatar" ng-src="/storage/avatars/{{$question->planner_id}}.small.jpg">
                        <a class="primary" href="/profile/{{$question->planner_id}}">{{$question->planner_name}}</a>
                    </div>
                </div>
                <hr/>
                <h3 translate="project.RelatedQuestions" translate-values="{cnt:rpagination.total}"></h3>
                <div ng-repeat="q in relates|limitTo:relates.numberPerPage:(relates.currentPage - 1)*relates.numberPerPage" class="answer-container">
                    <h4>
                      <a class="success" href="{{URL::asset('question')}}/<%q.question_id%>" target="_blank" ng-bind="q.subject"></a>
                    </h4>
                    <div style="margin-top: 10px">
                        <a href="{{URL::asset('project')}}/<%q.project_id%>" target="_blank" class="btn">
                            <span class="glyphicon glyphicon-tag"></span> <span ng-bind="q.title"></span>
                        </a>
                    </div>
                    <div style="display: flex;justify-content: space-between">
                        <div class="timestamp small" ng-bind="q.created_at | limitTo:16"></div>
                        <a class="primary" href ="{{URL::asset('profile')}}/<%q.user_id%>" target="_blank" class="title" ng-bind="q.username"></a>
                    </div>
                </div>
                <div class="text-bar">
                    <pagination ng-show="rpagination.show"
                                ng-model="rpagination.currentPage"
                                total-items="rpagination.total"
                                items-per-page="rpagination.perPage"
                                max-size="2"
                                boundary-links="true"
                                ng-change="relatePageChanged()"
                                previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;">
                    </pagination>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/js/directives/editor.js"></script>
@endsection