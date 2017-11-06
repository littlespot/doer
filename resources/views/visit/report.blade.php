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
<div class="margin-top-lg" ng-controller="reportCtrl" ng-init="init('{{$report->id}}', '{{$report->project_id}}')">
    <div class="container">
        <div class="tags">
            @foreach($tags as $tag)
                <div class="tag tag-default">
                    <a href="#">{{$tag->label}}&nbsp;<span class="tail">{{$tag->cnt}}</span></a>
                </div>
            @endforeach
        </div>
        <div class="margin-top-sm flex-rows">
            <div class="h3">{{$report->title}}</div>
            @if($report->admin)
                <div class="btn btn-text-info btn-sq-sm">
                    @if($report->lovers_cnt > 0)
                        <div class="fa fa-heart"></div>&nbsp;
                        <div>{{$report->lovers_cnt}}</div>
                    @else
                        <div class="fa fa-heart-o"></div>
                    @endif
                </div>
            @elseif($report->mylove)
                <div class="btn btn-text-important btn-sq-sm" id="lovers" ng-disabled="loving" ng-click="love()">
                    <div class="fa fa-heart fa-2x"></div>&nbsp;
                    <div id="count">{{$report->lovers_cnt}}</div>
                </div>
            @else
                <div class="btn btn-text-important btn-sq-sm" id="lovers" ng-disabled="loving" ng-click="love()">
                    <div class="fa fa-heart-o fa-2x" ></div>&nbsp;
                    <div id="count">@if($report->lovers_cnt > 0){{$report->lovers_cnt}}@endif</div>
                </div>
            @endif
        </div>
    </div>
    <div class="container content">
        <div class="row">
            <div class="col-md-8">
                <div class="flex-rows">
                    <div class="inner">
                        <img src="/context/avatars/{{$report->user_id}}.small.jpg" class="img-circle img-responsive inner" />
                        @if($report->admin)
                            <span class="text-info">{{Auth::user()->username}}</span>
                        @else
                            <a class="title" href="/profile/{{$report->user_id}}">{{$report->username}}</a>
                        @endif
                        &nbsp;<span class="text-mutted small">{{str_limit($report->created_at, 16, '')}}</span>
                    </div>
                    @if($report->admin)
                        <div>
                            <a class="btn btn-text-info" href="/admin/reports/{{$report->id}}">{{trans("project.BUTTONS.edit")}}</a>&nbsp;
                            <div class="btn btn-danger" ng-click="delete()">{{trans("project.BUTTONS.delete")}}</div>
                        </div>
                    @endif
                </div>
                <blockquote class="margin-top-sm">
                    {!! $report->synopsis !!}
                </blockquote>
                <div>
                    {!! $report->content !!}
                </div>
                <hr/>
                <div class="margin-bottom-md" >
                    <span class="text-uppercase" translate="user.Project.Comments"></span><sup ng-if="pagination.total" ng-bind="pagination.total"></sup>
                </div>
                <div style="position: relative">
                    <div comment-content related-option="reports">
                        @include('templates.comments')
                    </div>
                    <div ng-if="commenting" class="loader-content"><div class="loader"></div></div>
                </div>
            </div>
            <div class="col-md-4" style="padding-left: 20px">
                <h5 class="text-center text-default">{{trans('project.LABELS.report_related')}}</h5>
                <h5>
                    <a class="text-warning" href="/project/{{$report->project_id}}" target="_blank">{{$report->project_title}}</a>
                </h5>
                <div>
                    <img ng-src="/context/projects/{{$report->project_id}}.thumb.jpg" width="100%"/>
                </div>
                <div class="text-right">
                    <a class="inner" href="/profile/{{$report->planner_id}}">
                        <img class="img-circle img-responsive" src="/context/avatars/{{$report->planner_id}}.small.jpg" />
                    </a>
                    <a id="user" class="inner" href="/profile/{{$report->planner_id}}">
                        {{$report->planner_name}}
                    </a>
                </div>
                <hr/>
                <h5 class="text-center text-default text-uppercase" translate="project.RelatedReports" translate-values="{cnt:rpagination.total}"></h5>
                <div style="position: relative">
                    <div ng-repeat="r in reports">
                        <h6>
                            <a href="/reports/<%r.id%>" ng-bind="r.title"></a>
                        </h6>
                        <div class="flex-rows">
                            <a class="title" href="/profile/<%r.user_id%>" ng-bind="r.username"></a>
                            <span class="text-muted small" ng-bind="r.created_at | limitTo:16" ></span>
                        </div>
                        <blockquote ng-bind="r.synopsis"></blockquote>
                        <div class="flex-rows">
                            <div></div>
                            <div ng-if="r.mine" class="text-info">
                                <span class="fa" disabled ng-class="{'fa-heart-o':!r.lovers_cnt, 'fa-heart':r.lovers_cnt}"></span>&nbsp;
                                <span ng-if="r.lovers_cnt" ng-bind="r.lovers_cnt"></span>
                            </div>
                            <div ng-if="!r.mine" class="text-important">
                                <span class="fa" disabled ng-class="{'fa-heart-o':!r.mylove, 'fa-heart':r.mylove}"></span>&nbsp;
                                <span ng-if="r.lovers_cnt" ng-bind="r.lovers_cnt"></span>
                            </div>
                            <div class="text-info">
                                <span class="fa" ng-class="{'fa-comment-o':!r.comments_cnt, 'fa-commenting-o':r.comments_cnt}"></span>
                                <span ng-if="r.comments_cnt" ng-bind="r.comments_cnt"></span>
                            </div>
                        </div>
                        <hr/>
                    </div>
                    <div ng-if="reporting" class="loader-content"><div class="loader"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script src="/js/directives/comment.js"></script>
    <script src="/js/controllers/project/report.js"></script>
@endsection