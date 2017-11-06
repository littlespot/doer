@extends('auth.cover')
@section('background')
    <div id="layers">
        <div class="backlayer" style="background-image: url(/images/layers/BG_A03.svg);">
            &nbsp;
        </div>
        <div class="backlayer" style="background-image: url(/images/layers/BG_B02.svg);">
            &nbsp;
        </div>
        <div class="backlayer" style="background-image: url(/images/layers/BG_B01.svg);">
            &nbsp;
        </div>
    </div>
@endsection
@section('content')
    <div class="text-center text-info message">
        {!! trans("auth.activated") !!}
    </div>
    <div class="col-lg-offset-4 col-lg-4 col-md-offset-2 col-md-8 col-sm-offset-1 col-sm-10 col-xs-12"
         ng-controller="activationCtrl">
        <form name="zooform" id="zooform" novalidate class="fixed-form grey" action="/user/active" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="email" value="{{$email}}">
            <h4 class="text-center text-info">{{$username}}</h4>
            <div class="form-group">
                <input type="password" class="form-text" id="newpwd"
                       name="password" placeholder="<%'login.PLACES.pwd' | translate%>"
                       ng-model="user.password" ng-pattern="regex"
                       required />
                <div class="error" role="alert" ng-class="{'visible':zooform.pwd.$touched || zooform.submitted}">
                    <span ng-show="zooform.pwd.$error.required" translate="login.ERRORS.require.Pwd"></span>
                    <span ng-show="zooform.pwd.$error.minlength" translate="login.ERRORS.minlength.Pwd"></span>
                    <span ng-show="zooform.pwd.$error.maxlength" translate="login.ERRORS.maxlength.Pwd"></span>
                    <span ng-show="zooform.pwd.$error.pattern" translate="login.ERRORS.regex.Pwd"></span>
                    <span ng-show="error.pwd" translate="login.ERRORS.invalid.Pwd"></span>
                </div>
            </div>
            <div class="form-group">
                <input type="password" class="form-text" name="password_confirmation" pw-check="newpwd"  placeholder="<%'personal.PLACES.newpwd2' | translate%>"
                       ng-model="user.password_confirmation" ng-keyup="$event.keyCode == 13 && singin(zooform.$valid)"
                       required />
                <div class="error" role="alert" ng-class="{'visible':zooform.pwd2.$touched || zooform.submitted}">
                    <span ng-show="zooform.pwd2.$error.required" translate="login.ERRORS.require.Pwd"></span>
                    <span ng-show="zooform.pwd2.$error.pwmatch" translate="login.ERRORS.invalid.Pwd2"></span>
                </div>
            </div>
            <div class="form-group text-center">
                <div class="btn btn-primary btn-inverse btn-block" ng-click="changePwd(zooform.$valid)"><span translate="login.Login"></span></div>
            </div>
        </form>
        <div class="text-warning" translate="login.ERRORS.regex.Pwd"></div>
    </div>
@endsection
@section('script')
    <script src="/js/controllers/auth/activation.js"></script>
    <script lang="javascript">

        function init() {
            if( $(window).height() < 640){
                $("#layer").hide();
                return;
            }
            var xScale = ($(window).width() / 1600).toFixed(2);
            var ratio = $(window).width() / $(window).height();

            if (ratio >= 1.6)
                $('#layers .backlayer').css('background-size', 'cover');
            else
                $('#layers .backlayer').css('background-size', 'contain');

            $("#layers").show();

            var margin = $(window).height() - $('.header').height() - 330 * xScale;
            $('#content').height(margin);
        }

        $(document).ready(function () {
            init();
        })
    </script>
@endsection