@extends('layouts.zoomov')

@section('content')

<link rel="stylesheet" href="/css/tag.css" />
<div ng-controller="projectDetailCtrl" ng-init='init("{{$project->id}}", "{{$project->user_id}}","{{$comments_cnt}}", "{{$tab}}")'>
    @if($project->active == 1)
    <div id="project-status" class="mt-5 pt-5 fixed-right btn-group-vertical">
        <div class='btn text-primary d-flex flex-column border border-secondary'
             disabled data-toggle="tooltip" title="{{trans('layout.TIP.views')}}">
            <div class="fa fa-eye"></div>
            <div id="views_cnt" class="pt-1">{{$project->admin ? $views_cnt : $views_cnt + 1 }}</div>
        </div>
        @if($project->admin)
            <div class='btn btn-outline-success d-flex flex-column border-secondary' disabled data-toggle="tooltip" title="{{trans('layout.TIP.followers')}}">
                <div class='fa fa-bookmark{{$followers_cnt ? "" : "-o"}} fa-2x'></div>
                <div>{{$followers_cnt}}</div>
            </div>
        @else
        <div id="followers_cnt" class='btn btn-outline-success d-flex flex-column border-secondary' ng-disabled='following' data-toggle="tooltip"
                 title="{{$myfollow ? trans('layout.TIP.unfollow'):trans('layout.TIP.follow')}}"
                 data-title-0="{{trans('layout.TIP.follow')}}" data-title-1="{{trans('layout.TIP.unfollow')}}"
                 ng-click="followProject()">
                <div class='fa fa-bookmark{{$myfollow ? "" : "-o"}}'></div>
                <div>{{$followers_cnt}}</div>
            </div>
        @endif
        <div id="lovers_cnt" class='btn btn-outline-danger d-flex flex-column border-secondary'  ng-disabled='lovwing'
             title="{{$mylove ? trans('layout.TIP.unlove'):trans('layout.TIP.love')}}"
             data-title-0="{{trans('layout.TIP.love')}}" data-title-1="{{trans('layout.TIP.unlove')}}"
             ng-click="loveProject()">
            <div class='fa fa-heart{{$mylove ? "" : "-o"}}'></div>
            <div>{{$lovers_cnt}}</div>
        </div>
    </div>
   @else
        <div id="project-status" class="mt-5 pt-5 fixed-right btn-group-vertical">
            <div class='btn text-primary d-flex flex-column border border-secondary  disabled'
                 disabled data-toggle="tooltip" title="{{trans('layout.TIP.views')}}">
                <div class="fa fa-eye"></div>
                <div></div>
            </div>
            <div class='btn btn-outline-success d-flex flex-column border-secondary  disabled' disabled data-toggle="tooltip" title="{{trans('layout.TIP.followers')}}">
                <div class='fa fa-bookmark-o'></div>
                <div></div>
            </div>
            <div id="lovers_cnt" class='btn btn-outline-danger d-flex flex-column border-secondary disabled' disabled data-toggle="tooltip" >
                <div class='fa fa-heart-o'></div>
                <div></div>
            </div>
        </div>
    @endif
    <div class="container">
        @include('templates.synopsis')
        <ul class="row nav nav-pills nav-fill">
            <li class="nav-item border">
                <a class="nav-link"  ng-class="{'active':selectedTab == 0}"
                   id="pills-info-tab" data-index="0"
                   data-toggle="pill" href="#pills-info"
                   role="tab" aria-controls="pills-info"
                   aria-selected="true">{{trans('layout.LABELS.information')}}</a>
            </li>
            <li class="nav-item border">
                <a class="nav-link"  ng-class="{'active':selectedTab == 1}"
                   id="pills-team-tab" data-index="1"
                   data-toggle="pill" href="#pills-team"
                   role="tab" aria-controls="pills-team"
                   aria-selected="true">
                    {{trans('project.CREATION.team')}}
                </a>
            </li>
            <li class="nav-item border">
                <a class="nav-link"  ng-class="{'active':selectedTab == 2}"
                   id="pills-event-tab" data-index="2"
                   data-toggle="pill" href="#pills-event"
                   role="tab" aria-controls="pills-event"
                   aria-selected="true">
                    {{trans('project.LABELS.events')}}
                </a>
            </li>
            @if($project->active)
                <li class="nav-item border">
                    <a class="nav-link"  ng-class="{'active':selectedTab == 3}"
                       id="pills-comment-tab" data-index="3"
                       data-toggle="pill" href="#pills-comment"
                       role="tab" aria-controls="pills-comment"
                       aria-selected="true">
                        {{trans('layout.LABELS.comments')}}
                        <sup id="sup_comments" class="badge text-danger" ng-show="comments_cnt > 0"  ng-bind="comments_cnt"></sup>
                    </a>
                </li>
                <li class="nav-item border">
                    <a class="nav-link" ><span class="fa fa-share-alt"></span></a>
                </li>
            @else
                <li class="nav-item border">
                    <span class="nav-link text-muted" >{{trans('layout.LABELS.comments')}}</span>
                </li>
                <li class="nav-item border">
                    <span class="nav-link text-muted" ><span class="fa fa-share-alt"></span></span>
                </li>
            @endif
        </ul>
        <div style="position: relative;margin: 0 -15px" class="content bg-white">
            <div class="loader-content" ng-show="loading"><div class="loader"></div></div>
            <div class="tab-content p-3" id="pills-tabContent">
                <div class="tab-pane fade show" id="pills-info" role="tabpanel" aria-labelledby="pills-info-tab" ng-class="{'active':selectedTab == 0}">
                    <div class="row">
                        <div class="col-9 px-4 py-3" >
                            @if(sizeof($project->scripts) > 0)
                                <div class="table-responsive">
                                    <h4>{{trans('project.LABELS.script')}}</h4>
                                    <table class="table">
                                        <tbody>
                                        @foreach($project->scripts as $script)
                                            <tr>
                                                <td width="200px"><a href="{{$script->link}}" title="{{$script->description}}" target="_blank">{{$script->title}}</a></td>
                                                <td>
                                                    @foreach($script->authors as $key=>$author)
                                                        @if(!is_null($author->user_id))
                                                            <a href="/profile/{{$author->user_id}}" target="_blank">{{$author->name}}</a>
                                                        @elseif(is_null($author->link))
                                                            {{$author->name}}
                                                        @else
                                                            <a href="{{$author->link}}" target="_blank">{{$author->name}}</a>
                                                        @endif
                                                        @if($key < sizeof($script->authors) -1),@endif
                                                    @endforeach
                                                </td>
                                                <td width="100px" class="text-right">
                                            <span class="small"  class="pull-right">
                                                 {{str_limit($script->created_at, 10, '')}}
                                            </span>
                                                <td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            @if(sizeof($project->budget) > 0)
                                <div class="table-responsive">
                                    <h4 class="text-info">{{trans('project.LABELS.budget')}}</h4>
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>{{trans('project.LABELS.budget_type')}}</th>
                                            <th width="20%" class="number">{{trans('project.LABELS.sum')}}</th>
                                            <th>{{trans('project.LABELS.budget_comment')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($project->budget as $budget)
                                            <tr>
                                                <td align="center">
                                                    {{$budget->type->name}}
                                                </td>
                                                <td class="text-right">
                                                    ¥<span>{{$budget->quantity}}
                                        </span>.00
                                                </td>
                                                <td align="center">
                                                    {{$budget->comment}}
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-right">
                                                <div>
                                            <span translate="budget.total"
                                                  translate-values="{sum:{{$project->budget->sum('quantity')}}}"></span>
                                                </div>
                                                <div>
                                                    <span translate="budget.success" translate-values="{sum:{{$project->sponsor->sum('quantity')}}}"></span>
                                                </div>
                                                @if($project->sponsor->sum('quantity') < $project->sponsor->sum('quantity'))
                                                    <div translate="budget.inprogress" translate-values="{sum: {{$project->budget->sum('quantity') - $project->sponsor->sum('quantity')}}}"></div>
                                                @endif
                                            </td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif
                            <h4 class="px-4 pt-3">{{trans('project.LABELS.description')}}</h4>
                            <div class="px-4 py-3" style="word-wrap: break-word">
                                {!! $project->description !!}
                            </div>
                        </div>
                        <div class="col-3">
                            @if(sizeof($project->recruit)>0)
                                @include('templates.recruitments')
                            @endif
                            <div class="pt-3">
                                @foreach(Storage::disk('public')->files('pubs') as $file)
                                    <div><img src="/storage/{{$file}}" class="img-fluid"></div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-team" role="tabpanel" aria-labelledby="pills-team-tab" ng-class="{'active':selectedTab == 1}">
                    <div ng-if="!team ||team.length === 0">
                        @include('templates.empty')
                    </div>
                    <div class="d-flex my-5">
                        <div ng-repeat="member in team" class="card" style="width: 18rem;">
                            <img ng-if="!member.outsider" style="width: 8rem;margin: auto 5rem" class="card-img-top rounded-circle"
                                 src="/storage/avatars/<%member.user_id%>.jpg" />
                            <img ng-if="member.outsider" style="width: 8rem;margin: auto 5rem" class="card-img-top rounded-circle"
                                 src="/context/avatars/default.png" />
                            <div class="card-body text-center">
                                <a ng-if="!member.outsider" href="/profile/<%member.user_id%>" ng-bind="member.username"></a>
                                <a ng-if="member.outsider" href="<%member.link%>" ng-bind="member.username"></a>
                            </div>
                            <div class="card-footer bg-transparent text-center">
                                <span ng-repeat="role in member.occupation" class="badge badge-pill badge-primary mx-1"><span ng-bind="role.name"></span></span>
                            </div>
                        </div>
                    </div>

                    <div class="text-center" ng-show="pagination.show">
                        <ul uib-pagination ng-change="pageChanged()"
                            max-size="5"
                            rotate = true
                            items-per-page = 'pagination.perPage'
                            boundary-links="true"
                            total-items="pagination.total"
                            ng-model="pagination.currentPage"
                            class="pagination-sm"
                            previous-text="&lsaquo;"
                            next-text="&rsaquo;"
                            first-text="&laquo;"
                            last-text="&raquo;"></ul>
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-event" role="tabpanel" aria-labelledby="pills-event-tab" ng-class="{'active':selectedTab == 2}">
                    @include('templates.timeline')
                </div>
                <div class="tab-pane fade" id="pills-comment" role="tabpanel" aria-labelledby="pills--comment-tab" ng-class="{'active':selectedTab == 3}">
                    <div class="row">
                        <div class="col-9">
                            <div class="px-4 py-3" comment-content related-option="projects">
                                @include('templates.comments')
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="pubs">
                                @foreach(Storage::disk('public')->files('pubs') as $file)
                                    <div><img src="/storage/{{$file}}" class="img-fluid"></div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection
@section('script')
<script src="/js/directives/comment.js"></script>
<script src="/js/directives/budget.js"></script>
<script src="/js/directives/comment.js"></script>
<script src="/js/controllers/project/detail.js"></script>
@endsection