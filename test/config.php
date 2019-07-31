<?php

//注释里请勿使用英文方括号、分号、单引号，否则迁移Config时会出错

//config迁移附注（由开发者填写本次config迁移后需要注意的地方，站长勿动）
//如需换行，直接换行即可，无需换行符
//【新增/删除】config无需写入迁移附注
$System_Config['config_migrate_notice']=
'enable_geetest_* 已变更为 enable_*_captcha
crisp已被替换为mylivechat
telegrma_qrcode被重命名为qrcode

';
$System_Config['version']='1';	//仅当涉及【需要修改config以外的文件】时才需要+1，站长勿动


//基本设置--------------------------------------------------------------------------------------------
$System_Config['key'] = '1145141919810';						//!!! 瞎 jb 修改此key为随机字符串确保网站安全 !!!
$System_Config['debug'] = 'true';								//正式环境请确保为 false
$System_Config['appName'] = 'sspanel uim test';							//站点名称
$System_Config['baseUrl'] = 'http://sspanel.host:23480';					//站点地址
$System_Config['subUrl'] = $System_Config['baseUrl'].'/link/';	//订阅地址，如需和站点名称相同，请不要修改
$System_Config['muKey'] = 'NimaQu';								//用于校验魔改后端请求，可以随意修改，但请保持前后端一致，否则节点不能工作！
$System_Config['db_driver'] = 'mysql';							//数据库程序
$System_Config['db_host'] = 'localhost';						//数据库地址
$System_Config['db_database'] = 'sspanel';						//数据库名
$System_Config['db_username'] = 'root';							//数据库用户名
$System_Config['db_password'] = 'sspanel';						//用户名对应的密码


//邮件设置--------------------------------------------------------------------------------------------
$System_Config['mailDriver'] = 'none';	//发送邮件方式：none / mailgun / smtp / sendgrid
$System_Config['sendPageLimit']= 50;	//发信分页 解决大站发公告超时问题

# mailgun
$System_Config['mailgun_key'] = '';
$System_Config['mailgun_domain'] = '';
$System_Config['mailgun_sender'] = '';

# smtp
$System_Config['smtp_host'] = '';
$System_Config['smtp_username'] = '';
$System_Config['smtp_port'] = '465';
$System_Config['smtp_name'] = '';
$System_Config['smtp_sender'] = '';
$System_Config['smtp_passsword'] = '';
$System_Config['smtp_ssl'] = 'true';

# sendgrid
$System_Config['sendgrid_key'] = '';
$System_Config['sendgrid_sender'] = '';


//备份设置--------------------------------------------------------------------------------------------
$System_Config['auto_backup_email']='';		//接收备份的邮箱
$System_Config['auto_backup_passwd']='';	//备份的压缩密码
$System_Config['backup_notify'] = 'false';		//备份通知到TG群中


//用户注册设置-----------------------------------------------------------------------------------------
$System_Config['register_mode'] = 'open';					//注册模式。close：关闭，open：开放，invite：仅限邀请码
$System_Config['defaultTraffic'] = '1';						//用户初始流量 单位GB
$System_Config['user_expire_in_default']='3650';			//用户账户过期时间，在注册时设置。（天）
$System_Config['user_class_default']='0';					//用户注册等级，在注册时设置。
$System_Config['user_class_expire_default']='24';			//用户等级过期时间，在注册时设置。（小时）
$System_Config['user_conn']='0';							//用户注册客户端数量限制，0为不限制
$System_Config['user_speedlimit']='0';						//用户注册速度默认限制，0为不限制
$System_Config['reg_auto_reset_day']='0';					//注册时的流量重置日，0为不重置
$System_Config['reg_auto_reset_bandwidth']='0';				//需要重置的流量，0为不重置
$System_Config['ramdom_group']='0';							//注册时随机分组，注册时随机分配到的分组，多个分组请用英文半角逗号分隔。
$System_Config['reg_method']='rc4-md5';						//注册时默认加密方式
$System_Config['reg_protocol']='origin';					//注册时默认协议
$System_Config['reg_protocol_param']='';					//注册时默认协议参数
$System_Config['reg_obfs']='plain';							//注册时默认混淆方式
$System_Config['reg_obfs_param']='';						//注册时默认混淆参数
$System_Config['reg_forbidden_ip']='127.0.0.0/8,::1/128';	//注册时默认禁止访问IP列表，半角英文逗号分割
$System_Config['min_port']='10000';							//用户端口池最小值
$System_Config['max_port']='65535';							//用户端口池最大值
$System_Config['reg_forbidden_port']='';					//注册时默认禁止访问端口列表，半角英文逗号分割，支持端口段
$System_Config['mu_suffix']='microsoft.com';				//单端口多用户混淆参数后缀，可以随意修改，但请保持前后端一致
$System_Config['mu_regex']='%5m%id.%suffix';				//单端口多用户混淆参数表达式，%5m代表取用户特征 md5 的前五位，%id 代表用户id,%suffix 代表上面这个后缀。

