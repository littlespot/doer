<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
</head>
<body style="background:#999;font-family:helvetica;line-height:1.8em;color:#293a4f;font-size:12px;height:100%">
<div style="width:480px; display: table;vertical-align: middle;margin:auto;">
    <div style="text-align: left;padding:20px 40px;background:#fff;margin:50px auto;">
        <h3>Hi {{$user}}</h3>
        <P>Thanks for using ZOOMOV! Please activate your account by clicking the button below.</p>

        <p style="margin:18px auto">
            <a href="www.zoomov.com/active?key={{$key}}" target="_blank" style="background: rgba(42, 59, 81, 1);color:rgb(230,230,230);font-size: 14px;cursor: pointer;padding:12px 18px">
                Activate Account
            </a>
        </p>

        <p>In case you cant not open the link above, please copy the following address in your navigator <span style="color: #D77459;font-weight:bold">www.zoomov.com/active?key={{$key}}</span></p>

        <p>Thanks and happy projecting.</p>

        <p>ZOOMOVers.</p>
    </div>
</div>
</body>
</html>