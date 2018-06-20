<!DOCTYPE html>
<html lang="en"  ng-app="zooApp" >
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'ZOOMOV') }}</title>

    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="/favicon.ico">

    <link rel="stylesheet" href="/css/animate.min.css">
    <link rel="stylesheet" href="/bower_components/bootstrap/css/bootstrap.css" type="text/css">
    <link rel="stylesheet" href="/bower_components/font-awesome/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="/css/tag.css">
    <script src="/bower_components/jquery/jquery-3.3.1.min.js"></script>
    <script src="/bower_components/bootstrap/js/bootstrap.min.js"></script>

    <script src="/js/jquery.singlePageNav.min.js"></script>
    <script src="/js/typed.js"></script>
    <script src="/js/wow.min.js"></script>
    <script src="/js/custom.js"></script>

</head>
<body id="top" ng-controller="welcomeCtrl" ng-init="loaded()">
@include('templates.crazy-loader')
<div id="topNav" class="container-fluid  text-center header">
    <div>
        <img src="/images/logo.png" class="img-fluid" alt="">
    </div>

    <h4 class="text-secondary font-weight-bold py-3">{{trans('layout.SLOGANS.welcome')}}</h4>
</div>



<!-- start home -->
<section id="home">
    <style id=transit></style>
    <style>

        .transitLeft{
            animation:elevatorLeft 90s linear;
            -webkit-animation:elevatorLeft 90s linear;
        }

        .transitRight{
            animation:elevatorRight 80s linear;
            -webkit-animation:elevatorRight 80s linear;
        }
        .vader{
            visibility: hidden;
            position: absolute;
            bottom: 0;
            background-repeat: no-repeat;
            background-position: bottom left;
            background-size: contain;
            z-index: -1;
        }

        .card-body{
            padding: 0;
            line-height: 1.8rem;
        }
        .card-footer{
            padding: 0;
        }
    </style>
    <div id="layers" style="display: none">
        <div class="backlayer" style="background-image: url(/images/layers/layer7.svg);">
            &nbsp;
        </div>
        <div class="vader" id="layer6" style="background-image: url(/images/layers/layer6.svg);">
            &nbsp;&nbsp;
        </div>
        <div class="backlayer" style="background-image: url(/images/layers/layer5.svg);">
            &nbsp;
        </div>
        <div class="backlayer" style="background-image: url(/images/layers/layer4.svg);">
            &nbsp;
        </div>
        <div class="vader" id="layer3" style="background-image: url(/images/layers/layer3.svg);">
            &nbsp;
        </div>
        <div class="backlayer" style="background-image: url(/images/layers/layer2.svg);">
            &nbsp;
        </div>
        <div class="container" >
            <div class="row">
                <div class="col-lg-3"></div>
                <div class="col-lg-6 py-5 d-flex flex-column justify-content-between" id="slogan">
                    <div class="pt-5">
                        <h3 class="text-center">我来这里是为了</h3>
                        <div class="d-flex justify-content-between pt-5">
                            <a href="/discover" class="enter-button">{{trans('layout.BUTTONS.discover_projects')}}</a>
                            <a href="/festival" class="enter-button">{{trans('layout.BUTTONS.discover_festivals')}}</a>
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="#festivals">
                            <span class="fa fa-chevron-circle-down fa-2x"></span>
                            <span class="text-muted" style="font-size: 1.8rem">了解更多</span>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3"></div>
            </div>

        </div>
    </div>
    <div >
        <img src="/images/layers/forest.svg" class="facelayer">
    </div>
</section>
<!-- end home -->


<!-- start team -->
<section>

    <div class="content bg-green" id="festivals">
        <div class="container d-flex flex-column justify-content-between py-5" >
            @include('templates.zh.welcome')
            <div class="py-5 d-flex justify-content-between">
                @foreach(\Illuminate\Support\Facades\Storage::disk('public')->files('pub/festivals/logos') as $file)
                    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                        <img class="img-fluid rounded-circle" src="/storage/pub/festivals/logos/{{basename($file)}}" alt="{{basename($file)}}">
                    </div>
                @endforeach
            </div>
            <div class="text-center py-5" style="z-index: 999">
                <a href="/festival" class="btn btn-primary btn-lg">{{trans('layout.BUTTONS.festivals')}}</a>
            </div>
        </div>
    </div>
