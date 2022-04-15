<?php

/*
    网站设置
*/

$_ENV['debug'] = false; // 生产环境需设为false
$_ENV['appName'] = 'sspanel-uim'; // 站点名称
$_ENV['key'] = '32150285b345c48aa3492f9212f61ca2'; // 修改为随机字符串
$_ENV['baseUrl'] = 'https://domain.com';// 站点地址

/*
    数据库设置
*/

// db_host|db_socket 二选一，若设置 db_socket 则 db_host 会被忽略，不用请留空。若数据库在本机上推荐用 db_socket
// db_host 例: localhost(可解析的主机名), 127.0.0.1(IP 地址), 10.0.0.2:4406(含端口)
// db_socket 例：/var/run/mysqld/mysqld.sock(需使用绝对地址)

$_ENV['db_host'] = 'localhost';
$_ENV['db_database'] = '';
$_ENV['db_username'] = '';
$_ENV['db_password'] = '';

$_ENV['db_socket'] = '';
$_ENV['db_prefix'] = '';
$_ENV['db_driver'] = 'mysql';
$_ENV['db_charset'] = 'utf8mb4';
$_ENV['db_collation'] = 'utf8mb4_unicode_ci';

/*
    支付设置
*/

$_ENV['active_payments'] = [
    'alipay_f2f' => [
        'name' => '支付宝',
        'min' => '10',
        'max' => '1000',
        'enable' => true,
    ],
    'universal' => [
        'name' => '',
        'min' => '10',
        'max' => '1000',
        'gateway' => '',
        'sign_key' => '',
        'enable' => true,
    ],
];

/*
    与 Web Api 有关的设置
*/

$_ENV['WebAPI'] = true;
$_ENV['muKey'] = '3a7caa4b32ffb47e7bb2d0ec7d097110'; // 通信密钥
$_ENV['muKeyList'] = []; // 多key列表
$_ENV['checkNodeIp'] = true; // 是否验证节点ip

$_ENV['enableAdminApi'] = false; // 是否启用 Admin API, 如果不知道此项用途请保持为 false
$_ENV['adminApiToken']  = '7cb4ddeaea0a1a7a42f351f71a28124a'; // Admin API 的 Token, 请生成为高强度的 Token

// 如下设置将使397，297号节点复用4号节点的流媒体解锁
$_ENV['streaming_media_unlock_multiplexing'] = [
    //'397' => '4',
    //'297' => '4',
];

/*
    注册用户行为限制
*/

$_ENV['enable_kill'] = false; // 是否允许用户主动删除账户
$_ENV['enable_change_email'] = false;  // 是否允许用户主动更改账户邮箱
$_ENV['enable_checkin'] = true; // 是否允许用户签到
$_ENV['enable_expired_checkin'] = true; // 是否允许过期用户签到
$_ENV['checkinMin'] = 100; // 签到可获得的最低流量(MB)
$_ENV['checkinMax'] = 300; // 签到可获得的最多流量(MB)
$_ENV['enable_ticket'] = true; // 是否开启工单系统
$_ENV['enable_docs'] = true; // 是否开启文档系统

/*
    与邮件相关设置
*/

$_ENV['sendPageLimit'] = 50; // 发信分页数
$_ENV['email_queue'] = true; // 邮件队列开关
$_ENV['mail_filter'] = 0; // 0关闭; 1白名单模式; 2黑名单模式
$_ENV['mail_filter_list'] = ['qq.com', 'vip.qq.com', 'foxmail.com'];
$_ENV['mail_ticket'] = true; // 是否开启工单邮件提醒
$_ENV['notify_limit_mode'] = true; // false为关闭，per为按照百分比提醒，mb为按照固定剩余流量提醒
$_ENV['notify_limit_value'] = 20; // 当上一项为per时，此处填写百分比；当上一项为mb时，此处填写流量

/*
    后端设置
*/

$_ENV['keep_connect'] = false; // 流量耗尽则限速1Mbps
$_ENV['disconnect_time'] = 60; // 在用户超过套餐连接IP数后多久才会拒绝新连接

$_ENV['min_port'] = 10000; // 0为不分配; 其他值时为分配起始端口
$_ENV['max_port'] = 60000; // 0为不分配; 其他值时为分配终止端口

$_ENV['v2ray_port'] = 443;
$_ENV['v2ray_level'] = 0;
$_ENV['v2ray_alter_id'] = 2;
$_ENV['v2ray_protocol'] = 'HTTP/2 + TLS';

/*
    Telegram bot
*/

// 变更这些参数均需要执行 php xcat Tool setTelegram
$_ENV['telegram_bot'] = ''; // 机器人用户名
$_ENV['telegram_token'] = ''; // 机器人token
$_ENV['telegram_chatid'] = ''; // 群组会话id
$_ENV['enable_telegram'] = false; // 机器人开关
$_ENV['use_new_telegram_bot'] = true; // 新版机器人开关
$_ENV['telegram_group_quiet'] = false; // 是否在群组中回应
$_ENV['telegram_request_token'] = '51d38e0819930dbdb808a5c3e65d08a9'; // 修改为随机字符串

