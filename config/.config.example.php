<?php

//基本设置--------------------------------------------------------------------------------------------
$_ENV['key']        = 'ChangeMe';                     //请务必修改此key为随机字符串
$_ENV['pwdMethod']  = 'bcrypt';                       //密码加密 可选 md5, sha256, bcrypt, argon2i, argon2id（argon2i需要至少php7.2）
$_ENV['salt']       = '';                             //推荐配合 md5/sha256， bcrypt/argon2i/argon2id 会忽略此项

$_ENV['debug']      = false;                          //debug模式开关，生产环境请保持为false
$_ENV['appName']    = 'SSPanel-UIM';                  //站点名称
$_ENV['baseUrl']    = 'https://example.com';          //站点地址
$_ENV['muKey']      = 'ChangeMe';                      //WebAPI密钥，用于节点服务端与面板通信，请务必修改此key为随机字符串

//数据库设置--------------------------------------------------------------------------------------------
// db_host|db_socket 二选一，若设置 db_socket 则 db_host 会被忽略，不用请留空。若数据库在本机上推荐用 db_socket。
// db_host 例: localhost（可解析的主机名）, 127.0.0.1（IP 地址）, 10.0.0.2:4406（含端口)
// db_socket 例：/var/run/mysqld/mysqld.sock（需使用绝对地址）
$_ENV['db_driver']    = 'mysql';
$_ENV['db_host']      = '';
$_ENV['db_socket']    = '';
$_ENV['db_database']  = 'sspanel';           //数据库名
$_ENV['db_username']  = 'root';              //数据库用户名
$_ENV['db_password']  = 'sspanel';           //用户名对应的密码
$_ENV['db_port']      = '3306';              //端口
#高级
$_ENV['db_charset']   = 'utf8mb4';
$_ENV['db_collation'] = 'utf8mb4_unicode_ci';
$_ENV['db_prefix']    = '';

//流媒体解锁 如下设置将使397，297号节点复用4号节点的检测结果 使用时去掉注释符 //
$_ENV['streaming_media_unlock_multiplexing'] = [
    //'397' => '4',
    //'297' => '4',
];

//邮件设置--------------------------------------------------------------------------------------------
$_ENV['mail_filter']        = 0;            //0: 关闭; 1: 白名单模式; 2; 黑名单模式;
$_ENV['mail_filter_list']   = array("qq.com", "vip.qq.com", "foxmail.com");

//已注册用户设置---------------------------------------------------------------------------------------
#基础
$_ENV['enable_checkin']             = true;         //是否啓用簽到功能
$_ENV['checkinMin']                 = 1;            //用户签到最少流量 单位MB
$_ENV['checkinMax']                 = 50;           //用户签到最多流量

$_ENV['auto_clean_uncheck_days']    = -1;           //自动清理多少天没签到的0级用户，小于等于0时关闭
$_ENV['auto_clean_unused_days']     = -1;           //自动清理多少天没使用的0级用户，小于等于0时关闭
$_ENV['auto_clean_min_money']       = 1;            //余额低于多少的0级用户可以被清理

$_ENV['enable_bought_reset']        = true;         //购买时是否重置流量
$_ENV['enable_bought_extend']       = true;         //购买时是否延长等级期限（同等级配套）

#高级
$_ENV['class_expire_reset_traffic'] = 0;            //等级到期时重置为的流量值，单位GB，小于0时不重置
$_ENV['account_expire_delete_days'] = -1;           //账户到期几天之后会删除账户，小于0时不删除

$_ENV['enable_kill']                = true;         //是否允许用户注销账户
$_ENV['enable_change_email']        = true;         //是否允许用户更改賬戶郵箱

#用户流量余量不足邮件提醒
$_ENV['notify_limit_mode']          = false;         //false为关闭，per为按照百分比提醒，mb为按照固定剩余流量提醒
$_ENV['notify_limit_value']         = 20;           //当上一项为per时，此处填写百分比；当上一项为mb时，此处填写流量

//日志设置---------------------------------------------------------------------------------------
$_ENV['trafficLog']               = false;                          //是否记录用户每小时使用流量
$_ENV['trafficLog_keep_days']     = 14;                             //每小时使用流量记录保留天数

$_ENV['subscribeLog']               = false;                        //是否记录用户订阅日志
$_ENV['subscribeLog_keep_days']     = 7;                            //订阅记录保留天数

//订阅设置---------------------------------------------------------------------------------------
$_ENV['Subscribe']                  = true;                         //本站是否提供订阅功能
$_ENV['subUrl']                     = $_ENV['baseUrl'];             //订阅地址，如需和站点名称相同，请不要修改

//审计自动封禁设置--------------------------------------------------------------------------------------------
$_ENV['enable_auto_detect_ban']      = false;       // 审计自动封禁开关
$_ENV['auto_detect_ban_numProcess']  = 300;         // 单次计划任务中审计记录的处理数量
$_ENV['auto_detect_ban_allow_admin'] = true;        // 管理员不受审计限制
$_ENV['auto_detect_ban_allow_users'] = [];          // 审计封禁的例外用户 ID