</section>
<section class="bg-green" id="projects" style="position:relative;">

    <div class="container">
        <div class="card-deck">
            @foreach($projects as $project)
                <div class="card">
                    <a href="/project/{{$project->id}}">
                        <img class="card-img-top" src="/storage/projects/{{$project->id}}.thumb.jpg" alt="{{$project->title}}">
                    </a>
                    <div class="card-body d-flex flex-column">
                        <div class="px-3 py-2 font-weight-bold">
                            <a class="text-info" href="/project/{{$project->id}}">{{$project->title}}</a>
                        </div>
                        <div class="px-3 synopsis mb-auto">
                            <p class="small">{{$project->synopsis}}</p>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="text-default small pl-3">{{trans("project.LABELS.update")}}<span>{{substr($project->updated_at,0,10)}}</span></div>
                            <div class="clip" style="margin-right: -0.4rem"><span>{{$project->genre_name}}</span></div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-9 col-md-8 col-sm-6 small text-muted">
                                <p class="px-3 pt-1">
                                    <a href="/profile/{{$project->user_id}}">{{$project->username}}</a>
                                </p>
                                <p class="px-3">
                                    <i class="fa fa-clock-o" aria-hidden="true"></i>
                                    {{trans("project.LABELS.duration")}}: <span>{{$project->duration}}</span>m
                                </p>
                                <div class="d-flex justify-content-sm-between pb-3 px-3">
                                    <div class="text-muted">
                                        <span class="fa fa-map-marker"></span>
                                        <span>{{$project->city_name}}</span> (<span>{{$project->country}}</span>)
                                    </div>
                                    @if($project->comments_cnt)
                                        <div class="text-default">
                                            <span class="fa fa-comment-o"></span>
                                            <a href="/project/{{$project->id}}?tab=3">{{$project->comments_cnt}}</a>
                                        </div>
                                    @endif
                                    @if($project->followers_cnt)
                                        <div class="text-default">
                                            <span class="glyphicon fa fa-bookmark-o" >{{$project->followers_cnt}}</span>
                                            <span ng-bind="p.followers_cnt"></span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6 align-self-end">

                                <div style="position: absolute;bottom:40%;width: 100%; margin-left: -15px" class="text-center small {{$project->daterest > 7 ? 'text-primary' : ($project->daterest < 4 ? 'text-danger' : 'text-warning')}}">
                                    <span >{{$project->daterest}}</span>
                                </div>
                                <round-progress
                                        max="max"
                                        current="{{$project->daterest*100/$project->datediff}}"
                                        color="{{$project->daterest < 4 ?  '#993e25' : ($project->daterest > 7 ?  '#ae6892' : '#293a4f')}}"
                                        bgcolor="#e6e6e6"
                                        radius="100"
                                        stroke="9"
                                        semi="false"
                                        rounded="false"
                                        clockwise="true"
                                        responsive="true"
                                        duration="800"
                                        animation="easeOutCubic"
                                        animation-delay="0">

                                </round-progress>
                            </div>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

    </div>
    <div style="position: absolute;right:0;bottom: 0; z-index:99">
        <img src="/images/layers/treeright.svg" class="img-fluid" />
    </div>
    <div style="position: absolute;left:0;bottom: 0" >
        <img src="/images/layers/hillside.svg" class="hilllayer"/>
    </div>
