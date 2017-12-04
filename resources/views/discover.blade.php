@extends('layouts.zoomov')

@section('content')
    <div class="content padding-top-md" ng-controller="projectCtrl" ng-init="init('{{$filter}}')">
        <link href="/css/projects.css" rel="stylesheet" />
        <link href="/css/gallery.css" rel="stylesheet" />
        <div class="jumbotron container" >
            <div class="row" style="height: 100%">
                <div class="col-md-5 col-xs-12" style="height: 100%; display: table-cell" >
                    <div class="font-lg text-primary" translate="PANEL.header" style="position: relative; top:-10px"></div>
                    <div class="text-default font-l" style="position: relative; bottom: -15px;">
                        <table cellspacing="5px">
                            <tr>
                                <td class="text-right"><span translate="PANEL.div1"></span></td>
                                <td>
                                    <div class="dropdown text-info" id="order">
                                        <div class="dropdown-toggle" type="button" id="dropdownOrder"
                                             data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            <span translate="user.Project.<%filter.order%>"></span>
                                            <span class="caret"></span>
                                        </div>
                                        <ul class="dropdown-menu">
                                            <li ng-repeat="o in orderoptions" ng-if="o != filter.order">
                                                <a class="btn" ng-click="setOrder(o)" translate="user.Project.<%o%>"></a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right"><span translate="PANEL.div2"></span></td>
                                <td>
                                    <div class="dropdown text-success">
                                        <div class="dropdown-toggle" type="button" id="dropdownPerson"
                                             data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            <span id="person_name" translate="user.!!"></span>
                                            <span class="caret"></span>
                                        </div>
                                        <ul class="dropdown-menu">
                                            <li  ng-show="filter.person > 0" >
                                                <a id="person_0" ng-click="setPersonFilter(0)" class="btn text-important">
                                                    <span translate="user.!!"></span>
                                                </a>
                                            </li>
                                            @foreach($occupations as $person)
                                                <li ng-show="'{{$person->id}}' != filter.person">
                                                    <a class="btn" id="person_{{$person->id}}" ng-click="setPersonFilter('{{$person->id}}')">
                                                        <span translate="occupation.{{$person->name}}"></span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right"><span translate="PANEL.div3"></span></td>
                                <td>
                                    <div class="dropdown text-chocolate" id="location">
                                        <div class="dropdown-toggle" type="button" id="dropdownLocation" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            <span id="city_name" translate="location.!!"></span>
                                            <span class="caret"></span>
                                        </div>
                                        <ul class="dropdown-menu">
                                            <li ng-show="filter.city > 0" >
                                                <a class="btn text-important" id="city_0" ng-click="setLocationFilter(0)">
                                                    <span translate="location.!!"></span>
                                                </a>
                                            </li>
                                            @foreach($locations as $city)
                                                <li ng-show="'{{$city->id}}'!= filter.city">
                                                    <a class="btn" id="city_{{$city->id}}" ng-click="setLocationFilter('{{$city->id}}')">
                                                        {{$city->name}}&nbsp;({{$city->sortname}})
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col-md-7 col-xs-12" style="height: 100%; display: table-cell">
                    <div class="row">
                        <div class="col-sm-3 col-xs-6" >
                            &nbsp;
                        </div>
                        @for ($i = 0; $i < 3; $i++)
                        <div class="col-sm-3 col-xs-6">
                            <div class="category-link" ng-click="setGenre('{{$genres[$i]->id}}')"
                                 ng-class="{active:'{{$genres[$i]->id}}'==filter.genre}">
                                {{$genres[$i]->name}}</div>
                        </div>
                        @endfor
                    </div>
                    <div class="row margin-top-md">
                        <div class="col-sm-3 col-xs-6">
                            <div class="category-link" ng-class="{active:filter.genre == 0}" ng-click="setGenre(0)">
                                <span translate="genre.All"></span>
                            </div>
                        </div>
                        @for ($i = 3; $i < count($genres); $i++)
                            <div class="col-sm-3 col-xs-6">
                                <div class="category-link" ng-click="setGenre('{{$genres[$i]->id}}')"
                                 ng-class="{active:'{{$genres[$i]->id}}'==filter.genre}">
                                {{$genres[$i]->name}}
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
            <br/>
        </div>
        <div id="projects" style="display:block;">
            <div class="panel">
                <div class="container">
                    <div ng-if="projects.length == 0">
                        @include('templates.empty')
                    </div>
                    <br/>
                    <div ng-if="projects.length > 0" class="row">
                        <div ng-repeat="p in projects"
                             class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            @include('templates.project')
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
        </div>
    </div>
@endsection
@section('script')
    <script src="/js/controllers/project/list.js"></script>
@endsection