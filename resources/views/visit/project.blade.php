@extends('layouts.zoomov')

@section('content')

<link rel="stylesheet" href="/css/project.css" />
<link rel="stylesheet" href="/css/project-detail.css" />
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

<div class="container" ng-controller="projectDetailCtrl" ng-init='init("{{$project->id}}", "{{$project->user_id}}","{{$comments_cnt}}", "{{$tab}}")'>
    <div class="affix fixed-top fixed-right margin-top-lg padding-top-md">
        <div class='btn btn-text-info btn-sq-sm flex-vertical' disabled data-toggle="tooltip" title="{{trans('layout.TIP.views')}}">
            <div><img src="/images/icons/view.svg" class="img-responsive"></div>
            <div id="views_cnt">{{$project->admin ? $views_cnt : $views_cnt + 1 }}</div>
        </div>
        @if($project->admin)
            <div class='btn btn-text-success btn-sq-sm flex-vertical' disabled data-toggle="tooltip" title="{{trans('layout.TIP.followers')}}">
                <div class='fa fa-bookmark{{$followers_cnt ? "" : "-o"}} fa-2x'></div>
                <div>{{$followers_cnt}}</div>
            </div>
        @else
            <div id="followers_cnt" class='btn btn-text-success btn-sq-sm flex-vertical' ng-disabled='following' data-toggle="tooltip"
                 title="{{$myfollow ? trans('layout.TIP.unfollow'):trans('layout.TIP.follow')}}"
                 data-title-0="{{trans('layout.TIP.follow')}}" data-title-1="{{trans('layout.TIP.unfollow')}}"
                 ng-click="followProject()">
                <div class='fa fa-bookmark{{$myfollow ? "" : "-o"}} fa-2x'></div>
                <div>{{$followers_cnt}}</div>
            </div>
        @endif
        <div id="lovers_cnt" class='btn btn-text-danger btn-sq-sm flex-vertical'  ng-disabled='lovwing'
             title="{{$mylove ? trans('layout.TIP.unlove'):trans('layout.TIP.love')}}"
             data-title-0="{{trans('layout.TIP.love')}}" data-title-1="{{trans('layout.TIP.unlove')}}"
             ng-click="loveProject()">
            <div class='fa fa-heart{{$mylove ? "" : "-o"}} fa-2x'></div>
            <div>{{$lovers_cnt}}</div>
        </div>
    </div>
    @include('templates.synopsis')
    <div class="tab-menu-bar">
        <div class="tab-menu-item" ng-class="{'active':selectedTab == 0}" ng-click="selectTab(0)">
            {{trans('layout.LABELS.information')}}
        </div>
        <div class="tab-menu-item" ng-class="{'active':selectedTab == 1}" ng-click="selectTab(1)">
            {{trans('project.CREATION.team')}}
        </div>
        <div class="tab-menu-item" ng-class="{'active':selectedTab == 2}" ng-click="selectTab(2)">
            {{trans('project.LABELS.events')}}
        </div>
        @if($project->active)
            <div class="tab-menu-item" ng-class="{'active':selectedTab == 3}" ng-click="selectTab(3)">
                {{trans('layout.LABELS.comments')}}
                <sup id="sup_comments" ng-show="comments_cnt > 0"  ng-bind="comments_cnt"></sup>
            </div>
        <!--
            <div class="tab-menu-item" ng-class="{'active':selectedTab == 4}" ng-click="selectTab(4)">
                {{trans('layout.LABELS.questions')}}
                <sup id="sup_questions">{{$questions_cnt > 0 ? $questions_cnt.'' : ''}}</sup>
            </div>-->
            <div class="tab-menu-share">
                <span class="fa fa-share-alt"></span>
            </div>
        @else
            <div class="tab-menu-item disabled" >
                <span class="text-default">{{trans('layout.LABELS.comments')}}</span>
            </div>
        <!--
            <div class="tab-menu-item disabled">
                <span class="text-default">{{trans('layout.LABELS.questions')}}</span>
            </div>
            -->
            <div class="tab-menu-share disabled">
                <span class="fa fa-share-alt text-default"></span>
            </div>
        @endif
    </div>
    <div style="position: relative">
        <div class="loader-content" ng-show="loading"><div class="loader"></div></div>
        <div ng-switch="selectedTab">
            <div ng-switch-default class="row content">
                <div class="col-xs-7">
                    @if(sizeof($project->scripts) > 0)
                        <div class="table-responsive">
                            <h4>{{trans('project.LABELS.script')}}</h4>
                            <table class="table">
                                <tbody>
                                @foreach($project->scripts as $script)
                                    <tr>
                                        <td width="200px"><a href="{{url('http://'.$script->link)}}" title="{{$script->description}}" target="_blank">{{$script->title}}</a></td>
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
                        <h4 class="text-chocolate">{{trans('project.LABELS.description')}}</h4>
                        <div class="description">
                            {!! $project->description !!}
                        </div>
                </div>
                <div class="col-xs-offset-1 col-xs-4">
                    @if(sizeof($project->recruit)>0)
                        @include('templates.recruitments')
                    @endif
                    <div class="pubs">
                        <?php
                        $handle = opendir(public_path('/context/pubs'));
                        while (false !== ($file = readdir($handle))) {
                            list($filesname,$kzm)=explode(".",$file);
                            if(strcasecmp($kzm,"gif")==0 or strcasecmp($kzm, "jpg")==0 or strcasecmp($kzm, "png")==0)
                            {
                                if (!is_dir('./'.$file)) {
                                    echo "<div><img src=\"/context/pubs/$file\"></div>";
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div ng-switch-when="1" class="row content">
                <div ng-if="!team ||team.length === 0">
                    @include('templates.empty')
                </div>
                <div ng-repeat="member in team" class="col-lg-2 col-md-2 col-sm-3">
                    <div class="flex-top">
                        <div class="text-center">
                            <img ng-if="!member.outsider" class="img-circle img-responsive center"
                                 src="/context/avatars/<%member.user_id%>.thumb.jpg" />
                            <img ng-if="member.outsider" class="img-circle img-responsive center"
                                 src="/images/avatar.png" />
                        </div>
                        <h5 class="text-center" ng-if="!member.outsider">
                            <a href="/profile/<%member.user_id%>" ng-bind="member.username"></a>
                        </h5>
                        <h5 class="text-center" ng-if="member.outsider">
                            <a href="<%member.link%>" ng-bind="member.username"></a>
                        </h5>
                        <div class="flex-center" ng-repeat="role in member.occupation" >
                            <aside class='diamond text-center'><span ng-bind="role.name"></span></aside>
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
            <div ng-switch-when="2" class="content">
                @include('templates.timeline')
            </div>
            <div ng-switch-when="3" id="commentTab" class="row content">
                <div class="col-xs-7">
                    <div comment-content related-option="projects">
                        @include('templates.comments')
                    </div>
                </div>
                <div class="col-xs-offset-1 cols-xs-4">
                    <div class="pubs">
                        <?php
                        $handle = opendir(public_path('/context/pubs'));
                        while (false !== ($file = readdir($handle))) {
                            list($filesname,$kzm)=explode(".",$file);
                            if(strcasecmp($kzm,"gif")==0 or strcasecmp($kzm, "jpg")==0 or strcasecmp($kzm, "png")==0)
                            {
                                if (!is_dir('./'.$file)) {
                                    echo "<div><img src=\"/context/pubs/$file\"></div>";
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div ng-switch-when="4" id="questionTab" class="row content">
                <div class="col-xs-7">
                    <div class="text-right">
                        <a href="/ask/{{$project->id}}" target="_top" class="btn btn-info">
                            {{trans("project.BUTTONS.ask")}}
                        </a>
                    </div>
                    <div ng-if="!questions.length">
                        @include('templates.empty')
                    </div>
                    <div ng-if="questions.length">
                        @include('templates.questions')
                    </div>
                </div>
                <div class="col-xs-offset-1 col-xs-4">
                    <div class="pub">
                        <br/>
                        <?php
                        $handle = opendir(public_path('/context/pubs'));
                        while (false !== ($file = readdir($handle))) {
                            list($filesname,$kzm)=explode(".",$file);
                            if(strcasecmp($kzm,"gif")==0 or strcasecmp($kzm, "jpg")==0 or strcasecmp($kzm, "png")==0)
                            {
                                if (!is_dir('./'.$file)) {
                                    echo "<div><img src=\"/context/pubs/$file\"></div>";
                                }
                            }
                        }
                        ?>
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