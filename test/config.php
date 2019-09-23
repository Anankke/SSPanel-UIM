<?php

//注释里请勿使用英文方括号、分号、单引号，否则迁移Config时会出错

//config迁移附注（由开发者填写本次config迁移后需要注意的地方，站长勿动）
//如需换行，直接换行即可，无需换行符
//【新增/删除】config无需写入迁移附注
$_ENV['config_migrate_notice'] =
    'enable_geetest_* 已变更为 enable_*_captcha
crisp已被替换为mylivechat
telegrma_qrcode被重命名为qrcode

';
$_ENV['version'] = '1';    //仅当涉及【需要修改config以外的文件】时才需要+1，站长勿动


//基本设置--------------------------------------------------------------------------------------------
$_ENV['key'] = '1145141919810';                        //!!! 瞎 jb 修改此key为随机字符串确保网站安全 !!!
$_ENV['debug'] = true;                                //正式环境请确保为 false
$_ENV['appName'] = 'sspanel uim test';                            //站点名称
$_ENV['baseUrl'] = 'http://sspanel.host:23480';                    //站点地址
$_ENV['subUrl'] = $_ENV['baseUrl'] . '/link/';    //订阅地址，如需和站点名称相同，请不要修改
$_ENV['muKey'] = 'NimaQu';                                //用于校验魔改后端请求，可以随意修改，但请保持前后端一致，否则节点不能工作！
$_ENV['db_driver'] = 'mysql';                            //数据库程序
$_ENV['db_host'] = 'localhost';                        //数据库地址
$_ENV['db_database'] = 'sspanel';                        //数据库名
$_ENV['db_username'] = 'root';                            //数据库用户名
$_ENV['db_password'] = 'sspanel';                        //用户名对应的密码


//邮件设置--------------------------------------------------------------------------------------------
$_ENV['mailDriver'] = 'none';    //发送邮件方式：none / mailgun / smtp / sendgrid
$_ENV['sendPageLimit'] = 50;    //发信分页 解决大站发公告超时问题

# mailgun
$_ENV['mailgun_key'] = '';
$_ENV['mailgun_domain'] = '';
$_ENV['mailgun_sender'] = '';

# smtp
$_ENV['smtp_host'] = '';
$_ENV['smtp_username'] = '';
$_ENV['smtp_port'] = '465';
$_ENV['smtp_name'] = '';
$_ENV['smtp_sender'] = '';
$_ENV['smtp_passsword'] = '';
$_ENV['smtp_ssl'] = 'true';

# sendgrid
$_ENV['sendgrid_key'] = '';
$_ENV['sendgrid_sender'] = '';


//备份设置--------------------------------------------------------------------------------------------
$_ENV['auto_backup_email'] = '';        //接收备份的邮箱
$_ENV['auto_backup_passwd'] = '';    //备份的压缩密码
$_ENV['backup_notify'] = 'false';        //备份通知到TG群中


