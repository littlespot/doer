@extends('layouts.zoomov')

@section('content')
<link rel="stylesheet" href="/css/message.css" />
<script type="text/ng-template" id="confirm.html">
    <div class="modal-body" id="modal-body">
        <h3 translate="project.MESSAGES.<%confirm%>"></h3>
    </div>
    <div class="modal-footer">
        <button class="btn btn-default" type="button" ng-click="$close(false)">{{trans("project.BUTTONS.cancel")}}</button>
        <button class="btn btn-danger" type="button" ng-click="$close(true)">{{trans("project.BUTTONS.confirm")}}</button>
    </div>
</script>
<div ng-controller="answersCtrl" ng-init="init('{{$user->id}}','{{$tab}}')">
    <div class="container">
        <div class="flex-rows">
            <div>
                <a href="/profile/{{$user->id}}" class="inner">
                    <img class="img-circle img-responsive" src="/context/avatars/{{$user->id}}.small.jpg">
                </a>&nbsp;
                <a href="/profile/{{$user->id}}" class="inner">{{$user->username}}</a>
            </div>
            <div class="text-default small">
                <span class="glyphicon"><?php echo file_get_contents(public_path("/images/icons/location.svg")); ?></span>
                <span>{{$user->city_name}}&nbsp;({{$user->sortname}})</span>
            </div>
        </div>
        <div class="tags margin-top-md padding-left-lg">
            @foreach($occupations as $role)
                <aside class='diamond text-center text-capitalize'><span translate="occupation.{{$role->name}}"></span></aside>
            @endforeach
        </div>
    </div>
    <br/>
    <div class="container content margin-top-md margin-bottom-lg" style="position: relative">
        <uib-tabset justified="true">
            <uib-tab index="1" select="selectTab('asks')">
                <uib-tab-heading>
                    <span id="tab-asks">{!!trans('personal.QUESTION.asks', ['cnt' => $user->asks_cnt])  !!}</span>
                </uib-tab-heading>
                <div ng-if="results.length == 0">
                    @include('templates.empty');
                </div>
                <div ng-repeat="q in results">
                    <div class="row margin-top-sm">
                        <div class="col-md-1 flex-top text-center">
                            <div class="calendar">
                                <div class="calendar-header" translate="month.short.<%q.month+1%>"></div>
                                <div class="calendar-body"><%q.day%></div>
                                <div class="calendar-footer"><%q.year%></div>
                            </div>
                        </div>
                        <div class="col-md-11" >
                            <div class="comment-container">
                                <div class="right-btn">
                                    @if($admin == 1)
                                        <div class="btn" ng-if="!q.answers_cnt" ng-click="deleteQuestion(q)" >
                                            <span class="fa fa-trash"></span>
                                        </div>
                                        <div class="btn" disabled  ng-if="q.answers_cnt" title="<%'project.MESSAGES.undelable' | translate %>" >
                                            <span class="fa fa-trash"></span>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-rows">
                                    <a class="text-info" href="/questions/<%q.id%>" target="_blank">
                                        <label ng-bind="q.subject"></label>
                                    </a>
                                    <div ng-if="q.answers_cnt == 0">
                                        <span ng-if="q.mine" class="text-default">{{trans('project.QUESTION.answer_none')}}</span>
                                        <span ng-if="!q.mine" class="text-danger">{{trans("project.QUESTION.answer_first")}}</span>
                                    </div>
                                    <div ng-if="q.answers_cnt > 0" class="text-primary" translate="project.AnswerCnt" translate-values="{cnt:q.answers_cnt}"></div>
                                </div>
                                <div ng-bind-html="q.content"></div>
                                <div class="blockquote-reverse">
                                    <a class="text-chocolate" href="/project/<%q.project_id%>" target="_blank" ng-bind="q.title"></a>
                                </div>
                                <div ng-class="{'br':!$last}">
                                    @if($admin == 1)
                                    <span class="text-info">
                                        <span ng-class="{'fa-bookmark-o' :!q.followers_cnt, 'fa-bookmark': q.followers_cnt}"
                                            class="btn btn-sm fa"></span>
                                        <span ng-if="q.followers_cnt > 0" ng-bind="q.followers_cnt"></span>
                                    </span>
                                    @else
                                    <span class="text-warning" id="favorite_<%q.id%>">
                                       <span ng-class="{'fa-bookmark-o' :!q.myfollow, 'fa-bookmark': q.myfollow}"
                                             class="btn btn-sm fa" ng-disabled="q.following" ng-click="followQuestion(q, 0)"></span>
                                        <span ng-if="q.followers_cnt > 0" ng-bind="q.followers_cnt"></span>
                                    </span>
                                    @endif
                                </div>
                                <div class="loader-content" ng-if="q.deleting"><div class="loader"></div> </div>
                            </div>
                        </div>
                    </div>
                </div>
            </uib-tab>
            <uib-tab index="2" select="selectTab('answers')">
                <uib-tab-heading>
                    <span id="tab-answers">{!!trans('personal.QUESTION.answers', ['cnt' => $user->answers_cnt])  !!}</span>
                </uib-tab-heading>
                <div ng-if="results.length == 0">
                    @include('templates.empty');
                </div>
                <div ng-repeat="q in results">
                    <div class="row margin-top-sm">
                        <div class="col-md-1 flex-cols text-center">
                            <div class="calendar">
                                <div class="calendar-header" translate="month.short.<%q.month+1%>"></div>
                                <div class="calendar-body"><%q.day%></div>
                                <div class="calendar-footer"><%q.year%></div>
                            </div>
                            <div ng-if="q.mine"></div>
                        </div>
                        <div class="col-md-11">
                            <div class="comment-container">
                                @if($admin)
                                    <div class="btn right-btn" ng-click="deleteAnswer(q)" >
                                        <span class="fa fa-trash"></span>
                                    </div>
                                @endif
                                <div class="flex-rows">
                                    <div>
                                        <span class="text-muted small" translate="FOR"></span>:&nbsp;
                                        <a class="text-info" href="/questions/<%q.question_id%>" target="_blank">
                                            <label ng-bind="q.subject"></label>
                                        </a>
                                    </div>
                                    <div ng-if="q.answers_cnt > 1" class="text-primary" translate="project.AnswerOther" translate-values="{cnt:q.answers_cnt-1}"></div>
                                    <div ng-if="q.answers_cnt == 1 && !q.mine" class="text-primary">{{trans("project.QUESTION.answer_wait")}}</div>
                                </div>
                                <div class="margin-top-sm" ng-bind-html="q.content"></div>
                                <div ng-class="{'br':!$last}">
                                    <div ng-if="q.mine" class="text-info">
                                        <span class="fa" ng-class="{'fa-star-o':!q.supports_cnt, 'fa-star':q.supports_cnt > 0}"></span>
                                        &nbsp;<span ng-if="q.supports_cnt > 0" ng-bind="q.supports_cnt"></span>
                                    </div>
                                    <div ng-if="!q.mine" class="text-warning" id="favorite_<%q.id%>" >
                                        <span class="btn btn-sm fa" ng-class="{'fa-star-o': !q.mysupport, 'fa-star':q.mysupport}" ng-disabled="q.supporting"
                                              ng-click="supportAnswer(q, false)"></span>
                                        <span ng-if="q.supports_cnt > 0" ng-bind="q.supports_cnt"></span>
                                    </div>
                                </div>
                                <div class="loader-content" ng-if="q.deleting"><div class="loader"></div> </div>
                            </div>
                        </div>
                    </div>
                </div>
            </uib-tab>
            <uib-tab index="3" select="selectTab('follows')">
                <uib-tab-heading>
                    <span id="tab-follows">{!!trans('personal.QUESTION.follows', ['cnt' => $user->follows_cnt])  !!}</span>
                </uib-tab-heading>
                <div ng-if="results.length == 0">
                    @include('templates.empty');
                </div>
                <div ng-repeat="q in results" class="margin-top-sm">
                    <div class="row">
                        <div class="col-md-1 flex-top text-center">
                            <div class="calendar">
                                <div class="calendar-header" translate="month.short.<%q.month+1%>"></div>
                                <div class="calendar-body"><%q.day%></div>
                                <div class="calendar-footer"><%q.year%></div>
                            </div>
                        </div>
                        <div class="col-md-11">
                            <div class="comment-container">
                                <div class="btn right-btn"  ng-if="q.mine && !q.answers_cnt" ng-click="deleteQuestion(q)" >
                                    <span class="fa fa-trash"></span>
                                </div>
                                <div class="btn right-btn" disabled  ng-if="q.mine && q.answers_cnt" title="<%'project.MESSAGES.undelable' | translate %>" >
                                    <span class="fa fa-trash"></span>
                                </div>
                                <a class="btn right-btn" ng-if="!q.mine" href="/questions/<%q.id%>?answer=1" title="{{trans("project.QUESTION.answer_wait")}}" >
                                    <span class="fa fa-hand-spock-o"></span>
                                </a>
                                <div>
                                    <div class="flex-rows">
                                        <a class="text-primary" href="/questions/<%q.id%>" target="_blank">
                                            <label ng-bind="q.subject"></label>
                                        </a>
                                        <div ng-if="q.answers_cnt == 0">
                                            <span ng-if="q.mine" class="text-default">{{trans('project.QUESTION.answer_none')}}</span>
                                            <span ng-if="!q.mine" class="text-danger">{{trans("project.QUESTION.answer_first")}}</span>
                                        </div>
                                        <div ng-if="q.answers_cnt > 0" class="text-primary" translate="project.AnswerCnt" translate-values="{cnt:q.answers_cnt}"></div>
                                    </div>
                                    <div>
                                        <span class="small text-muted" ng-bind="q.created_at | limitTo:16"></span>&nbsp;
                                        <a class="title" ng-if="q.username" href="{{URL::asset('profile')}}/<%q.user_id%>" target="_blank">
                                            <span ng-bind="q.username"></span>
                                        </a>
                                    </div>
                                </div>

                                <div ng-bind-html="q.content"></div>
                                <div class="blockquote-reverse" ng-bind="q.title"></div>
                                <div ng-class="{'br': !$last }">
                                    <div ng-if="q.mine" class="text-info">
                                        <span class="btn btn-sm fa" ng-class="{'fa-bookmark-o':!q.followers_cnt, 'fa-bookmark':q.followers_cnt}"></span>
                                        <span ng-if="q.followers_cnt > 0" ng-bind="q.followers_cnt"></span>
                                    </div>
                                    <div ng-if ="!q.mine" class="text-warning" id="favorite_<%q.id%>">
                                         <span ng-class="{'fa-bookmark-o' :!q.myfollow, 'fa-bookmark': q.myfollow}" class="btn btn-sm t fa"
                                               ng-disabled ='q.following'
                                               ng-click="followQuestion(q, '{{$admin}}')"></span>
                                         <span ng-if="q.followers_cnt > 0" ng-bind="q.followers_cnt"></span>
                                    </div>
                                </div>
                                <div class="loader-content" ng-if="q.deleting"><div class="loader"></div> </div>
                            </div>
                        </div>
                    </div>
                </div>
            </uib-tab>
            <uib-tab index="4" select="selectTab('supports')">
                <uib-tab-heading>
                    <span id="tab-supports">{!!trans('personal.QUESTION.supports', ['cnt' => $user->supports_cnt])  !!}</span>
                </uib-tab-heading>
                <div ng-if="results.length == 0">
                    @include('templates.empty');
                </div>
                <div ng-repeat="q in results">
                    <div class="row margin-top-sm">
                        <div class="col-md-1 flex-top text-center">
                            <div class="calendar">
                                <div class="calendar-header" translate="month.short.<%q.month+1%>"></div>
                                <div class="calendar-body"><%q.day%></div>
                                <div class="calendar-footer"><%q.year%></div>
                            </div>
                        </div>
                        <div class="col-md-11">
                            <div class="comment-container">
                                <div ng-if="q.mine" class="btn right-btn" ng-click="deleteAnswer(q)" >
                                    <span class="fa fa-trash"></span>
                                </div>
                                <div>
                                    <div class="flex-rows">
                                        <div>
                                            <span class="text-muted small" translate="FOR"></span>:&nbsp;
                                            <a class="text-info" href="/questions/<%q.question_id%>" target="_blank">
                                                <label ng-bind="q.subject"></label>
                                            </a>
                                        </div>
                                        <div>
                                            <div ng-if="q.answers_cnt > 1" class="text-primary" translate="project.AnswerOther" translate-values="{cnt:q.answers_cnt-1}"></div>
                                            <div ng-if="q.answers_cnt == 1 && !q.mine" class="text-primary">{{trans("project.QUESTION.answer_wait")}}</div>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="small text-muted" ng-bind="q.created_at | limitTo:16"></span>&nbsp;
                                        <a class="title" href="/profile/<%q.user_id%>">
                                            <span ng-bind="q.username"></span>
                                        </a>
                                    </div>
                                </div>
                                <div ng-bind-html="q.content"></div>
                                <div ng-class="{'br':!$last}">
                                    <div ng-if="q.mine" class="text-info">
                                        <span class="btn btn-sm fa fa-star"></span>
                                        <span ng-bind="q.supports_cnt"></span>
                                    </div>
                                    <div ng-if ="!q.mine" class="text-warning" id="support_<%q.id%>">
                                        <span ng-class="{'fa-star-o' :!q.mysupport, 'fa-star': q.mysupport}"
                                            ng-disabled="q.supporting"  ng-click="supportAnswer(q, '{{$admin}}')"
                                            class="btn btn-sm fa" ></span>
                                        <span ng-bind="q.supports_cnt"></span>
                                    </div>
                                </div>
                                <div class="loader-content" ng-if="q.deleting"><div class="loader"></div> </div>
                            </div>
                        </div>
                    </div>
                </div>
            </uib-tab>
        </uib-tabset>
        <div class="text-center" ng-show="pagination.show">
            <ul uib-pagination ng-change="pageChanged()"
                max-size="5"
                rotate = true
                items-per-page ="pagination.perPage"
                boundary-links="true"
                total-items="pagination.total"
                ng-model="pagination.currentPage"
                class="pagination-sm"
                previous-text="&lsaquo;"
                next-text="&rsaquo;"
                first-text="&laquo;"
                last-text="&raquo;"></ul>
        </div>
        <div class="loader-content" ng-if="loading"><div class="loader"></div> </div>
    </div>
</div>
    @endsection
@section('script')
    <script src="/js/controllers/user/question.js"></script>
@endsection