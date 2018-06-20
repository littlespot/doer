@extends('layouts.zoomov')

@section('content')
    <link rel="stylesheet" href="{{ URL::asset('css/profile.css') }}" type="text/css">
    <link rel="stylesheet" href="/css/tag.css" type="text/css">
    <link rel="stylesheet" href="/css/projects.css" type="text/css">
<div id="profile" class="content " ng-controller="profileCtrl as $ctrl" style="overflow-y: hidden" ng-init="init('{{$user->id}}', '{{$user->username}}', '{{$admin}}', '{{$anchor}}')">
    <div class="modal fade" id="unfollowConfirmModal" tabindex="-1" role="dialog" aria-labelledby="unfollowConfirmModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal-body">
                    <h3 translate="project.MESSAGES.confirmR" translate-values="{'user': selectedUser.username}"></h3>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" type="button" data-dismiss="modal" >
                        {{trans("project.BUTTONS.cancel")}}
                    </button>
                    <button class="btn btn-danger" type="button" ng-click="relation(selectedUser.id,2)">{{trans("project.BUTTONS.confirm")}}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bd-example-modal-lg" id="recomFilterModal" tabindex="-1" role="dialog" aria-labelledby="recomFilterModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered " role="document">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body text-center">
                    <h5 ng-repeat="c in filters" ng-click="setFilter(c)" class="p-3 btn-link text-white" ng-class="{'text-danger':filterChosen.id==c.id}"
                        data-dismiss="modal" aria-label="Close">
                        <span translate="user.Project.<%c.name%>"></span>
                    </h5>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bd-example-modal-lg" id="orderFilterModal" tabindex="-1" role="dialog" aria-labelledby="orderFilterModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered " role="document">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body text-center">
                    <h5 ng-repeat="c in orders" ng-click="setOrder(c)" class="p-3 btn-link text-white" ng-class="{'text-danger':orderChosen.id==c.id}"
                        data-dismiss="modal" aria-label="Close">
                        <span translate="user.Project.<%c.name%>"></span>
                    </h5>
                </div>
            </div>
        </div>
    </div>
    <div class="container py-5">
        <div class="jumbotron bg-white d-flex justify-content-between">
            <div id="profileRelation" class="align-self-top" style="min-width: 150px">
                <div class="text-center float-left">
                    <img class="rounded-circle img-fluid" style="border: 1px solid #999;width: 120px"  src="/storage/avatars/{{$user->id}}.jpg?{{time()}}" />

                    <div class="pt-3">
                        @include('templates.relation')
                    </div>
                    <div class="relation-circle pt-1">
                        <div class="btn btn-md text-uppercase px-3" ng-class = "{'btn-outline-info':selectedTab != 1, 'btn-info': selectedTab == 1}" ng-click="selectTab(1)">
                            {{trans("layout.LABELS.relations")}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-md-6 col-xs-12 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between">
                    <div clss="font-lg text-info">{{$user->username}}</div>
                    <div class="text-muted">
                        <span class="fa fa-map-marker"></span>
                        <span>{{$city->name}} ({{$city->country}})</span>
                    </div>
                </div>
                <div style="word-break: break-all">{{$user->presentation}}</div>
                <h5>
                    @foreach($occupations as $role)
                        <div class='badge badge-pill badge-info text-capitalize'>{{$role->name}}</div>
                    @endforeach
                </h5>
            </div>
            <div class="col-lg-2 col-md-3 col-xs-12 d-flex flex-column justify-content-between text-right">
                <div>
                    <div class="btn btn-md btn-block btn-outline-info text-uppercase"
                         ng-click="changeLocation('/person/reports/{{$user->id}}')" >
                        {{trans("layout.LABELS.reports")}}
                    </div>
                    <div class="btn btn-md btn-block mt-3 text-uppercase" ng-class="{'btn-outline-secondary': selectedTab != 2, 'btn-primary': selectedTab==2 }" ng-click="selectTab(2)">
                        {{trans("layout.LABELS.sns")}}
                    </div>
                </div>
                @if($admin)
                    <a class="btn btn-md btn-block btn-outline-primary text-uppercase" ng-click="changeLocation('/account')" >
                        {{trans("layout.LABELS.preparations")}}
                    </a>
                @else
                    <div class="btn btn-md btn-block btn-outline-danger text-uppercase"  data-toggle="modal" data-target="#invitationModal">
                        {{trans("layout.LABELS.invite")}}
                    </div>
                    @include('templates.invitation')
                @endif
            </div>
        </div>
    </div>
    <div class="panel container-fluid bg-light pb-5">
        <div id="projects" class="py-3" ng-show="selectedTab == 0">
            <h4 class="d-flex justify-content-center">
                <div class="text-uppercase">
                    <span>{{trans("layout.MENU.see")}}</span>
                    <span class="btn-link text-danger mr-1"  data-toggle="modal" data-target="#recomFilterModal" translate="user.Project.<%filterChosen.name%>"></span>
                    <span>
                    @if($admin)
                        {{trans("layout.LABELS.me")}}
                    @else
                        {{trans('layout.LABELS.'.strtolower($user->sex))}}
                    @endif
                    </span>
                </div>
                <div class="dropdown bg-light" >
                    <a class="text-danger dropdown-toggle px-2" id="cataloguesMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span id="catalogue_name">{{$catalogues['creator']}}</span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="cataloguesMenuButton">
                        @foreach($catalogues as $key=>$catalogue)
                            <div id="catalog_{{$key}}" class="dropdown-item" ng-click="chooseCatalogue('{{$key}}')">
                                <span>{{$catalogue}}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="text-uppercase">
                    <span >{{trans("layout.MENU.order")}}</span>
                    <span class="btn-link text-danger"  data-toggle="modal" data-target="#orderFilterModal">
                        <span translate="user.Project.<%orderChosen.name%>"></span>
                    </span>
                </div>
            </h4>
            <div class="container projects pb-3">
                <div  ng-if="(projects|filter:{active:filterChosen.id}).length == 0">
                    @include('templates.empty')
                </div>
                <div class="row">
                    <div ng-repeat="p in projects|filter:{active:filterChosen.id}|orderBy :orderChosen.id:true|limitTo:pagination.perPage:(pagination.currentPage - 1)*pagination.perPage" class="col-md-4 col-sm-6 col-xs-12 py-3">
                        <div class="card">
                            @include('templates.project')
                        </div>
                    </div>
                </div>
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
        <div class="container" ng-show="selectedTab == 1">
            <div class="pull-right">
                <div class="btn btn-lg text-danger" ng-click="selectTab(0)"><span class="fa fa-times fa-6x"></span></div>
            </div>
            <br>
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" ng-repeat="tab in relationTabs">
                    <a class="nav-link" id="<%tab%>-tab" data-toggle="tab" ng-class="{'bg-white':selectedTopTab ==  relationTabs[$index]}" ng-click="selectTopTab($index)">
                        <span translate="user.Views.<%tab%>"></span><sup class="text-danger" id="sup_<%tab%>"></sup>
                    </a>
                </li>
            </ul>
            <div ng-if="relations.length == 0">
                @include('templates.empty');
            </div>
            <div class="content bg-white">
                <div class="row py-5">
                    <div ng-repeat="i in relations" id="relation_content_<%i.id%>"
                         class="py-3 col-md-6 col-xs-12 col-sm-6">
                        <div class="d-flex">
                            <div class="text-center ml-3">
                                <img class="rounded-circle img-fluid" ng-class="{'friend':i.love}" style="width: 100px" src="/storage/avatars/<%i.id%>.jpg"/>
                                <div class="relation-circle my<%i.relation%>" id="relation_<%i.id%>">
                                    <div ng-click="changeRelation(i.id, i.username, '{{$admin ? 1 : 0}}')">
                                        <div class="ifollow">
                                            <span ng-bind="i.fans_cnt"></span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="followme">
                                            <span ng-bind="i.idols_cnt"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-5 d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between">
                                    <a class="text-pink" href="/profile/<%i.id%>" ng-bind="i.username"></a>
                                    <div class="text-muted small">
                                        <span class="fa fa-map-marker"></span>
                                        <span ng-bind="i.city_name">&nbsp;(<span ng-bind="i.sortname"></span>)</span>
                                    </div>
                                </div>
                                <div class="pb-3">
                                    <span ng-bind="i.presentation|limitTo:64"></span>
                                    <attr ng-if="i.presentation.length > 64" title="<%i.presentation%>">...</attr>
                                </div>
                            </div>
                        </div>
                        <hr/>
                    </div>
                </div>
            </div>
            <nav class="container pt-2" ng-show="rpagination.show">
                <ul class="pagination justify-content-center">
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                            <span class="sr-only">Previous</span>
                        </a>
                    </li>
                    <li class="page-item" ng-repeat="i in rpagination.pages" ng-class="{'active':i == rpagination.currentPage}" ng-click="relationPageChanged(i)">
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
        <div class="container content slide-up"  ng-show="selectedTab == 2">
            <div class="pull-right">
                <div class="btn btn-lg text-important" ng-click="selectTab(0)"><span class="fa fa-times fa-6x"></span></div>
            </div>
            <br>
            <div ng-if="!sns.length">
                @include('templates.empty')
            </div>
            <div ng-repeat="t in snsMenu" class="py-5" ng-if="(sns | filter:{type:t}:true).length > 0">
                <label translate="personal.SNS.<%t%>" ></label>
                <hr>
                <div class="d-flex">
                    <div ng-repeat="s in sns | filter:{type:t}:true" class="text-center px-4">
                        <a href="<%sns.url%>" target="_blank">
                            <img class="btn-sq-sm" ng-src="{{URL::asset('images/sns')}}/<%s.id%>.png" />
                        </a>
                        <div ng-bind="s.sns_name"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
    <script src="/js/modules/project.js"></script>
    <script src="/js/directives/message.js"></script>
    <script src="/js/controllers/user/profile.js"></script>
@endsection