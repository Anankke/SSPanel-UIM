<?php

//基本设置---------------------------------------------------------------------------------------------------------------
$_ENV['key'] = 'ChangeMe';     // Cookie加密密钥，请务必修改此key为随机字符串
$_ENV['pwdMethod'] = 'bcrypt'; // 密码加密 可选 bcrypt, argon2i, argon2id
$_ENV['salt'] = '';            // bcrypt/argon2i/argon2id 会忽略此项

$_ENV['debug'] = false;                  // debug模式开关，生产环境请保持为false
$_ENV['appName'] = 'SSPanel-UIM';         // 站点名称
$_ENV['baseUrl'] = 'https://example.com'; // 站点地址，必须以https://开头，不要以/结尾

// WebAPI
$_ENV['webAPI'] = true;                // 是否开启WebAPI功能
$_ENV['webAPIUrl'] = $_ENV['baseUrl']; // WebAPI地址，如需和站点地址相同，请不要修改
$_ENV['muKey'] = 'ChangeMe';           // WebAPI密钥，用于节点服务端与面板通信，请务必修改此key为随机字符串
$_ENV['checkNodeIp'] = true;           // 是否webapi验证节点ip

//数据库设置--------------------------------------------------------------------------------------------------------------
// db_host|db_socket 二选一，若设置 db_socket 则 db_host 会被忽略，不用请留空
// db_host 例: localhost（可解析的主机名）, 127.0.0.1（IP 地址）
// db_socket 例：/var/run/mysqld/mysqld.sock（需使用绝对地址）
$_ENV['db_host'] = '';
$_ENV['db_socket'] = '';
$_ENV['db_database'] = 'sspanel'; // 数据库名
$_ENV['db_username'] = 'root';    // 数据库用户名
$_ENV['db_password'] = 'sspanel'; // 用户密码
$_ENV['db_port'] = '3306';        // 端口
#读写分离相关配置
$_ENV['enable_db_rw_split'] = false; // 是否开启读写分离
$_ENV['read_db_hosts'] = [''];       // 从库地址，可配置多个
$_ENV['write_db_host'] = '';         // 主库地址
#高级
$_ENV['db_charset'] = 'utf8mb4';
$_ENV['db_collation'] = 'utf8mb4_unicode_ci';
$_ENV['db_prefix'] = '';

//Redis设置--------------------------------------------------------------------------------------------------------------
$_ENV['redis_host'] = '127.0.0.1';    // Redis地址，使用unix domain socket时填写文件路径
$_ENV['redis_port'] = 6379;           // Redis端口，使用unix domain socket时填写-1
$_ENV['redis_db'] = 0;                // Redis数据库编号，留空则使用默认的0
$_ENV['redis_connect_timeout'] = 2.0; // Redis连接超时时间，单位秒
$_ENV['redis_read_timeout'] = 8.0;    // Redis读取超时时间，单位秒
$_ENV['redis_username'] = '';         // Redis用户名，留空则不使用用户名连接
$_ENV['redis_password'] = '';         // Redis密码，留空则无密码
$_ENV['redis_ssl'] = false;           // 是否使用SSL连接Redis，如果使用了SSL，那么Redis端口应为Redis实例的TLS端口
$_ENV['redis_ssl_context'] = [];      // 使用SSL时的上下文选项，参考 https://www.php.net/manual/zh/context.ssl.php
$_ENV['enable_redis_queue'] = false;

//Rate Limit 设置--------------------------------------------------------------------------------------------------------
$_ENV['enable_rate_limit'] = true;     // 是否开启请求限制
$_ENV['rate_limit_sub_ip'] = 10;       // 每分钟每个IP的订阅链接请求限制
$_ENV['rate_limit_sub'] = 10;          // 每分钟每个用户的订阅链接请求限制
$_ENV['rate_limit_webapi_ip'] = 120;   // 每分钟每个IP的WebAPI请求限制
$_ENV['rate_limit_webapi'] = 1200;     // 每分钟WebAPI全局请求限制
$_ENV['rate_limit_user_api_ip'] = 60;  // 每分钟每个IP的用户API请求限制
$_ENV['rate_limit_user_api'] = 60;     // 每分钟每个用户的API请求限制
$_ENV['rate_limit_admin_api_ip'] = 60; // 每分钟每个管理员的API请求限制
$_ENV['rate_limit_admin_api'] = 60;    // 每分钟每个管理员的API请求限制
$_ENV['rate_limit_node_api_ip'] = 60;  // 每分钟每个IP的节点API请求限制
$_ENV['rate_limit_node_api'] = 60;     // 每分钟每个节点的API请求限制

//邮件设置----------------------------------------------------------------------------------------------------------------
$_ENV['mail_filter'] = 0;        // 0: 关闭; 1: 白名单模式; 2; 黑名单模式;
$_ENV['mail_filter_list'] = [];

