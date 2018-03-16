<?php

//  ss-panel v3 配置
//
$System_Config['key'] = '114514';			// !!! 瞎jb修改此key为随机字符串确保网站安全 !!!
$System_Config['debug'] =  'false';  //  正式环境请确保为false
$System_Config['appName'] = '';             //站点名称
$System_Config['baseUrl'] = 'http://url.com';            // 站点地址
$System_Config['timeZone'] = 'PRC';        // PRC 天朝时间  UTC 格林时间
$System_Config['pwdMethod'] = 'sha256';       // 密码加密   可选 md5,sha256
$System_Config['salt'] = '';               // 密码加密用，从旧版升级请留空
$System_Config['theme']    = 'material';   // 主题
$System_Config['authDriver'] = 'cookie';   //不能更改此项
$System_Config['sessionDriver'] = 'cookie'; //  可选: cookie,redis
$System_Config['cacheDriver'] = 'cookie';   //  可选: cookie,redis
$System_Config['tokenDriver'] = 'db';   //  可选: db,redis

//$System_Config['version'] = '<a href="https://www.zhaoj.in">Glzjin</a> mod-2016042210000';

// mu key 用于校验ss-go mu的请求,半角逗号分隔
$System_Config['muKey'] = '';
// 邮件
$System_Config['mailDriver'] = 'mailgun';   // mailgun or smtp or sendgrid

// 用户签到设置
$System_Config['checkinMin'] = '1';       // 签到最少流量 单位MB
$System_Config['checkinMax'] = '50';       // 签到最多流量

//
$System_Config['defaultTraffic'] = '1';      // 用户初始流量 单位GB

// 注册后获得的邀请码数量
$System_Config['inviteNum'] = '10';

# database 数据库配置
$System_Config['db_driver'] = 'mysql';
$System_Config['db_host'] = 'localhost';
$System_Config['db_database'] = '';
$System_Config['db_username'] = 'root';
$System_Config['db_password'] = '';
$System_Config['db_charset'] = 'utf8';
$System_Config['db_collation'] = 'utf8_general_ci';
$System_Config['db_prefix'] = '';

# redis
$System_Config['redis_scheme'] = 'tcp';
$System_Config['redis_host'] = '127.0.0.1';
$System_Config['redis_port'] = '6379';
$System_Config['redis_database'] = '0';
$System_Config['redis_password']="";

# mailgun
$System_Config['mailgun_key'] = '';
$System_Config['mailgun_domain'] = '';
$System_Config['mailgun_sender'] = '';

# smtp
$System_Config['smtp_host'] = '';
$System_Config['smtp_username'] = '';
$System_Config['smtp_port'] = '';
$System_Config['smtp_name'] = '';
$System_Config['smtp_sender'] = '';
$System_Config['smtp_passsword'] = '';
$System_Config['smtp_ssl'] = 'true';

# aws
$System_Config['aws_access_key_id'] = '';
$System_Config['aws_secret_access_key'] = '';


#glzjin's time

#功能开关
$System_Config['enable_wecenter']='false';
$System_Config['enable_radius']='false';
$System_Config['enable_cloudxns']='false';
$System_Config['enable_duoshuo']='false';




#用户过期时间，在注册时设置。（天）
$System_Config['user_expire_in_default']='3650';

#Radius数据库设置
$System_Config['radius_db_host']='';
$System_Config['radius_db_database']='';
$System_Config['radius_db_user']='';
$System_Config['radius_db_password']='';

#Radius连接密钥
$System_Config['radius_secret']='';




#多说设置
$System_Config['duoshuo_shortname']="";
$System_Config['duoshuo_apptoken']="";


#节点离线设置
$System_Config['node_offline_warn']='true';

#异地登陆提示
$System_Config['login_warn']='false';

#端口池
$System_Config['min_port']='10000';
$System_Config['max_port']='65535';

#两种方式相对于ss端口的偏移
$System_Config['pacp_offset']='-20000';
$System_Config['pacpp_offset']='-20000';

#测速周期/h
$System_Config['Speedtest_duration']='6';


#随机分组，注册时随机分配到的分组，多个分组请用英文半角逗号分隔。
$System_Config['ramdom_group']='0';

