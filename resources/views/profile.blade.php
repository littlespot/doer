@extends('layouts.zoomov')

@section('content')
    <link rel="stylesheet" href="{{ URL::asset('css/profile.css') }}" type="text/css">
    <link href="{{ URL::asset('css/projects.css')}}" rel="stylesheet" />
    <link href="{{ URL::asset('css/gallery.css')}}" rel="stylesheet" />
<div id="profile" class="content " ng-controller="profileCtrl as $ctrl" style="overflow-y: hidden" ng-init="init('{{$user->id}}', '{{$user->username}}', '{{$admin}}')">
        <script type="text/ng-template" id="confirm.html">
        <div class="modal-body" id="modal-body">
            <h3 translate="project.MESSAGES.confirmR" translate-values="{'user': selectedUser}"></h3>
        </div>
        <div class="modal-footer">
            <button class="btn btn-default" type="button" ng-click="$close(false)">
                {{trans("project.BUTTONS.cancel")}}
            </button>
            <button class="btn btn-danger" type="button" ng-click="$close(true)">{{trans("project.BUTTONS.confirm")}}</button>
        </div>
    </script>
    <div class="overlay ng-hide" ng-show="overlay" ng-click="overlay=false;">
        <div>
            <div class="category">
                <div ng-repeat="c in filters" ng-click="setFilter(c)">
                    <span class="link" ng-class="{active:filterChosen.id==c.id}" translate="user.Project.<%c.name%>"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="overlay ng-hide" ng-show="overlayOrder" ng-click="overlayOrder=false;">
        <div>
            <div class="category">
                <div ng-repeat="c in orders"  ng-click="setOrder(c)">
                    <span class="link" ng-class="{active:orderChosen.id==c.id}" translate="user.Project.<%c.name%>" ></span>
                </div>
            </div>
        </div>
    </div>
    <div class="jumbotron container">
        <div class="flex-rows">
            <div id="profileRelation" class="panel-content flex-top margin-top-xs">
                <img class="img-circle margin-bottom-sm" src="/context/avatars/{{$user->id}}.jpg?{{time()}}" />
                @include('templates.relation')
                <div class="relation-circle padding-top-xs">
                    <div class="btn-squash text-uppercase btn-text-info" ng-class = "{'btn-text-success': selectedTab == 1}" ng-click="selectTab(1)">
                        {{trans("layout.LABELS.relations")}}
                    </div>
                </div>
            </div>
            <div class="flex-cols margin-horizonal-lg">
                <div class="flex-rows">
                    <div class="font-l text-info">{{$user->username}}</div>
                    <div class="text-default small">
                        <span class="glyphicon"><?php echo file_get_contents(public_path("/images/icons/location.svg")); ?></span>
                        <span>{{$city->name}} ({{$city->sortname}})</span>
                    </div>
                </div>
                <div>{{$user->presentation}}</div>
                <div class="tags">
                    @foreach($occupations as $role)
                        <aside class='diamond text-center text-capitalize'>{{$role->name}}</aside>
                    @endforeach
                </div>
            </div>
            <div class="flex-cols">
                <div>
                    <div class="btn-squash-lg btn-text-info text-uppercase"
                         ng-click="changeLocation('/person/reports/{{$user->id}}')" >
                        {{trans("layout.LABELS.reports")}}
                    </div>
                    <!--
                    <div class="btn-squash-lg btn-text-info margin-top-sm text-uppercase" ng-click="changeLocation('/person/questions/{{$user->id}}')">
                        {{trans("layout.LABELS.questions")}}
                    </div>
                    -->
                    <div class="btn-squash-lg margin-top-sm text-uppercase" ng-class="{'btn-text-primary': selectedTab != 2, 'btn-text-danger': selectedTab==2 }" ng-click="selectTab(2)">
                        {{trans("layout.LABELS.sns")}}
                    </div>
                </div>
                @if($admin)
                    <a class="btn-squash-lg btn-text-important text-uppercase" ng-click="changeLocation('/account')" >
                        {{trans("layout.LABELS.preparations")}}
                    </a>
                @else
                    <div class="btn-squash-lg text-uppercase" ng-class="{'btn-text-important':selectedTab != 3, 'btn-default':selectedTab == 3}" ng-click="selectTab(3)">
                        {{trans("layout.LABELS.invite")}}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="panel margin-top-md">
        @if(!$admin)
            <div ng-if="selectedTab == 3" class="container">
                @include('templates.invitation')
            </div>
        @endif
        <div id="projects" class="container content slide-down" ng-show="selectedTab == 0">
            <div class="h3">
                <div>
                    <span class=" text-uppercase">{{trans("layout.MENU.see")}}</span>
                    <span class="link active" ng-click="openCatalogue()" translate="user.Project.<%filterChosen.name%>"></span>
                    @if($admin)
                        {{trans("layout.LABELS.me")}}
                    @else
                        <span translate="user.{{$user->sex}}"></span>
                    @endif
                </div>
                <div class="dropdown text-important" id="catalogues">
                    <div class="dropdown-toggle" type="button" id="dropdownPerson"
                         data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <span id="catalogue_name">{{$catalogues['creator']}}</span>
                        <span class="caret"></span>
                    </div>
                    <ul class="dropdown-menu">
                        @foreach($catalogues as $key=>$catalogue)
                            <li id="catalogue_{{$key}}" data-bind="catalogues">
                                <a class="btn"  ng-click="chooseCatalogue('{{$key}}','{{$catalogue}}')">
                                    <span>{{$catalogue}}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <span class="text-uppercase">{{trans("layout.MENU.order")}}</span>
                    <span class="link active" ng-click="openOrder()"
                          translate="user.Project.<%orderChosen.name%>"></span>
                </div>
            </div>

            <br>
            <div class="row" ng-if="(projects|filter:{active:filterChosen.id}).length == 0">
                @include('templates.empty')
            </div>
            <div class="row">
                <div ng-repeat="p in projects|filter:{active:filterChosen.id}|orderBy :orderChosen.id:true|limitTo:pagination.perPage:(pagination.currentPage - 1)*pagination.perPage"
                     class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    @include('templates.project')
                </div>
            </div>
            <div class="text-center" ng-show="pagination.show">
                <ul uib-pagination
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
        <div class="container content slide-up" ng-show="selectedTab == 1">
            <div class="pull-right">
                <div class="btn btn-lg text-important" ng-click="selectTab(0)"><span class="fa fa-times fa-6x"></span></div>
            </div>
            <br>
            <uib-tabset ng-if="selectedTab == 1">
                <uib-tab index="$index + 1" ng-repeat="tab in relationTabs" select="selectTopTab($index)">
                    <uib-tab-heading>
                        <span translate="user.Views.<%tab%>"></span>
                        <sup id="sup_<%tab%>"></sup>
                    </uib-tab-heading>
                    <div ng-if="relations.length == 0">
                        @include('templates.empty');
                    </div>
                    <div class="row">
                        <div ng-repeat="i in relations" id="relation_content_<%i.id%>"
                             class="col-md-6 col-xs-12 col-sm-6 flex-left" style="padding-bottom: 30px">
                            <div class="relation-image">
                                <img class="img-circle" ng-class="{'friend':i.love}" src="{{ URL::asset('/context/avatars')}}/<%i.id%>.jpg"/>
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
                            <div class="padding-left-lg flex-cols">
                                <div class="flex-rows">
                                    <a class="text-primary" href="/profile/<%i.id%>" ng-bind="i.username"></a>
                                    <div class="text-default small padding-right-md">
                                        <span class="glyphicon"><?php echo file_get_contents(public_path("/images/icons/location.svg")); ?></span>
                                        <span ng-bind="i.city_name">&nbsp;(<span ng-bind="i.sortname"></span>)</span>
                                    </div>
                                </div>
                                <div>
                                    <span ng-bind="i.presentation|limitTo:64"></span>
                                    <attr ng-if="i.presentation.length > 64" title="<%i.presentation%>">...</attr>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center" ng-show="rpagination.show">
                        <ul uib-pagination ng-change="relationPageChanged()"
                            max-size="5"
                            rotate = true
                            items-per-page = 'rpagination.perPage'
                            boundary-links="true"
                            total-items="rpagination.total"
                            ng-model="rpagination.currentPage"
                            class="pagination-sm"
                            previous-text="&lsaquo;"
                            next-text="&rsaquo;"
                            first-text="&laquo;"
                            last-text="&raquo;"></ul>
                    </div>
                </uib-tab>
            </uib-tabset>
        </div>
        <div class="container content slide-up"  ng-show="selectedTab == 2">
            <div class="pull-right">
                <div class="btn btn-lg text-important" ng-click="selectTab(0)"><span class="fa fa-times fa-6x"></span></div>
            </div>
            <br>
            <div ng-if="!sns.length">
                @include('templates.empty')
            </div>
            <div ng-repeat="t in snsMenu" class="margin-bottom-md" ng-if="(sns | filter:{type:t}:true).length > 0">
                <label translate="personal.SNS.<%t%>" ></label>
                <hr>
                <div class="flex-left">
                    <div ng-repeat="s in sns | filter:{type:t}:true" class="text-center padding-left-lg">
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