//用户注册设置-----------------------------------------------------------------------------------------
$_ENV['register_mode'] = 'open';                    //注册模式。close：关闭，open：开放，invite：仅限邀请码
$_ENV['defaultTraffic'] = '1';                        //用户初始流量 单位GB
$_ENV['user_expire_in_default'] = '3650';            //用户账户过期时间，在注册时设置。（天）
$_ENV['user_class_default'] = '0';                    //用户注册等级，在注册时设置。
$_ENV['user_class_expire_default'] = '24';            //用户等级过期时间，在注册时设置。（小时）
$_ENV['user_conn'] = '0';                            //用户注册客户端数量限制，0为不限制
$_ENV['user_speedlimit'] = '0';                        //用户注册速度默认限制，0为不限制
$_ENV['reg_auto_reset_day'] = '0';                    //注册时的流量重置日，0为不重置
$_ENV['reg_auto_reset_bandwidth'] = '0';                //需要重置的流量，0为不重置
$_ENV['ramdom_group'] = '0';                            //注册时随机分组，注册时随机分配到的分组，多个分组请用英文半角逗号分隔。
$_ENV['reg_method'] = 'rc4-md5';                        //注册时默认加密方式
$_ENV['reg_protocol'] = 'origin';                    //注册时默认协议
$_ENV['reg_protocol_param'] = '';                    //注册时默认协议参数
$_ENV['reg_obfs'] = 'plain';                            //注册时默认混淆方式
$_ENV['reg_obfs_param'] = '';                        //注册时默认混淆参数
$_ENV['reg_forbidden_ip'] = '127.0.0.0/8,::1/128';    //注册时默认禁止访问IP列表，半角英文逗号分割
$_ENV['min_port'] = '10000';                            //用户端口池最小值
$_ENV['max_port'] = '65535';                            //用户端口池最大值
$_ENV['reg_forbidden_port'] = '';                    //注册时默认禁止访问端口列表，半角英文逗号分割，支持端口段
$_ENV['mu_suffix'] = 'microsoft.com';                //单端口多用户混淆参数后缀，可以随意修改，但请保持前后端一致
$_ENV['mu_regex'] = '%5m%id.%suffix';                //单端口多用户混淆参数表达式，%5m代表取用户特征 md5 的前五位，%id 代表用户id,%suffix 代表上面这个后缀。

#邀请链接
$_ENV['inviteNum'] = '10';            //注册后的邀请链接可用次数
$_ENV['invite_get_money'] = '1';        //新用户通过私人邀请链接注册时，获得奖励金额（作为初始资金）
$_ENV['invite_price'] = '-1';        //用户购买邀请码所需要的价格，价格小于0时视为不开放购买
$_ENV['custom_invite_price'] = '-1';        //用户定制邀请码所需要的价格，价格小于0时视为不开放购买

#邮箱验证
$_ENV['enable_email_verify'] = 'false';        //是否启用注册邮箱验证码
$_ENV['email_verify_ttl'] = '3600';            //邮箱验证码有效期
$_ENV['email_verify_iplimit'] = '10';        //验证码有效期内，单IP可请求验证码次数


//已注册用户设置---------------------------------------------------------------------------------------
#基础
$_ENV['checkinMin'] = '1';            //用户签到最少流量 单位MB
$_ENV['checkinMax'] = '50';            //用户签到最多流量
$_ENV['auto_clean_uncheck_days'] = '-1';            //自动清理多少天没签到的0级用户，小于等于0时关闭
$_ENV['auto_clean_unused_days'] = '-1';            //自动清理多少天没使用的0级用户，小于等于0时关闭
$_ENV['auto_clean_min_money'] = '1';        //余额低于多少的0级用户可以被清理
$_ENV['code_payback'] = '20';            //充值返利百分比
$_ENV['invite_gift'] = '2';            //邀请新用户获得流量奖励，单位G
$_ENV['enable_bought_reset'] = 'true';            //购买时是否重置流量
$_ENV['enable_bought_extend'] = 'true';            //购买时是否延长等级期限（同等级配套）
$_ENV['port_price'] = '-1';            //用户随机重置端口所需要的价格，价格小于0时视为不开放购买
$_ENV['port_price_specify'] = '-1';        //用户指明钦定端口所需要的价格，价格小于0时视为不开放购买

#高级
$_ENV['class_expire_reset_traffic'] = '0';        //等级到期时重置为的流量值，单位GB，小于0时不重置
$_ENV['account_expire_delete_days'] = '-1';        //账户到期几天之后会删除账户，小于0时不删除
$_ENV['enable_kill'] = 'true';                    //是否允许用户注销账户
$_ENV['notify_limit_mode'] = 'false';            //false为关闭，per为按照百分比提醒，mb为按照固定剩余流量提醒
$_ENV['notify_limit_value'] = '20';            //当上一项为per时，此处填写百分比；当上一项为mb时，此处填写流量
$_ENV['mergeSub'] = 'false';                        //合并订阅设置 可选项 false / true

