
@extends('layouts.zoomov')
<link href="/css/festival.css" rel="stylesheet" />
<style>
    @if(!auth()->check())
    .festival-banner{
        height:250px;
    }
    .festival-banner >img{
        height: auto;
        width: 250px;
    }
    .festival-banner .media-body{
        height:250px;
    }
    @endif
</style>
@section('content')
    <div class="container" ng-controller="festivalCtrl" ng-init="init('{{json_encode($festivals)}}')">
        <div class="text-right py-4">
            <b class="badge text-muted" >{{trans("layout.MENU.festival_list")}}</b>
            <span class="px-1">/</span>
            <a class="badge" href="/entries">{{trans("layout.MENU.festival_inscription")}}</a>
            <span class="px-1">/</span>
            <a class="badge" href="/archives">{{trans("layout.MENU.films")}}</a>
            <span class="px-1">/</span>
            <a class="badge" href="/myfestivals">{{trans("layout.MENU.favorites")}}</a>
        </div>
        <div class="row">
            @if(auth()->check())
            <div class="col-md-3 col-sm-4 col-xs-12 pr-2 border-right border-secondary">
                <div class="card bg-secondary">
                    <div class="card-body">
                        <h5 class="card-title text-primary"><strong>{{trans('festival.HEADERS.my_contact')}}</strong></h5>

                        @if(auth()->user()->active < 1)
                            <div class="p-1"><a href="/personal" class="text-danger">{{trans('festival.BUTTONS.complete_contact')}}</a></div>
                        @elseif($information['invalid'])
                            <div class="p-1"><a href="/personal?anchor=contact" class="text-danger">{{trans('festival.BUTTONS.complete_contact')}}</a></div>
                        @else
                        <dl>
                            <dt class="text-muted">{{trans('festival.LABELS.name')}}</dt>
                            <dd class="pl-3">{{$information['contact']->last_name.' '.$information['contact']->first_name}}</dd>
                            <dt class="text-muted">{{trans('festival.LABELS.phone')}}</dt>
                            @if($information['contact']->fix)
                                <dd class="pl-3">{{$information['contact']->fix_code.'-'.$information['contact']->fix_number}}</dd>
                            @endif
                            @if($information['contact']->mobile)
                                <dd class="pl-3">{{$information['contact']->mobile_code.'-'.$information['contact']->mobile_number}}</dd>
                            @endif
                            <dt class="text-muted">{{trans('festival.LABELS.mail')}}</dt>
                            <dd class="pl-3">{{auth()->user()->email}}</dd>
                            <dt class="text-muted">{{trans('festival.LABELS.address')}}</dt>
                            @if($information['contact']->company)
                                <dd class="pl-3">{{$information['contact']->company}}</dd>
                            @endif
                            <dd class="pl-3">{{$information['contact']->address}}</dd>
                            <dd class="pl-3">{{$information['contact']->postal}} {{$information['contact']->city}}</dd>
                            <dd class="pl-3">{{$information['contact']->department}},  {{$information['contact']->country}}</dd>
                        </dl>
                            <a ng-href="/personal?anchor=contact">{{trans('festival.BUTTONS.change_contact')}}>></a>
                        @endif
                    </div>
                </div>
                <hr/>
                <div class="card bg-secondary">
                    <div class="card-body">
                        <h5 class="card-title text-primary"><strong>{{trans('festival.HEADERS.entry_info')}}</strong></h5>
                        <div class="p-1">{!!trans('festival.HEADERS.film_completed', ['cnt'=>$information['counters']['completed']]) !!}</div>
                        <div class="p-1">
                            {!! trans('festival.HEADERS.film_progress', ['cnt'=>$information['counters']['incompleted']]) !!}
                            @if($information['counters']['incompleted'] > 0)
                                <a class="text-danger" ng-href="/archives">{{trans('festival.BUTTONS.tocomplete')}}>></a>
                            @endif
                        </div>
                        <br/>
                        <div class="p-1">{!! trans('festival.HEADERS.entry_sending', ['cnt'=>$information['counters']['inscriptions']]) !!}</div>
                        <div class="p-1">{!! trans('festival.HEADERS.entry_received', ['cnt'=>$information['counters']['confirmed']]) !!}</div>
                        <div class="p-1">{!! trans('festival.HEADERS.film_honored', ['cnt'=>$information['counters']['honors']]) !!}</div>
                        <div class="p-1">{!! trans('festival.HEADERS.film_rewarded', ['cnt'=>$information['counters']['honors']]) !!}</div>
                    </div>
                </div>
                <hr/>
                <div class="card bg-secondary">
                    <div class="card-body">
                        <h5 class="card-title text-primary"><strong>{{trans('festival.HEADERS.entry_way')}}</strong></h5>
                        @foreach(trans('festival.HEADERS.entry_process') as $key=>$val)
                            <div class="p-1">{{$key}}. {{$val}}</div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            <div class="{{auth()->check() ? 'col-md-9 col-sm-8':''}} col-xs-12">
                <div id="festivalBanner" class="carousel slide pl-3" data-ride="carousel">
                    <ol class="carousel-indicators">
                        @foreach(\Illuminate\Support\Facades\Storage::disk('public')->files('pub/festivals/banners') as $key=>$file)
                            <li data-target="#festivalBanner" data-slide-to="{{$key}}" class="{{$loop->first?'active':''}}"></li>
                        @endforeach
                    </ol>
                    <div class="carousel-inner">
                        @foreach(\Illuminate\Support\Facades\Storage::disk('public')->files('pub/festivals/banners') as $file)
                        <div class="carousel-item  {{$loop->first?'active':''}}">
                            <img class="d-block w-100" src="/storage/pub/festivals/banners/{{basename($file)}}" alt="{{basename($file)}}">
                        </div>
                        @endforeach
                    </div>

                </div>
                <div class="mt-4 pl-3 d-flex justify-content-between">
                    <div>
                        <div class="btn" ng-click="display()"  ng-class="{'btn-secondary':show != 'all', 'btn-primary':show == 'all'}">
                            {{trans('festival.BUTTONS.all_festival')}}
                        </div>
                        <div class="btn" ng-click="display('script', 1)" ng-class="{'btn-primary':params.script>0, 'btn-secondary':params.script < 0}">
                            {{trans('festival.BUTTONS.script_festival')}}
                        </div>
                        @if(auth()->check())
                        <div class="btn" ng-click="favorite()" ng-class="{'btn-primary':show == 'favorite', 'btn-secondary':show != 'favorite'}">
                            {{trans('festival.BUTTONS.favorite_festival')}}
                        </div>
                        @endif
                    </div>
                    <div>
                        <span class="btn btn-sm" ng-click="display('status', 2)"><img src="/images/icons/top-right-red.small.png" />{{trans('festival.BUTTONS.festival_closing')}}</span>
                        <span class="btn btn-sm" ng-click="display('status', 1)"><img src="/images/icons/top-right-green.small.png" />{{trans('festival.BUTTONS.festival_open')}}</span>
                        <span class="btn btn-sm" ng-click="display('status', 0)"><img src="/images/icons/top-right-grey.small.png" />{{trans('festival.BUTTONS.festival_closed')}}</span>
                    </div>
                </div>
                <div ng-if="!festivals.data">
                    @include('templates.empty')
                </div>
                <div ng-if="festivals.data" class="media bg-white my-4 festival-banner" ng-repeat="festival in festivals.data"  id="tab-<%festival.id%>">
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
    </div>
@endsection
@section('script')
    <script src="/js/controllers/festival/list.js"></script>
@endsection