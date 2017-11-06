<?php
return [
    'HEADER' => [
        "prefix"=>  "YOU HAVE RECEIVED",
        "sent"=>  "SENT",
        "got" =>  "GOT",
        "set" =>  "Set"
    ],
    'LABELS' => [
        "refused"=>"NO",
        "accepted"=>"YES",
        "wait"=>"WAIT",
        "replied"=>"REPLIED",
        "invitations"=>"Invitations",
        "as" => "as",
        "participate"=>"participate",
        "messages"=>"Messages",
        "received_invitation"=>"Received Invitations",
        "sent_invitation"=>"Sent Invitations",
        "received_message"=>"Received Messages",
        "sent_message"=>"Sent Messages",
        "read" => "Read",
        "envelop" => "Envelop",
        "receivers" => "Find the member to receiver this message",
        "subject" => "Subject: ",
        "applications" => "APPLICATIONS",
        "reminders" => "REMINDERS",
        "remind" => "REMIND MEMBERS OF PROJECT ",
        "projects" => "YOU HAVE <strong class='text-important'><%cnt > 0 ? cnt : 'NONE'%></strong> PROJECTS TO RECEIVE <b>APPLICATIONS</b>, AND <strong class='text-important'><%total > 0 ? total : 'NONE'%></strong> TO GET/SET <b>REMINDERS</b>",
        "owns" => "YOU HAVE <strong class='text-important'><%cnt > 0 ? cnt : 'NONE'%></strong> PROJECTS TO RECEIVE/SEND <b>INVITATIONS</b>",
    ],
    'title' =>  [
        'add_script'=>'A new script declared you as writer',
        'delete_script' => 'Your script has been removed',
        'delete_author' => 'A script remove you from writers'
    ],
    'body' =>  [
        'add_script'=>':user add script :script of project :title',
        'delete_script' => ':user remove script :script from project :title',
        'delete_author' => ':user drop your name from script :script of project :title'
    ],
    'errors'=> [
        "projects" => "To invite a zoomover, you must have at least one project on line."
    ]
];