@extends('layouts.zoomov')
<style>
    .card-header .collapse{
        display: none;
    }
</style>
@section('content')
    <link href="/css/festival.css" rel="stylesheet" />
    <div class="container" ng-controller="festivalCtrl" ng-init="loaded()">
        <div class="py-5">
            @include('templates.festival-top')
        </div>
        <div class="alert alert-danger">{{trans('film.alert.entry_final')}}</div>
        <div class="p-5 bg-white">
            <div class="row">
                <label class="col-md-3 col-4">
                    {{trans('film.header.archive_name')}}
                </label>
                <div class="col-md-9 col-8">
                    {{$film->title}}
                </div>
            </div>
            <hr/>
            <div class="row py-2">
                <label class="col-md-3 col-4">
                    {{trans('film.header.unit_name')}}
                </label>
                <div class="col-md-9 col-8">
                    {{is_null($unit->name_locale) ?$unit->name : $unit->name_locale}}
                </div>
            </div>
            <hr/>
            <div class="row py-2">
                <label class="col-md-3 col-4">
                    {{trans('film.header.payed_fee')}}
                </label>
                <div class="col-md-9 col-8">
                    {{$unit->fee.' '.$unit->currency}}
                </div>
            </div>
            <hr/>
            <div class="row">
                <label class="col-md-3 col-4">
                    {{trans('film.label.contact')}}
                </label>
                <div class="col-md-9 col-8">
                    @if(app()->getLocale() == 'zh')
                        {{$contact->last_name.' '.$contact->first_name.($contact->prefix ? ' '.trans('personal.TITLES.'.$contact->prefix):'')}}<br/>
                    @else
                        {{($contact->prefix ? trans('personal.TITLES.'.$contact->prefix).' ':'').$contact->last_name.' '.$contact->first_name}}<br/>
                    @endif
                        <a href="mailTo::{{$contact->email}}">{{auth()->user()->email}}</a><br/>
                        @if($contact->fix){{$contact->fix}}@endif
                        @if($contact->mobile){{$contact->mobile}}@endif
                        <address>
                            {{$contact->country.' '.$contact->department.' '.$contact->city}}<br/>
                            {{$contact->address.','.$contact->postal}}<br/>
                        </address>
                </div>
            </div>
            <hr/>
            <form name="entryForm" action="/entry" method="POST" class="text-right">
                <input type="hidden" value="{{$unit->id}}" name="festival_unit_id">
                <input type="hidden" value="{{$film->id}}" name="film_id">
                <div class="checkbox-primary checkbox-inline  py-2">
                    <input type="checkbox" name="term" checked ng-model="terms" required>
                    <label><i class="text-danger pr-1">*</i><span ng-class="{'text-danger':!terms}">{{trans('film.alert.agree_terms')}}</span></label>
                </div>
                <br/>
                <div>
                    <a href="{{url()->previous()}}" class="btn btn-outline-danger">{{trans('layout.BUTTONS.previous')}}</a>
                    <button class="btn btn-primary" type="submit" ng-disabled="!terms">{{trans('layout.BUTTONS.submit')}}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('script')
    <script src="/js/controllers/festival/detail.js"></script>
@endsection