#邀请链接
$System_Config['inviteNum'] = '10';			//注册后的邀请链接可用次数
$System_Config['invite_get_money']='1';		//新用户通过私人邀请链接注册时，获得奖励金额（作为初始资金）
$System_Config['invite_price']='-1';		//用户购买邀请码所需要的价格，价格小于0时视为不开放购买
$System_Config['custom_invite_price']='-1';		//用户定制邀请码所需要的价格，价格小于0时视为不开放购买

#邮箱验证
$System_Config['enable_email_verify']='false';		//是否启用注册邮箱验证码
$System_Config['email_verify_ttl']='3600';			//邮箱验证码有效期
$System_Config['email_verify_iplimit']='10';		//验证码有效期内，单IP可请求验证码次数


//已注册用户设置---------------------------------------------------------------------------------------
#基础
$System_Config['checkinMin'] = '1';			//用户签到最少流量 单位MB
$System_Config['checkinMax'] = '50';			//用户签到最多流量
$System_Config['auto_clean_uncheck_days']='-1';	        //自动清理多少天没签到的0级用户，小于等于0时关闭
$System_Config['auto_clean_unused_days']='-1';	        //自动清理多少天没使用的0级用户，小于等于0时关闭
$System_Config['auto_clean_min_money']='1';		//余额低于多少的0级用户可以被清理
$System_Config['code_payback']='20';			//充值返利百分比
$System_Config['invite_gift']='2';			//邀请新用户获得流量奖励，单位G
$System_Config['enable_bought_reset']='true';	        //购买时是否重置流量
$System_Config['enable_bought_extend']='true';	        //购买时是否延长等级期限（同等级配套）
$System_Config['port_price']='-1';			//用户随机重置端口所需要的价格，价格小于0时视为不开放购买
$System_Config['port_price_specify']='-1';		//用户指明钦定端口所需要的价格，价格小于0时视为不开放购买

#高级
$System_Config['class_expire_reset_traffic']='0';		//等级到期时重置为的流量值，单位GB，小于0时不重置
$System_Config['account_expire_delete_days']='-1';		//账户到期几天之后会删除账户，小于0时不删除
$System_Config['enable_kill']='true';					//是否允许用户注销账户
$System_Config['notify_limit_mode'] = 'false';			//false为关闭，per为按照百分比提醒，mb为按照固定剩余流量提醒
$System_Config['notify_limit_value'] = '20';			//当上一项为per时，此处填写百分比；当上一项为mb时，此处填写流量
$System_Config['mergeSub'] = 'false';						//合并订阅设置 可选项 false / true

//Bot 设置--------------------------------------------------------------------------------------------
#通用
$System_Config['qrcode']='zxing_local';				//二维码解码方式，online，phpzbar，zxing_online，zxing_local
$System_Config['finance_public']='false';			//财务报告是否向群公开
$System_Config['enable_welcome_message']='true';	//机器人发送欢迎消息

#Discord
$System_Config['enable_discord']='false';	//是否开启Discord bot（仍未完成）
$System_Config['discord_token']='';			//Discord bot,bot 的 token，在 https://discordapp.com/developers/applications/ 申请


#Telegram
$System_Config['enable_telegram']='false';			//是否开启Telegram bot
$System_Config['telegram_token']='';				//Telegram bot,bot 的 token ，跟 father bot 申请
$System_Config['telegram_chatid']='';				//Telegram bot,群组会话 ID,把机器人拉进群里之后跟他 /ping 一下即可得到。
$System_Config['enable_tuling']='false';			//是否开启图灵机器人
$System_Config['tuling_apikey']='';					//图灵机器人API Key
$System_Config['tuling_apisecert']='';				//图灵机器人密钥
$System_Config['telegram_bot']='_bot';				//Telegram 机器人账号
$System_Config['telegram_group_quiet']='false';		//Telegram 机器人在群组中不回应
$System_Config['telegram_request_token']='';		//Telegram 机器人请求Key，随意设置，由大小写英文和数字组成，更新这个参数之后请 php xcat setTelegram


//沟通设置--------------------------------------------------------------------------------------------
#客服系统设置，注册地址 https://www.mylivechat.com
$System_Config['enable_mylivechat']='false';		//是否开启客服系统
$System_Config['mylivechat_id']='null';			//客服系统ID

