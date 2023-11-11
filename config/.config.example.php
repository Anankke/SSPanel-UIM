<?php

//基本设置--------------------------------------------------------------------------------------------
$_ENV['key']        = 'ChangeMe';                     //Cookie加密密钥，请务必修改此key为随机字符串
$_ENV['pwdMethod']  = 'bcrypt';                       //密码加密 可选 bcrypt, argon2i, argon2id
$_ENV['salt']       = '';                             //bcrypt/argon2i/argon2id 会忽略此项

$_ENV['debug']      = false;                          //debug模式开关，生产环境请保持为false
$_ENV['appName']    = 'SSPanel-UIM';                  //站点名称
$_ENV['baseUrl']    = 'https://example.com';          //站点地址，必须以https://开头，不要以/结尾

// WebAPI
$_ENV['webAPI']      = true;               //是否开启WebAPI功能
$_ENV['webAPIUrl']   = $_ENV['baseUrl'];   //WebAPI地址，如需和站点地址相同，请不要修改
$_ENV['muKey']       = 'ChangeMe';         //WebAPI密钥，用于节点服务端与面板通信，请务必修改此key为随机字符串
$_ENV['checkNodeIp'] = true;               //是否webapi验证节点ip

//数据库设置-------------------------------------------------------------------------------------------
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

//Redis设置-------------------------------------------------------------------------------------------
$_ENV['redis_host']     = '127.0.0.1';        //Redis地址
$_ENV['redis_port']     = 6379;               //Redis端口
$_ENV['redis_timeout']  = 2;                  //Redis连接超时时间，单位秒
$_ENV['redis_username'] = '';                 //Redis用户名，留空则不使用用户名连接
$_ENV['redis_password'] = '';                 //Redis密码，留空则无密码
$_ENV['redis_ssl']      = false;              //是否使用SSL连接Redis，如果使用了SSL，那么Redis端口应为Redis实例的TLS端口

//Rate Limit设置--------------------------------------------------------------------------------------------
$_ENV['enable_rate_limit']    = true;            //是否开启请求限制
$_ENV['rate_limit_ip']        = 120;             //每分钟每个IP的全局请求限制
$_ENV['rate_limit_sub']       = 30;              //每分钟每个用户的订阅链接请求限制
$_ENV['rate_limit_webapi']    = 600;             //每分钟每个节点WebAPI密钥请求限制
$_ENV['rate_limit_user_api']  = 60;              //每分钟每个用户的API请求限制
$_ENV['rate_limit_admin_api'] = 60;              //每分钟每个管理员的API请求限制

//邮件设置--------------------------------------------------------------------------------------------
$_ENV['mail_filter']        = 0;            //0: 关闭; 1: 白名单模式; 2; 黑名单模式;
$_ENV['mail_filter_list']   = [];

//已注册用户设置---------------------------------------------------------------------------------------
#基础
$_ENV['enable_checkin']             = true;         //是否啓用簽到功能
$_ENV['checkinMin']                 = 1;            //用户签到最少流量 单位MB
$_ENV['checkinMax']                 = 50;           //用户签到最多流量

#高级
$_ENV['class_expire_reset_traffic'] = 0;            //等级到期时重置为的流量值，单位GB，小于0时不重置
$_ENV['enable_kill']                = true;         //是否允许用户注销账户
$_ENV['enable_change_email']        = true;         //是否允许用户更改賬戶郵箱

#用户流量余量不足邮件提醒
$_ENV['notify_limit_mode']          = false;         //false为关闭，per为按照百分比提醒，mb为按照固定剩余流量提醒
$_ENV['notify_limit_value']         = 500;           //当上一项为per时，此处填写百分比；当上一项为mb时，此处填写流量

//订阅设置---------------------------------------------------------------------------------------
$_ENV['Subscribe']                  = true;                         //本站是否提供订阅功能
$_ENV['subUrl']                     = $_ENV['baseUrl'];             //订阅地址，如需和站点名称相同，请不要修改
$_ENV['sub_token_len']              = 16;                           //订阅token长度

//审计自动封禁设置--------------------------------------------------------------------------------------------
$_ENV['auto_detect_ban_allow_admin'] = true;        // 管理员不受审计限制
$_ENV['auto_detect_ban_allow_users'] = [];          // 审计封禁的例外用户 ID
$_ENV['auto_detect_ban_number']      = 30;          // 每次执行封禁所需的触发次数
$_ENV['auto_detect_ban_time']        = 60;          // 每次封禁的时长 (分钟)

//节点检测-----------------------------------------------------------------------------------------------
#GFW检测
$_ENV['detect_gfw_port']     = 443;                                                  //所有节点服务器都打开的TCP端口
$_ENV['detect_gfw_url']      = 'http://example.com:8080/v1/tcping?ip={ip}&port={port}'; //检测节点是否被gfw墙了的API的URL

#离线检测
$_ENV['enable_detect_offline']  = true;

//高级设置-----------------------------------------------------------------------------------------------
$_ENV['enable_login_bind_ip']     = true;             //是否将登陆线程和IP绑定
$_ENV['enable_login_bind_device'] = true;             //是否将登陆线程和设备绑定
$_ENV['rememberMeDuration']       = 7;                //登录时记住账号时长天数
$_ENV['timeZone']                 = 'Asia/Taipei';    //需使用 PHP 兼容的时区格式
$_ENV['theme']                    = 'tabler';         //默认主题
$_ENV['locale']                   = 'zh-TW';          //默认语言
$_ENV['jump_delay']               = 1200;             //跳转延时，单位ms
$_ENV['keep_connect']             = false;            // 流量耗尽用户限速至 1Mbps

// cdn.jsdelivr.net / fastly.jsdelivr.net / gcore.jsdelivr.net / testingcf.jsdelivr.net
$_ENV['jsdelivr_url'] = 'fastly.jsdelivr.net';

// https://sentry.io for production debugging
$_ENV['sentry_dsn'] = '';

// Maxmind GeoIP2 database
$_ENV['maxmind_license_key'] = '';
$_ENV['geoip_locale']        = 'en';

// Large language model powered ticket reply and more
$_ENV['llm_backend'] = 'openai'; // openai/palm/huggingface/cf-workers-ai
// OpenAI ChatGPT
$_ENV['openai_api_key'] = '';
$_ENV['openai_model']   = 'gpt-3.5-turbo-1106';
// Google PaLM API
$_ENV['palm_api_key']    = '';
$_ENV['palm_text_model'] = 'text-bison-001';
// Hugging Face Inference API
$_ENV['huggingface_api_key']      = '';
$_ENV['huggingface_endpoint_url'] = '';
// Cloudflare Workers AI
$_ENV['cf_workers_ai_account_id'] = '';
$_ENV['cf_workers_ai_api_token']  = '';
$_ENV['cf_workers_ai_model_id']   = '@cf/meta/llama-2-7b-chat-int8';

// ClientDownload 命令解决 API 访问频率高而被限制使用的 Github access token
$_ENV['github_access_token'] = '';

// use Cloudflare R2 for clients download
$_ENV['enable_r2_client_download']  = false;
$_ENV['r2_bucket_name']             = '';
$_ENV['r2_account_id']              = '';
$_ENV['r2_access_key_id']           = '';
$_ENV['r2_access_key_secret']       = '';
$_ENV['r2_client_download_timeout'] = 10;
