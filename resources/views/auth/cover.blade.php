<html lang="en" ng-app="zooApp">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>ZOOMOV</title>
    <link rel="icon" href="/favicon.ico">
    <!-- Bootstrap core CSS -->
    <link href="/bower_components/bootstrap/css/bootstrap.css" rel="stylesheet" />
    <link href="/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="/css/base.css" rel="stylesheet" type="text/css">
    <link href="/css/login.css" rel="stylesheet" type="text/css" >
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="/bower_components/jquery/jquery-2.2.1.min.js"></script>
    <script src="/bower_components/bootstrap/js/bootstrap.min.js"></script>
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
        }
    </style>
</head>
<style id=transit></style>
<body>
@yield('background')
@include('templates.crazy-loader')
<div id="main">
    <div class="header">
        <div class="logo"></div>
        <h4>{{trans('layout.SLOGAN')}}</h4>

    </div>
    <div id="content" class="row">
        @yield('content')
    </div>
</div>
<div class="dropdown" id="languageBar">
    <div class="dropdown-toggle btn btn-default text-uppercase"
         data-toggle="dropdown" role="button"
         aria-haspopup="true" aria-expanded="false">
        <span class="btn-sq-md text-uppercase" ng-bind="currentLang.name"></span>
    </div>
    <ul class="dropdown-menu">
        <li ng-repeat="l in languages" ng-if="l.id != '{{Lang::locale()}}'" class="btn btn-primary"
            ng-click="setLanguage(l.id);">
            <span ng-bind="l.name"></span>
        </li>
    </ul>
</div>
<form id="currentLangForm" method = 'POST' action = '/languages' class = 'container text-center' style="z-index: 10000">
    {{ csrf_field() }}
    <input name="locale" id="current_local" type="hidden" value="zh" />
</form>

<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="/bower_components/assets/ie10-viewport-bug-workaround.js"></script>
<script src="/bower_components/angular/angular.min.js" ></script>
<script src="/bower_components/angular/angular-translate.min.js"></script>
<script src="/bower_components/angular/angular-translate-loader-static-files.min.js"></script>
<script src="/bower_components/angular/angular-animate.min.js"></script>
<script src="/bower_components/angular/angular-touch.min.js"></script>
<script src="/bower_components/bootstrap/js/ui-bootstrap-tpls.min.js"></script>
<script src="/bower_components/angular/angular-cookies.min.js"></script>
<script src="/js/modules/login.js"></script>
<script src="/js/directives/common.js"></script>
@yield('script')
</body>
</html>
