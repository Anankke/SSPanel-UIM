# 欢迎使用ss panel v3 mod 自带支付宝与微信监听修改版

- 免签约支付宝程序自检测 根据COOKIE
- 免签约微信支付程序自检测 根据COOKIE
- EMAIL通知失效情况
- 加入XCAT命令
- 加入定时任务自动检测
- 相关配置在.config.php
- 不定时同步[NimaQu](https://github.com/NimaQu/ss-panel-v3-mod_Uim)库
- 我的站点[云](http://yun.9in.info)
- 我的博客[CHEN](http://9in.info)
### 本次更新
- 免签约微信支付程序自检测 根据COOKIE
- 支付宝判断完善
- 微信判断完善
- 多人同时支付错乱问题（点击x时会自动删除改订单间隔尽量缩短）
- 每分钟更改为5次的检测（测试不会被ban）
- 完善检测失效机制
- 添加手动开启支付开关（检测机制有可能在cookie失效情况下会直接关掉）
- 添加固定金额支付模式（也可手动输入支付模式【未测试】）

### 特别说明
- 出现cookie失效有可能是服务器无法访问相关接口原因导致掉线
- 打算弄个xposed hook来实时生成付款码可以解决多个人无法同时支付问题

### 相关截图
<img src="http://ww1.sinaimg.cn/large/006v0omggy1fvgz36p0ckj30u02kck43.jpg" width="300"/>
<img src="http://ww1.sinaimg.cn/large/006v0omggy1fvgzmfn25pj30u02xodt6.jpg" width="300"/>

### 运行
    # 数据库导入
    sql/config.sql

    # crontab -e
    */1 * * * * php /你的目录/xcat alipay
    */1 * * * * php /你的目录/xcat wxpay

### 支付宝获取COOKIE
    https://mbillexprod.alipay.com/enterprise/tradeListQuery.htm
    访问后按F12查看
    https://mbillexprod.alipay.com/enterprise/tradeListQuery.json
    接口
    
### 微信获取COOKIE
    https://wx.qq.com
    访问后按F12查看
    https://wx.qq.com/cgi-bin/mmwebwx-bin/webwxinit
    接口
    设置中微信登录地址一定要登录的地址

### 大概流程
<img src="http://ww1.sinaimg.cn/large/006v0omggy1fv6sq3h0dfg308s0fnx6s.gif" width="250"/>
<img src="http://ww1.sinaimg.cn/large/006v0omggy1fvgyx8bf97g304p08cb2a.gif" width="250"/>

### 赞助我才能有更多的动力啊 哈哈
<img src="http://ww1.sinaimg.cn/large/006v0omggy1fvgzvir0aij30q913t406.jpg" width="300"/>
<img src="http://ww1.sinaimg.cn/large/006v0omggy1fvgzwth0dvj30u715fwgz.jpg" width="300"/>

### 赞助老铁
@TNOID| 咸鱼萌新|破墙机场

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