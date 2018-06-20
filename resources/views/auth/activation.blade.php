@extends('auth.cover')
@section('background')
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
        <div class="backlayer" style="background-image: url(/images/layers/layer1.svg);">
            &nbsp;
        </div>
    </div>
@endsection
@section('content')
    <div>
    @if($user)

        <h1 class="text-center">{{trans('auth.welcome', ['name'=>$user->username])}}</h1>
        <h3 class="py-3" style="line-height: 3.6rem">{!! $message !!}</h3>
        <div class="text-center">
            <a class="btn btn-primary" href="{{config('app.url')}}">{{trans('layout.BUTTONS.welcome')}}</a>
        </div>

    @else
        <h1 class="text-center">{{$message}}</h1>
        <div class="text-center">
            <a class="btn btn-primary" href="{{config('app.url')}}">{{trans('layout.BUTTONS.welcome')}}</a>
        </div>
        @endif
        <div class="text-center"><a href="mailTo:contact@zoomov.com">{{trans('layout.FOOTER.contact')}}</a></div>
    </div>
@endsection
@section('script')
    <script src="/js/controllers/auth/login.js"></script>
    <script lang="javascript">
        function initLayer() {

            if( $(window).height() < 640){
                $("#layer").hide();
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

                dasR.height(rWidth * 3.17);
                dasR.width(rWidth);

                $("#layers").show();

                var margin = $(window).height() - $('.header').height() - 330 * xScale;
                $('#content').height(margin);
                $('#main').css('top', -lWidth * 3.2 - 40)
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

                initLeft();
            }

            init();

            $(window).resize(function() {
                dasR.removeClass('transitRight');
                dasL.removeClass('transitLeft');
                init();
            });
        }

        $(document).ready(function () {
            initLayer();
        })
    </script>
@endsection