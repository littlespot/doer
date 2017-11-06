<!DOCTYPE html>
<html>
    <head>
        <title>Script {{$script}} of project {{$title}} has been modified</title>
    </head>
    <body style="background:#999;font-family:helvetica;line-height:1.8em;color:#293a4f;font-size:12px;height:100%">
        <div style="width:480px; display: table;vertical-align: middle;margin:auto;">
            <div style="text-align: left;padding:20px 40px;background:#fff;margin:50px auto;">
                <h3>Hello, {{$guest}}</h3>
                <P>{{$user}} as the administrator of project {{$title}} has changed script {{$oldlink}}{{$newlink == $oldlink ? '' : ' to '.$newlink}}, and at the same time, remove your from its authors.</p>

                <p>If you have any objections or questions, please contact us as soon as possible.</p>

                <p>If you are not our member yet, keep the following link carefully to follow the progress of project.</p>

                <p style="margin:18px auto">
                    <a href="www.zoomov.com/guest/{{$link}}" target="_blank" style="background: rgba(42, 59, 81, 1);color:rgb(230,230,230);font-size: 14px;cursor: pointer;padding:12px 18px">
                        {{$title}}
                    </a>
                </p>

                <p>If you are one of us, simply login in and you can see more about this project.</p>

                <p>Thanks for your attention. Have fun!</p>

                <p>ZOOMOVers</p>
            </div>
        </div>
    </body>
</html>