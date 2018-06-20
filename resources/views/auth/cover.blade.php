<html lang="en" ng-app="zooApp">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>ZOOMOV</title>
    <link rel="icon" href="/favicon.ico">
    <!-- Bootstrap core CSS -->

    <link rel="stylesheet" href="/css/animate.min.css">
    <link rel="stylesheet" href="/bower_components/bootstrap/css/bootstrap.css" type="text/css">
    <link rel="stylesheet" href="/bower_components/font-awesome/css/font-awesome.min.css" type="text/css">

    <link rel="stylesheet" href="/css/base.css">

    <script src="/bower_components/jquery/jquery-3.3.1.min.js"></script>
    <script src="/bower_components/jquery/popper.min.js"></script>
    <script src="/bower_components/bootstrap/js/bootstrap.min.js"></script>

    <!--
    <script src="/bower_components/bootstrap/js/bootstrap-hover-dropdown.min.js"></script>
    -->
    <script src="/bower_components/assets/ie-emulation-modes-warning.js"></script>
    <script src="/bower_components/assets/ie-emulation-modes-warning.js"></script>
    <style>
        body{
            overflow: hidden;
        }
        .backlayer{
            width: 100%;
            height: 100%;
            display: table;
            position: absolute;
            bottom:0;
            background-repeat: no-repeat;
            background-position: bottom left;
        }
        #main{
            z-index: 10;
            height: 100%;
            min-height: 100%;
            width: 100%;
            position: absolute;
        }

        div.logo{
            height: 40px;
            width: 100%;
            text-align: right;
            background: url('/images/logo.png') no-repeat top center;
            background-size: contain;
        }
    </style>
</head>
<style id=transit></style>
<body class="bg-secondary">

@yield('background')

@include('templates.crazy-loader')

<div class="container">

    <div class="logo" >

        <div class="dropdown" id="languageBar">
            <!--<div class="dropdown-toggle btn btn-default text-uppercase"
                 data-toggle="dropdown" role="button"
                 aria-haspopup="true" aria-expanded="false">
                <span class="btn-sq-md text-uppercase" ng-bind="currentLang.name"></span>
            </div>
            <ul class="dropdown-menu">
                <li ng-repeat="l in languages" ng-if="l.id != '{{app()->getLocale()}}'" class="btn btn-primary"
                    ng-click="setLanguage(l.id);">
                    <span ng-bind="l.name"></span>
                </li>
            </ul>-->
        </div>
    </div>
    <h5 class="mt-2 font-lg text-center text-muted">{{trans('layout.SLOGANS.home')}}</h5>
</div>
<div id="main" class="d-flex flex-column justify-content-center" >
    <div id="content" class="container d-flex justify-content-center" ng-controller="loginCtrl"  ng-init="init('{{app()->getLocale()}}')">
        @yield('content')

    </div>
</div>
<!--
<form id="currentLangForm" method = 'POST' action = '/languages' class = 'container text-center' style="z-index: 10000">
    {{ csrf_field() }}
    <input name="locale" id="current_local" type="hidden" value="zh" />
</form>
--
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="/bower_components/assets/ie10-viewport-bug-workaround.js"></script>
<script src="/bower_components/angular/angular.min.js" ></script>
<script src="/bower_components/angular/angular-translate.min.js"></script>
<script src="/bower_components/angular/angular-translate-loader-static-files.min.js"></script>
<script src="/bower_components/angular/angular-animate.min.js"></script>
<script src="/bower_components/angular/angular-touch.min.js"></script>
<script src="/bower_components/bootstrap/js/ui-bootstrap-tpls.min.js"></script>
<script src="/js/modules/login.js"></script>
<script src="/js/directives/common.js"></script>
@yield('script')
</body>
</html>