#Wecenter 数据库信息
$System_Config['wecenter_db_host']='localhost';
$System_Config['wecenter_db_database']='';
$System_Config['wecenter_db_user']='';
$System_Config['wecenter_db_password']='';

#系统根域名 zhaoj.in 这样
$System_Config['wecenter_system_main_domain']='';

#访问路径，不开启这个功能的话就清空这个设置项吧
$System_Config['wecenter_url']='';

#可从安装好的 wecenter 目录下的 system/config.inc.php 得到

#G_COOKIE_PREFIX
$System_Config['wecenter_cookie_prefix']='mmg_';

#G_COOKIE_HASH_KEY
$System_Config['wecenter_cookie_key']='';

#充值返利百分比
$System_Config['code_payback']='20';

#注册时的流量重置日以及需要重置的流量,0不重置
$System_Config['reg_auto_reset_day']='0';
$System_Config['reg_auto_reset_bandwidth']='0';

#Cloudxns
$System_Config['cloudxns_apikey']='';#自己去 cloudxns.net 申请
$System_Config['cloudxns_apisecret']='';
$System_Config['cloudxns_domain']='zhaoj.in';#你的域名



#自动备份设置
$System_Config['enable_auto_backup']='false';
$System_Config['auto_backup_email']='your email';
$System_Config['auto_backup_webroot']='your base dir ,such as /home/wwwroot/ss.panel/';
$System_Config['auto_backup_passwd']='zip password';


#跳转延时,ms
$System_Config['jump_delay']='1200';  //1000ms=1s,不建议太长

#是否启用邀请码注册
$System_Config['enable_invite_code']='false';

#是否启用邮箱验证码
$System_Config['enable_email_verify']='true';

#是否允许用户注销账户
$System_Config['enable_kill']='true';

#邮箱验证码有效期
$System_Config['email_verify_ttl']='3600';

#单IP可请求验证码次数（上面那个周期内）
$System_Config['email_verify_iplimit']='10';


#注册时默认加密方式
$System_Config['reg_method']='chacha20';

#注册时默认协议
$System_Config['reg_protocol']='auth_sha1_v4';

#注册时默认协议参数
$System_Config['reg_protocol_param']='';

#注册时默认混淆方式
$System_Config['reg_obfs']='';

#注册时默认混淆参数
$System_Config['reg_obfs_param']='amazon.cn';

#注册时默认禁止访问IP列表，半角英文逗号分割
$System_Config['reg_forbidden_ip']='127.0.0.0/8,::1/128';

#注册时默认禁止访问端口列表，半角英文逗号分割，支持端口段
$System_Config['reg_forbidden_port']='';

#是否将登陆线程和IP绑定
$System_Config['enable_login_bind_ip']='false';



#等级到期时是否重置流量
$System_Config['enable_class_expire_reset']='true';

#等级到期时重置为的流量值，GB
$System_Config['enable_class_expire_reset_traffic']='0';

#账户到期时是否重置流量
$System_Config['enable_account_expire_reset']='false';

#账户到期时重置为的流量值，GB
$System_Config['enable_account_expire_reset_traffic']='0';

#购买时是否重置流量
$System_Config['enable_bought_reset']='false';

#账户到期之后是否会删除账户
$System_Config['enable_account_expire_delete']='false';

#账户到期几天之后会删除账户
$System_Config['enable_account_expire_delete_days']='0';

#验证码相关设置，自己去 Geetest.com 申请
$System_Config['geetest_id']='';
$System_Config['geetest_key']='';

#启用注册验证码
$System_Config['enable_geetest_reg']='false';

#启用登录验证码
$System_Config['enable_geetest_login']='false';

#启用签到验证码
$System_Config['enable_geetest_checkin']='false';

#自动清理多少天没签到的0级用户
$System_Config['enable_auto_clean_uncheck']='false';
$System_Config['enable_auto_clean_uncheck_days']='7';


#自动清理多少天没使用的0级用户
$System_Config['enable_auto_clean_unused']='false';
$System_Config['enable_auto_clean_unused_days']='30';


