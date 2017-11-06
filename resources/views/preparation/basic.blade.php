@extends('preparation.top')

@section('tabcontent')
<link href="/bower_components/crop-master/cropper.min.css" rel="stylesheet" />
<link href="/css/project.css" rel="stylesheet" />
<link href="/css/picture.css" rel="stylesheet" />
    <div class="content-margin" ng-controller="preparationCtrl" ng-init="init('{{$project}}')">
        @include('templates.preparation')
    </div>

    @endsection
@section('tabscript')
    <script src="/bower_components/crop-master/cropper.js"></script>
    <script src="/js/directives/picture.js"></script>
    <script src="/js/directives/location.js"></script>
    <script src="/js/controllers/admin/basic.js"></script>
@endsection