</section>
<script src="{{ URL::asset('bower_components/angular/angular.min.js')}}" ></script>
<script src="{{ URL::asset('bower_components/angular/angular-translate.min.js')}}"></script>
<script src="{{ URL::asset('bower_components/angular/i18n/angular-locale_zh.js')}}" ></script>
<script src="{{ URL::asset('bower_components/angular/angular-animate.min.js') }}"></script>
<script src="{{ URL::asset('bower_components/angular/angular-touch.min.js') }}"></script>
<script src="{{ URL::asset('bower_components/angular/angular-scroll-animate.js') }}"></script>
<script src="{{ URL::asset('bower_components/angular/roundProgress.js') }}"></script>
<script src="{{ URL::asset('js/modules/welcome.js') }}"></script>
<script lang="javascript">
    function initLayer() {

        if( $(window).height() < 640){
            $("#layers").show();
            return;
        }

        var flag;
        var  dasL = $("#layer3"), dasR = $("#layer6");
        function initRight() {
            dasL.removeClass('transitLeft');
            dasR.addClass('transitRight');
            flag = setTimeout(initLeft, 81000)
        }

        function initLeft() {
            dasR.removeClass('transitRight');
            dasL.addClass('transitLeft');
            flag = setTimeout(initRight, 90500);
            $("#layers").show();
        }

        function init() {
            var xScale = ($(window).width() / 1600).toFixed(2);
            var yScale = ($(window).height() / 1000).toFixed(2);
            var ratio = $(window).width() / $(window).height();

            if (ratio >= 1.6)
                $('#layers .backlayer').css('background-size', 'cover');
            else
                $('#layers .backlayer').css('background-size', 'contain');


            var lWidth = 25 * (xScale < yScale ? xScale : yScale);
            var rWidth = 18 * (xScale < yScale ? xScale : yScale);

            dasL.height(lWidth * 3.2);
            dasL.width(lWidth);

            dasR.width(rWidth);
            dasR.height(rWidth * 3.17);



            var margin = $(window).height() - $('.header').height() - 330 * xScale;
            $('#slogan').height(margin);
            $('#home').height($(window).height());
            $('#festivals').height($(window).height());

            $('#projects').height($(window).height());

            var xLinit = $(window).width() * 0.58, xRinit = $(window).width() * 0.46;
            var lTurn =  $(window).width() * 0.1 - lWidth/2, rTurn = $(window).width() * 0.91;
            var yLMax =  -295 * xScale, yRMax =  -255 * xScale;
            var xRMax = $(window).width() + rWidth;
            var style = document.getElementById("transit");
            var leftInnerHTML = 'elevatorLeft {0% {visibility:visible;transform: translateX(' + xLinit + 'px);\n'+
                '-ms-transform: translateX(' + xLinit + 'px);\n-webkit-transform: translateX(' + xLinit + 'px);}\n' +
                '84.22% {transform: translate(' + lTurn + 'px, ' + yLMax + 'px);' +
                '-ms-transform: translate(' + lTurn + 'px, ' + yLMax + 'px);\n-webkit-transform: translate(' + lTurn + 'px, ' + yLMax + 'px);}\n'+
                '100% {transform: translate(-' + lWidth + 'px,' + yLMax + 'px);' +
                '-ms-transform: translate(-' + lWidth + 'px, ' + yLMax + 'px);\n-webkit-transform: translate(-' + lWidth + 'px, ' + yLMax + 'px);}}';
            var rightInnerHTML = 'elevatorRight {0% {visibility:visible;transform: translateX(' + xRinit + 'px,0px);' +
                '-ms-transform: translateX(' + xRinit + 'px);\n-webkit-transform: translateX(' + xRinit + 'px);}\n'+
                '80% {transform: translate(' + rTurn + 'px, ' + yRMax + 'px);' +
                '-ms-transform: translate(' + rTurn + 'px, ' + yRMax + 'px);\n-webkit-transform: translate(' + rTurn + 'px, ' + yRMax + 'px);}\n'+
                '100% {transform: translate(' +xRMax + 'px, ' + yRMax + 'px);' +
                '-ms-transform: translate(' + xRMax + 'px, ' + yRMax + 'px);\n-webkit-transform: translate(' + xRMax + 'px, ' + yRMax + 'px);}}';
            style.innerHTML = '@keyframes '+ leftInnerHTML + '\n@keyframes '+ rightInnerHTML +
                '\n@-webkit-keyframes '+ leftInnerHTML + '\n@-webkit-keyframes '+ rightInnerHTML;
            $('.facelayer').width($(window).width());
            $('.hilllayer').width($(window).width());
            $('.facelayer').css('margin-top',-$('#topNav').height());
            initLeft();
        }

        init();

        $(window).resize(function() {
            dasR.removeClass('transitRight');
            dasL.removeClass('transitLeft');
            if ((navigator.userAgent.toLowerCase().indexOf("firefox")!=-1)){

                document.addEventListener("DOMMouseScroll",scrollFun,false);

            }

            else if (document.addEventListener) {

                document.addEventListener("mousewheel",scrollFun,false);

            }

            else if (document.attachEvent) {

                document.attachEvent("onmousewheel",scrollFun);

            }

            else{

                document.onmousewheel = scrollFun;

            }
            init();
        });
    }

    $(document).ready(function () {
        initLayer();
    })
</script>
<style>

    body{
        overflow-x: hidden;
    }
    section{
        z-index: 10;
        height: 100%;
        min-height: 100%;

    }

    .content {
        position: relative;
        width: 100%;
        min-height: 1px;
    }
    .bg-green{
        background: #092322;
    }
    .backlayer{
        width: 100%;
        height: 100%;
        display: table;
        position: absolute;
        bottom:0;
        background-repeat: no-repeat;
        background-position: bottom left;
        z-index: -1;
    }
    .enter-button{
        font-size: 2.4rem;
        position: relative;
    }
    .enter-button:hover{
        font-weight: bold;

    }
    .enter-button:after {
        content: '';
        position: absolute;
        top: 100%;
        left:0;
        width: 100%;
        height: 3px;
        background: #fff;
        -webkit-transform: scale3d(1, 0.4, 1);
        transform: scale3d(1, 0.4, 1);
        -webkit-transform-origin: 50% 100%;
        transform-origin: 50% 100%;
        -webkit-transition: -webkit-transform 0.3s, background-color 0.3s;
        transition: transform 0.3s, background-color 0.3s;
        -webkit-transition-timing-function: cubic-bezier(0.2, 1, 0.3, 1);
        transition-timing-function: cubic-bezier(0.2, 1, 0.3, 1);
    }
    .enter-button:hover:after{
        background-color:  #0c5460;
        -webkit-transform: scale3d(1, 1, 1);
        transform: scale3d(1, 1, 1);
    }
</style>
</body>
</html>