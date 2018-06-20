<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */
    "HEADER" =>[
        "title" => "用户中心",
        "alert" => "完善个人信息，以待合作伙伴",
        "active" => "请填写本页所有带*号的信息并上传个人头像，才能正式开通您的账户",
        "contact" => "完善联系信息",
        "film" => '如需投报电影节，还需继续填写联系方式中所有带*号的内容'
    ],
    "TABS" => [
        "information" => "基本信息",
        "contact" => "联系方式",
        "password" => "修改密码",
        "social" => "社交账号",
       /* "newsletter"=> "我的订阅"*/
    ],
    "QUESTION" => [
        'asks' => '提出了 <span class="text-important">:cnt</span> 个问题',
        'answers' => '贡献了 <span class="text-important">:cnt</span> 个答案',
        "follows" => "关注了 <span class='text-important'>:cnt</span> 个问题",
        "supports" => "顶赞了 <span class='text-important'>:cnt</span> 个答案"
    ],
    "PROJECTS" => [
        'creator' => '创建的',
        'participator' => '参与的',
        "follower" => "关注的",
        "lover" => "喜爱的"
    ],
    'TITLES' =>[
        'mr' => '先生',
        'ms' => '女士',
    ],
    'BUTTONS' => [
        'change_contact' => '修改联系方式',
        'complete_information' => '先去激活账户>>'
    ],
    'LABELS' => [
        'name' => '姓名',
        'title' => '称呼',
        'first_name' => '名',
        'last_name' => '姓',
        'middle_name' => '教名：',
        'birthday' => '生日',
        'born' => '出生年份',
        'sex' => '性别',
        'nationality' => '国籍',
        'fix' => '座机',
        'mobile' => '手机',
        'country_code' => '国家/地区号',
        'fix_number'=> '座机号码',
        'mobile_number'=> '手机号码',
        'email' => '邮箱',
        'address_book' => '地址簿',
        'address_name' => '地址名称',
        'address' => '地址',
        'postal' => '邮编',
        'city' => '城市',
        'state' => '省/地区',
        'country' => '国家',
        'web' => '个人网址',
        'personal' => '个人情况',
        'contact' => '联系方式',
        'company' => '公司名称',
        'username' => '用户名',
        'occupation' => '添加技能',
        'description' => '自我介绍'
    ],
    'PLACES' =>[
        'title' => '称呼',
        'first_name' => '名',
        'last_name' => '姓',
        'username' => '每30天只能更换一次用户名',
        'username_datediff' => '还有:cnt天才能再更换用户名',
        'talents' => '才能',
        'location' => '住地',
        'birthday' => '生日',
        'sex' => '性别',
        'address' => '具体地址（不用写城市，不超过200个字符）',
        'country_code' => '国家/地区号',
        'phone' => '例：123456789',
        'description' => '简要地介绍自己（性格、兴趣、才华……）',
        'occupation' => '选择技能',
        'postal' => '邮编',
        'city' => '城市',
        'state' => '省/直辖市/自治区',
        'region' => '国家/地区',
        'address' => '街道门牌',
        'address_name' => '给地址一个名字以便管理地址本',
        'sns_name' => '输入您在该网站的用户名',
        'description' => '这个人没有留下任何介绍。'
    ],
    'SEX' => [
        's' => '保密',
        'm' => '男',
        'f' => '女',
        'o' => '其他'
    ],
    'ALERTS' => [
        'contact' => '联系信息已修改',
        'info_changed' => "您的个人信息已成功修改",
        'pwd_changed' => "您的密码已成功修改",
        'contact_changed' => '您的联系信息已成功修改',
        'page_jump' => '页面将在10秒后自动跳转。',
        'phone' => '至少填写一个电话号码'
    ],
    'MESSAGES' =>[
        'page_jump' => '按“确认”立刻跳转回前一页。按“取消”留在当前页',
        'delete_sns' => '您确定要删除这个社交账号吗？'
    ],
    'ERRORS'=> [
        'require_days' => ':cnt天后才能再更换',
        'require_prefix' => '务必选择一个称呼',
        'require_occupation' => '务必填写一项技能',
        'require_location' => '务必填写居住城市',
        'require_description' => '请简短地介绍自己',
        'repeat_address_name' => '该地址名已存在，请另填一个',
        'require_address_name' => '务必给地址设定一个名称',
        'maxlength_address_name' => '地址名不能超过:cnt个字符',
        'maxlength_address' => '必须填写详细地址（不超过:cnt个字符）',
        'maxlength_presentation' => '自我介绍不能超过:cnt个字符',
        'require_username' => '务必填写用户名（不超过:cnt个字符）',
        'require_avatar' => '请先上传个人头像',
        'require_name' => '必须填写名字才能提交联系信息',
        'maxlength_sns_name' => '用户名不能超过:cnt个字符'
    ]
];
