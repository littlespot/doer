@extends('layouts.zoomov')

@section('content')
    <div ng-controller="homeCtrl" class="content" ng-init="init('{{$pictures}}', '{{$ratio}}')">
        <link href="/css/home.css" rel="stylesheet" />
        <link href="/css/gallery.css" rel="stylesheet" />
        <div class="overlay ng-hide" ng-show="overlay" ng-click="overlay=false;">
            <div>
                <div class="category">
                    <div ng-click="setFilter(0)"  ng-class="{active:filterChosen.id == '!!'}" >
                        {{trans("layout.MENU.all")}}
                    </div>
                    @foreach($categories as $category)
                        <div ng-class="{active:filterChosen.id == '{{$category->id}}'}" ng-click="setFilter('{{$category->id}}', '{{$category->name}}')">
                            <span>{{$category->name}}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div style="display: block;margin-top: 40px;position: relative">
            <div class="zooCarousel"  uib-carousel active="true" interval="myInterval" no-wrap="false">
                <div uib-slide ng-repeat="slide in slides track by slide.id" index="$index">
                    <img ng-src="/context/carousel/<%slide.image%>" style="height:<%slide.height%>px">
                </div>
            </div>
            <div class="carousel-header">
                <div class="container ">
                    <div class="header-slogan">
                        <div>
                            {!! trans("layout.HOME.header", ["username"=>Auth::user()->username, "userid"=>Auth::id()])!!}
                        </div>
                        <div>
                            @if(is_null($counts) || sizeof($counts) == 0)
                                <span class="text-info">{!! trans("layout.HOME.start")!!}</span>
                            @else
                                {!! trans("layout.HOME.count")!!}
                                @if($counts[0]->active > 0)
                                    {!! trans("layout.HOME.projects", ["cnt"=>$counts[0]->cnt])!!}
                                @elseif($counts[0]->active == 0)
                                    {!! trans("layout.HOME.online", ["cnt"=>$counts[0]->cnt])!!}
                                    @if(sizeof($counts) > 1)
                                        , {!! trans("layout.HOME.projects", ["cnt"=>$counts[1]->cnt])!!}
                                    @endif
                                @else
                                    {!! trans("layout.HOME.preparations", ["cnt"=>$counts[0]->cnt])!!}
                                    @if(sizeof($counts) > 2)
                                        ,{!! trans("layout.HOME.online", ["cnt"=>$counts[1]->cnt])!!}
                                        ,{!! trans("layout.HOME.projects", ["cnt"=>$counts[2]->cnt])!!}
                                    @elseif(sizeof($counts) > 1)
                                        @if($counts[0]->active > 0)
                                            ,{!! trans("layout.HOME.projects", ["cnt"=>$counts[1]->cnt])!!}
                                        @else
                                            ,{!! trans("layout.HOME.online", ["cnt"=>$counts[1]->cnt])!!}
                                        @endif
                                    @endif
                                @endif
                            @endif
                        </div>
                        <div>{!! trans("layout.HOME.slogan")!!}</div>
                    </div>
                    <br/>
                    <div class="form-group">
                        <a href="/profile/{{Auth::id()}}" class="btn btn-primary">
                            {{trans("layout.HOME.manage")}}
                        </a>
                    </div>
                    <div class="form-group">
                        <a href="/preparations" class="btn btn-default">
                            {{trans("layout.HOME.create")}}
                        </a>
                    </div>
                </div>

            </div>
        </div>
        <div id="projects" style="display:block;margin-top:10px">
            <div class="panel">
                <div class="container">
                    <h3>
                        {{trans("layout.TITLES.best")}}
                        <span class="link" ng-click="openCatalogue(0)">
                            <span ng-if="filters[0].id == '!!'">
                                {{trans("layout.MENU.all")}}
                            </span>
                            <span name="genre_<%filters[0].id%>"  ng-if="filters[0].id != '!!'" ng-bind="filters[0].name"></span>
                        </span>
                    </h3>
                    <div ng-if="(recommendations| filter:{genre_id:filters[0].id}).length == 0">
                        @include('templates.empty')
                    </div>
                    <br/>
                    <div class="row">
                        <div ng-repeat="p in recommendations| filter:{genre_id:filters[0].id} |limitTo:3"
                             class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            @include('templates.project')
                        </div>
                    </div>
                    <br/>
                    <h3 >
                        {{trans("layout.TITLES.latest")}}
                        <span class="link" ng-click="openCatalogue(1)">
                            <span ng-if="filters[1].id == '!!'">
                                 {{trans("layout.MENU.all")}}
                            </span>
                            <span name="genre_<%filters[1].id%>"  ng-if="filters[1].id != '!!'" ng-bind="filters[1].name"></span>
                        </span>
                    </h3>
                    <div ng-if="(latest| filter:{genre_id:filters[1].id}).length == 0">
                        @include('templates.empty')
                    </div>
                    <br/>
                    <div class="row" id="last_projects">
                        <div ng-repeat= "p in latest| filter:{genre_id:filters[1].id} | orderBy:'updated_at' : true |limitTo:3"
                             class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            @include('templates.project')
                        </div>
                    </div>
                </div>
                <div class="row" style="text-align: center">
                    <a class="btn btn-default" href="/discover">
                        {{trans("layout.HOME.all")}}
                    </a>
                </div>
            </div>
        </div>
        <div style="width: 100%;height: 100%">
            <img src="/images/footer.png" style="width: 100%;height: 100%">
        </div>
    </div>
@endsection
@section('script')
    <script src="/js/controllers/home.js"></script>
@endsection