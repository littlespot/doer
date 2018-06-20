@extends('layouts.zoomov')

@section('content')
    <link href="/css/projects.css" rel="stylesheet" />
    <link rel="stylesheet" href="/css/tag.css" type="text/css">
    <div class="pt-3" ng-controller="projectCtrl" ng-init="init('{{$filter}}')">
        <div class="jumbotron container bg-white" >
            <div class="row" style="height: 100%">
                <div class="col-md-5 col-xs-12" style="height: 100%; display: table-cell" >
                    <h5 class="font-lg text-primary" translate="PANEL.header" style="position: relative; top:-10px"></h5>
                    <div class="text-default font-l" style="position: relative; bottom: -15px;">
                        <table cellspacing="5px">
                            <tr>
                                <td class="text-right"><span translate="PANEL.div1"></span></td>
                                <td>
                                    <div class="btn-group dropright" id="order">
                                        <button type="button" class="btn dropdown-toggle bg-transparent" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"  id="orderDropdown">
                                            <span class="text-info" translate="user.Project.<%filter.order%>"></span>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="orderDropdown">
                                            <a class="dropdown-item" ng-repeat="o in orderoptions" ng-if="o != filter.order" ng-click="setOrder(o)" translate="user.Project.<%o%>"></a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right"><span translate="PANEL.div2"></span></td>
                                <td>
                                    <div class="btn-group dropright">
                                        <button type="button" class="btn dropdown-toggle bg-transparent" id="dropdownPerson"
                                             data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            <span class="text-danger" translate="user.!!" id="person_name"></span>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownPerson">
                                            <a class="dropdown-item" ng-show="filter.person > 0" id="person_0" ng-click="setPersonFilter(0)" translate="user.!!"></a>
                                        @foreach($occupations as $person)
                                            <a class="dropdown-item" id="person_{{$person->id}}" ng-show="'{{$person->id}}' != filter.person" ng-click="setPersonFilter('{{$person->id}}')"
                                               translate="occupation.{{$person->name}}"></a>
                                        @endforeach
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right"><span translate="PANEL.div3"></span></td>
                                <td>
                                    <div class="btn-group dropright" id="location">
                                        <button class="btn dropdown-toggle bg-transparent" type="button" id="dropdownLocation" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            <span class="text-success" id="city_name" translate="location.!!"></span>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownLocation">
                                            <a class="dropdown-item" ng-show="filter.city > 0" id="city_0" ng-click="setLocationFilter(0)" translate="location.!!"></a>
                                            @foreach($locations as $city)
                                                <a class="dropdown-item" id="city_{{$city->id}}" ng-show="'{{$city->id}}'!= filter.city" ng-click="setLocationFilter('{{$city->id}}')">
                                                    {{$city->name}}&nbsp;({{$city->country}})
                                                </a>
                                            @endforeach
                                        </div>
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
                    <div class="row pt-3">
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
        <div id="projects" class="container-fluid bg-light  py-3">
            <div class="container projects">
                <div ng-if="projects.length == 0">
                    @include('templates.empty')
                </div>
                <br/>
                <div class="row">
                    <div ng-repeat="p in projects" class="col-md-4 col-sm-6 col-xs-12 py-3">
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
    </div>
@endsection
@section('script')
    @if(auth()->check())
    <script src="/js/controllers/project/list.js"></script>
    @else
        <script src="/js/controllers/project/visit.js"></script>
    @endif
@endsection