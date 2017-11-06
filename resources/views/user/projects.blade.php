@extends('layouts.zoomov')

@section('content')
    <link href="{{ URL::asset('css/gallery.css')}}" rel="stylesheet" />
    <style>
        .preparation-poster{
            background-repeat: no-repeat;
            background-size:contain;
            background-position: center;
            width: 100%;
            display: inline-block;
            position: relative;
        }

        .preparation-poster:after {
            padding-top: 56.25%;
            /* 16:9 ratio */
            display: block;
            content: '';
        }

        .preparation-poster>div{
            position: absolute;
            top: 0;
            left: 0;
            background-repeat: no-repeat;
            background-size:cover;
            width: 100%;
        }

    </style>
    <div class="content padding-top-md">
        <div class="margin-top-lg">
            <div class=" flex-horizontal font-lg">
                @if(sizeof($preparations) > 0)
                    {!! trans('layout.HOME.count') !!}
                    {!! trans('layout.HOME.preparations', ['cnt'=>sizeof($preparations)]) !!}
                    @if(sizeof($projects) > 0)
                        ,{!! trans('layout.HOME.online', ['cnt'=>sizeof($projects)]) !!}
                    @endif
                @elseif(sizeof($projects) > 0)
                    {!! trans('layout.HOME.count') !!}
                    {!! trans('layout.HOME.online', ['cnt'=>sizeof($projects)]) !!}
                @else
                    {!! trans('layout.HOME.start') !!}
                @endif
            </div>
            <br>
            <div class="container font-md text-center text-chocolate" role="alert">
                {{trans('project.ALERTS.online')}}
            </div>
            <br/>
        </div>
        <div id="projects">
            <div class="panel">
                <div class="container">
                    @if(sizeof($preparations) == 0 && sizeof($projects) == 0)
                        @include('templates.empty')
                    @else
                        @if(sizeof($preparations) > 0)
                            <h4 class="text-uppercase">{{trans("project.LABELS.preparation")}}</h4>
                        @endif
                    <div class="row">
                        @foreach($preparations as $index=>$p)
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                <div class="project-item">
                                    <div class="project-content">
                                        <a href="/admin/preparations/{{$p->id}}" class="preparation-poster" style="background-image:url('/images/icons/waiting.svg')">
                                            <div>
                                                <img style="width:100%" src="/context/projects/{{$p->id}}.thumb.jpg">
                                            </div>
                                        </a>
                                        <div class="project-info-panel">
                                            <h6>
                                                <a class="text-info" href="/admin/preparations/{{$p->id}}">{{$p->title}}</a>
                                            </h6>
                                            <div class="description">
                                                {{$p->synopsis}}
                                            </div>
                                            <div class="flex-rows margin-top-sm">
                                                <div class="text-default small" style="display: inline-block">
                                                    {{trans("project.TAGS.update", ["date"=>date('Y-m-d', strtotime($p->updated_at))])}}
                                                </div>
                                                <div class="clip" class="{{$p->active ? 'text-success': 'text-chocolate'}}">{{$p->genre_name}}</div>
                                            </div>
                                            <div class="progress-table">
                                                <div class="text-chocolate small margin-bottom-xs">
                                                    <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                    {{trans("project.TAGS.duration", ["min"=>$p->duration])}}
                                                </div>
                                                <div class="flex-rows small">
                                            <span class="text-default">
                                                <span class="glyphicon"><?php echo file_get_contents(public_path("/images/icons/location.svg")); ?></span>
                                                {{$p->city_name}} ({{$p->sortname}})
                                            </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if(sizeof($projects) > 0)
                        <h4 class="text-uppercase">{{trans("project.LABELS.online")}}</h4>
                        @endif
                    <div class="row">
                        @foreach($projects as $index=>$p)
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <div class="project-item">
                                <div class="project-content">
                                    <a  href="/project/{{$p->id}}" class="project-poster" style="background-image:url('/context/projects/{{$p->id}}.thumb.jpg');">
                                    </a>
                                    <div class="project-info-panel">
                                        <h6>
                                           <a href="/project/{{$p->id}}">{{$p->title}}</a>
                                        </h6>
                                        <div class="description">
                                            {{$p->synopsis}}
                                        </div>
                                        <div class="flex-rows margin-top-sm">
                                            <div class="text-default small" style="display: inline-block">
                                                {{trans("project.TAGS.update", ["date"=>date('Y-m-d', strtotime($p->updated_at))])}}
                                            </div>
                                            <div class="clip" class="{{$p->active ? 'text-success': 'text-chocolate'}}">{{$p->genre_name}}</div>
                                        </div>
                                        <div class="progress-table">
                                            <div class="text-chocolate small margin-bottom-xs">
                                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                {{trans("project.TAGS.duration", ["min"=>$p->duration])}}
                                            </div>
                                            <div class="flex-rows small">
                                                <span class="text-default">
                                                    <span class="glyphicon"><?php echo file_get_contents(public_path("/images/icons/location.svg")); ?></span>
                                                    {{$p->city_name}} ({{$p->sortname}})
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
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