// 功能设置
$_ENV['finance_public'] = false; // 财务报告是否向群公开
$_ENV['enable_welcome_message'] = true; // 机器人发送欢迎消息
$_ENV['enable_telegram_login'] = false; // 需配置并启用新版机器人开关
$_ENV['allow_to_join_new_groups'] = true; // 允许 Bot 加入下方配置之外的群组
$_ENV['group_id_allowed_to_join'] = []; // 允许加入的群组 ID
$_ENV['telegram_admins'] = []; // 额外的 Telegram 管理员 ID
$_ENV['delete_message_time'] = 180; // 0为关闭; 其他数值为在此时间后删除用户触发的 bot 回复
$_ENV['delete_admin_message_time'] = 86400; // 0为关闭; 其他数值为在此时间后删除管理命令触发的 bot 回复
$_ENV['enable_delete_user_cmd'] = false; // 删除用户触发的 bot 回复功能开关
$_ENV['help_any_command'] = false; // 其他未知命令触发 /help 回复
$_ENV['enable_user_email_group_show'] = false; // false时隐藏用户完整邮箱

/*
    订阅设置
*/

$_ENV['Subscribe'] = true; // 本站是否提供订阅功能
$_ENV['subUrl'] = $_ENV['baseUrl'] . '/link/'; // 订阅地址，如需和站点名称相同，请不要修改
$_ENV['mergeSub'] = true; // 合并订阅设置 可选项 false / true
$_ENV['enable_sub_extend'] = true; // 是否开启订阅中默认显示流量剩余以及账户到期时间以及 sub_message 中的信息
$_ENV['enable_forced_replacement'] = true; // 用户修改账户登录密码时，是否强制更换订阅地址
$_ENV['sub_message'] = []; // 订阅中的营销信息，使用数组形式，将会添加在订阅列表的顶端，可用于为用户推送最新地址等信息，尽可能简短且数量不宜太多
$_ENV['disable_sub_mu_port'] = false; // 将订阅中单端口的信息去除
$_ENV['subscribeLog'] = true; // 是否记录用户订阅日志
$_ENV['subscribeLog_show'] = true; // 是否允许用户查看订阅记录
$_ENV['subscribeLog_keep_days'] = 7; // 订阅记录保留天数
$_ENV['mu_port_migration'] = false; // 为后端直接下发偏移后的端口
$_ENV['add_emoji_to_node_name'] = false; // 为部分订阅中默认添加 emoji
$_ENV['add_appName_to_ss_uri'] = true; // 为 SS 节点名称中添加站点名
$_ENV['subscribe_client'] = true; // 下载协议客户端时附带节点和订阅信息
$_ENV['subscribe_client_url'] = ''; // 使用独立的服务器提供附带节点和订阅信息的协议客户端下载，为空表示不使用
$_ENV['Clash_DefaultProfiles'] = 'default'; // Clash 默认配置方案
$_ENV['Surge_DefaultProfiles'] = 'default'; // Surge 默认配置方案
$_ENV['Surge2_DefaultProfiles'] = 'default'; // Surge2 默认配置方案
$_ENV['Surfboard_DefaultProfiles']  = 'default'; // Surfboard 默认配置方案

/*
    注册设置
*/

$_ENV['random_group'] = '0'; // 注册时随机分配到的分组，英文半角逗号分隔
$_ENV['enable_reg_im'] = false; // 注册时是否要求用户输入IM联系方式
$_ENV['reg_forbidden_ip'] = '127.0.0.0/8,::1/128'; // 注册时默认禁止访问IP列表，英文半角逗号分隔
$_ENV['reg_forbidden_port'] = ''; // 注册时默认禁止访问端口列表，英文半角逗号分隔，支持端口段
$_ENV['mu_suffix'] = 'microsoft.com'; // 单端口多用户混淆参数后缀，可以随意修改，但请保持前后端一致
$_ENV['mu_regex'] = '%5m%id.%suffix'; // 单端口多用户混淆参数表达式，%5m代表取用户特征 md5 的前五位，%id 代表用户id, %suffix 代表上面这个后缀

/*
    第三方服务
*/

// cloudflare.com
$_ENV['cloudflare_enable'] = false; // 是否开启 Cloudflare 解析
$_ENV['cloudflare_email'] = ''; // Cloudflare 邮箱地址
$_ENV['cloudflare_key'] = ''; // Cloudflare API Key
$_ENV['cloudflare_name'] = ''; // 域名

// sentry.io
$_ENV['sentry_dsn'] = '';

// github.com
$_ENV['github_access_token'] = '';

/*
    杂项
*/

$_ENV['authDriver'] = 'cookie'; // 不能更改
$_ENV['pwdMethod'] = 'md5'; // md5,sha256,bcrypt,argon2i,argon2id
$_ENV['salt'] = ''; // 加盐仅支持 md5,sha256
$_ENV['tokenDriver'] = 'db';
$_ENV['cacheDriver'] = 'cookie';
$_ENV['sessionDriver'] = 'cookie';
$_ENV['theme'] = 'material'; // 默认主题
$_ENV['timeZone'] = 'PRC'; // PRC / UTC
$_ENV['jump_delay'] = 1200;
$_ENV['enable_login_bind_ip'] = true; // 是否将登陆线程和IP绑定
$_ENV['cookie_expiration_time'] = 1; // cookie 过期时间
$_ENV['php_user_group'] = 'www:www';

/*
    获取客户端地址
*/

$_ENV['cdn_forwarded_ip'] = array('HTTP_X_FORWARDED_FOR', 'HTTP_ALI_CDN_REAL_IP', 'X-Real-IP', 'True-Client-Ip');
foreach ($_ENV['cdn_forwarded_ip'] as $cdn_forwarded_ip) {
    if (isset($_SERVER[$cdn_forwarded_ip])) {
        $list = explode(',', $_SERVER[$cdn_forwarded_ip]);
        $_SERVER['REMOTE_ADDR'] = $list[0];
        break;
    }
}
