@extends('layouts.zoomov')

@section('content')
    <div ng-controller="videosCtrl" class="content">
        <link href="/css/gallery.css" rel="stylesheet" />
        <div class="container">
            <div class="row">
            <div class="col-md-3 col-sm-4 col-xs-6">
                <div class="dropdown text-info" id="genre">
                    <div class="dropdown-toggle" type="button" id="dropdownGenre"
                         data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <span class="text-uppercase" ng-bind="chosenGenre"></span>
                        <span class="caret"></span>
                    </div>
                    <ul class="dropdown-menu">
                        <li ng-hide="filter.genre == 0">
                            <a class="btn" ng-click="setGenre(0)" translate="genre.Catalogue"></a>
                        </li>
                        @foreach($genres as $genre)
                            <li value="'{{$genre->id}}'" ng-hide="filter.genre == '{{$genre->id}}'">
                                <a class="btn" ng-click="setGenre('{{$genre->id}}','{{$genre->name}}')">{{$genre->name}}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-md-3 col-sm-4 col-xs-6">
                <div class="dropdown text-info" id="lang">
                    <div class="dropdown-toggle" type="button" id="dropdownLang"
                         data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <span class="text-uppercase" ng-bind="chosenLang"></span>
                        <span class="caret"></span>
                    </div>
                    <ul class="dropdown-menu">
                        <li ng-hide="filter.language == 0">
                            <a class="btn" ng-click="setLang(0)" translate="project.All"></a>
                        </li>
                        @foreach($languages as $language)
                            <li value="'{{$language->id}}'" ng-hide="filter.language == '{{$language->id}}'">
                                <a class="btn" ng-click="setLang('{{$language->id}}','{{$language->name}}')">{{$language->name}}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-md-3 col-sm-4 col-xs-6">
                <div class="dropdown text-info" id="duration">
                    <div class="dropdown-toggle" type="button" id="dropdownDuration"
                         data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <span class="text-uppercase" ng-bind="chosenDuration.label"></span>
                        <span class="caret"></span>
                    </div>
                    <ul class="dropdown-menu">
                        <li id="duration_0" ng-hide="chosenDuration.id == 0">
                            <a class="btn" ng-click="setDuration(0, 0, null)" translate="project.PLACES.duration"></a>
                        </li>
                        <li id="duration_1">
                            <a class="btn" ng-hide="chosenDuration.id == 1" ng-click="setDuration(1, null, 5)">
                                &lt;<span translate="project.Duration" translate-values="{min:5}"></span>
                            </a>
                        </li>
                        <li ng-repeat="d in durations" ng-hide="chosenDuration.id == d.id" id="duration_<%d.id%>">
                            <a class="btn" ng-click="setDuration(d.id, d.min, d.max)">
                                <%d.min%> - <span translate="project.Duration" translate-values="{min:<%d.max%>}"></span>
                            </a>
                        </li>
                        <li id="duration_7" ng-hide="chosenDuration.id == 7">
                            <a class="btn" ng-click="setDuration(7, 60, null)">
                                &gt; <span translate="project.Duration" translate-values="{min:60}"></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-3 col-sm-4 col-xs-6">
                <div class="input-group">
                    <select class="form-control" name="order"
                            ng-model="filter.order" required>
                        <option ng-repeat="o in orderoptions" ng-selected="filter.order == o.id"
                                value="<%o.id%>" translate="user.Project.<%o.label%>"></option>
                    </select>
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button">Go!</button>
                    </span>
                </div>
            </div>
        </div>
        </div>
        <div class="margin-top-sm">
            <div class="panel">
                <div class="container">
                    <h3>
                        <span translate="TITLES.best"></span>&nbsp;
                        <span class="link" ng-click="openCatalogue(0)">
                            <span ng-if="filters.id == '!!'" translate="project.All"></span>
                            <span name="genre_<%filters.id%>"  ng-if="filters.id != '!!'" ng-bind="filters.name"></span>
                        </span>
                    </h3>
                    <div ng-if="videos.length == 0">
                        @include('templates.empty')
                    </div>
                    <br/>
                    <div class="row">
                        <div ng-repeat="p in videos"
                             class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <%p%>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-bar" ng-show="(projects|filter:projectFilter).length>numberPerPage">
            <pagination
                    ng-model="currentPage"
                    total-items="(projects|filter:projectFilter).length"
                    items-per-page="numberPerPage"
                    max-size="5"
                    boundary-links="true"
                    previous-display = "false"
                    previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;">
            </pagination>
        </div>
        <div style="width: 100%;height: 100%">
            <img src="/images/footer.png" style="width: 100%;height: 100%">
        </div>
    </div>
@endsection
@section('script')
    <script src="/js/controllers/video.js"></script>
@endsection