<?php

return [
    "NOTE" =>"All fields marked with an asterisk (*) have to be filled in to submit the project.",
    "PAGE" =>"This page has to be filled in to submit the project.",
    "TAGS"=>[
        "team"=>"HAS <span class='text-title'><strong>:cnt</strong></span> TALENTS",
        "recruitment"=>"WAITING <span class='text-danger'><strong>:cnt</strong></span> TO GO",
        "participate"=>"Come to join us！",
        "recruited"=>"Team completed!",
        "duration" => ":min minutes",
        "create" => "created :date",
        "update" => "updated :date",
        "finish" => "FINISH AT: <span class='text-primary'><strong>:date</strong></span>",
        "recruit" => ":cnt person",
        "application" => "I apply to join <label>:project</label>"
    ],
    "CREATION"=>[
        "pitch"=>"Pitch",
        "description"=>"Description",
        "container"=>"Container",
        "team"=>"Team",
        "recruitment"=>"Recruitment",
    ],
    "BUTTONS"=>[
        "edit"=>"Edit",
        "delete"=>"Delete",
        "submit"=>"Submit",
        "finish"=>"Mission Completed",
        "preview"=>"Preview",
        "send"=>"Send",
        "confirm"=>"Confirm",
        "cancel"=>"Cancel",
        "upload"=>"UPLOAD",
        "ask" => "Ask",
        "answer" => "Answer"
    ],
    "agreement"=>"Submitting means you have read and agree on <a class='title' href='/contracts' target='_blank'>contract</a>",
    "ALERTS"=>[
        "online"=>"Normally we put a project on line in on week.",
        "funds"=>"Details of founds are visible only to project's owner, but total amount will be shown on the page of project.",
        "team"=>"For ZOOMOVer, you can not put them in or drop them out of your team until they accept your invitation.",
        "author" => "You can not add a ZOOMOVer's script until they accept your invitation to be writer in this project",
        "member" => "For ZOOMOVer who's not your friend, you can not invite them into your team until the project is online",
        "add_author"=>"You declared a non-ZOOMOVer as the author of script. For sake of copyrights, please carefully complete following fields.",
        "delete_author"=>"Remove authors from your team (only if they are NOT authors of other scripts and don't take other occupation in this project)",
        "add_member"=>"You declared a non-ZOOMOVer as your team member. For sake of copyrights, please carefully complete following fields.",
        "recruitment" => "The description fo role can not be modified, please be careful."
    ],
    "MESSAGES"=>[
        "complete"=> "Are you sure to complete modification and return to the project page?",
    ],
    "LABELS"=>[
        "title"=>"Title",
        "poster"=>"Poster",
        "lang" => "Language(s)",
        "location"=>"Location",
        "finish"=>"Finish at",
        "synopsis"=>"Synopsis",
        "author"=>"Add author",
        "duration"=>"Duration",
        "genre"=>"Genre",
        "budget"=>"Budget Table",
        "budget_type"=> "Item",
        "budget_comment"=> "Comment",
        "funds"=> "Funds Table",
        "sponsor"=> "Sponsor",
        "funds_date"=> "Raised Date",
        "sum"=> "Amount（¥）",
        "script"=> "Script",
        "script_title"=> "Title",
        "link"=> "Link to the script",
        "description"=>"Description",
        "script_author"=>"Author(s)",
        "script_date"=>"Publication date",
        "email"=>"Email Address",
        "author_info"=> "Author's information",
        "member_info"=> "Team member's information",
        "recruitment" => "Look Forward",
        "preparation" => "Drafts to to be submitted",
        "online" => "Projects waiting to be online",
        "events" => "Timeline",
        "apply" => "APPLY",
        "application" => "Motivation",
        "report_related" =>"This report is about:",
        "question_related" =>"This question is about:",
    ],
    "STATUS" =>[
        "completed"=>"Completed",
        "online" => "We are checking the project",
        "wait" => "We are checking the status",
        "refused"=>"NEXT TIME",
        "accepted"=>"IN TEAM",
        "suspend"=>"WAITING",
    ],
    "TIPS"=>[
        "followers"=>"How many ZOOMOVers follows",
        "follow"=>"Click to follow"
    ],
    "QUESTION" =>[
        "answer_first" => "Be the first one to answer",
        "answer_wait" => "Contribute an idea",
        "answer_my" => "My idea is...",
        "answer_none" => "WAITING FOR IDEAS"
    ],
    "REPORT" => [
        "writes"=>"PUBLISHED <span class='text-important'>:cnt</span> REPORTS",
        "loves"=> "PRAISED <span class='text-important'>:cnt</span> REPORTS",
        "comments"=> "POSTED <span class='text-important'>:cnt</span> NOTES"
    ],
    "PLACES"=>[
        "title"=>"Title has at most 40 characters",
        "location"=>"Main location to run this project",
        "finish"=>"When you plan to finish this project",
        "synopsis"=>"Write a synopsis (40 to 256 characters) about your project",
        "description"=>"Present your project in details (> 200 characters): team, schedules, technologies, etc",
        "author"=>"Add author",
        "duration"=>"How many minutes the output lasts",
        "genre"=> "Choose a genre of your project",
        "lang"=>"Language(s) used during project or spoken in the output video",
        "poster"=>"Maximum size: 2M",
        "budget_type"=>"The object of spending",
        "budget_comment"=> "Memo to mange this budget in future",
        "sponsor"=>"Find your sponsor",
        "script_title"=> "Title to identify the version of scripts",
        "script_link"=> "Link of web site or drive to read the script",
        "script_description"=>"Introduce this version of script in short words (4 to 400 characters)",
        "script_author"=>"Author(s)",
        "script_date"=>"Publication date",
        "author_email"=>"Compulsive. Author's valid email address",
        "author_name"=> "Compulsive. Author's real name or pseudonym.",
        "author_site"=> "Optional. Author's blog, personal web, sns page, etc",
        "team_occupation" => "Add occupation for this team member",
        "team" => "Input username of your team member",
        "team_role" => "Choose an occupation for this team member",
        "member_email"=>"Team member's valid email address",
        "member_name"=> "Them member's real name or pseudonym.",
        "member_site"=> "Optional. Team member's blog, personal web, sns page, etc",
        "recruitment_role" => "Choose an position to post",
        "recruitment_description" =>"Your request and reward for this position (under 400 characters)",
        "budget"=>"Find a reason to spend",
        "tags"=>"Input tags of question, splitting by comma",
        "question_subject"=>"A fine terse subject",
        "motivation"=>"Represent yourself, and express your expectations of rewards for your talents.",
        "report_title"=>"Input the title of your report",
    ],
    "ERRORS"=>[
        "picture"=>[
            "size"=>"The uploaded file exceeds <%size%>M",
            "partially"=>"The file was only partially uploaded",
            "no"=>"No file was uploaded",
            "missing"=>"Missing a temporary folder",
            "disk"=>"Failed to write file to disk",
            "extension"=>"File upload stopped by extension"
        ],
        "require"=>[
            "poster"=>"Upload a picture to present your project",
            "title"=>"A project must have a title",
            "location"=>"Please specify the location",
            "finish"=>"Please specify the date of finish",
            "synopsis"=>"Please input the synopsis",
            "description"=>"Please input the description",
            "duration"=>"Please specify how many minutes the final output of the project lasts",
            "role"=>"To be in team, must have some occupations",
            "quantity"=>"Please input a positive integer",
            "budget_type"=>"Please specify the nature of this budget",
            "budget_comment"=>"Please leave some note about this budget.",
            "sponsor"=> "Please input your sponsor",
            "funds_date"=> "Please specify the date received this fund",
            "script_title"=>"A title is compulsive for a script",
            "script_link"=>"Link for text is compulsive for a script",
            "script_date"=>"Publication date is compulsive for a script.",
            "script_description"=>"Please write some notes about this version of script",
            "script_author" => "Specify authors of this script",
            "author_email"=> "Author must have a an email to contact.",
            "member_name"=> "Team member must have a an name to contact.",
            "member_email"=> "Team member must have a an email to contact.",
            "recruitment_description" => "Your must have some request ans rewards for this position",
            "motivation"=>"Oh, you must some motivation to join the team",
            "question_subject"=>"Subject is compulsive for a question.",
            "question_content"=>"Content is compulsive for a question.",
            "question_tag"=>"Tag is compulsive for a question",
            "report_tag"=>"Tag is compulsive for a report",
            "report_title"=>"Title is compulsive for a report",
            "report_synopsis"=>"Synopsis is compulsive for a report",
            "report_editor"=>"Content is compulsive for a report",
            "invitation" => "Content is compulsive for an invitation.",
        ],
        "invalid"=>[
            "finish"=>"Invalid date",
            "language"=>"This language is already in list.",
            "quantity"=>"Please input a positive integer",
            "question_content"=>"A text (15-4000 characters) is compulsive for a question"
        ],
        "unique"=>[
            "email"=>"User with the same email has already exists!"
        ],
        "minlength"=>[
            "synopsis"=>"Synopsis must be at least 40 characters.",
            "duration"=>"The final output of the project must lasts more than :cnt minutes",
            "description"=>"Description must be at least 200 characters.",
            "comment"=>"Comment must be at least 15 characters.",
            "script_link"=> "Link must be at least 4 characters.",
            "recruitment_description" => "It seems you don't have request about this position",
            "motivation"=>"Motivation must be at least :cnt characters",
            "question_subject"=>"Subject must be at least :cnt characters.",
            "question_content"=>"Content must be at least :cnt characters.",
            "report_title"=>"Report title must be at least :cnt characters",
            "report_synopsis"=>"Report synopsis must be at least :cnt characters",
            "report_editor"=>"Report content must be at least :cnt characters",
            "invitation" => "Invitation must be at least :cnt characters.",
        ],
        "maxlength"=>[
            "title"=>"Title may not be greater than 40 characters.",
            "synopsis"=>"Synopsis may not be greater than 256 characters.",
            "duration"=>"The final output of the project may not lasts more than :cnt minutes",
            "comment"=>"Comment may not be greater than 800 characters.",
            "budget_comment"=>"Memo of budget may not be greater than 40 characters.",
            "script_title"=> "Script's title may not be greater than 40 characters.",
            "script_link"=>"Link may not be greater than 200 characters.",
            "script_description"=>"Presentation of script description may not be greater than 400 characters.",
            "email"=> "Email may not be greater than 100 characters.",
            "recruitment_description"=> "It seems you don't have too mush request about this position",
            "motivation"=>"Motivation may not be greater than :cnt characters",
            "question_subject"=>"Subject may not be greater than :cnt characters",
            "question_content"=>"Content may not be greater than :cnt characters.",
            "report_title"=>"Report title may not be greater than :cnt characters",
            "report_synopsis"=>"Report synopsis may not be greater than :cnt characters",
            "report_editor"=>"Report content may not be greater than :cnt characters",
            "invitation" => "Invitation must may not be greater than :cnt characters.",
        ]
    ]
];