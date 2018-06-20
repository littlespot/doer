@extends('layouts.zoomov')

@section('content')
<link href="/css/project-detail.css" rel="stylesheet" />
<link href="/css/message.css" rel="stylesheet" />
<script type="text/ng-template" id="confirm.html">
    <div class="modal-body" id="modal-body">
        <h3 translate="project.MESSAGES.<%confirm%>"></h3>
    </div>
    <div class="modal-footer">
        <button class="btn btn-default" type="button" ng-click="$close(false)">{{trans("project.BUTTONS.cancel")}}</button>
        <button class="btn btn-danger" type="button" ng-click="$close(true)">{{trans("project.BUTTONS.confirm")}}</button>
    </div>
</script>
<script type="text/ng-template" id="error.html">
    <div class="modal-body" id="modal-body">
        <h3 translate="FAIL"></h3>
    </div>
</script>
<div class="margin-top-lg" ng-controller="questionCtrl" ng-init="init('{{$question->id}}', '{{$question->mineCnt}}', '{{$answer}}')">
    <div class="container">
        <div class="tags">
            @foreach($tags as $tag)
                <div class="tag tag-default">
                    <a href="#">{{$tag->label}}&nbsp;<span class="tail">{{$tag->cnt}}</span></a>
                </div>
            @endforeach
        </div>
        <div class="margin-top-sm flex-rows">
            <div class="h3">{{$question->subject}}</div>
            @if($question->user_id == Auth::id())
                <div class="btn btn-text-info btn-sq-sm" tooltip="{{trans('project.TIPS.followers')}}">
                    @if($question->followers_cnt > 0)
                        <div class="fa fa-bookmark"></div>&nbsp;
                        <div>{{$question->followers_cnt}}</div>
                    @else
                        <div class="fa fa-bookmark-o"></div>
                    @endif
                </div>
            @elseif($question->myfollow)
                <div class="btn btn-text-warning btn-sq-sm" id="followers" ng-click="follow()">
                    <div class="fa fa-bookmark fa-2x"></div>&nbsp;
                    <div id="count">@if($question->followers_cnt > 0){{$question->followers_cnt}}@endif</div>
                </div>
            @else
                <div class="btn btn-text-warning btn-sq-sm" id="followers" tooltip="{{trans('project.TIPS.follow')}}" ng-click="follow()">
                    <div class="fa fa-bookmark-o fa-2x" ></div>&nbsp;
                    <div id="count">@if($question->followers_cnt > 0){{$question->followers_cnt}}@endif</div>
                </div>
            @endif
        </div>
    </div>

    <div class="container content">
        <div class="row">
            <div class="col-md-8">
                <div>
                    @if($question->user_id == Auth::id())
                        <a class="text-important" href="/admin/question/{{$question->id}}">
                            {{trans("project.BUTTONS.edit")}}
                        </a>
                    @else
                        <span class="text-mutted small">
                            {!!trans("project.TAGS.create", ["date"=>date('Y-m-d', strtotime($question->created_at))])!!}
                         </span>
                        <a class="title" href="/profile/{{$question->user_id}}">{{$question->username}}</a>
                    @endif
                </div>
                <blockquote class="margin-top-sm">
                    {!! $question->content !!}
                </blockquote>
                <hr/>
                <div ng-if="apagination.total == 0" class="btn text-important">
                    {{trans("project.QUESTION.answer_first")}}
                </div>
                <div class="flex-rows">
                    <div ng-if="apagination.total > 0" class="btn">
                        <span ng-if="mineCnt > 0" class="text-chocolate" translate="project.MyAnswersCnt" translate-values="{cnt:mineCnt}"></span>
                        <span ng-if="mineCnt == 0" class="text-danger">
                            {{trans("project.QUESTION.answer_wait")}}
                        </span>
                    </div>
                    <div class="text-right" ng-show="answer.new == 0" ng-click="answer.new = 1">
                        <div class="btn btn-text-success">
                            {{trans("project.BUTTONS.answer")}}
                        </div>
                    </div>
                </div>

                <div ng-show="answer.new == 1" style="position: relative">
                    @include('templates.editor', ['content'=>trans("project.QUESTION.answer_my"), 'picture'=>null, 'parent_id'=>null])
                    <br/>
                    <div class="text-right">
                        <div class="btn btn-default" ng-click="cancel()"><span class="fa fa-undo"></span></div>
                        <div class="btn btn-primary" ng-click="send()"><span class="fa fa-check"></span></div>
                    </div>
                    <div class="loader-content" ng-if="answer.sending"><div class="loader"></div></div>
                </div>
                <br>
                <div ng-repeat="a in answers">
                    <div class="row">
                        <div class="col-md-1 flex-cols text-center">
                            <div class="margin-top-sm">
                                <a class="text-center " href="/profile/profile/<%a.user_id%>">
                                    <img class="center img-circle img-fluid" src="/storage/avatars/<%a.user_id%>.small.jpg" />
                                </a>
                                <div ng-if="a.mine" class="text-info">
                                    <span class="fa" ng-class="{'fa-star-o' :!a.supports_cnt, 'fa-star': a.supports_cnt}"></span>
                                </div>
                                <div ng-if ="!a.mine" class="text-warning" id="support_<%a.id%>" ng-disabled="a.supporting"
                                     ng-click="supportAnswer(a, '{{auth()->id()}}')">
                                   <span ng-class="{'fa-star-o' :!a.mysupport, 'fa-star': a.mysupport}"
                                         class="btn btn-sm fa" ></span>
                                </div>
                                <div class="counter" ng-class="{'text-info':a.mine, 'text-warning':!a.mine}"
                                     ng-if="a.supports_cnt > 0" ng-bind="a.supports_cnt"></div>
                            </div>
                            <div><aside ng-if="a.newest" class="sheer" translate="NEW"></aside></div>
                        </div>
                        <div class="col-md-11 comment-container">
                            <div ng-bind-html="a.content"></div>
                            <div>
                                <div class="text-right text-muted small" ng-bind="a.created_at|limitTo:16"></div>
                                <div class="hidden-bar flex-rows" ng-class="{'br': !$last}">
                                    <div>
                                        <a ng-if="a.username" class="title" href="/profile/<%a.user_id%>" ng-bind="a.username"></a>
                                        <a ng-if="!a.username" class="title" href="/profile/{{auth()->id()}}">{{Auth::user()->username}}</a>
                                    </div>
                                    <div class="btn" ng-if="a.mine" ng-click="deleteAnswer(a,'{{auth()->id()}}')">
                                        <span class="text-danger fa fa-trash"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="loader-content" ng-if="a.deleting"><div class="loader"></div> </div>
                        </div>

                    </div>
                </div>
                <div class="text-center" ng-show="apagination.show">
                    <ul uib-pagination ng-change="answerPageChanged()"
                        max-size="5"
                        rotate = true
                        items-per-page = 'apagination.perPage'
                        boundary-links="true"
                        total-items="apagination.total"
                        ng-model="apagination.currentPage"
                        class="pagination-sm"
                        previous-text="&lsaquo;"
                        next-text="&rsaquo;"
                        first-text="&laquo;"
                        last-text="&raquo;"></ul>
                </div>
            </div>
            <div class="col-md-4" style="padding-left: 20px;">
                <div class="margin-top-md text-right text-danger">
                    <span ng-if="apagination.total >0" translate="project.AnswerCnt" translate-values="{cnt:apagination.total}"></span>
                    <span ng-if="apagination.total == 0">
                        {{trans("project.QUESTION.answer_none")}}
                    </span>
                </div>
                <br>
                <h5 class="text-center text-default">{{trans('project.LABELS.question_related')}}</h5>
                <h5>
                    <a class="text-warning" href="/project/{{$question->project_id}}" target="_blank">{{$question->title}}</a>
                </h5>
                <div>
                    <img ng-src="/storage/projects/{{$question->project_id}}.thumb.jpg" width="100%"/>
                </div>
                <div class="text-right">
                    <a class="inner" href="/profile/{{$question->planner_id}}">
                        <img class="img-circle img-fluid" src="/storage/avatars/{{$question->planner_id}}.small.jpg" />
                    </a>
                    <a id="user" class="inner" href="/profile/{{$question->planner_id}}">
                        {{$question->planner_name}}
                    </a>
                </div>
                <hr/>
                <h5 class="text-center text-default text-uppercase" translate="project.RelatedQuestions" translate-values="{cnt:rpagination.total}"></h5>
                <div ng-repeat="q in relates" class="answer-container padding-top-sm">
                    <div class="font-md">
                      <a class="text-chocolate" href="/questions/<%q.question_id%>" ng-bind="q.subject"></a>
                    </div>
                    <div class="blockquote-reverse">
                        <a class="text-default" href="/project/<%q.project_id%>" target="_blank" class="btn">
                           <span ng-bind="q.title"></span>
                        </a>
                    </div>
                    <div class="flex-rows br padding-bottom-sm" style="vertical-align: bottom">
                        <a class="title" href ="/profile/<%q.user_id%>" target="_blank" class="title" ng-bind="q.username"></a>
                        <span class="time-stamp" ng-bind="q.created_at | limitTo:16">
                        </span>
                    </div>
                </div>

                    <div class="text-center" ng-show="rpagination.show">
                        <ul uib-pagination ng-change="relatePageChanged()"
                            max-size="5"
                            rotate = true
                            items-per-page = 'rpagination.perPage'
                            boundary-links="true"
                            total-items="rpagination.total"
                            ng-model="rpagination.currentPage"
                            class="pagination-sm"
                            previous-text="&lsaquo;"
                            next-text="&rsaquo;"
                            first-text="&laquo;"
                            last-text="&raquo;"></ul>
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script src="/js/controllers/project/question.js"></script>
@endsection