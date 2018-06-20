@extends('layouts.zoomov')

@section('content')
    <link href="/bower_components/crop-master/cropper.min.css" rel="stylesheet" />

<style>
    .profile{
        display: flex;
        justify-content: space-around;
    }

    .profile-image{
        position: relative;
        width: 150px;
        cursor: pointer;
    }

    .profile-image>div{
        position: absolute;
        top:0px;
        border-radius: 100px;
        border: 2px solid #999;
        width: 100%;
        height:150px;;
        text-align: center;
        padding-top: 60px;
        background: #999;
        color: #fff;
        opacity: 0.9;
        visibility: hidden;
    }

    .profile-image img{
        width: 100%;
        border-radius: 100px;
        border: 2px solid #999;
    }

    .poster{
        width: 100%;
        height: 100%;

    }

    .poster img{
        width: 150px;
        height:150px;
        border-radius: 75px;
        border: 1px #999 solid;
    }

    .img-preview {
        display:inline-block;
        margin-right: 1rem;
        margin-bottom: 1rem;
        overflow: hidden;
    }

    .picture-upload{
        display: none;
    }

    .img-container{
        display:none;
        text-align:center;
    }

    .preview-lg{
        width:150px;
        height:150px;
    }

    .preview-md{
        width:75px;
        height:75px;
    }

    .preview-sm{
        width:30px;
        height:30px;
    }

    #picture_wrapper{
        width:150px;
        height:150px;
    }

    .profile-image:hover>div{
        visibility: visible;
    }

</style>

<div class="container" ng-controller="personalCtrl" ng-init="init('{{$user->username}}', '{{$user->birthday}}', '{{$user->sex}}', '{{$user->city_id}}', '{{json_encode($occupations)}}')">
    <div class="jumbotron bg-transparent">

        <h3 class="text-center">{{trans('personal.HEADER.title')}}</h3>
        @if(!auth()->user()->active)
            <div class="mt-5 alert alert-danger">{{trans('personal.HEADER.film')}}</div>
        @else
            <div class="mt-5 alert alert-info">{{trans('personal.HEADER.alert')}}</div>
        @endif

    </div>
    @if(session('status'))
        <div class="alert alert-danger">
            {{trans('personal.ALERTS.'.((string)session('status')))}}
        </div>
    @endif

    <input type="hidden" value="{{$previous}}" name="previous" id="previous" />
    <div class="content my-2">
        @if($user->active == 1)
            <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                @foreach(trans('personal.TABS') as $key=>$tab)
                    <li class="nav-item">
                        <a class="nav-link {{$anchor == $key?'active':''}}" id="{{$key}}-tab"  data-toggle="tab" href="#{{$key}}" role="tab" aria-controls="{{$key}}">{{$tab}}</a>
                    </li>
                @endforeach
            </ul>
            <div class="tab-content">
                @foreach(trans('personal.TABS') as $key=>$tab)
                    <div class="tab-pane fade {{$anchor == $key?'active show':''}}  bg-white p-5" id="{{$key}}" role="tabpanel" aria-labelledby="{{$key}}-tab">
                        @include('templates.account.'.$key)
                    </div>
                @endforeach
            </div>
        @else
            @include('templates.account.basic')
        @endif
    </div>
</div>

    @endsection
@section('script')
    <script src="/bower_components/crop-master/cropper.js"></script>
    <script src="/js/directives/common.js"></script>
    <script src="/js/directives/picture.js"></script>

    <script src="/js/controllers/admin/personal.js"></script>
@endsection