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
    'HEADERS' => [
        'entry_info' => '报名信息',
        'entry_way' => '如何投报电影节',
        'my_contact' => '联系信息',
        'film_completed' => '<a href="/archives" class="btn-link">:cnt</a> 部作品已完成',
        'film_progress' => '<a href="/archives" class="btn-link text-danger">:cnt</a> 部作品未完成',
        'film_honored' => '<a href="/archives" class="btn-link">:cnt</a> 部作品已入围',
        'film_rewarded' => '<a href="/archives" class="btn-link">:cnt</a> 部作品已获奖',
        'entry_sending' => '<a href="/entries" class="btn-link">:cnt</a> 份报名表正在投至电影节',
        'entry_received' => '<a href="/entries" class="btn-link">:cnt</a> 份报名表已收到确认',
        'entry_process' => [
            '1' => '创建作品档案',
            '2' => '上传作品',
            '3' => '发现心仪的电影节',
            '4' => '点击报名，完成报名'
        ],
        'presentation' => '介绍',
        'rules' => '要求',
        'entry_time' => '报名时间',
        'honors' => '入围获奖情况',
        'entries_all' => '所有报名表',
        'entries_inpayed' => '未支付的',
        'entries_outdated' => '已失效的'
    ],
    'COMPETITIONS' => [
        'INT' => '国际竞赛',
        'NAT' => '国内竞赛',
        'MANDARIN' => '华语竞赛'
    ],
    'SHOWS' => [
        'INT' => '国际展映',
        'NAT' => '国内展映',
        'MANDARIN' => '华语展映',
        'KILL' => '特别展映'
    ],
    'LABELS' => [
        'name' => '姓名',
        'phone' => '电话',
        'fix' => '座机',
        'mobile' => '手机',
        'mail' => '邮箱',
        'address' => '住址',
        'nearest_deadline' => '最近截止日期',
        'genre' => '类型',
        'location' => '举办地',
        'opening' => '举办时间',
        'script' => '剧本',
        'session' => '第 :cnt 届',
        'unit_cnt' => '共有 :cnt 个单元征片中',
        'entry_start' => '报名开始',
        'entry_end' => '报名截止',
        'choose_film' => '请选择您的作品',
        'official_rules' => '官方要求',
        'order_id' => '订单号',
        'receipt_id' => '回执号',
        'entry_film' => '报名作品',
        'entry_unit' => '报名单元',
        'entry_fee' => '报名费用',
        'entry_status' => '投报状态'
    ],
    'TIPS' => [
        'receipt_number' => '回执号是电影节组委会在收到您的报名后，提供的确认号码',
        "follow"=>"收藏电影节",
        "unfollow"=>"取消收藏",
    ],
    'PLACES'=>[

    ],
    'MESSAGES' =>[
        'copyright' => "我声明本人乃本片版权所有者或本片版权所有者指定的代理者 <sup>*</sup>",
        'entry_term' => "我声明我已阅读并同意此电影节本单元的报名规则",
        'rules_invalid' => '您的作品以下方面不符合电影节报名要求',
        'change_film' => '去更新作品资料 >>',
        'incompleted' => '资料不完整'
    ],
    'BUTTONS' => [
        'tocomplete' => '去完成',
        'complete_contact' => '去填写联系信息>>',
        'change_contact' => '修改联系信息',
        'all_festival' => '全部电影节',
        'script_festival' => '接收剧本的电影节',
        'favorite_festival' => '我关注的',
        'festival_closing' => '即将截止报名',
        'festival_open' => '长期开放报名',
        'festival_opening' => '开放报名',
        'festival_closed' => '未开放报名',
        'entry' => '报名',
        'view' => '查看电影节',
        'pdf' => '介绍PDF',
        'presentation' => '查看单元',
        'collapse' => '展开',
        'fold' => '折叠',
        'go_edit'=>'继续编辑',
        'go_create' => '去创建',
        'go_pay' => '去支付'
    ],
    'ERRORS' => [
        'no_archive' => '您还没有作品',
        'no_play' => '您还没有剧本作品',
        'no_movie' => '您还没有影片作品',
        'require_entry_term' => '提交前请先同意此电影节本单元的报名规则。'
    ]
];
