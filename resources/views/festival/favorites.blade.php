
@extends('layouts.zoomov')
<link href="/css/festival.css" rel="stylesheet" />
@section('content')
    <div class="container" ng-controller="festivalCtrl" ng-init="init('{{json_encode($festivals)}}')">
        <div class="text-right py-5">
            <a class="badge" href="/festivals">{{trans("layout.MENU.festival_list")}}</a>
            <span class="px-1">/</span>
            <a class="badge" href="/entries">{{trans("layout.MENU.festival_inscription")}}</a>
            <span class="px-1">/</span>
            <a class="badge" href="/archives">{{trans("layout.MENU.films")}}</a>
            <span class="px-1">/</span>
            <b class="badge text-muted">{{trans("layout.MENU.favorites")}}</b>
        </div>
        <div class="d-flex nav-film">
            <ul class="col-6 nav nav-tabs nav-fill mr-auto" id="myTab" role="tablist">
                <li class="nav-item">
                    <div class="nav-link active" id="all-tab" data-toggle="tab" role="tab" aria-controls="all" aria-selected="true" ng-click="display()">
                        {{trans('festival.BUTTONS.all_festival')}}
                    </div>
                </li>
                <li class="nav-item">
                    <div class="nav-link" id="opening-tab" data-toggle="tab" role="tab" aria-controls="opening" aria-selected="false" ng-click="display('status', 3)">
                        {{trans('festival.BUTTONS.festival_opening')}}
                    </div>
                </li>
                <li class="nav-item">
                    <div class="nav-link" id="outdated-tab" data-toggle="tab" role="tab" aria-controls="outdated" aria-selected="false" ng-click="display('status', 0)">
                        {{trans('festival.BUTTONS.festival_closed')}}
                    </div>
                </li>
            </ul>
        </div>
        <div class="mt-4 d-flex justify-content-between">
            <div>
                <div class="btn" ng-click="display('script', -1)"  ng-class="{'btn-secondary':params.script > 0, 'btn-primary':params.script < 0}">
                    {{trans('festival.BUTTONS.all_festival')}}
                </div>
                <div class="btn" ng-click="display('script', 1)" ng-class="{'btn-primary':params.script>0, 'btn-secondary':params.script < 0}">
                    {{trans('festival.BUTTONS.script_festival')}}
                </div>
            </div>
            <div>
                <span class="btn btn-sm" ng-click="display('status', 1)"><img src="/images/icons/top-right-red.small.png" />{{trans('festival.BUTTONS.festival_closing')}}</span>
                <span class="btn btn-sm" ng-click="display('status', 2)"><img src="/images/icons/top-right-green.small.png" />{{trans('festival.BUTTONS.festival_open')}}</span>
                <span class="btn btn-sm" ng-click="display('status', 0)"><img src="/images/icons/top-right-grey.small.png" />{{trans('festival.BUTTONS.festival_closed')}}</span>
            </div>
        </div>
        <div class="tab-content" id="myTabContent">
            <div ng-if="!festivals.data">
                @include('templates.empty')
            </div>
            <div  ng-if="festivals.data" class="media bg-white my-4 d-flex festival-banner" ng-repeat="festival in festivals.data"  id="tab-<%festival.id%>">
                @include('templates.festival-banner')
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
    <script src="/js/controllers/festival/favorites.js"></script>
@endsection