<!DOCTYPE html>
<html lang="en" ng-app="zooApp" >
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="/favicon.ico">
    <!-- Bootstrap core CSS -->
    @section('header')
    <link rel="stylesheet" href="/bower_components/font-awesome/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="/bower_components/bootstrap/css/bootstrap.css" type="text/css">
    <link rel="stylesheet" href="/bower_components/angular/angucomplete-alt.css" type="text/css">
    <link rel="stylesheet" href="/bower_components/font-awesome/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="/css/tag.css" type="text/css">
    <link rel="stylesheet" href="/css/base.css" type="text/css">
    <link rel="stylesheet" href="/css/common.css" type="text/css">
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="/bower_components/jquery/jquery-1.11.3.min.js"></script>
    <script src="/bower_components/bootstrap/js/bootstrap.min.js"></script>
    <script src="/bower_components/assets/ie-emulation-modes-warning.js"></script>
        <style>
            #searchinput .angucomplete-image-holder{
                background-size: contain;
            }

            #searchinput .angucomplete-image{
                border-radius: 0;
                width: 48px;
                height: 27px;
            }
        </style>
        <!--
<script language="JavaScript">

  if (window.Event)
      document.captureEvents(Event.MOUSEUP);

  function nocontextmenu()
  {
      event.cancelBubble = true
      event.returnValue = false;

      return false;
  }

  function norightclick(e)
  {
      if (window.Event)
      {
          if (e.which == 2 || e.which == 3)
              return false;
      }
      else
      if (event.button == 2 || event.button == 3)
      {
          event.cancelBubble = true
          event.returnValue = false;
          return false;
      }

  }

  document.oncontextmenu = nocontextmenu; // for IE5+
  document.onmousedown = norightclick; // for all others

      </script>-->
@show
    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body class="'{{App::getLocale()}}'">
<input type="hidden" name="csrfmiddlewaretoken" value="<?php echo csrf_token(); ?>">
<nav class="navbar navbar-default">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#top-navbar-collapse" aria-expanded="false">
                <span>&#9776;</span>
            </button>
            <a class="navbar-brand" href="/home">
                <span>ZOOMOV</span>
                <img alt="ZOOMOV" src="/images/logo.png" />
            </a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="top-navbar-collapse" ng-controller="headerCtrl" ng-init="init('{{Lang::locale()}}')">
            <ul class="nav navbar-nav" id="navleft">
                <li ng-class="{active: currentPath == 'discover'}">
                    <a href="/discover">{{trans("layout.MENU.discover")}}</a>
                </li>
                <li ng-class="{active: currentPath == 'video'}">
                    <a href="/videos">{{trans("layout.MENU.videos")}}</a>
                </li>
                <li ng-class="{active: currentPath == 'preparations'}">
                    <a href="/preparations">{{trans("layout.MENU.creation")}}</a>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right" id="navright">
                <li ng-class="{active: $location.path().indexOf('notifications') > 0}">
                    <a href="/notifications">
                        <?php echo file_get_contents("images/icons/notification.svg"); ?>
                    </a>
                    <sup ng-if="notificationCnt>0" ng-bind="notificationCnt"></sup>
                </li>
                <li ng-class="{active: $location.path().indexOf('messages') > 0}">
                    <a href="/messages">
                        <?php echo file_get_contents("images/icons/message.svg"); ?>
                    </a>
                    <sup ng-if="messageCnt > 0" ng-bind="messageCnt"></sup>
                </li>
                @if(Auth::check())
                <li class="dropdown" id="account" >
                    <a href="javascript:void(0)" class="dropdown-toggle"
                       data-toggle="dropdown" role="button"
                       aria-haspopup="true" aria-expanded="false">
                        <img id="avatars-img" class="img-circle"
                             src="/context/avatars/{{Auth::id()}}.small.jpg?{{time()}}"
                             onError="this.onerror=null;this.src='/images/avatar.png';"/>
                    </a>
                    <ul class="dropdown-menu">
                        @if(!Auth::user()->professional)
                        <li ng-class="{active: $route.current.scope.name == 'profile'}">
                            <a class="btn" ng-href="/profile/{{Auth::id()}}" class="separator">
                                <span class="text-info">{{trans("layout.MENU.profile")}}</span>
                            </a>
                        </li>
                        @endif
                        <li ng-if="preparations.length">
                            <a href="/account" class="btn btn-info">{{trans("layout.MENU.preparations")}}</a>
                        </li>
                            <!--
                        <li ng-repeat="p in preparations|orderBy:title">
                            <a class="btn btn-sm text-left" ng-if="p.title.length > 0" href="/admin/preparations/<%p.id%>"
                               ng-class="{'separator': $last}" ng-bind="p.title">
                            </a>
                        </li>
                        -->
                        <li>
                            <a class="btn" href="/personal">
                                <span class="text-primary">{{trans("layout.MENU.person")}}</span>
                            </a>
                        </li>
                        <li>
                            <a class="btn" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                <span class="text-important">{{trans("layout.MENU.signout")}}</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </li>
                @endif
            </ul>

            <form id="zooHeaderSearch" class="navbar-form navbar-right" role="search" style="margin: 0;padding: 0">
                <angucomplete-alt id="searchinput" input-name="search"
                                  placeholder="{{trans('layout.MENU.search')}}"
                                  pause="100"
                                  selected-object="projectSelected"
                                  local-data="projectIndex"
                                  focus-in="searchFocus()"
                                  search-fields="title,username"
                                  title-field="title"
                                  description-field="username"
                                  image-uri="/context/projects"
                                  image-field="id"
                                  image-error="icons/waiting.svg"
                                  minlength="1"
                                  clear-selected = "true"
                                  input-class="form-text"
                                  match-class="highlight"
                                  text-no-results="{{trans('layout.MENU.none')}}"
                                  text-searching="{{trans('layout.MENU.searching')}}"/>
            </form>
        </div>
    </div>
