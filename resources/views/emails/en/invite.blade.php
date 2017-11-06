<!DOCTYPE html>
<html>
<head>
    <title>Invite</title>
</head>
<body style="background:#999;font-family:helvetica;line-height:1.8em;color:#293a4f;font-size:12px;height:100%">
<div style="width:480px; display: table;vertical-align: middle;margin:auto;">
    <div style="text-align: left;padding:20px 40px;background:#fff;margin:50px auto;">
        <h3>Hello，</h3>
        <P>Amazed by your talents, {{$sender}} invite you to join ZOOMOV! Please register by clicking the button below.</p>

        <p style="margin:18px auto">
            <a href="www.zoomov.com/#/new/en/{{$user}}" target="_blank" style="background: rgba(42, 59, 81, 1);color:rgb(230,230,230);font-size: 14px;cursor: pointer;padding:12px 18px">
                JOIN US!
            </a>
        </p>

        <p>Your invite code is <span style="color: #D77459;font-weight:bold">{{$code}}</span></p>

        <p>Thanks for your attention. Have fun!</p>

        <p>ZOOMOVers</p>
    </div>
</div>
</body>
</html>