# 欢迎使用ss panel v3 mod 再次修改版

- 免签约支付宝程序自检测 根据COOKIE
- EMAIL通知失效情况
- 加入XCAT命令
- 加入定时任务自动检测
- 相关配置在.config.php

### 相关配置
    $System_Config['AliPay_EMail'] = ''; //失效通知email
    $System_Config['AliPay_QRcode'] = ''; //支付宝二维码
    $System_Config['AliPay_Cookie'] = ''; //支付宝cookie
    $System_Config['payment_system']='chenAlipay';

### 运行
    crontab -e
    */1 * * * * php /你的目录/xcat alipay
    
https://mbillexprod.alipay.com/enterprise/tradeListQuery.htm访问后按F12查看
https://mbillexprod.alipay.com/enterprise/tradeListQuery.json接口


### 原作者介绍

**ss-panel-v3-mod**是一款专为shadowsocks设计的web前端面板，再次感谢ss-panel-v3-mod 的制作者，修改后的功能简介：

- **支付系统集成**：集成 支付宝程序自检测 支付宝当面付 黛米付 易付通 码支付等多种支付系统，使用方法见项目[wiki](https://github.com/NimaQu/ss-panel-v3-mod_Uim/wiki/)
- **UI** ：修改为 ~~援交~~ 圆角、并自定义了几个图标的显示，节点列表等級0可见等級1节点但无法看见节点详情，增加了国家图标显示
- **商店**：商品增加同时连接设备数，用户限速属性
- 从肥羊那里**抄**来的：新用户注册现金奖励|高等级节点体验|设备数量限制
- **优化**：css和js等置入本地提升加载速度
- 增加**v2Ray** 功能，详情请看 [wiki](https://github.com/NimaQu/ss-panel-v3-mod_Uim/wiki/V2Ray-%E5%AF%B9%E6%8E%A5%E6%95%99%E7%A8%8B)

**原作者** [galaxychuck](https://github.com/galaxychuck)

**原作者** [NimaQu](https://github.com/NimaQu/ss-panel-v3-mod_Uim)

### 搭建教程

原作教材 : https://github.com/NimaQu/ss-panel-v3-mod_Uim/wiki/%E5%89%8D%E7%AB%AF%E5%AE%89%E8%A3%85