//已注册用户设置-----------------------------------------------------------------------------------------------------------
//TODO: move these settings to DB
#高级
$_ENV['class_expire_reset_traffic'] = 0; // 等级到期时重置为的流量值，单位GB，小于0时不重置
$_ENV['enable_kill'] = false;            // 是否允许用户注销账户
$_ENV['enable_change_email'] = true;     // 是否允许用户更改賬戶郵箱
#用户流量不足提醒
$_ENV['notify_limit_mode'] = false; // false为关闭，per为按照百分比提醒，mb为按照固定剩余流量提醒
$_ENV['notify_limit_value'] = 500;  // 当上一项为per时，此处填写百分比；当上一项为mb时，此处填写流量

//订阅设置----------------------------------------------------------------------------------------------------------------
$_ENV['Subscribe'] = true;          // 本站是否提供订阅功能
$_ENV['subUrl'] = $_ENV['baseUrl']; // 订阅地址，如需和站点名称相同，请不要修改
$_ENV['sub_token_len'] = 16;        // 订阅token长度

//审计自动封禁设置---------------------------------------------------------------------------------------------------------
//TODO: move these settings to DB
$_ENV['auto_detect_ban_allow_admin'] = true; // 管理员不受审计限制
$_ENV['auto_detect_ban_allow_users'] = [];   // 审计封禁的例外用户 ID
$_ENV['auto_detect_ban_number'] = 30;        // 每次执行封禁所需的触发次数
$_ENV['auto_detect_ban_time'] = 60;          // 每次封禁的时长 (分钟)

//节点检测---------------------------------------------------------------------------------------------------------------
//TODO: move these settings to DB
#GFW检测
$_ENV['detect_gfw_port'] = 443;                                                //所有节点服务器都打开的TCP端口
$_ENV['detect_gfw_url'] = 'https://example.com/v1/tcping?ip={ip}&port={port}'; // https://github.com/SSPanel-NeXT/NetStatus-API-Go
#离线检测
$_ENV['enable_detect_offline'] = true;

//高级设置---------------------------------------------------------------------------------------------------------------
$_ENV['enable_login_bind_ip'] = true;     //是否将登陆线程和IP绑定
$_ENV['enable_login_bind_device'] = true; //是否将登陆线程和设备绑定
$_ENV['rememberMeDuration'] = 7;          //登录时记住账号时长天数
$_ENV['timeZone'] = 'Asia/Shanghai';        //需使用 PHP 兼容的时区格式
$_ENV['theme'] = 'tabler';                //默认主题
$_ENV['locale'] = 'zh-CN';                //默认语言
$_ENV['jump_delay'] = 1000;               //跳转延时，单位ms
$_ENV['keep_connect'] = false;            // 流量耗尽用户限速至 1Mbps

//Other-----------------------------------------------------------------------------------------------------------------
// cdn.jsdelivr.net / fastly.jsdelivr.net / testingcf.jsdelivr.net
$_ENV['jsdelivr_url'] = 'fastly.jsdelivr.net';
// https://sentry.io for production debugging
$_ENV['sentry_dsn'] = '';
// Maxmind GeoIP2 database
//TODO: move these settings to DB
$_ENV['maxmind_account_id'] = '';
$_ENV['maxmind_license_key'] = '';
$_ENV['geoip_locale'] = 'en';
// ClientDownload 命令解决 API 访问频率高而被限制使用的 Github access token
$_ENV['github_access_token'] = '';
// use Cloudflare R2 for clients download
$_ENV['enable_r2_client_download'] = false;
$_ENV['r2_bucket_name'] = '';
$_ENV['r2_account_id'] = '';
$_ENV['r2_access_key_id'] = '';
$_ENV['r2_access_key_secret'] = '';
$_ENV['r2_client_download_timeout'] = 10;
// Clash Meta TCP Concurrency. Default true, false
$_ENV['tcp_concurrent'] = true;
// Custom DNS, sing-box DNS Server setting(Rule mode)
$_ENV['dns_select'] = 'google';            //Default mine_853, mine_443, google, cloudflare, opendns, alidns
//(direct)mine_853 default type tls, quic
$_ENV['dns_server_853'] = 'dns.google';     //The address of the DNS server. Default domain, ip
$_ENV['dns_server_port_853'] = 853;         //The port of the DNS server. Default 853
$_ENV['dns_type_853'] = 'tls';              //The type of the DNS server. Default tls, quic
//(direct)mine_443 default type https, h3
$_ENV['dns_server_443'] = 'dns.google';     //The address of the DNS server. Default domain, ip
$_ENV['dns_server_port_443'] = 443;         //The port of the DNS server. Default 443
$_ENV['dns_path_443'] = '/dns-query';       //The path of the DNS server. Default /dns-query
$_ENV['dns_type_443'] = 'https';            //The type of the DNS server. Default https, h3