//Bot 设置--------------------------------------------------------------------------------------------
#通用
$_ENV['qrcode'] = 'zxing_local';                //二维码解码方式，online，phpzbar，zxing_online，zxing_local
$_ENV['finance_public'] = 'false';            //财务报告是否向群公开
$_ENV['enable_welcome_message'] = 'true';    //机器人发送欢迎消息

#Discord
$_ENV['enable_discord'] = 'false';    //是否开启Discord bot（仍未完成）
$_ENV['discord_token'] = '';            //Discord bot,bot 的 token，在 https://discordapp.com/developers/applications/ 申请


#Telegram
$_ENV['enable_telegram'] = 'false';            //是否开启Telegram bot
$_ENV['telegram_token'] = '';                //Telegram bot,bot 的 token ，跟 father bot 申请
$_ENV['telegram_chatid'] = '';                //Telegram bot,群组会话 ID,把机器人拉进群里之后跟他 /ping 一下即可得到。
$_ENV['enable_tuling'] = 'false';            //是否开启图灵机器人
$_ENV['tuling_apikey'] = '';                    //图灵机器人API Key
$_ENV['tuling_apisecert'] = '';                //图灵机器人密钥
$_ENV['telegram_bot'] = '_bot';                //Telegram 机器人账号
$_ENV['telegram_group_quiet'] = 'false';        //Telegram 机器人在群组中不回应
$_ENV['telegram_request_token'] = '';        //Telegram 机器人请求Key，随意设置，由大小写英文和数字组成，更新这个参数之后请 php xcat setTelegram


//沟通设置--------------------------------------------------------------------------------------------
#客服系统设置，注册地址 https://www.mylivechat.com
$_ENV['enable_mylivechat'] = 'false';        //是否开启客服系统
$_ENV['mylivechat_id'] = 'null';            //客服系统ID

# PushBear  基于微信模板的向关注了二维码的用户以微信方式推送消息 https://pushbear.ftqq.com/
$_ENV['usePushBear'] = 'false';        // true 启用	false 禁用
$_ENV['PushBear_sendkey'] = '';    //请填写您在PushBear获取的sendkey  请仔细检查勿粘贴错

#工单系统设置
$_ENV['enable_ticket'] = 'true';        //是否开启工单系统
$_ENV['mail_ticket'] = 'true';        //是否开启工单邮件提醒

# Server酱  用户提交新工单或者回复工单时用微信提醒机场主 http://sc.ftqq.com/
$_ENV['useScFtqq'] = 'false';        //是否开启工单Server酱提醒
$_ENV['ScFtqq_SCKEY'] = '';    //请填写您在Server酱获取的SCKEY  请仔细检查勿粘贴错

#管理员联系方式设置
$_ENV['enable_admin_contact'] = 'false';            //是否开启管理员联系方式
$_ENV['admin_contact1'] = 'QQ：1233456';        //QQ、邮箱、微信仅用于举例
$_ENV['admin_contact2'] = '邮箱123456@qq.com';    //也可以写电话、tg等其他联系方式
$_ENV['admin_contact3'] = '微信～123456';        //没有格式要求，想怎么写就怎么写，可留空


//验证码设置------------------------------------------------------------------------------------------

$_ENV['captcha_provider'] = 'recaptcha';        //取值 recaptcha | geetest(极验)

$_ENV['recaptcha_sitekey'] = '';
$_ENV['recaptcha_secret'] = '';

$_ENV['geetest_id'] = '';
$_ENV['geetest_key'] = '';

$_ENV['enable_reg_captcha'] = 'false';        //启用注册验证码
$_ENV['enable_login_captcha'] = 'false';    //启用登录验证码
$_ENV['enable_checkin_captcha'] = 'false';    //启用签到验证码


