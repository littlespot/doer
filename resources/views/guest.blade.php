@extends('layouts.zoomov')

@section('content')

<link rel="stylesheet" href="/css/project.css" />
<link rel="stylesheet" href="/css/project-detail.css" />
<div class="container">
    <div class="flex-rows">
        <div id="title" class="text-primary font-xl">{{$title}}</div>
        <div class="tag tag-default {{$active ? '' : 'text-chocolate'}}">
            <a href="/discover?genre={{$genre_id}}">{{$genre}} <span class="tail">{{$genres_cnt}}</span></a>
        </div>
    </div>
    <br/>
    <div class="flex-rows">
        <div class="poster-panel">
            <img src="/context/projects/{{$id}}.jpg" />
        </div>
        <div class="info-panel flex-cols">
            <div>
                <label class="text-primary" translate="project.PLACES.lang"></label> :
                @if(is_null($langs))
                    <span translate="NONE"></span>
                @else
                    {{$langs}}
                @endif
            </div>
            <div class="flex-rows">
                <span class="text-chocolate">
                    <i class="fa fa-clock-o" aria-hidden="true"></i>
                    {{trans('messages.duration', ["min"=>$duration])}}
                </span>
                <span class="text-default">
                    <?php echo file_get_contents(public_path("/images/icons/location.svg")); ?>
                    <a href="/discover?city={{$city_id}}">{{$city}}</a>
                </span>
            </div>

            <div class="synopsis">{{ $synopsis }}</div>
            <div>
                <a class="inner" target="_blank" href="/profile/{{$user_id}}">
                    <img class="img-circle img-responsive" src="/context/avatars/{{$user_id}}.small.jpg" />
                </a>
                <a id="user" class="inner" target="_blank" href="/profile/{{$user_id}}">
                    {{$username}}
                </a>
            </div>
            <div style="position: relative;display: table">
                <div class="text-default" style="display:table-cell; width: 100%; vertical-align: middle">
                    <div>{!! trans('messages.finish', ["date" => str_limit($finish_at, 10, '')]) !!}</div>
                    <div>
                        {!! trans('messages.member', ["cnt"=>$members_cnt]) !!}
                    </div>
                    @if(sizeof($recruit) === 0)
                        <div class="text-danger">{{trans('messages.recruited')}}</div>
                    @else
                        <div>{!! trans('messages.recruitment', ["cnt"=>sizeof($recruit)]) !!}</div>
                    @endif
                </div>
                <div class="progress-content">
                    <div class="progress-text text-important">
                        <div class='{{$daterest > 7 ? "text-primary" : ($daterest > 3 ? "text-warning" : "text-danger") }}' translate="project.TAGS.rest" translate-values="{days:{{$daterest}}}"></div>
                    </div>
                    <div class="progress-wrapper">
                        <div
                                round-progress
                                max="100"
                                current="{{(int)$daterest * 100/(int)$datediff}}"
                                color="#293a4f"
                                bgcolor="#e6e6e6"
                                radius="80"
                                semi="false"
                                rounded="false"
                                clockwise="true"
                                responsive="true"
                                stroke="9"
                                animation="easeOutCubic"
                                offset="inherit">
                        </div>
                    </div>
                </div>
            </div>
            <br/>
        </div>
    </div>
    <div class="row content">
        <div class="col-xs-7">
            @if(sizeof($scripts) > 0)
                <div class="table-responsive">
                    <h4>{{trans('project.LABELS.synopsis')}}</h4>
                    <table class="table">
                        <tbody>
                        @foreach($scripts as $script)
                            <tr>
                                <td width="50%"><a href="{{$script->link}}" target="_blank">{{$script->title}}</a></td>
                                <td>
                                    @foreach($script->authors as $author)
                                        @if(is_null($author->link))
                                            {{$author->name}}
                                        @else
                                            <a href="{{$author->link}}">{{$author->name}}</a>
                                        @endif
                                    @endforeach
                                </td>
                                <td width="100px" class="text-right">
                                    <span class="small"  class="pull-right">
                                         {{substr($script->created_at, 0, 11)}}
                                    </span>
                                <td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            @if(sizeof($budget) > 0)
                <div class="table-responsive">
                    <h4 class="text-info">{{trans('messages.budget.title')}}</h4>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>{{trans('project.LABELS.budget_type')}}</th>
                            <th width="20%" class="number">{{trans('messages.budget.sum')}}</th>
                            <th>{{trans('project.LABELS.budget_comment')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($budget as $b)
                            <tr>
                                <td align="center">
                                    {{$b->type->name}}
                                </td>
                                <td class="text-right">
                                    ¥&nbsp;{{$b->quantity}}.00
                                </td>
                                <td align="center">
                                    {{$b->comment}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3" class="text-right">
                                <div>
                                    {{trans('messages.budget.total', ["sum" => $budget->sum('quantity')])}}
                                </div>
                                <div>
                                    {{trans('messages.budget.success', ["sum" => $sponsor->sum('quantity')])}}
                                </div>
                                @if($sponsor->sum('quantity') < $sponsor->sum('quantity'))
                                    <div>
                                        {{trans('messages.budget.success', ["sum" => ($budget->sum('quantity') - $sponsor->sum('quantity'))])}}
                                    </div>
                                @endif
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
            <h4 class="text-chocolate">{{trans('project.LABELS.description')}}</h4>
            <div>
                {!! $description !!}
            </div>
        </div>
        <div class="col-xs-offset-1 col-xs-4">

            @foreach($recruit as $r)
                <div class="title">
                    {{$r->name}}
                     <span class="quantity">{{trans("project.TAGS.recruit", ["cnt"=>$r->quantity])}}</span>
                </div>
                <div>{!! $r->description !!}</div>
                <hr/>
            @endforeach
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
    <script>
        $(document).ready(function () {
            $("#crazyloader").hide();
        })
    </script>
@endsection