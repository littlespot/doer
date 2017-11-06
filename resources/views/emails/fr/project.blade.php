<!DOCTYPE html>
<html>
<head>
    <title>Pass</title>
</head>
<body style="background:#999;font-family:helvetica;line-height:1.8em;color:#293a4f;font-size:12px;height:100%">
<div style="width:480px; display: table;vertical-align: middle;margin:auto;">
    <div style="text-align: left;padding:20px 40px;background:#fff;margin:50px auto;">
        <h3>Hello, {{$user}}</h3>
        <P>Your project {{$name}} is already online. Click the button to check. it</p>

        <p style="margin:18px auto">
            <a href="www.zoomov.com/#/projects/{{$id}}" target="_blank" style="text-decoration: none; background: rgba(42, 59, 81, 1);color:rgb(230,230,230);font-size: 14px;cursor: pointer;padding:12px 18px">
                {{$name}}
            </a>
        </p>

        <p>Have fun!</p>

        <p>ZOOMOV Team</p>
    </div>
</div>
</body>
</html>