//支付系统设置----------------------------------------------------------------------------------------
#取值 none | codepay | trimepay | f2fpay | chenAlipay | paymentwall | spay |tomatopay
$_ENV['payment_system'] = 'none';

#codepay码支付
#wiki地址:https://goo.gl/dRwRDi  http://t.cn/RnsWjtB
$_ENV['codepay_id'] = '';                    //码支付ID
$_ENV['codepay_key'] = '';                    //码支付通信密钥

#alipay,f2fpay
$_ENV['f2fpay_app_id'] = '';
$_ENV['f2fpay_p_id'] = '';
$_ENV['alipay_public_key'] = '';
$_ENV['merchant_private_key'] = '';
$_ENV['f2fNotifyUrl'] = null;                  //自定义当面付回调地址

#PaymentWall
$_ENV['pmw_publickey'] = '';
$_ENV['pmw_privatekey'] = '';
$_ENV['pmw_widget'] = 'm2_1';
$_ENV['pmw_height'] = '350px';

#alipay,spay
$_ENV['alipay_id'] = '';
$_ENV['alipay_key'] = '';
$_ENV['amount'] = [2, 23, 233, 2333, 23333];        //充值金额选项设定

#alipay,zfbjk.com
$_ENV['zfbjk_pid'] = '';
$_ENV['zfbjk_key'] = '';
$_ENV['zfbjk_qrcodeurl'] = '';

#Trimepay https://portal.trimepay.com/#/auth/register/134
$_ENV['trimepay_appid'] = '';                //AppID
$_ENV['trimepay_secret'] = '';                //AppSecret

# BitPay 数字货币支付（比特币、以太坊、EOS等） 商户后台获取授权码 https://merchants.mugglepay.com/
#   客服和技术 24x7 在线支持： https://t.me/joinchat/GLKSKhUnE4GvEAPgqtChAQ
$_ENV['bitpay_secret'] = '';


//其他面板显示设置------------------------------------------------------------------------------------------
#后台商品列表 销量统计
$_ENV['sales_period'] = '30';    //统计指定周期内的销量，值为【expire/任意大于0的整数】

#国旗
$_ENV['enable_flag'] = 'false';            //启用该项之前务必先仔细阅读教程
$_ENV['flag_regex'] = '/.*?(?=\s)/';        //从站点全名中匹配【国家/地区】的正则表达式(php版)

#捐赠
$_ENV['enable_donate'] = 'false';    //是否显示用户捐赠（所有收入将被公开）

#iOS账户显示
$_ENV['display_ios_class'] = '-1';    //至少等级为多少的用户可以看见，小于0时关闭此功能
$_ENV['display_ios_topup'] = '0';    //满足等级要求后，累计充值高于多少的用户可以看见
$_ENV['ios_account'] = '';            //iOS账户
$_ENV['ios_password'] = '';            //iOS密码


//节点检测-----------------------------------------------------------------------------------------------
#GFW检测，请通过crontab进行【开启/关闭】
$_ENV['detect_gfw_interval'] = '3600';                                        //检测间隔，单位：秒，低于推荐值会爆炸
$_ENV['detect_gfw_port'] = '22';                                                //所有节点服务器都打开的TCP端口，常用的为22（SSH端口）
$_ENV['detect_gfw_url'] = 'https://cn-qz-tcping.torch.njs.app/{ip}/{port}';    //检测节点是否被gfw墙了的API的URL
$_ENV['detect_gfw_judge'] = '$json_tcping[\'status\']=="true"';                //判断是否被墙的依据，json_tcping为上方URL返回的json数组
$_ENV['detect_gfw_count'] = '3';                                                //尝试次数

#离线检测
$_ENV['enable_detect_offline'] = 'true';
#离线检测是否推送到Server酱 请配置好上文的Server配置
$_ENV['enable_detect_offline_useScFtqq'] = 'true';


