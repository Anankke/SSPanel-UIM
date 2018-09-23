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

### 相关截图
<img src="http://ww1.sinaimg.cn/large/006v0omggy1fvgz36p0ckj30u02kck43.jpg" width="300"/>
<img src="http://ww1.sinaimg.cn/large/006v0omggy1fvgzmfn25pj30u02xodt6.jpg" width="300"/>

### 使用步骤:
#### 1. 设置配置文件
在配置文件```/config/.config.php```中的```$System_Config['payment_system']```后插入以下内容:
```
//支付系统设置--------------------------------------------------------------------
#取值 codepay,doiampay,paymentwall,zfbjk,spay,f2fpay,yftpay,none,f2fpay_codepay,chenAlipay
$System_Config['payment_system']='chenAlipay';
```

#### 2. 导入数据
- 使用一般mysql工具导入sql/config.sql即可（不会影响数据）

#### 3. 获取支付宝COOKIE
- 浏览器访问：https://mbillexprod.alipay.com/enterprise/tradeListQuery.htm
- 登录支付宝账号
- 浏览器按f12再刷新一下
- 可以看到tradeListQuery.json
- 点击它找到Cookie: 后面就是cookie全部复制到后台配置框内

#### 4. 获取微信COOKIE
- 浏览器访问：https://wx.qq.com（此地址必须设置到后台支付设置里，登录完成后会有所变更）
- 手机扫码登录微信账号
- 浏览器按f12再刷新一下
- 可以看到webwxinit?r=*******
- 点击它找到Cookie: 后面有cookie了

#### 5. 获取微信&支付宝付款码
- 直接到支付点加号
- 找到收付款
- 点击二维码收款
- 设置金额（如果你想多金额）
- 保存收款码
- 访问：https://cli.im/deqr 上传付款码二维码
- 解析出来的地址复制到后台管理的二维码地址框

#### 6. 添加定时任务
```
# crontab -e
*/1 * * * * php /你的目录/xcat alipay
*/1 * * * * php /你的目录/xcat wxpay
```

### 特别说明:
- 出现cookie失效有可能是服务器无法访问相关接口原因导致掉线
- eamil通知必须要设置其中的一个邮箱
- 刷新频率为10秒一次
- 打算弄个xposed hook来实时生成付款码可以解决多个人无法同时支付问题

### 大概流程
<img src="http://ww1.sinaimg.cn/large/006v0omggy1fv6sq3h0dfg308s0fnx6s.gif" width="250"/>
<img src="http://ww1.sinaimg.cn/large/006v0omggy1fvgyx8bf97g304p08cb2a.gif" width="250"/>

### 赞助我才能有更多的动力啊 哈哈
<img src="http://ww1.sinaimg.cn/large/006v0omggy1fvgzvir0aij30q913t406.jpg" width="300"/>
<img src="http://ww1.sinaimg.cn/large/006v0omggy1fvgzwth0dvj30u715fwgz.jpg" width="300"/>

### 赞助老铁
@TNOID| 咸鱼萌新|破墙机场 @anlio

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