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
    'card' => [
        'title' => '标题',
        'duration' => '出品日期 / 片长',
        'language' => '出品国家（地区） / 语言',
        'shooting' => '拍摄格式',
        'screen' => '放映格式',
        'genre' => '类型',
        'synopsis' => '梗概',
        'director' => '导演',
        'credits' => '演职名单',
        'producer' => '制片',
        'market' => '发行/版权',
        'attachments' => '附件'
    ],
    'alert' => [
        'title' => '标题中如有冠词，按： <b><u>The</u> Title</b> 格式登记。',
        'duration' =>'通常视第一个放映拷贝准备完成的时间为影片的<u>完成日期</u>。若影片仍在制作中，请预估并填写其最终的完成日期。<br/>同理，若影片仍在剪辑中，请预估并填写最终的放映时长',
        'nation' => '按照国家在影片制作中的重要程度顺序填写。<b>拍摄地点</b> 和 <b>对白语言</b> 是电影需要的重要信息。',
        'shooting' => '选择与填写影片的拍摄和制作手法。',
        'screen1' => '这对于电影节申请是极其重要的参数。',
        'screen2' => '基于放映设施，各电影节接受的放映格式迥异。',
        'screen3' => '请声明影片在电影节上的放映格式（如果您的影片被选中）。',
        'genre1' => '每一栏至多能勾选两个选项。',
        'genre2' => '例: 动画/剧情 意为影片由动画场景（一图一图组成）和真人场景构成。',
        'synopsis' => '梗概是影片的门面，请确保其拼写及翻译语法正确、无语法错误。',
        'step1' => 'Fill out complete film form(title, country, credits, synopsis, photo, etc.)',
        'step2' => 'Upload your video (preview copy). Then playback your film to check it is correct.',
        'step3' => 'Submit your film to a festival. Then follow progress of your submission status.',
        'history' => '如果本片曾在电影节、电视台、或影院放映，请具述。',
        'attachment' => '照片和图片只能使用 JPEG 或 PNG 格式及 RGB 模式。 文件大小不能超过2M。',
        'picture' => ' JPEG 或 PNG 格式；不超过2M； 200 DPI',
        'document' => '.pdf， .txt， .rtf， .doc，.docx 或 .odt 文件；不超过2M。',
        'media' => ''
    ],
    'tip' => [
        'location' => '主要取景国家',
        'computer' => '如果影片有电脑合成',
        'animation' => "如果这是一部动画片",
        'TAPE' => '如：Mini DV / HDCAM / Betacam 等',
        'other' => '如：SONY PXW-FS7 / Canon EOS C300 等'
    ],
    'placeholder' =>[
        'title_original' => '花样年华',
        'title_latin' => 'Faa yeung nin wa',
        'title_inter' => 'In the Mood for Love',
        'title_trans' =>  'Любовное настроение',
        'search' => '我的影人库',
        'book' => '我的地址库',
        'other' => '请选择其他制作国家/地区（至多五个）',
        'shooting' => '请选择拍摄国家/地区（至多九个）',
        'dialog' => '请选择对白/旁白使用的主要语言',
        'other_lang' => '可填写一种其他语言（如：克林贡语）',
        'principal' => '只能选择一个，请谨慎填写',
        'size' => 'ex:300M或20G。如果只填数字，则默认以G为单元',
        'cineFormat' => '胶片规格',
        'playFormat' => '视频规格',
        'ratio' => '屏幕比例',
        'sound' => '放映声音',
        'language' => '选择语言',
        'standard' => '选择标准',
        'city' => '选择城市',
        'year' => '选择年份',
        'country' => '选择国家',
        'channel' => '选择节目类型',
    ],
    'label' => [
        'title_original' => '原文标题<sup>*</sup>：',
        'title_latin' => '罗马拼音：',
        'title_inter' => '国际标题：',
        'title_trans' => '标题翻译：',
        'date_complete' => '完成日期<sup>*</sup>：',
        'duration' => '片长<sup>*</sup>：',
        'nation' => '制片国家/地区',
        'nation_principal' => '主要制片国家/地区<sup>*</sup>：',
        'nation_other' => '其他制片国家/地区：',
        'nation_shooting' => '拍摄国家/地区：',
        'dialogue' => '对白<sup>*</sup>：',
        'commentaries' => '旁白<sup>*</sup>：',
        'silent' => '默片<sup>*</sup>：',
        'dialogue_language' => '语言：',
        'film_format' => '胶片格式：',
        'video_format' => '数字格式：',
        'software' => '使用的软件：',
        'animation' => '动画技术：',
        'process' => '色彩<sup>*</sup> ：',
        'special' => '制式<sup>*</sup> ：',
        'color' => '彩色',
        'wb' => '黑白',
        'cwb' => '彩色 / 黑白',
        'digital_files' => '数字格式',
        'film_print' => '电影胶片',
        'video_copy' => '视频拷贝',
        'ratio' => '比例',
        'sound' => '声音',
        'resolution' => '分辨率：',
        'size' => '大小',
        'standard' => '标准',
        'fps' => '每秒帧数',
        'format'=> '格式',
        'genre' => '类型<sup>*</sup>：',
        'style' => '风格：',
        'subject' => '主题：',
        'reel' => '卷',
        'subtitle' => '字幕',
        'subbed' => '字幕及配音',
        'dubbed' => '配音',
        'summary' => '中文简介',
        'summary_trans' => '梗概翻译',
        'name_book' => '已有人员：',
        'prefix' => '称呼：',
        'virgin' => '首部电影<sup>*</sup>：',
        'school' => '学校电影<sup>*</sup>：',
        'school_name' => '学校名：',
        'function' => '职能：',
        'seller' => '发行人：',
        'music_original' => '原创音乐：',
        'script_original' => '原创剧本：',
        'music_rights' => '音乐版权都清了<sup>*</sup>：',
        'film_rights' => '影片的国际版权可用<sup>*</sup>： ',
        'history_festival'=> '影片参加过电影节',
        'history_tv' => '影片曾在电视/DVD/网络发行',
        'history_theatre' => '影片参加曾在影院放映',
        'year' => '时间',
        'event' => '活动名称',
        'competition' => '竞赛单元',
        'award' => '奖项',
        'name_tv' => '电视/DVD/网络名称',
        'channel' => '渠道',
        'program' => '单元',
        'name_program' => '单元名称',
        'distributed' => '发行方',
        'contact' => '联系方式',
        'film_photo' => '电影照片<sup>*</sup>：',
        'film_poster' => '海报：',
        'other_file' => '文本<br/>（台本，剧本，等）',
        'format_accepted' => '可用格式',
        'parameter_recom' => '参数推荐',
        'format_recom' => '推荐格式',
        'size_maxium' => '最大容量',
        'video' => '视频',
        'video_codec'=>'视频编码器：',
        'frame_rate' => '帧率：',
        'data_rate' => '数据率：',
        'audio' => '音频',
        'audio_codec' => '音频编解码器：',
        'sample_rate' => '采样率：',
        'inlaid_subtitle' => '视频内嵌字幕',
        'version' => '影片进度',
        'preview' => '预览',
        'final' => '成片',
        '360' => '使用全景摄影机拍摄：',
        'other_title' => '又名：',
        'no_dialog' => '无对白而且无旁白',
        'has_dialog' => '有对白或者有旁白',
        'english' => '英语：'
    ],
    'channel'=>[
        'TV' => '电视台',
        'DVD' => 'DVD',
        'internet' => '网站'
    ],
    'program'=>[
        'short' => '短片',
        'before' => '贴片',
        'feature' => '长片'
    ],
    'header'=>[
        'reel_nbr' => '卷数：',
        'reel_length' => '卷长：',
        'inlaid_subtitle' => '内嵌字幕',
        'new' => '新增影片',
        'my'=>'我的影片',
        'subtitle' => '字幕/配音',
        'rights' => '版权',
        'history' => '放映史',
        'cast' => '名单'
    ],
    'place'=>[
        'lang' => '选择语言',
        'synopsis'=>'填写至多:cnt字的梗概'
    ],
    'declaration' =>[
        'copyright' => "我声明本人乃本片版权所有者或本片版权所有者指定的代理者 <sup>*</sup>"
    ],
    'buttons' => [
        'add' => '添加影片',
        'upload' => '上传 （预览/成片）',
        'credit' => '添加演职人员'
    ],
    'progress' => [
        'form_completed' => '已完成:cnt部影片的表格',
        'form_tocomplete' => '尚有:cnt部影片的表格待完成',
        'copy_uploaded' => '已有:cnt份拷贝上传',
        'copy_toupload' => '尚有:cnt份拷贝待上传',
        'submission_tofinish' => ':cnt份申请待提交',
        'submission_forward' => ':cnt份申请已交送电影节',
        'submission_confirmed' => ':cnt份申请被电影节接受',
        'submission_canceled' => ':cnt份申请被电影节取消',
        'film_selected' => ':cnt部影片进入竞赛单元',
        'film_unselected' => ':cnt部影片未进入竞赛单元',
        'film_another' => ':cnt部影片被选入电影节其他单元',
        'film_award' => ':cnt部影片得到奖项或提名',
    ],
    'show'=>[
        'title_latin' => '罗马拼音'
    ],
    'error' => [
        'require_title' => '必须填写影片的原标题才能进行下一步',
        'maxlength_title' => '影片的标题不能超过:cnt个字符',
        'require_rights' => '您必须是版权所有者或者版权所有者指定的代理者，才能添加影片'
    ]
];