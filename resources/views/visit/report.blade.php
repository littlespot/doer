@extends('layouts.zoomov')

@section('content')
<link rel="stylesheet" href="/css/message.css" />
<link rel="stylesheet" href="/css/tag.css" />

<div class="py-5" ng-controller="reportCtrl" ng-init="init('{{$report->id}}', '{{$report->project_id}}')">
    <div class="modal fade" id="unloveConfirmModal" tabindex="-1" role="dialog" aria-labelledby="unloveConfirmModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal-body">
                    <h6 translate="project.MESSAGES.confirmL"></h6>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" type="button" data-dismiss="modal" >
                        {{trans("project.BUTTONS.cancel")}}
                    </button>
                    <div class="btn btn-danger" type="button" ng-click="loveConfirmed()">{{trans("project.BUTTONS.confirm")}}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="container content">
        <div class="row">
            <div class="col-md-8">
                <h3 class="d-flex justify-content-between">
                    {{$report->title}}
                    @if($report->admin)
                    <div class="btn-group">
                        <a class="btn btn-outline-info" href="/admin/reports/{{$report->id}}">{{trans("project.BUTTONS.edit")}}</a>&nbsp;
                        <div class="btn btn-danger" ng-click="delete()">{{trans("project.BUTTONS.delete")}}</div>
                    </div>
                    @endif
                </h3>
                <div class="mt-3">
                    <img src="/storage/avatars/{{$report->user_id}}.small.jpg" class="mr-3 rounded-circle" />
                    <div class="d-inline-flex align-self-center pl-3">
                        @if($report->admin)
                            <a class="title" href="/profile/{{auth()->id()}}">{{auth()->user()->username}}</a>
                        @else
                            <a class="title" href="/profile/{{$report->user_id}}">{{$report->username}}</a>
                        @endif
                        <div class="ml-3 badge text-muted align-self-center">{{str_limit($report->created_at, 16, '')}}</div>
                    </div>
                </div>
                <blockquote class="mt-3 text-muted small">
                    {!! $report->synopsis !!}
                </blockquote>
                <div>
                    {!! $report->content !!}
                </div>
                <hr/>
                <div class="my-3" ng-if="pagination.total" >
                    <span class="text-uppercase" translate="user.Project.Comments"></span><sup class="text-danger" ng-bind="pagination.total"></sup>
                </div>
                <div>
                    <div comment-content related-option="reports">
                        @include('templates.comments')
                    </div>
                </div>
            </div>
            <div class="col-md-4 pl-3">
                <div class="d-flex justify-content-end">
                    @if($report->admin)
                        <div style="width:80px"  class='btn btn-outline-danger d-flex flex-column' disabled data-toggle="tooltip" title="{{trans('layout.TIP.followers')}}">
                            @if($report->lovers_cnt > 0)
                                <div class="fa fa-heart"></div>&nbsp;
                                <div>{{$report->lovers_cnt}}</div>
                            @else
                                <div class="fa fa-heart-o"></div>
                            @endif
                        </div>
                    @elseif($report->mylove)
                        <div style="width:80px"  class='btn btn-outline-danger d-flex flex-column' id="lovers" ng-disabled="loving" ng-click="love()">
                            <div class="fa fa-heart"></div>&nbsp;
                            <div id="count">{{$report->lovers_cnt}}</div>
                        </div>
                    @else
                        <div style="width:80px"  class="btn btn-outline-secondary d-flex flex-column" id="lovers" ng-disabled="loving" ng-click="love()">
                            <div class="fa fa-heart-o" ></div>&nbsp;
                            <div id="count">@if($report->lovers_cnt > 0){{$report->lovers_cnt}}@endif</div>
                        </div>
                    @endif
                </div>
                <h5 class="mt-3">{{trans('project.LABELS.report_related')}}</h5>
                <h5>
                    <a class="text-warning" href="/project/{{$report->project_id}}" target="_blank">{{$report->project_title}}</a>
                </h5>
                <div class="my-3">
                    <a class="mr-1 " href="/profile/{{$report->planner_id}}">
                        <img class="rounded-circle img-thumbnail" src="/storage/avatars/{{$report->planner_id}}.small.jpg" />
                    </a>
                    <div class="d-inline-flex align-self-center pl-3">
                        <a id="user" href="/profile/{{$report->planner_id}}">
                            {{$report->planner_name}}
                        </a>
                        <div class="ml-3 badge text-muted align-self-center">{{str_limit($report->created_at, 16, '')}}</div>
                    </div>
                </div>
                <div>
                    <img ng-src="/storage/projects/{{$report->project_id}}.thumb.jpg" class="img-thumbnail"/>
                </div>

                <h5 class="text-center mt-5 text-uppercase" translate="project.RelatedReports" translate-values="{cnt:rpagination.total}"></h5>
                <hr>
                <div>
                    <div ng-repeat="r in reports">
                        <h6>
                            <a href="/reports/<%r.id%>" ng-bind="r.title"></a>
                        </h6>
                        <div class="my-3">
                            <a class="mr-1 " href="/profile/<%r.user_id%>">
                                <img class="rounded-circle img-thumbnail" src="/storage/avatars/<%r.user_id%>.small.jpg" />
                            </a>
                            <div class="d-inline-flex align-self-center pl-3">
                                <a class="title" href="/profile/<%r.user_id%>" ng-bind="r.username"></a>
                                <div class="ml-3 badge text-muted align-self-center" ng-bind="r.created_at | limitTo:16"></div>
                            </div>
                        </div>
                        <blockquote class="text-muted small" ng-bind="r.synopsis"></blockquote>
                        <div class="d-flex justify-content-end">
                            <div ng-if="r.mine" class="text-info mr-3">
                                <span class="fa" disabled ng-class="{'fa-heart-o':!r.lovers_cnt, 'fa-heart':r.lovers_cnt}"></span>&nbsp;
                                <span class="badge" ng-if="r.lovers_cnt" ng-bind="r.lovers_cnt"></span>
                            </div>
                            <div ng-if="!r.mine" class="mr-3 text-danger">
                                <span class="fa" disabled ng-class="{'fa-heart-o':!r.mylove, 'fa-heart':r.mylove}"></span>&nbsp;
                                <span ng-if="r.lovers_cnt" ng-bind="r.lovers_cnt"></span>
                            </div>
                            <div class="text-muted">
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