#是否夹带统计代码，自己在 resources/views/{主题名} 下创建一个 analytics.tpl ，如果有必要就用 literal 界定符
$System_Config['enable_analytics_code']='false';

#是否启用捐赠相关功能
$System_Config['enable_donate']='false';

#telegram bot,是否启用
$System_Config['enable_telegram']='false';

#telegram bot,bot 的 token ，跟 father bot 申请
$System_Config['telegram_token']='';

#telegram bot,群组会话 ID,把机器人拉进群里之后跟他 /ping 一下即可得到。
$System_Config['telegram_chatid']='';

#多用户混淆参数后缀
$System_Config['mu_suffix']='microsoft.com';

#多用户混淆参数表达式，%5m代表取用户特征 md5 的前五位，%id 代表用户id,%suffix 代表上面这个后缀。
$System_Config['mu_regex']='%5m%id.%suffix';


#图灵机器人
$System_Config['enable_tuling']='false';
$System_Config['tuling_apikey']='';
$System_Config['tuling_apisecert']='';

#Telegram 机器人账号
$System_Config['telegram_bot']='_bot';

#Telegram 机器人在群组中不回应
$System_Config['telegram_group_quiet']='false';

#二维码解码方式，online，phpzbar，zxing_online，zxing_local
$System_Config['telegram_qrcode']='zxing_local';

#Telegram 机器人请求Key，随意设置，由大小写英文和数字组成，复杂一些，用于校验请求，更新这个参数之后请 php xcat setTelegram
$System_Config['telegram_request_token']='';

# sendgrid
$System_Config['sendgrid_key'] = '';
$System_Config['sendgrid_sender'] = '';


#支付系统

#取值 doiampay,paymentwall,zfbjk,spay,f2fpay,yftpay,none
$System_Config['payment_system']='none';

#doiampay,https://www.daimiyun.cn/v3/
$System_Config['doiampay']=[
    'enabled' => [
        'wepay'=>1, // 微信支付1 启用 0 关闭
        'alipay'=>1, // 支付宝1 启用 0 关闭
        'qqpay'=>1, // QQ支付1 启用 0 关闭
    ],
    'mchdata' => [
        'wepay'=>[
            'mchid' => 188888888,   // 商户号
            'phone' => 18888888888, //手机号
            'token' => "example" // 安全验证码
        ],
        'alipay'=>[
            'mchid' => 188888888,   // 商户号
            'phone' => 188888888, //手机号
            'token' => "188888888" // 安全验证码
        ],
        'qqpay'=>[
            'mchid' => 188888888,   // 商户号
            'phone' => 188888888, //手机号
            'token' => "188888888" // 安全验证码
        ],
    ]
];

#alipay,f2fpay amount为充值金额
$System_Config['f2fpay_app_id']='';
$System_Config['f2fpay_p_id']='';
$System_Config['alipay_public_key']='';
$System_Config['merchant_private_key']='';
$System_Config['amount']=[5,10,20,50,100];

#PaymentWall
$System_Config['pmw_publickey']='';
$System_Config['pmw_privatekey']='';
$System_Config['pmw_widget']='m2_1';
$System_Config['pmw_height']='350px';

#alipay,spay
$System_Config['alipay_id']='';
$System_Config['alipay_key']='';

#yftpay,https://pay.anypay.me/
$System_Config['yft_secret']='';
$System_Config['yft_accesskey']='';

#alipay,zfbjk.com
$System_Config['zfbjk_pid']='';
$System_Config['zfbjk_key']='';
$System_Config['zfbjk_qrcodeurl']='https://www.zhaojin97.cn/wp-content/uploads/2016/01/630c50ab52bc139b2a33af8cc64e47ad2016010111570425541.jpg';

#不安全中转模式，这个开启之后使用除了 auth_aes128_md5 或者 auth_aes128_sha1 以外的协议地用户也可以设置和使用中转，但是，强烈推荐不开启。
$System_Config['relay_insecure_mode']='false';//默认关闭不安全中转

#在套了CDN之后获取用户真实ip，如果您不知道这是什么，请不要乱动

if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ) {

$list = explode("," , $_SERVER["HTTP_X_FORWARDED_FOR"]);

$_SERVER["REMOTE_ADDR"] = $list[0];

}
