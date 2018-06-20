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
        "unreplied" => "Un-replied"
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
    'ERRORS'=> [
        "projects" => "To invite a zoomover, you must have at least one project on line.",
        'require_reminder_subject' => 'Reminder must have subject (4-40 characters)',
        'require_reminder_project' => 'Choose a project to remind',
        'require_team' => 'Project ":title" has no other member to set reminder for',
        'require_message_subject' => 'Message must have a subject',
        'require_message_body' => 'Message must have content',
    ]
];