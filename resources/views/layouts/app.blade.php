<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ZOOMOV</title>
    <link rel="icon" href="../favicon.ico">
    <!-- Bootstrap core CSS -->
    <link href="../../bower_components/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../../bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="../../css/admins.css" rel="stylesheet" type="text/css" >
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../../bower_components/jquery/jquery-2.2.1.min.js"></script>
    <script src="../../bower_components/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../bower_components/assets/ie-emulation-modes-warning.js"></script>
    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/admins') }}">
                        ZOOMOV Admin
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        <li><a href="/admins/professionals">Professionals</a></li>
                        <li><a href="/admins/invite">Applications</a></li>
                        <li><a href="/admins/projects">Projects</a></li>
                        <li><a href="/admins/recommendations">Recommendations</a></li>
                        <li><a href="/admins/videos">Videos</a></li>
                        <li><a href="/admins/travelers">Travelers</a></li>
                    </ul>
                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guard('admin')->guest())
                            <li><a href="/auth/admins/login">Login</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::guard('admin')->user()->username }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="/auth/admins/logout"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="/admins/logout" method="get" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')
    </div>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../bower_components/assets/ie10-viewport-bug-workaround.js"></script>
    <script src="../../bower_components/angular/angular.min.js" ></script>
    <script src="../../bower_components/bootstrap/js/bootstrap.js"></script>
    <script src="../../js/admins.js"></script>
</body>
</html>