//V2Ray相关设置------------------------------------------------------------------------------------------
$_ENV['v2ray_port'] = '443';                    //V2Ray端口
$_ENV['v2ray_protocol'] = 'HTTP/2 + TLS';    //V2Ray协议
$_ENV['v2ray_alter_id'] = '32';
$_ENV['v2ray_level'] = '0';

//以下所有均为高级设置（一般用不上，不用改---------------------------------------------------------------------
#杂项
$_ENV['enable_login_bind_ip'] = 'false';        //是否将登陆线程和IP绑定
$_ENV['rememberMeDuration'] = '7';           //登录时记住账号时长天数
$_ENV['authDriver'] = 'cookie';            //不能更改此项
$_ENV['pwdMethod'] = 'md5';                //密码加密 可选 md5, sha256, bcrypt, argon2i, argon2id（argon2i需要至少php7.2）
$_ENV['salt'] = '';                        //推荐配合 md5/sha256， bcrypt/argon2i/argon2id 会忽略此项
$_ENV['sessionDriver'] = 'cookie';            //可选: cookie,redis
$_ENV['cacheDriver'] = 'cookie';            //可选: cookie,redis
$_ENV['tokenDriver'] = 'db';                //可选: db,redis
$_ENV['jump_delay'] = '1200';                //跳转延时，单位ms，不建议太长
$_ENV['theme']    = 'material';            //主题
$_ENV['pacp_offset'] = '-20000';                //VPN 端口偏移
$_ENV['pacpp_offset'] = '-20000';
$_ENV['Speedtest_duration'] = '6';            //显示多长时间的测速记录
$_ENV['login_warn'] = 'false';                //异地登陆提示
$_ENV['timeZone'] = 'PRC';                    //PRC 天朝时间  UTC 格林时间
$_ENV['db_charset'] = 'utf8';
$_ENV['db_collation'] = 'utf8_general_ci';
$_ENV['db_prefix'] = '';
$_ENV['muKeyList'] = ['　'];                //多 key 列表

#aws
$_ENV['aws_access_key_id'] = '';
$_ENV['aws_secret_access_key'] = '';

#redis
$_ENV['redis_scheme'] = 'tcp';
$_ENV['redis_host'] = '127.0.0.1';
$_ENV['redis_port'] = '6379';
$_ENV['redis_database'] = '0';
$_ENV['redis_password'] = '';

#Radius设置
$_ENV['enable_radius'] = 'false';            //是否开启Radius
$_ENV['radius_db_host'] = '';                //以下4项为Radius数据库设置
$_ENV['radius_db_database'] = '';
$_ENV['radius_db_user'] = '';
$_ENV['radius_db_password'] = '';
$_ENV['radius_secret'] = '';                    //Radius连接密钥

#Cloudxns
$_ENV['enable_cloudxns'] = 'false';            //是否开启Cloudxns
$_ENV['cloudxns_apikey'] = '';                //自己去 cloudxns.net 申请
$_ENV['cloudxns_apisecret'] = '';
$_ENV['cloudxns_domain'] = '';        //你的域名

#Cloudflare
$_ENV['cloudflare_enable'] = 'false';                                        //是否开启 Cloudflare 解析
$_ENV['cloudflare_email'] = 'user@example.com';                                //Cloudflare 邮箱地址
$_ENV['cloudflare_key'] = 'c2547eb745079dac9320b638f5e225cf483cc5cfdda41';    //Cloudflare API Key
$_ENV['cloudflare_name'] = 'example.com';                                    //域名

#不安全中转模式，这个开启之后使用除了 auth_aes128_md5 或者 auth_aes128_sha1 以外的协议地用户也可以设置和使用中转
$_ENV['relay_insecure_mode'] = 'false';        //强烈推荐不开启

#是否夹带统计代码，自己在 resources/views/{主题名} 下创建一个 analytics.tpl ，如果有必要就用 literal 界定符
$_ENV['enable_analytics_code'] = 'false';
$_ENV['sspanelAnalysis'] = 'true';
