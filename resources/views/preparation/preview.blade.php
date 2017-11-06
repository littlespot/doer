@extends('layouts.zoomov')

@section('content')

<link rel="stylesheet" href="/css/project.css" />
<link rel="stylesheet" href="/css/project-detail.css" />
<link rel="stylesheet" href="/css/message.css" />
<style>
    .tab-menu-item{
        width: 160px;
    }
</style>
<div class="container" ng-init="loaded()">
    <div class="affix fixed-top fixed-right padding-top-md">
        <div class='btn btn-text-info btn-sq-sm flex-vertical'>
            <div class='fa fa-bullseye fa-2x'></div>
        </div>
        <div class='btn btn-text-success btn-sq-sm flex-vertical' disabled>
            <div class='fa fa-bookmark-o fa-2x'></div>
        </div>
        <div id="lovers_cnt" class='btn btn-text-danger btn-sq-sm flex-vertical' disabled>
            <div class='fa fa-heart-o fa-2x'></div>
        </div>
    </div>
    @include('templates.synopsis')
    <div class="tab-menu-bar">
        <div class="tab-menu-item active">
           {{trans('layout.LABELS.information')}}
        </div>
        <div class="tab-menu-item disabled">
            <span class="text-muted"> {{trans('project.CREATION.team')}}</span>
        </div>
        <div class="tab-menu-item disabled">
            <span class="text-muted"> {{trans('project.LABELS.events')}}</span>
        </div>
        <div class="tab-menu-item disabled">
            <span class="text-muted"> {{trans('layout.LABELS.comments')}}</span>
        </div>
        <div class="tab-menu-item true">
            <span class="text-muted">  {{trans('layout.LABELS.questions')}}</span>
        </div>
        <div class="tab-menu-share">

        </div>
    </div>
    <div style="position: relative">
        <div class="row content">
            <div class="col-xs-7">
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
    </div>
</div>
@endsection
@section('script')
<script src="/js/directives/budget.js"></script>
@endsection