// 审计封禁判断类型：
//   - 1 = 仁慈模式，每触碰多少次封禁一次
//   - 2 = 疯狂模式，累计触碰次数按阶梯进行不同时长的封禁
$_ENV['auto_detect_ban_type']        = 1;
$_ENV['auto_detect_ban_number']      = 30;             // 仁慈模式每次执行封禁所需的触发次数
$_ENV['auto_detect_ban_time']        = 60;             // 仁慈模式每次封禁的时长 (分钟)

// 疯狂模式阶梯
// key 为触发次数
//   - type：可选 time 按时间 或 kill 删号
//   - time：时间，单位分钟
$_ENV['auto_detect_ban'] = [
    100 => [
        'type' => 'time',
        'time' => 120
    ],
    300 => [
        'type' => 'time',
        'time' => 720
    ],
    600 => [
        'type' => 'time',
        'time' => 4320
    ],
    1000 => [
        'type' => 'kill',
        'time' => 0
    ]
];

//Bot 设置--------------------------------------------------------------------------------------------
# Telegram bot
$_ENV['enable_telegram']                    = false;        //是否开启 Telegram bot
$_ENV['telegram_token']                     = '';           //Telegram bot,bot 的 token ，跟 father bot 申请
$_ENV['telegram_chatid']                    = -111;         //Telegram bot,群组会话 ID,把机器人拉进群里之后跟他 /ping 一下即可得到
$_ENV['telegram_bot']                       = '_bot';       //Telegram 机器人账号
$_ENV['telegram_request_token']             = '';           //Telegram 机器人请求Key，随意设置，由大小写英文和数字组成，更新这个参数之后请 php xcat Tool setTelegram

//节点检测-----------------------------------------------------------------------------------------------
#GFW检测，请通过crontab进行【开启/关闭】
$_ENV['detect_gfw_interval']             = 3600;                                                               //检测间隔，单位：秒，低于推荐值会爆炸
$_ENV['detect_gfw_port']                 = 22;                                                                 //所有节点服务器都打开的TCP端口，常用的为22（SSH端口）
$_ENV['detect_gfw_url']                  = 'http://cn-sh-tcping.sspanel.org:8080/tcping?ip={ip}&port={port}'; //检测节点是否被gfw墙了的API的URL
$_ENV['detect_gfw_count']                = '3';                                                                //尝试次数

#离线检测
$_ENV['enable_detect_offline']           = true;

//以下所有均为高级设置（一般用不上，不用改---------------------------------------------------------------------

// 主站是否提供 WebAPI
// - 为了安全性，推荐使用 WebAPI 模式对接节点并关闭公网数据库连接。
// - 如果您全部节点使用数据库连接或者拥有独立的 WebAPI 站点或 gRPC API，则可设为 false。
$_ENV['WebAPI']     = true;

#杂项
$_ENV['authDriver']             = 'cookie';            //不能更改此项
$_ENV['sessionDriver']          = 'cookie';            //可选: cookie
$_ENV['cacheDriver']            = 'cookie';            //可选: cookie
$_ENV['tokenDriver']            = 'db';                //可选: db

$_ENV['enable_login_bind_ip']   = false;        //是否将登陆线程和IP绑定
$_ENV['rememberMeDuration']     = 7;           //登录时记住账号时长天数

$_ENV['timeZone']               = 'PRC';                 //PRC 天朝时间  UTC 格林时间
$_ENV['theme']                  = 'tabler';              //默认主题
$_ENV['jump_delay']             = 1200;                  //跳转延时，单位ms，不建议太长

$_ENV['checkNodeIp']            = true;                 //是否webapi验证节点ip
$_ENV['keep_connect']           = false;               // 流量耗尽用户限速至 1Mbps

#Cloudflare
$_ENV['cloudflare_enable']      = false;         //是否开启 Cloudflare 解析
$_ENV['cloudflare_email']       = '';            //Cloudflare 邮箱地址
$_ENV['cloudflare_key']         = '';            //Cloudflare API Key
$_ENV['cloudflare_name']        = '';            //域名

#是否夹带统计代码，自己在 resources/views/{主题名} 下创建一个 analytics.tpl ，如果有必要就用 literal 界定符
$_ENV['enable_analytics_code']  = false;

#在套了CDN之后获取用户真实ip，如果您不知道这是什么，请不要乱动
$_ENV['cdn_forwarded_ip'] = array('HTTP_X_FORWARDED_FOR', 'HTTP_ALI_CDN_REAL_IP', 'X-Real-IP', 'True-Client-Ip');
foreach ($_ENV['cdn_forwarded_ip'] as $cdn_forwarded_ip) {
    if (isset($_SERVER[$cdn_forwarded_ip])) {
        $list = explode(',', $_SERVER[$cdn_forwarded_ip]);
        $_SERVER['REMOTE_ADDR'] = $list[0];
        break;
    }
}

// https://sentry.io for production debugging
$_ENV['sentry_dsn'] = '';

// Maxmind GeoIP2 database
$_ENV['maxmind_license_key'] = '';
$_ENV['geoip_locale'] = 'en';

// ClientDownload 命令解决 API 访问频率高而被限制使用的 Github access token
$_ENV['github_access_token'] = '';

$_ENV['php_user_group'] = 'www:www';
