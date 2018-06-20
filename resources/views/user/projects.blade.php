@extends('layouts.zoomov')

@section('content')
    <link href="/css/projects.css" rel="stylesheet" />
    <link rel="stylesheet" href="/css/tag.css" type="text/css">
    <div class="jumbotron container bg-transparent mt-5">
        <h5 class="d-flex justify-content-center">
            @if(sizeof($preparations) > 0)
               <div>
                   {!! trans('layout.HOME.count') !!}
                   {!! trans('layout.HOME.preparations', ['cnt'=>sizeof($preparations)]) !!}
               </div>
                @if(sizeof($projects) > 0)
               <div class="pl-5">{!! trans('layout.HOME.online', ['cnt'=>sizeof($projects)]) !!}</div>
                @endif
            @elseif(sizeof($projects) > 0)
                <div>
                    {!! trans('layout.HOME.count') !!}
                    {!! trans('layout.HOME.online', ['cnt'=>sizeof($projects)]) !!}
                </div>
            @else
                <div>{!! trans('layout.HOME.start') !!}</div>
            @endif
        </h5>
        <hr>
        <div class="text-info text-center" role="alert">
            {{trans('project.ALERTS.online')}}
        </div>
    </div>
    <div id="projects" class="container-fluid bg-light">
        <div class="container py-5 projects">
            @if(sizeof($preparations) == 0 && sizeof($projects) == 0)
                @include('templates.empty')
            @else
                @if(sizeof($preparations) > 0)
                    <h4 class="text-uppercase text-center">{{trans("project.LABELS.preparation")}}</h4>
                    <div class="row">
                        @foreach($preparations as $index=>$p)
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                <div class="card my-3">
                                    <a href="/admin/preparations/{{$p->id}}">
                                        <img class="card-img-top" src="/storage/projects/{{$p->id}}.thumb.jpg" alt="{{$p->title}}">
                                    </a>
                                    <div class="px-3 py-2 font-weight-bold">
                                        <a class="text-info" href="/admin/preparations/{{$p->id}}">{{$p->title}}</a>
                                    </div>
                                    <div class="px-3 synopsis">
                                        <p class="small">{{$p->synopsis}}</p>
                                    </div>
                                    <div class="d-flex justify-content-between pt-2">
                                        <div class="text-default small pl-3"> {{trans("project.TAGS.update", ["date"=>date('Y-m-d', strtotime($p->updated_at))])}}</div>
                                        <div class="clip " style="margin-right: -0.4rem">{{$p->genre_name}}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-9 col-md-8 col-sm-6 small text-muted">
                                            <p class="px-3 pt-1">
                                                <a class="title" href="/profile/{{auth()->id()}}">{{auth()->user()->username}}</a>
                                            </p>
                                            <p class="px-3">
                                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                {{trans("project.LABELS.duration")}}: {{$p->duration}}m
                                            </p>
                                            <div class="d-flex justify-content-sm-between pb-3 px-3">
                                                <div class="text-muted">
                                                    <span class="fa fa-map-marker"></span>
                                                    <span>{{$p->city_name}}</span> (<span>{{$p->country}}</span>)
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-4 col-sm-6 align-self-end">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            <br/>
                <hr/>
                <br/>
                @if(sizeof($projects) > 0)
                    <h4 class="text-uppercase  text-center">{{trans("project.LABELS.online")}}</h4>
                    <div class="row">
                        @foreach($projects as $index=>$p)
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                <div class="card my-3">
                                    <a href="/project/{{$p->id}}">
                                        <img class="card-img-top" src="/storage/projects/{{$p->id}}.thumb.jpg" alt="{{$p->title}}">
                                    </a>
                                    <div class="px-3 py-2 font-weight-bold">
                                        <a class="text-info" href="/project/{{$p->id}}">{{$p->title}}</a>
                                    </div>
                                    <div class="px-3 synopsis">
                                        <p class="small">{{$p->synopsis}}</p>
                                    </div>
                                    <div class="d-flex justify-content-between pt-2">
                                        <div class="text-default small pl-3"> {{trans("project.TAGS.update", ["date"=>date('Y-m-d', strtotime($p->updated_at))])}}</div>
                                        <div class="clip " style="margin-right: -0.4rem">{{$p->genre_name}}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-9 col-md-8 col-sm-6 small text-muted">
                                            <p class="px-3 pt-1">
                                                <a class="title" href="/profile/{{auth()->id()}}">{{auth()->user()->username}}</a>
                                            </p>
                                            <p class="px-3">
                                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                {{trans("project.LABELS.duration")}}: {{$p->duration}}m
                                            </p>
                                            <div class="d-flex justify-content-sm-between pb-3 px-3">
                                                <div class="text-muted">
                                                    <span class="fa fa-map-marker"></span>
                                                    <span>{{$p->city_name}}</span> (<span>{{$p->country}}</span>)
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-4 col-sm-6 align-self-end">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
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
    <script>
        $(document).ready(function () {
            $("#crazyloader").hide();
        })
    </script>
@endsection