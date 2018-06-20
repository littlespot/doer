<?php
return [
    'HEADER' => [
        "prefix"=>  "您收到了",
        "sent"=>  "送出了",
        "got" =>  "有",
        "set" =>  "设定",
        'delete_notification' => "删除通知",
        'delete_application' => '从信箱中移除申请',
        'delete_invitation' => '从信箱中移除邀请',
        'delete_reminder' => '移除提醒',
         'refuse_invitation' => '拒绝邀请',
         'accept_invitation' => '接受邀请',
        'refuse_application' => '拒绝申请',
        'accept_application' => '接受申请'
    ],
    'LABELS' => [
        "refused"=>"拒了",
        "accepted"=>"应了",
        "wait"=>"未决",
        "replied"=>"已复",
        "invitations"=>"邀请",
        "as" => "作为",
        "participate" => "加入",
        "messages"=>"小纸条",
        "received_invitation"=>"收到的邀请",
        "sent_invitation"=>"寄出的邀请",
        "received_message"=>"收到的小纸条",
        "sent_message"=>"递出的小纸条",
        "read" => "展开",
        "envelop" => "收叠",
        "receivers" => "指明这张小纸条的归宿",
        "message_subject"=>"小纸条的主题（4到40个字符）",
        "replies"=>"<strong>:cnt</strong> 前文",
        "applications" => "申请信",
        "reminders" => "提醒",
        "remind" => "提醒其成员关于项目",
        "unreplied" => "未答复",
        "notification" => "通知",
        'application_received' => '收到的申请',
        'application_sent' => '发送的申请',
        'reminder_sent' => '发送的提醒',
        'check' => '查收',
        'reply' => '回复'
    ],
    'BUTTONS' => [
        'remind' => '设定提醒',
        'invite' => '写邀请',
        'message' => '递小纸条'
    ],
    'PLACES' => [
        'choose_project' => '选择需提醒的项目（4至40个字符）',
        'reminder_subject' => '简要填写提醒内容',
        "message_body"=>"填写小纸条的内容（10到800个字符）"
    ],
    'MESSAGES' => [
        'delete' => '您确定要删除选定的信息吗？',
        'refuse_invitation' => '您确定要拒绝这次邀请吗？',
        'accept_invitation' => '确认后您将自动进入所邀项目的团队。您确定要接受这次邀请吗？',
        'refuse_application' => '您确定要拒绝这封申请吗？',
        'accept_application' => '确认后对方将自动成为项目的团队。您确定要接受这封申请吗？',
    ],
    'ALERTS' => [
        'delete_reminder' => '被删除的提醒，在收件人那里将被标记为 「撤销」。',
        'delete_application' => '从收信箱中删除未确认的申请将默认为拒绝。',
        'delete_invitation' => '从收信箱中删除未确认的邀请将默认为拒绝。'
    ],
    'title' =>  [
        'add_script'=>'您的新脚本上线',
        'delete_script' => '您的脚本被移除',
        'delete_author' => '脚本将您从作者名单中移除'
    ],
    'body' =>  [
        'add_script'=>':user 为项目 《:project》 添加了剧本 《:script》',
        'delete_script' => ':user 从项目 《:project》 中移除了剧本 《:script》',
        'delete_author' => ':user 将您从剧本 《:script》（项目 《:project》）的作者名单中移除了'
    ],
    'ERRORS'=> [
        "projects" => "您必须有至少一个项目上线才能邀请其他用户。",
        'require_reminder_subject' => '请务必填写提醒的内容（4至40个字符）',
        'require_reminder_project' => '请务必选定要设定提醒的项目',
        'require_team' => '项目《:title》中没有其他成员，无法发送提醒',
        'require_message_subject' => '没有主题的小纸条无法寄出',
        'require_message_body' => '没有内容的小纸条无法寄出',
    ]
];