# PushBear  基于微信模板的向关注了二维码的用户以微信方式推送消息 https://pushbear.ftqq.com/
$System_Config['usePushBear'] = 'false';		// true 启用	false 禁用
$System_Config['PushBear_sendkey'] = '';	//请填写您在PushBear获取的sendkey  请仔细检查勿粘贴错

#工单系统设置
$System_Config['enable_ticket']='true';		//是否开启工单系统
$System_Config['mail_ticket']='true';		//是否开启工单邮件提醒

# Server酱  用户提交新工单或者回复工单时用微信提醒机场主 http://sc.ftqq.com/
$System_Config['useScFtqq'] = 'false';		//是否开启工单Server酱提醒
$System_Config['ScFtqq_SCKEY'] = '';	//请填写您在Server酱获取的SCKEY  请仔细检查勿粘贴错

#管理员联系方式设置
$System_Config['enable_admin_contact']='false';			//是否开启管理员联系方式
$System_Config['admin_contact1'] = 'QQ：1233456';		//QQ、邮箱、微信仅用于举例
$System_Config['admin_contact2'] = '邮箱123456@qq.com';	//也可以写电话、tg等其他联系方式
$System_Config['admin_contact3'] = '微信～123456';		//没有格式要求，想怎么写就怎么写，可留空


//验证码设置------------------------------------------------------------------------------------------

$System_Config['captcha_provider'] = 'recaptcha';		//取值 recaptcha | geetest(极验)

$System_Config['recaptcha_sitekey'] = '';
$System_Config['recaptcha_secret'] = '';

$System_Config['geetest_id'] = '';
$System_Config['geetest_key'] = '';

$System_Config['enable_reg_captcha'] = 'false';		//启用注册验证码
$System_Config['enable_login_captcha'] = 'false';	//启用登录验证码
$System_Config['enable_checkin_captcha'] = 'false';	//启用签到验证码


//支付系统设置----------------------------------------------------------------------------------------
#取值 none | codepay | trimepay | f2fpay | chenAlipay | paymentwall | spay |tomatopay
$System_Config['payment_system']='none';

#codepay码支付
#wiki地址:https://goo.gl/dRwRDi  http://t.cn/RnsWjtB
$System_Config['codepay_id']='';					//码支付ID
$System_Config['codepay_key']='';					//码支付通信密钥

#alipay,f2fpay
$System_Config['f2fpay_app_id']='';
$System_Config['f2fpay_p_id']='';
$System_Config['alipay_public_key']='';
$System_Config['merchant_private_key']='';
$System_Config['f2fNotifyUrl']=null;                  //自定义当面付回调地址

#PaymentWall
$System_Config['pmw_publickey']='';
$System_Config['pmw_privatekey']='';
$System_Config['pmw_widget']='m2_1';
$System_Config['pmw_height']='350px';

#alipay,spay
$System_Config['alipay_id']='';
$System_Config['alipay_key']='';
$System_Config['amount']=[2,23,233,2333,23333];		//充值金额选项设定

#alipay,zfbjk.com
$System_Config['zfbjk_pid']='';
$System_Config['zfbjk_key']='';
$System_Config['zfbjk_qrcodeurl']='';

#Trimepay https://portal.trimepay.com/#/auth/register/134
$System_Config['trimepay_appid']='';				//AppID
$System_Config['trimepay_secret']='';				//AppSecret

# BitPay 数字货币支付（比特币、以太坊、EOS等） 商户后台获取授权码 https://merchants.mugglepay.com/
#   客服和技术 24x7 在线支持： https://t.me/joinchat/GLKSKhUnE4GvEAPgqtChAQ
$System_Config['bitpay_secret']='';


//其他面板显示设置------------------------------------------------------------------------------------------
#后台商品列表 销量统计
$System_Config['sales_period']='30';	//统计指定周期内的销量，值为【expire/任意大于0的整数】

#国旗
$System_Config['enable_flag']='false';			//启用该项之前务必先仔细阅读教程
$System_Config['flag_regex']='/.*?(?=\s)/';		//从站点全名中匹配【国家/地区】的正则表达式(php版)

#捐赠
$System_Config['enable_donate']='false';	//是否显示用户捐赠（所有收入将被公开）

#iOS账户显示
$System_Config['display_ios_class']='-1';	//至少等级为多少的用户可以看见，小于0时关闭此功能
$System_Config['display_ios_topup']='0';	//满足等级要求后，累计充值高于多少的用户可以看见
$System_Config['ios_account']='';			//iOS账户
$System_Config['ios_password']='';			//iOS密码


