@extends('layouts.zoomov')
<link href="/css/festival.css" rel="stylesheet" />
@section('content')
    <div class="container" ng-controller="festivalCtrl" ng-init="loaded();">
        <div class="text-right py-5">
            <a class="badge" href="/festivals" >{{trans("layout.MENU.festival_list")}}</a>
            <span class="px-1">/</span>
            <b class="badge text-muted">{{trans("layout.MENU.festival_inscription")}}</b>
            <span class="px-1">/</span>
            <a class="badge" href="/archives">{{trans("layout.MENU.films")}}</a>
            <span class="px-1">/</span>
            <a class="badge" href="/myfestivals">{{trans("layout.MENU.favorites")}}</a>
        </div>
        <div class="d-flex nav-film">
            <ul class="col-6 nav nav-tabs nav-fill mr-auto" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="all-tab" ng-click="selectedYear = null" data-toggle="tab" href="#all" role="tab" aria-controls="all" aria-selected="true">
                        {{trans('festival.HEADERS.entries_all')}}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-danger" id="unpayed-tab" data-toggle="tab" href="#unpayed" role="tab" aria-controls="unpayed" aria-selected="false">
                        {{trans('festival.HEADERS.entries_inpayed')}}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="outdated-tab" data-toggle="tab" href="#outdated" role="tab" aria-controls="outdated" aria-selected="false">
                        {{trans('festival.HEADERS.entries_outdated')}}
                    </a>
                </li>
            </ul>
            <form class="form-inline my-2 my-lg-0">
                <a href="/archive/creation" class="btn btn-primary my-2 my-sm-0">{{trans('film.buttons.create')}}</a>
            </form>
        </div>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active py-3" id="all" role="tabpanel" aria-labelledby="all-tab">
                <div class="">
                    @foreach($entries->keys() as $key)
                        <a class="btn btn-sm btn-secondary" id="tab-{{$key}}" ng-class="{'active':selectedYear =={{$key}}}" ng-click="selectedYear = {{$key}}">{{$key}}</a>
                    @endforeach
                </div>
                @foreach($entries as $key=>$entry)
                    <div class="tabpanel-{{$key}}" ng-hide="selectedYear && selectedYear != {{$key}}">
                        @foreach($entry as $e)
                            <div class="card my-3">
                                @if($e->payed == 1)
                                    <div class="card-header text-white bg-primary d-flex justify-content-between">
                                        <div>
                                            {{trans('film.status.sent_at')}}:{{substr($e->sent_at, 0, 10)}}
                                        </div>
                                        <div>
                                            {{trans('festival.LABELS.order_id')}}:<span class="text-uppercase">{{substr($e->id, 1)}}</span>
                                        </div>
                                        <div>
                                            <span class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom"
                                                  data-html="true" title="{{trans('festival.TIPS.receipt_number')}}"></span>
                                            {{trans('festival.LABELS.receipt_id')}}:
                                            {{$e->receipt_number?:trans('film.status.wait_receipt')}}
                                        </div>
                                        <div class="text-right">
                                            @if(is_null($e->rewarded))
                                                {{trans('festival.LABELS.entry_status')}}: {{$e->accepted?trans('film.status.accepted'):($e->received_at?trans('film.status.received'):trans('film.status.entried'))}}
                                            @elseif($e->rewarded)
                                                {{trans('film.status.rewarded')}}: {{$e->honor}}
                                            @else
                                                {{trans('film.status.honored')}}: {{$e->honor}}
                                            @endif
                                        </div>
                                    </div>
                                @elseif($e->payed == 0)
                                    <div class="card-header text-white bg-danger d-flex justify-content-between">
                                        <div>
                                            {{trans('film.status.updated_at')}}:{{substr($e->updated_at, 0, 10)}}
                                        </div>
                                        <div>
                                            {{trans('festival.LABELS.order_id')}}:<span class="text-uppercase">{{substr($e->id, 1)}}</span>
                                        </div>
                                        <div class="text-right">
                                            {{trans('festival.LABELS.entry_status')}}:{{trans('film.status.unpayed')}}
                                        </div>
                                    </div>
                                @else
                                    <div class="card-header bg-light d-flex justify-content-between">
                                        <div>
                                            {{trans('film.status.updated_at')}}:{{substr($e->updated_at, 0, 10)}}
                                        </div>
                                        <div>
                                            {{trans('festival.LABELS.order_id')}}:<span class="text-uppercase">{{substr($e->id, 1)}}</span>
                                        </div>
                                        <div class="text-right">
                                            {{trans('festival.LABELS.entry_status')}}:{{trans('film.status.outdated')}}
                                        </div>
                                    </div>
                                @endif
                                <div class="media bg-white my-1 d-flex festival-banner">
                                    <img class="align-self-center mr-3" ng-src="/storage/festivals/{{$e->festival_id}}/{{$e->session}}/poster.jpg" />
                                    <div class="media-body d-flex flex-column justify-content-between px-3 py-2">
                                        <div>
                                            <div class="d-flex">
                                                <h3 class="mr-auto">
                                                    <a href="/festivals/{{$e->short}}">
                                                        {{$e->festival}}
                                                    </a>
                                                </h3>
                                                <div>
                                                    <span class="btn btn-white fa fa-share-alt"></span>
                                                    @if($e->payed != 1)
                                                    <span class="btn text-secondary fa fa-trash"></span>
                                                    @endif
                                                </div>
                                            </div>

                                            <h5>{{trans('festival.LABELS.entry_film')}}: {{$e->title}}</h5>
                                            <h6>{{trans('festival.LABELS.entry_unit')}}: {{$e->unit}}</h6>
                                        </div>
                                        <div class="text-muted small">
                                            {{trans('festival.LABELS.entry_fee')}}: {{$e->fee}} {{$e->currency}}
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-muted small mr-auto">
                                                <div>{{trans('festival.LABELS.entry_end')}}: {{$e->due_at}}</div>
                                                <div>{{trans('festival.LABELS.opening')}}: {{$e->open_at}} - {{$e->end_at}}</div>
                                            </div>
                                            @if(!$e->payed)
                                                <a class="btn btn-danger">{{trans('festival.BUTTONS.go_pay')}}</a>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>

                        @endforeach
                    </div>
                @endforeach
            </div>
            <div class="tab-pane fade py-3" id="unpayed" role="tabpanel" aria-labelledby="unpayed-tab">
                @if($outdated)
                    @foreach($entries as $key=>$entry)
                        @foreach($entry as $e)
                            @if(!$e->payed)
                                <div class="card my-3">
                                    <div class="card-header bg-light d-flex justify-content-between">
                                        <div>
                                            {{trans('film.status.updated_at')}}:{{substr($e->updated_at, 0, 10)}}
                                        </div>
                                        <div>
                                            {{trans('festival.LABELS.order_id')}}:<span class="text-uppercase">{{substr($e->id, 1)}}</span>
                                        </div>
                                        <div class="text-right">
                                            {{trans('festival.LABELS.entry_status')}}:{{trans('film.status.outdated')}}
                                        </div>
                                    </div>
                                    <div class="media bg-white my-1 d-flex festival-banner">
                                        <img class="align-self-center mr-3" ng-src="/storage/festivals/{{$e->festival_id}}/{{$e->session}}/poster.jpg" />
                                        <div class="media-body d-flex flex-column justify-content-between px-3 py-2">
                                            <div>
                                                <div class="d-flex">
                                                    <h3 class="mr-auto">
                                                        <a href="/festivals/{{$e->short}}">
                                                            {{$e->festival}}
                                                        </a>
                                                    </h3>
                                                    <div>
                                                        <span class="btn btn-white fa fa-share-alt"></span>
                                                        <span class="btn text-secondary fa fa-trash"></span>
                                                    </div>
                                                </div>

                                                <h5>{{trans('festival.LABELS.entry_film')}}: {{$e->title}}</h5>
                                                <h6>{{trans('festival.LABELS.entry_unit')}}: {{$e->unit}}</h6>
                                            </div>
                                            <div class="text-muted small">
                                                {{trans('festival.LABELS.entry_fee')}}: {{$e->fee}} {{$e->currency}}
                                            </div>
                                            <div class="d-flex">
                                                <div class="text-muted small mr-auto">
                                                    <div>{{trans('festival.LABELS.entry_end')}}: {{$e->due_at}}</div>
                                                    <div>{{trans('festival.LABELS.opening')}}: {{$e->open_at}} - {{$e->end_at}}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endforeach
                @else
                    @include('templates.empty')
                @endif
            </div>
            <div class="tab-pane fade py-3" id="outdated" role="tabpanel" aria-labelledby="outdated-tab">
                @if($inpayed)
                    @foreach($entries as $key=>$entry)
                        @foreach($entry as $e)
                            @if(!$e->payed)
                                <div class="card my-3">
                                    <div class="card-header text-white bg-danger d-flex justify-content-between">
                                        <div>
                                            {{trans('film.status.updated_at')}}:{{substr($e->updated_at, 0, 10)}}
                                        </div>
                                        <div>
                                            {{trans('festival.LABELS.order_id')}}:<span class="text-uppercase">{{substr($e->id, 1)}}</span>
                                        </div>
                                        <div class="text-right">
                                            {{trans('festival.LABELS.entry_status')}}:{{trans('film.status.unpayed')}}
                                        </div>
                                    </div>
                                    <div class="media bg-white my-1 d-flex festival-banner">
                                        <img class="align-self-center mr-3" ng-src="/storage/festivals/{{$e->festival_id}}/{{$e->session}}/poster.jpg" />
                                        <div class="media-body d-flex flex-column justify-content-between px-3 py-2">
                                            <div>
                                                <div class="d-flex">
                                                    <h3 class="mr-auto">
                                                        <a href="/festivals/{{$e->short}}">
                                                            {{$e->festival}}
                                                        </a>
                                                    </h3>
                                                    <div>
                                                        <span class="btn btn-white fa fa-share-alt"></span>
                                                        <span class="btn text-secondary fa fa-trash"></span>
                                                    </div>
                                                </div>

                                                <h5>{{trans('festival.LABELS.entry_film')}}: {{$e->title}}</h5>
                                                <h6>{{trans('festival.LABELS.entry_unit')}}: {{$e->unit}}</h6>
                                            </div>
                                            <div class="text-muted small">
                                                {{trans('festival.LABELS.entry_fee')}}: {{$e->fee}} {{$e->currency}}
                                            </div>
                                            <div class="d-flex">
                                                <div class="text-muted small mr-auto">
                                                    <div>{{trans('festival.LABELS.entry_end')}}: {{$e->due_at}}</div>
                                                    <div>{{trans('festival.LABELS.opening')}}: {{$e->open_at}} - {{$e->end_at}}</div>
                                                </div>
                                                <a class="btn btn-danger">{{trans('festival.BUTTONS.go_pay')}}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endforeach
                @else
                    @include('templates.empty')
                @endif
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="/js/controllers/festival/detail.js"></script>
@endsection