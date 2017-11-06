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
    <div class="col-xs-12" ng-controller="loginCtrl" ng-init="init('{{App::getLocale()}}')">
        <div class="inner">
            <div class="middle">
                @if(!is_null(session('message')))
                    {{session('message')}}
                @endif
                <form name="zooform" id="zooform" novalidate class="fixed-form grey" method="POST" action="{{ route('login') }}">
                    {{ csrf_field() }}
                    @if(is_null($user))
                    <div class="avatar">
                        <img id="avatar" class="img-circle img-responsive center"
                             ng-src="/images/avatar.png" />
                    </div>
                    <div class="form-group">
                        <input id="email" type="email" class="form-text" name="email" placeholder="{{trans('auth.email')}}"
                               ng-model="email" ng-init="setEmail('{{ old('email') }}')"
                               ng-keyup="$event.keyCode == 13 && getUser(zooform.$valid)"
                               required autofocus />

                        @if ($errors && count($errors) > 0)
                        <div class="text-danger small" role="alert">
                            @foreach ($errors->all() as $error)
                                {{ $error }}
                            @endforeach
                        </div>
                        @else
                        <div class="text-danger small" role="alert" ng-show="zooform.email.$touched || zooform.submitted">
                            <span ng-show="zooform.email.$error.required">{{trans('auth.error_email_required')}}</span>
                            <span ng-show="zooform.email.$error.email">{{trans('auth.error_email_invalid')}}</span>
                        </div>
                        @endif
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary btn-inverse btn-block" ng-disabled="zooform.$invalid">{{trans('auth.next')}}</button>
                    </div>
                    @else
                        <div class="avatar">
                            <img id="avatar"  class="img-circle img-responsive center"
                                 ng-src="/context/avatars/{{$user->id}}.jpg?{{time()}}"
                                 onError="this.onerror=null;this.src='/images/avatar.png';"/>
                        </div>
                        <h4 class="text-center text-info">{{$user->username}}</h4>
                        <div class="form-group{{ ($errors->has('password')) ? 'error' : '' }}">
                            <input id="email" type="email" name="email" value="{{$user->email}}" readonly="readonly" style="display: none;">
                            <input type="password" ng-model="password" class="form-text" ng-minlength="6" ng-maxlength="16"
                                   name="password"  placeholder="{{trans('auth.password')}}"
                                   ng-keyup="$event.keyCode == 13 && login(zooform.$valid)"
                                   required autofocus />
                            <div class="text-danger small" role="alert">
                                @if ($errors && count($errors) > 0)
                                    @foreach ($errors->all() as $error)
                                        {{ $error }}
                                    @endforeach
                                @else
                                    <span ng-show="zooform.password.$error.required">{{trans('auth.error_password_required')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class=" checkbox checkbox-small ">
                                <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }} />
                                <label for="remember"></label><span>{!! trans('auth.login_remember') !!}</span>
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary btn-inverse btn-block" ng-disabled="zooform.$invalid">{{trans('auth.login')}}</button>
                        </div>
                        @endif
                </form>
                @if(!is_null($user))
                <div class="text-center">
                    <p>
                        <a class="title small" href="{{ route('password.request') }}">{{trans('passwords.forget')}}</a>
                    </p>
                    <p>
                        <a class="small text-default" href="relogin">{{trans('auth.another')}}</a>
                    </p>
                </div>
                @endif
                <div class="text-center">
                    <p><a class="small text-default" href="{{ route('register') }}">{{trans('auth.register')}}</a></p>
                    <p><a href="" class="small text-important">{{trans('auth.help')}}</a> </p>
                </div>
            </div>
        </div>
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

                dasR.width(rWidth);
                dasR.height(rWidth * 3.17);

                $("#layers").show();

                var margin = $(window).height() - $('.header').height() - 330 * xScale;
                $('#content').height(margin);
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