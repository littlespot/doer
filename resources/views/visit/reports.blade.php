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
<div class="container">
    <div class="flex-rows">
        <div>
            <a href="{{URL::asset('profile')}}/{{$user->id}}" class="inner">
                <img class="img-circle img-responsive" src="{{URL::asset('context/avatars')}}/{{$user->id}}.small.jpg">
            </a>&nbsp;
            <a href="{{URL::asset('profile')}}/{{$user->id}}" class="inner">{{$user->username}}</a>
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
<br>
<div ng-controller="reportsCtrl" ng-init="init('{{$user->id}}','{{$tab}}')">
    <div class="container content margin-top-md margin-bottom-lg" style="position: relative">
        <uib-tabset justified="true">
            <uib-tab index="1" select="selectTab('writes')">
                <uib-tab-heading>
                    <span id="tab-writes">{!! trans('project.REPORT.writes', ['cnt'=>$user->writes_cnt]) !!}</span>
                </uib-tab-heading>
                <div ng-if="results.length == 0">
                    @include('templates.empty');
                </div>
                <div ng-repeat="q in results">
                    <div class="row margin-top-sm">
                        <div class="col-md-1 flex-top text-center">
                            <div class="calendar">
                                <div class="calendar-header" translate="month.short.<%q.month%>"></div>
                                <div class="calendar-body"><%q.day%></div>
                                <div class="calendar-footer"><%q.year%></div>
                            </div>
                        </div>
                        <div class="col-md-11" >
                            <div class="comment-container">
                                @if($admin == 1)
                                    <a class="btn right-btn" href="/admin/reports/<%q.id%>" target="_blank">
                                        <span class="fa fa-edit"></span>
                                    </a>
                                @endif
                                <div class="flex-rows">
                                    <a class="text-info" href="/reports/<%q.id%>" target="_blank">
                                        <label ng-bind="q.title"></label>
                                    </a>
                                </div>
                                <div ng-bind="q.synopsis"></div>
                                <div class="blockquote-reverse">
                                    <a class="text-chocolate" href="/project/<%q.project_id%>" target="_blank" ng-bind="q.project_title"></a>
                                </div>
                                <div class="flex-rows" ng-class="{'br':!$last}">
                                    @if($admin == 1)
                                        <div class="text-info">
                                            <span ng-class="{'fa-heart-o' :!q.lovers_cnt, 'fa-heart': q.lovers_cnt}"
                                                  class="btn btn-sm fa"></span>
                                            <span ng-if="q.lovers_cnt > 0" ng-bind="q.lovers_cnt"></span>
                                        </div>
                                    @else
                                        <div class="text-important" id="favorite_<%q.id%>">
                                           <span ng-class="{'fa-heart-o' :!q.mylove, 'fa-heart': q.mylove}"
                                                 class="btn btn-sm fa" ng-disabled="q.loving" ng-click="loveReport(q, 0)"></span>
                                            <span ng-if="q.lovers_cnt > 0" ng-bind="q.lovers_cnt"></span>
                                       </div>
                                    @endif
                                        <div class="text-info">
                                            <span class="fa" ng-class="{'fa-comment-o':!q.comments_cnt, 'fa-commenting-o':q.comments_cnt}"></span>
                                            <span ng-if="q.comments_cnt" ng-bind="q.comments_cnt"></span>
                                        </div>
                                    <div>&nbsp;</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </uib-tab>
            <uib-tab index="2" select="selectTab('loves')">
                <uib-tab-heading>
                    <span id="tab-loves">{!! trans('project.REPORT.loves', ['cnt'=>$user->lovers_cnt]) !!}</span>
                </uib-tab-heading>
                <div ng-if="results.length == 0">
                    @include('templates.empty');
                </div>
                <div ng-repeat="q in results" class="margin-top-md">
                    <div class="row">
                        <div class="col-md-1 flex-top text-center">
                            <div class="calendar">
                                <div class="calendar-header" translate="month.short.<%q.month%>"></div>
                                <div class="calendar-body"><%q.day%></div>
                                <div class="calendar-footer"><%q.year%></div>
                            </div>
                        </div>
                        <div class="col-md-11">
                            <div class="comment-container">
                                <a ng-if="q.mine" class="btn right-btn" href="/admin/reports/<%q.id%>" target="_blank">
                                    <span class="fa fa-edit"></span>
                                </a>
                                <a class="text-primary" href="/reports/<%q.id%>" target="_blank">
                                    <label ng-bind="q.title"></label>
                                </a>
                                <div>
                                    <span class="small text-muted" ng-bind="q.created_at | limitTo:16"></span>&nbsp;
                                    <a class="title" href="{{URL::asset('profile')}}/<%q.user_id%>" target="_blank">
                                        <span ng-bind="q.username"></span>
                                    </a>
                                </div>
                                <div ng-bind="q.synopsis"></div>
                                <div class="blockquote-reverse" href="{{URL::asset('project')}}/<%q.project_id%>" target="_blank" ng-bind="q.project_title">
                                </div>
                                <div class="flex-rows" ng-class="{'br': !$last }">
                                    <div ng-if="q.mine" class="text-info">
                                        <span class="fa fa-heart"></span>
                                        <span ng-bind="q.lovers_cnt"></span>
                                    </div>
                                    <div ng-if ="!q.mine" class="text-important" id="favorite_<%q.id%>" >
                                       <span ng-class="{'fa-heart-o' :!q.mylove, 'fa-heart': q.mylove}" ng-disabled ='q.loving'
                                             ng-click="loveReport(q, '{{$admin}}')"
                                             class="btn btn-sm fa" ></span>
                                        <span ng-bind="q.lovers_cnt"></span>
                                    </div>
                                    <div class="text-info">
                                        <span class="fa" ng-class="{'fa-comment-o' :!q.comments_cnt, 'fa-commenting-o': q.comments_cnt}"></span>
                                        <span ng-if="q.comments_cnt > 0" ng-bind="q.comments_cnt"></span>
                                    </div>
                                    <div></div>
                                </div>
                                <div class="loader-content" ng-if="q.deleting"><div class="loader"></div> </div>
                            </div>
                        </div>
                    </div>
                </div>
            </uib-tab>
            <uib-tab index="3" select="selectTab('comments')">
                <uib-tab-heading>
                    <span id="tab-comments">{!! trans('project.REPORT.comments', ['cnt'=>$user->comments_cnt]) !!}</span>
                </uib-tab-heading>
                <div ng-if="results.length == 0">
                    @include('templates.empty');
                </div>
                <div ng-repeat="q in results" class="margin-top-md">
                    <div class="row">
                        <div class="col-md-1 flex-top text-center">
                            <div class="calendar">
                                <div class="calendar-header" translate="month.short.<%q.month%>"></div>
                                <div class="calendar-body"><%q.day%></div>
                                <div class="calendar-footer"><%q.year%></div>
                            </div>
                        </div>
                        <div class="col-md-11">
                            <div class="comment-container">
                                @if($admin)
                                <span class="btn right-btn" ng-click="deleteComment(q)">
                                    <span class="fa fa-trash"></span>
                                </span>
                                @endif
                                <div ng-bind="q.message"></div>
                                <div class="blockquote-reverse">
                                    <a class="text-primary" href="{{URL::asset('/api/report')}}/<%q.report_id%>" target="_blank">
                                        <label ng-bind="q.title"></label>
                                    </a>
                                    <div>
                                        <span class="text-mutted small" ng-bind="q.created_at|limitTo:16"></span>&nbsp;
                                        <a class="title" href="{{URL::asset('profile')}}/<%q.user_id%>" ng-bind="q.username"></a>
                                    </div>
                                </div>
                                <div class="flex-rows" ng-class="{'br': !$last }">
                                    @if($admin)
                                    <div class="text-info">
                                        <span class="fa" ng-class="{'fa-star-o':!q.supports_cnt,'fa-star':q.supports_cnt}"></span>
                                        <span ng-if="q.supports_cnt" ng-bind="q.supports_cnt"></span>
                                    </div>
                                    @else
                                    <div class="text-warning" id="favorite_<%q.id%>" >
                                       <span ng-class="{'fa-star-o' :!q.mysupport, 'fa-star': q.mysupport}" ng-disabled ='q.supporting'
                                             ng-click="supportComment(q)"
                                             class="btn btn-sm fa" >
                                       </span>
                                        <span ng-if="q.supports_cnt"  ng-bind="q.supports_cnt"></span>
                                    </div>
                                    @endif
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
    <script src="/js/controllers/user/report.js"></script>
@endsection