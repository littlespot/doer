<!DOCTYPE html>
<html>
<head>
    <title>404</title>

    <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

    <style>
        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            color: #B0BEC5;
            display: table;
            font-weight: 100;
            font-family: 'Lato';
        }

        .container {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }

        .content {
            text-align: center;
            display: inline-block;
        }

        h1
        {
            font-family: "Microsoft YaHei", SimHei, helvetica;
            font-size: 28px;
            font-weight: bold;
            color:#293a4f;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="content">
        <img src="{{URL::to('/')}}/images/errors/404.png" />
    </div>
    <h1>{!! trans('messages.error') !!}</h1>
    <br>
    <h3>{!! trans('messages.404') !!}</h3>
</div>
</body>
</html>