</nav>
<div class="affix fixed-right dropdown" id="language" style="top:0">
    <div class="dropdown-toggle"
       data-toggle="dropdown" role="button"
       aria-haspopup="true" aria-expanded="false">
        <span class="btn-sq-md text-uppercase" ng-bind="currentLang.name"></span>
    </div>
    <ul class="dropdown-menu">
        <li ng-repeat="l in languages" ng-if="l.id != '{{App::getLocale()}}'" class="btn btn-primary"
            ng-click="setLanguage(l.id);">
            <span ng-bind="l.name"></span>
        </li>
    </ul>
</div>
<div class="padding-top-lg">
    @yield('content')
</div>

<div class="footer">
    <div class="container footer-content">
        <div class="row">
            <div class="col-md-2 text-center">
                <div>
                    <a href="/">{{trans("layout.FOOTER.home")}}</a>
                </div>
                <div>
                    <a href="/discover">{{trans("layout.FOOTER.discover")}}</a>
                </div>
                <div>
                    <a href="/preparations">{{trans("layout.FOOTER.create")}}</a>
                </div>
                <div>
                    <a href="/personal">{{trans("layout.FOOTER.profile")}}</a>
                </div>
                <br/>
                <div><a href="/login">{{trans("layout.FOOTER.login")}}</a></div>
                <div><a href="/register">{{trans("layout.FOOTER.register")}}</a></div>
                <br/>
                <div><span>{{trans("layout.FOOTER.search")}}</span></div>
            </div>
            <div class="col-md-2 text-center">
                <div><span>{{trans("layout.FOOTER.zoomov")}}</span></div>
                <div><span>{{trans("layout.FOOTER.help")}}</span></div>
                <div><span>{{trans("layout.FOOTER.rules")}}</span></div>
                <div><span>{{trans("layout.FOOTER.faq")}}</span></div>
                <br/>
                <br/>
                <div><span>{{trans("layout.FOOTER.terms")}}</span></div>
                <div><a href="/privacy" target="_blank">{{trans("layout.FOOTER.privacy")}}</a></div>
                <div><span>{{trans("layout.FOOTER.cookies")}}</span></div>
                <div><span>{{trans("layout.FOOTER.copyright")}}</span></div>
            </div>
            <div class="col-md-2 text-center">
                <div><span>{{trans("layout.FOOTER.us")}}</span></div>
                <div><span>{{trans("layout.FOOTER.timeline")}}</span></div>
                <div><span>{{trans("layout.FOOTER.news")}}</span></div>
                <div><a href="/contact" target="_blank">{{trans("layout.FOOTER.contact")}}</a></div>
                <div><a href="/join" target="_blank">{{trans("layout.FOOTER.join")}}</a></div>
                <div><span>{{trans("layout.FOOTER.verification")}}</span></div>
                <br/>
                <div><span>{{trans("layout.FOOTER.activities")}}</span></div>
                <div><span>{{trans("layout.FOOTER.festival")}}</span></div>
            </div>
            <div class="col-md-2  text-center">
                <div><span>{{trans("layout.FOOTER.languages")}}</span></div>
                <div ng-click="setLanguage('zh')">简体中文</div>
                <div>繁体中文</div>
                <div ng-click="setLanguage('en')">English</div>
                <div ng-click="setLanguage('fr')">Française</div>
            </div>
            <div class="col-md-2 text-center icon" >
                <div><span>{{trans("layout.FOOTER.newsletter")}}</span></div>
                <div>
                    <a href="weibo.com/zoomov" target="_blank"><img src="/images/icons/weibo.svg" /></a>

                </div>
                <div>
                    <a href="twitter.com/zoomov_com" target="_blank"><img src="/images/icons/twitter.svg" /></a>
                </div>
            </div>
            <div class="col-md-2">
                <img src="/images/qcode.jpg" class="img-responsive" />
            </div>
        </div>
    </div>
