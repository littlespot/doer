<!DOCTYPE html>
<html>
<head>
    <title>项目上线</title>
</head>
<body style="background:#999;font-family:helvetica;line-height:1.8em;color:#293a4f;font-size:12px;height:100%">
<div style="width:480px; display: table;vertical-align: middle;margin:auto;">
    <div style="text-align: left;padding:20px 40px;background:#fff;margin:50px auto;">
        <h3>您好，{{$user}}</h3>
        <P>您的项目{{$name}}已经上线。请点击下面的按钮查看。</p>

        <p style="margin:18px auto">
            <a href="www.zoomov.com/#/projects/{{$id}}" target="_blank" style="text-decoration: none; background: rgba(42, 59, 81, 1);color:rgb(230,230,230);font-size: 14px;cursor: pointer;padding:12px 18px">
                {{$name}}
            </a>
        </p>

        <p>祝玩得愉快！</p>

        <p>ZOOMOV的小伙伴们敬上</p>
    </div>
</div>
</body>
</html>