@extends('layouts.zoomov')

@section('content')
    <link rel="stylesheet" href="/css/tag.css" type="text/css">
    <link rel="stylesheet" href="/css/projects.css" type="text/css">
    <div ng-controller="homeCtrl" ng-init="init('{{$pictures}}', '{{$height}}')">
        <div class="modal fade bd-example-modal-lg" id="recomFilterModal" tabindex="-1" role="dialog" aria-labelledby="recomFilterModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content bg-transparent border-0">
                    <div class="modal-body text-center">
                        <h5 ng-click="setFilter(0, 0)" class="p-3" data-dismiss="modal" aria-label="Close">
                            <span class="text-white" ng-class="{'text-danger':filters[0].id == '!!'}" >{{trans("layout.MENU.all")}}</span>
                        </h5>
                        @foreach($categories as $category)
                            <h5 ng-click="setFilter(0, '{{$category->id}}', '{{$category->name}}')" class="p-3 btn-link text-white" ng-class="{'text-danger':filters[0].id == '{{$category->id}}'}"
                                data-dismiss="modal" aria-label="Close">
                                <span>{{$category->name}}</span>
                            </h5>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade bd-example-modal-lg" id="latestFilterModal" tabindex="-1" role="dialog" aria-labelledby="latestFilterModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content bg-transparent border-0">
                    <div class="modal-body text-center">
                        <h5 ng-click="setFilter(1, 0)" class="p-3" data-dismiss="modal" aria-label="Close">
                            <span class="text-white" ng-class="{'text-danger':filters[1].id == '!!'}" >{{trans("layout.MENU.all")}}</span>
                        </h5>
                        @foreach($categories as $category)
                            <h5 ng-click="setFilter(1, '{{$category->id}}', '{{$category->name}}')" class="p-3 btn-link text-white" ng-class="{'text-danger':filters[1].id == '{{$category->id}}'}"
                                data-dismiss="modal" aria-label="Close">
                                <span>{{$category->name}}</span>
                            </h5>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div id="zooCarousel" class="jumbotron bg-transparent" style="position: relative">
            <div id="homeBanner" class="carousel slide" data-ride="carousel" >
                <ol class="carousel-indicators">
                    <li ng-repeat="slide in slides" data-target="#homeBanner" data-slide-to="<%$index%>" ng-class="{'active':$index === 0}"></li>
                </ol>
                <div class="carousel-inner container">
                    <div class="d-flex flex-column justify-content-start" style="position: absolute; top:0; height: 100%; left:50px; width: 100%;z-index: 999">
                        <h3 style="line-height: 2.5rem" class="mt-3">
                            {!! trans("layout.HOME.header", ["username"=>auth()->user()->username, "userid"=>auth()->id()])!!}
                        </h3>
                        <h4 style="line-height: 2.5rem">
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
                        </h4>
                        <h4 style="line-height: 2.5rem">{!! trans("layout.HOME.slogan")!!}</h4>
                        <div class="mt-3">
                            <a href="/profile/{{auth()->id()}}" class="btn btn-lg btn-primary">
                                {{trans("layout.HOME.manage")}}
                            </a>
                            <br/>
                            <a href="/admin/preparations" class="mt-3 btn btn-lg btn-outline-primary">
                                {{trans("layout.HOME.create")}}
                            </a>
                        </div>
                    </div>
                    <div class="carousel-item" ng-repeat="slide in slides track by slide.id" ng-class="{'active':$index === 0}">
                        <img class="d-block" ng-src="/storage/<%slide.image%>">
                    </div>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
        <div id="projects" class="pt-3 container-fluid bg-light">
            <div class="container">
                <h3 class="text-center">
                    {{trans("layout.TITLES.best")}}
                    <span class="btn-link text-danger"  data-toggle="modal" data-target="#recomFilterModal">
                        <span ng-if="filters[0].id == '!!'">
                            {{trans("layout.MENU.all")}}
                        </span>
                        <span ng-if="filters[0].id != '!!'" name="genre_<%filters[0].id%>" ng-bind="filters[0].name"></span>
                    </span>
                </h3>
                <div ng-if="(recommendations| filter:{genre_id:filters[0].id}).length == 0">
                    @include('templates.empty')
                </div>
                <br/>
                <div class="row">
                    <div class="col-md-4 col-sm-6 col-xs-12" ng-repeat="p in recommendations| filter:{genre_id:filters[0].id} | limitTo:3" >
                        <div class="card">
                            @include('templates.project')
                        </div>
                    </div>
                </div>
                <hr/>
                <h3 class="text-center">
                    {{trans("layout.TITLES.latest")}}
                    <span class="btn-link text-danger" data-toggle="modal" data-target="#latestFilterModal">
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
                <div class="row">
                    <div class="col-md-4 col-sm-6 col-xs-12"  ng-repeat="p in (latest| filter:{genre_id:filters[1].id} | orderBy:'updated_at' : true | limitTo:3)">
                        <div class="card">
                            @include('templates.project')
                        </div>

                    </div>

                </div>
                <div class="text-center p-3">
                    <a class="btn btn-outline-primary" href="/discover">
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