//节点检测-----------------------------------------------------------------------------------------------
#GFW检测，请通过crontab进行【开启/关闭】
$System_Config['detect_gfw_interval']='3600';										//检测间隔，单位：秒，低于推荐值会爆炸
$System_Config['detect_gfw_port']='22';												//所有节点服务器都打开的TCP端口，常用的为22（SSH端口）
$System_Config['detect_gfw_url']='https://cn-qz-tcping.torch.njs.app/{ip}/{port}';	//检测节点是否被gfw墙了的API的URL
$System_Config['detect_gfw_judge']='$json_tcping[\'status\']=="true"';				//判断是否被墙的依据，json_tcping为上方URL返回的json数组
$System_Config['detect_gfw_count']='3';												//尝试次数

#离线检测
$System_Config['enable_detect_offline']='true';
#离线检测是否推送到Server酱 请配置好上文的Server配置
$System_Config['enable_detect_offline_useScFtqq']='true';


//V2Ray相关设置------------------------------------------------------------------------------------------
$System_Config['v2ray_port']='443';					//V2Ray端口
$System_Config['v2ray_protocol']='HTTP/2 + TLS';	//V2Ray协议
$System_Config['v2ray_alter_id']='32';
$System_Config['v2ray_level']='0';

//以下所有均为高级设置（一般用不上，不用改---------------------------------------------------------------------
#杂项
$System_Config['enable_login_bind_ip']='false';		//是否将登陆线程和IP绑定
$System_Config['rememberMeDuration']='7';           //登录时记住账号时长天数
$System_Config['authDriver'] = 'cookie';			//不能更改此项
$System_Config['pwdMethod'] = 'md5';				//密码加密 可选 md5, sha256, bcrypt, argon2i, argon2id（argon2i需要至少php7.2）
$System_Config['salt'] = '';						//推荐配合 md5/sha256， bcrypt/argon2i/argon2id 会忽略此项
$System_Config['sessionDriver'] = 'cookie';			//可选: cookie,redis
$System_Config['cacheDriver'] = 'cookie';			//可选: cookie,redis
$System_Config['tokenDriver'] = 'db';				//可选: db,redis
$System_Config['jump_delay']='1200';				//跳转延时，单位ms，不建议太长
$System_Config['theme']    = 'material';			//主题
$System_Config['pacp_offset']='-20000';				//VPN 端口偏移
$System_Config['pacpp_offset']='-20000';
$System_Config['Speedtest_duration']='6';			//显示多长时间的测速记录
$System_Config['login_warn']='false';				//异地登陆提示
$System_Config['timeZone'] = 'PRC';					//PRC 天朝时间  UTC 格林时间
$System_Config['db_charset'] = 'utf8';
$System_Config['db_collation'] = 'utf8_general_ci';
$System_Config['db_prefix'] = '';
$System_Config['muKeyList'] = ['　'];                //多 key 列表

#aws
$System_Config['aws_access_key_id'] = '';
$System_Config['aws_secret_access_key'] = '';

#redis
$System_Config['redis_scheme'] = 'tcp';
$System_Config['redis_host'] = '127.0.0.1';
$System_Config['redis_port'] = '6379';
$System_Config['redis_database'] = '0';
$System_Config['redis_password']= '';

#Radius设置
$System_Config['enable_radius']='false';			//是否开启Radius
$System_Config['radius_db_host']='';				//以下4项为Radius数据库设置
$System_Config['radius_db_database']='';
$System_Config['radius_db_user']='';
$System_Config['radius_db_password']='';
$System_Config['radius_secret']='';					//Radius连接密钥

#Cloudxns
$System_Config['enable_cloudxns']='false';			//是否开启Cloudxns
$System_Config['cloudxns_apikey']='';				//自己去 cloudxns.net 申请
$System_Config['cloudxns_apisecret']='';
$System_Config['cloudxns_domain']='';		//你的域名

#Cloudflare
$System_Config['cloudflare_enable']='false';										//是否开启 Cloudflare 解析
$System_Config['cloudflare_email']='user@example.com';								//Cloudflare 邮箱地址
$System_Config['cloudflare_key']='c2547eb745079dac9320b638f5e225cf483cc5cfdda41';	//Cloudflare API Key
$System_Config['cloudflare_name']='example.com';									//域名

#不安全中转模式，这个开启之后使用除了 auth_aes128_md5 或者 auth_aes128_sha1 以外的协议地用户也可以设置和使用中转
$System_Config['relay_insecure_mode']='false';		//强烈推荐不开启

#是否夹带统计代码，自己在 resources/views/{主题名} 下创建一个 analytics.tpl ，如果有必要就用 literal 界定符
$System_Config['enable_analytics_code']='false';
$System_Config['sspanelAnalysis'] = 'true';

#在套了CDN之后获取用户真实ip，如果您不知道这是什么，请不要乱动
if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
$list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
$_SERVER['REMOTE_ADDR'] = $list[0];
}