</div>
<div class="affix fixed-bottom fixed-right">
    <div>
        <div class='btn btn-sm-svg' id="toTop" onClick="$('html, body').animate({ scrollTop: 0 }, 'fast');">
            <?php echo file_get_contents(public_path("/images/icons/backtoup.svg")); ?>
        </div>
    </div>
    <div>
        <div class='btn btn-sm-svg' id="toHelp">
            <?php echo file_get_contents(public_path("/images/icons/help.svg")); ?>
        </div>
    </div>
</div>


<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="{{ URL::asset('bower_components/assets/ie10-viewport-bug-workaround.js')}}"></script>
<script src="{{ URL::asset('bower_components/angular/angular.min.js')}}" ></script>
<script src="{{ URL::asset('bower_components/angular/angular-translate.min.js')}}"></script>
<script src="{{ URL::asset('bower_components/angular/angular-translate-loader-static-files.min.js')}}"></script>
<script src="{{ URL::asset('bower_components/angular/i18n/angular-locale_zh.js')}}" ></script>
<script src="{{ URL::asset('bower_components/angular/angular-animate.min.js') }}"></script>
<script src="{{ URL::asset('bower_components/angular/angular-touch.min.js') }}"></script>
<script src="{{ URL::asset('bower_components/angular/angular-route.min.js') }}"></script>
<script src="{{ URL::asset('bower_components/angular/angular-cookies.min.js') }}"></script>
<script src="{{ URL::asset('bower_components/angular/angular-messages.min.js') }}"></script>
<script src="{{ URL::asset('bower_components/angular/angular-resource.min.js') }}"></script>
<script src="{{ URL::asset('bower_components/angular/angular-sanitize.min.js') }}"></script>
<script src="{{ URL::asset('bower_components/bootstrap/js/ui-bootstrap-tpls.min.js') }}"></script>
<script src="{{ URL::asset('bower_components/angular/roundProgress.js') }}"></script>
<script src="{{ URL::asset('bower_components/angular/angular-scroll-animate.js') }}"></script>
<script src="{{ URL::asset('bower_components/angular/angucomplete-alt.js') }}"></script>
<script src="{{ URL::asset('js/modules/zoomovApp.js') }}"></script>
<script src="{{ URL::asset('js/controllers/header.js') }}"></script>
@yield('script')
<script type="text/javascript">
    $(window).scroll(function() {
        if ($(this).scrollTop()) {
            $('#toHelp').animate({"bottom": '45px'}, 500);
            $('#toTop:hidden').stop(true, true).fadeIn();
            $('.overlay').css("top", 0);
        } else {
            $('#toHelp').animate({"bottom": '10px'}, 200);
            $('#toTop').stop(true, true).fadeOut();
            $('.overlay').css("top", 60);
        }
    });


</script>
</body>
</html>
