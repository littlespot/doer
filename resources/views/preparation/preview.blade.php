@extends('layouts.zoomov')

@section('content')

<link rel="stylesheet" href="/css/tag.css" />
<div id="project-status" class="mt-5 pt-5 fixed-right btn-group-vertical">
    <div class='btn text-primary d-flex flex-column border border-secondary'
         disabled data-toggle="tooltip" title="{{trans('layout.TIP.views')}}">
        <div class="fa fa-eye"></div>
        <div></div>
    </div>
    <div class='btn btn-outline-success d-flex flex-column border-secondary' disabled data-toggle="tooltip" title="{{trans('layout.TIP.followers')}}">
        <div class='fa fa-bookmark-o'></div>
        <div></div>
    </div>
    <div id="lovers_cnt" class='btn btn-outline-danger d-flex flex-column border-secondary' disabled data-toggle="tooltip" >
        <div class='fa fa-heart-o'></div>
        <div></div>
    </div>
</div>
<div class="container" ng-init="loaded()">
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
        <li class="nav-item border">
            <a class="nav-link disabled" >{{trans('layout.LABELS.comments')}}</a>
        </li>
        <li class="nav-item border">
            <a class="nav-link disabled" ><span class="fa fa-share-alt"></span></a>
        </li>
    </ul>
    <div class="my-5">
        <div class="row">
            <div class="col-9">
                @if(sizeof($project->scripts) > 0)
                    <div class="table-responsive">
                        <h4>{{trans("project.LABELS.script")}}</h4>
                        <table class="table">
                            <tbody>
                            @foreach($project->scripts as $script)
                                <tr>
                                    <td width="200px"><a href="{{$script->link}}">{{$script->description}}</a></td>
                                    <td>
                                        @foreach($script->authors as $key=>$author)
                                            @if(!is_null($author->user_id))
                                                <a href="/profile/{{$author->user_id}}" target="_blank">{{$author->name}}</a>
                                            @elseif(is_null($author->link))
                                                {{$author->name}}
                                            @else
                                                <a href="{{$author->link}}" target="_blank">{{$author->name}}</a>
                                            @endif
                                            @if($key < sizeof($script->authors)),@endif
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
                        <h4 class="text-info">{{trans("project.LABELS.budget")}}</h4>
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
                <h4 class="text-chocolate">{{trans("project.LABELS.description")}}</h4>
                <div class="description">
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
</div>
@endsection
@section('script')
<script src="/js/directives/budget.js"></script>
@endsection