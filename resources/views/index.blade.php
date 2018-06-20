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

    @section('header')

        <link rel="stylesheet" href="/css/animate.min.css">
        <link rel="stylesheet" href="/bower_components/bootstrap/css/bootstrap.css" type="text/css">
        <link rel="stylesheet" href="/bower_components/font-awesome/css/font-awesome.min.css" type="text/css">

        <link rel="stylesheet" href="/css/base.css" type="text/css">

        <script src="/bower_components/jquery/jquery-3.3.1.min.js"></script>
        <script src="/bower_components/jquery/popper.min.js"></script>
        <script src="/bower_components/bootstrap/js/bootstrap.min.js"></script>

    @show

</head>
<body>
<img src="/images/logo.png" class="img-fluid" alt="">
<div id="container">
    <nav id="topNav" ng-controller="headerCtrl" ng-init="init('{{app()->getLocale()}}')" class="container navbar sticky-top navbar-expand-lg">
        <a class="navbar-brand" href="{{env('APP_URL')}}">

        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="topNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            @if(auth()->check())
                <ul class="navbar-nav mr-auto"  id="leftBar">
                    <li class="nav-item dropdown">
                        <a href="/discover" id="projectDropdown" class="nav-link dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="600" data-close-others="false">
                            <span>{{trans("layout.MENU.projects")}}</span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="projectDropdown">
                            <a class="dropdown-item" href="/discover">{{trans("layout.MENU.project_list")}}</a>
                            <a class="dropdown-item" href="/projects">{{trans("layout.MENU.my_projects")}}</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="/profile?anchor=follower">{{trans("layout.MENU.favorites")}}</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a  href="/festivals" id="festivalDropdown" class="nav-link dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="600" data-close-others="false">
                            <span>{{trans("layout.MENU.festivals")}}</span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="festivalDropdown">
                            <a href="/festivals" class="dropdown-item">{{trans("layout.MENU.festival_list")}}</a>
                            <a href="/entries" class="dropdown-item">{{trans("layout.MENU.festival_inscription")}}</a>
                            <a href="/archives" class="dropdown-item">{{trans("layout.MENU.films")}}</a>
                            <a href="/myfestivals" class="dropdown-item border-bottom">{{trans("layout.MENU.festival_favorite")}}</a>
                            <a href="/film" class="dropdown-item text-danger">{{trans("layout.MENU.film_creation")}}</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="/preparations">{{trans("layout.MENU.creation")}}</a>
                    </li>
                </ul>
                <form id="rightBar" class="form-inline my-2 my-lg-0" action="{{ route('logout') }}" method="POST" >
                    {{ csrf_field() }}
                    <div class="input-group bg-white">
                        <div class="input-group-prepend">
                            <button class="btn "><span class="fa fa-search"></span></button>
                        </div>
                        <div angucomplete-alt id="searchinput" input-name="search"
                             placeholder="{{trans('layout.MENU.search')}}"
                             pause="100"
                             selected-object="itemSelected"
                             remote-url="{{config('url')}}/api/search/"
                             search-fields="title,description"
                             title-field="title"
                             description-field="description"
                             image-uri="/context"
                             image-field="image"
                             image-error="/icons/waiting.svg"
                             minlength="1"
                             clear-selected = "true"

                             match-class="highlight"
                             text-no-results="{{trans('layout.MENU.none')}}"
                             text-searching="{{trans('layout.MENU.searching')}}"></div>
                    </div>
                    <ul class="navbar-nav" role="group">
                        <li class="nav-item">
                            <a class="nav-link btn" href="/notifications">
                                <i class=" fa fa-bell-o"></i>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link  btn" href="/messages">
                                <i class=" fa fa-envelope-o"></i>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a id="personDropdown" class="nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img id="avatars-img" class="rounded-circle"
                                     src="/storage/avatars/{{auth()->id()}}.small.jpg?{{time()}}"
                                     onError="this.onerror=null;this.src='/images/avatar.png';"/>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="personDropdown">
                                <a href="/profile" class="dropdown-item">{{trans("layout.MENU.profile")}}</a>
                                <a href="/account" class="dropdown-item">{{trans("layout.MENU.preparations")}}</a>
                                <a class="dropdown-item" href="/personal">
                                    {{trans("layout.MENU.person")}}
                                </a>
                                <div class="dropdown-divider"></div>
                                <button class="dropdown-item"  href="{{ route('logout') }}" type="submit">
                                    <span class="text-danger">{{trans("layout.MENU.signout")}}</span>
                                </button>

                            </div>
                        </li>
                    </ul>
                </form>
            @else
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="/">ZOOMOV <span class="sr-only">(current)</span></a>
                    </li>
                    <li><a href="#about">ABOUT</a></li>
                    <li><a href="#team">TEAM</a></li>
                    <li><a href="#service">SERVICE</a></li>
                    <li><a href="#portfolio">PORTFOLIO</a></li>
                    <li><a href="#contact">CONTACT</a></li>
                </ul>
                <form class="form-inline my-2 my-lg-0">
                    <a class="btn btn-outline-success s my-2 my-sm-0" href="/login">{{trans('auth.login')}}</a>
                    <a class="btn btn-primary" href="/register">{{trans('auth.create')}}</a>
                </form>
            @endif
        </div>
    </nav>
    <div id="langBar" class="dropdown fixed-right">
        <a id="langDropdown" class="dropdown-toggle btn btn-white"
           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            {{config('constants.language.'.strtolower(app()->getLocale()))}}
        </a>
        <div class="dropdown-menu" aria-labelledby="langDropdown">
            @foreach(array_except(config('constants.language'), strtolower(app()->getLocale())) as $code=>$name)
                <a class="dropdown-item" href="/languages/{{$code}}">
                    {{$name}}
                </a>
            @endforeach
        </div>
    </div>
    <div class="content">
        @yield('content')
    </div>

    <footer id="copyright" class="pt-3 bg-dark text-light">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-xs-12">
                    <p>
                        <a href="#" class="btn btn-link">{{trans("layout.FOOTER.help")}}</a>
                        <a href="/discover" class="btn btn-link">{{trans("layout.FOOTER.discover")}}</a>
                        <a href="/festivals"  class="btn btn-link">{{trans("layout.FOOTER.festival")}}</a>
                    </p>
                    <p>
                        <a href="/discover" class="btn btn-link">{{trans("layout.FOOTER.rules")}}</a>
                        <a href="/discover" class="btn btn-link">{{trans("layout.FOOTER.terms")}}</a>
                        <a href="/discover" class="btn btn-link">{{trans("layout.FOOTER.faq")}}</a>
                    </p>
                </div>
                <div class="col-md-6 col-xs-12 text-right">
                    <p class="pr-3">
                        <a href="/languages/zh">
                            简体中文
                        </a>
                    </p>
                    <p class="pr-3">
                        <a href="/languages/en">
                            English
                        </a>
                    </p>
                    <p>
                    <ul class="list-inline">
                        <li class="btn "><a href="#" class="fa fa-facebook"></a></li>
                        <li class="btn"><a href="twitter.com/zoomov_com" class="fa fa-twitter"></a></li>
                        <li class="btn "><a href="weibo.com/zoomov" class="fa fa-weibo"></a></li>
                        <li class="btn "><a href="#" class="fa fa-wechat"></a></li>
                    </ul>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center small">
                    <p class="wow bounceIn" data-wow-delay="0.3s">
                        苏ICP备17002583号-1 &copy; 2016-{{date('Y')}} zoomov.com all rights reserved
                    </p>
                </div>
            </div>
        </div>
    </footer>
</div>
</body>