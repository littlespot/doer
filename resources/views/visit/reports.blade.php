@extends('layouts.zoomov')

@section('content')
<link href="{{URL::asset('css/message.css') }}" rel="stylesheet" />
<script type="text/ng-template" id="confirm.html">
    <div class="modal-body" id="modal-body">
        <h3 translate="project.MESSAGES.<%confirm%>"></h3>
    </div>
    <div class="modal-footer">
        <button class="btn btn-default" type="button" ng-click="$close(false)">{{trans("project.BUTTONS.cancel")}}</button>
        <button class="btn btn-danger" type="button" ng-click="$close(true)">{{trans("project.BUTTONS.confirm")}}</button>
    </div>
</script>
<div ng-controller="reportsCtrl" ng-init="init('{{$user->id}}','{{$tab}}')">
    <div class="container">
        <div class="row ">
            <div id="profileRelation" class="align-self-top col-lg-2 col-md-3 col-sm-4 col-xs-12">
                <div class="text-center float-left py-5">
                    <a href="/profile/{{$user->id}}">
                        <img class="rounded-circle img-fluid" style="border: 1px solid #999;width: 120px"  src="/storage/avatars/{{$user->id}}.jpg?{{time()}}" />
                    </a>
                    <div class="pt-3">
                        <a class="text-info" href="/profile/{{$user->id}}">{{$user->username}}</a>
                    </div>
                    <div class="text-muted text-right">
                        <span class="fa fa-map-marker"></span>
                        <span>{{$user->city_name}} ({{$user->country}})</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-10 col-md-9 col-sm-8 col-xs-12 d-flex flex-column justify-content-between">
                <div></div>
                <div class="pl-2">
                    <h5>
                        @foreach($occupations as $role)
                            <div class='badge badge-pill badge-info text-capitalize mr-3'>{{$role->name}}</div>
                        @endforeach
                    </h5>
                </div>

                <ul class="nav nav-tabs nav-justified">
                    <li class="nav-item">
                        <a class="nav-link" ng-class="{'active':selectedTab=='writes'}" href="javascript:void(0)" ng-click="selectTab('writes')">{!! trans('project.REPORT.writes', ['cnt'=>$user->writes_cnt]) !!}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" ng-class="{'active':selectedTab=='loves'}" href="javascript:void(0)" ng-click="selectTab('loves')">{!! trans('project.REPORT.loves', ['cnt'=>$user->lovers_cnt]) !!}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" ng-class="{'active':selectedTab=='comments'}" href="javascript:void(0)" ng-click="selectTab('comments')">{!! trans('project.REPORT.comments', ['cnt'=>$user->comments_cnt]) !!}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="container content bg-white py-5">
        <div ng-if="results.length == 0">
            @include('templates.empty')
        </div>
        <div ng-repeat="q in results">
            <div class="row py-3">
                <div class="align-self-top col-lg-2 col-md-3 col-sm-4 col-xs-12 text-center">
                    <div class="calendar px-3">
                        <div class="calendar-header" translate="month.short.<%q.month%>"></div>
                        <div class="calendar-body"><%q.day%></div>
                        <div class="calendar-footer"><%q.year%></div>
                    </div>
                </div>
                <div class="col-lg-9 col-md-8 col-sm-6 col-xs-8 d-flex flex-column justify-content-between border-left ml-2">
                    <div>
                        <a class="text-info" href="/reports/<%q.id%>" target="_blank">
                            <label ng-bind="q.title"></label>
                        </a>
                        <a class="btn text-muted" ng-if="q.mine" href="/admin/reports/<%q.id%>" target="_blank">
                            <span class="fa fa-edit"></span>
                        </a>
                    </div>
                    <div class="text-right">
                        <a ng-if="selectedTab == 'writes'" class="text-info" href="/profile/{{$user->id}}">{{$user->username}}</a>
                        <a ng-if="selectedTab != 'writes'" class="text-info" href="/profile/<%q.user_id%>" ng-bind="q.username">{{$user->username}}</a>
                        @
                        <a class="text-info" href="/project/<%q.project_id%>" target="_blank" ng-bind="q.project_title"></a>
                    </div>
                    <div class="py-3" ng-bind="q.synopsis"></div>

                    <div class="d-flex" ng-class="{'br':!$last}">
                        <div class="text-danger" id="favorite_<%q.id%>">
                           <span ng-class="{'fa-heart-o' :!q.mylove, 'fa-heart': q.mylove}"
                                 class="btn btn-sm fa" ng-disabled="q.loving" ng-click="loveReport(q, 0)"></span>
                            <span ng-if="q.lovers_cnt > 0" ng-bind="q.lovers_cnt"></span>
                        </div>
                        <div class="text-info ml-5">
                            <span class="fa" ng-class="{'fa-comment-o':!q.comments_cnt, 'fa-commenting-o':q.comments_cnt}"></span>
                            <span ng-if="q.comments_cnt" ng-bind="q.comments_cnt"></span>
                        </div>
                        <hr/>
                    </div>

                </div>
            </div>
        </div>
        <nav class="container pt-2" ng-show="pagination.show">
            <ul class="pagination justify-content-center">
                <li class="page-item">
                    <a class="page-link" href="#" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">Previous</span>
                    </a>
                </li>
                <li class="page-item" ng-repeat="i in pagination.pages" ng-class="{'active':i==pagination.currentPage}" ng-click="pageChanged(i)">
                    <a class="page-link" href="#" ng-bind="i"></a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">Next</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>

@endsection
@section('script')
    <script src="/js/controllers/user/report.js"></script>
@endsection