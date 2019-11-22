{include file='user/main.tpl'}
{$ssr_prefer = URL::SSRCanConnect($user, 0)}

<style>
.table {
    box-shadow: none;
}
table tr td:first-child {
    text-align: right;
    font-weight: bold;
}
</style>

<main class="content">

    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">用户中心</h1>
        </div>
    </div>
    <div class="container">
        <section class="content-inner margin-top-no">
            <div class="ui-card-wrap">

                <div class="col-xx-12 col-xs-6 col-lg-3">
                    <div class="card user-info">
                        <div class="user-info-main">
                            <div class="nodemain">
                                <div class="nodehead node-flex">
                                    <div class="nodename">帐号等级</div>
                                </div>
                                <div class="nodemiddle node-flex">
                                    <div class="nodetype">
                                        {if $user->class!=0}
                                            <dd>VIP {$user->class}</dd>
                                        {else}
                                            <dd>普通用户</dd>
                                        {/if}
                                    </div>
                                </div>
                                <div class="nodemiddle node-flex">
                                    {if $user->class_expire!="1989-06-04 00:05:00"}
                                        <div style="font-size: 14px">等级到期时间 {$user->class_expire}</div>
                                    {else}
                                        <div style="font-size: 14px">账户等级不会过期</div>
                                    {/if}
                                </div>
                            </div>
                        </div>
                        <div class="user-info-bottom">
                            <div class="nodeinfo node-flex">
                                {if $user->class!=0}
                                    <span><i class="icon icon-md">add_circle</i>到期流量清空</span>
                                {else}
                                    <span><i class="icon icon-md">add_circle</i>升级解锁 VIP 节点</span>
                                {/if}
                                <a href="/user/shop" class="card-tag tag-orange">商店</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xx-12 col-xs-6 col-lg-3">
                    <div class="card user-info">
                        <div class="user-info-main">
                            <div class="nodemain">
                                <div class="nodehead node-flex">
                                    <div class="nodename">余额</div>
                                </div>
                                <div class="nodemiddle node-flex">
                                    <div class="nodetype">
                                        {$user->money} CNY
                                    </div>
                                </div>
                                <div class="nodemiddle node-flex">
                                    <div style="font-size: 14px">账户有效时间 {$user->expire_in}</div>
                                </div>
                            </div>
                        </div>
                        <div class="user-info-bottom">
                            <div class="nodeinfo node-flex">
                                <span><i class="icon icon-md">attach_money</i>到期账户自动删除</span>
                                <a href="/user/code" class="card-tag tag-green">充值</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xx-12 col-xs-6 col-lg-3">
                    <div class="card user-info">
                        <div class="user-info-main">
                            <div class="nodemain">
                                <div class="nodehead node-flex">
                                    <div class="nodename">在线设备数</div>
                                </div>
                                <div class="nodemiddle node-flex">
                                    <div class="nodetype">
                                        {if $user->node_connector!=0}
                                            <dd>{$user->online_ip_count()} / {$user->node_connector}</dd>
                                        {else}
                                            <dd>{$user->online_ip_count()} / 不限制</dd>
                                        {/if}
                                    </div>
                                </div>
                                <div class="nodemiddle node-flex">
                                    {if $user->lastSsTime()!="从未使用喵"}
                                        <div style="font-size: 14px">上次使用：{$user->lastSsTime()}</div>
                                    {else}
                                        <div style="font-size: 14px">从未使用过</div>
                                    {/if}
                                </div>
                            </div>
                        </div>
                        <div class="user-info-bottom">
                            <div class="nodeinfo node-flex">
                                <span><i class="icon icon-md">donut_large</i>在线设备/设备限制数</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xx-12 col-xs-6 col-lg-3">
                    <div class="card user-info">
                        <div class="user-info-main">
                            <div class="nodemain">
                                <div class="nodehead node-flex">
                                    <div class="nodename">端口速率</div>
                                </div>
                                <div class="nodemiddle node-flex">
                                    <div class="nodetype">
                                        {if $user->node_speedlimit!=0}
                                            <dd><code>{$user->node_speedlimit}</code>Mbps</dd>
                                        {else}
                                            <dd>无限制</dd>
                                        {/if}
                                    </div>
                                </div>
                                <div class="nodemiddle node-flex">
                                    <div style="font-size: 14px">实际速率受限于运营商带宽上限</div>
                                </div>
                            </div>
                        </div>
                        <div class="user-info-bottom">
                            <div class="nodeinfo node-flex">
                                <span><i class="icon icon-md">signal_cellular_alt</i>账户最高下行网速</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="ui-card-wrap">

                <div class="col-xx-12 col-sm-5">

                    <div class="card">
                        <div class="card-main">
                        <div class="card-inner margin-bottom-no">
                            <p class="card-heading" style="margin-bottom: 0;"><i class="icon icon-md">account_circle</i>流量使用情况</p>
                                <div class="progressbar">
                                    <div class="before"></div>
                                    <div class="bar tuse color3"
                                         style="width:calc({($user->transfer_enable==0)?0:($user->u+$user->d-$user->last_day_t)/$user->transfer_enable*100}%);"></div>
                                    <div class="label-flex">
                                        <div class="label la-top">
                                            <div class="bar ard color3"></div>
                                            <span class="traffic-info">今日已用</span>
                                            <code class="card-tag tag-red">{$user->TodayusedTraffic()}</code>
                                        </div>
                                    </div>
                                </div>
                                <div class="progressbar">
                                    <div class="before"></div>
                                    <div class="bar ard color2"
                                         style="width:calc({($user->transfer_enable==0)?0:$user->last_day_t/$user->transfer_enable*100}%);">
                                        <span></span>
                                    </div>
                                    <div class="label-flex">
                                        <div class="label la-top">
                                            <div class="bar ard color2"><span></span></div>
                                            <span class="traffic-info">过去已用</span>
                                            <code class="card-tag tag-orange">{$user->LastusedTraffic()}</code>
                                        </div>
                                    </div>
                                </div>
                                <div class="progressbar">
                                    <div class="before"></div>
                                    <div class="bar remain color"
                                         style="width:calc({($user->transfer_enable==0)?0:($user->transfer_enable-($user->u+$user->d))/$user->transfer_enable*100}%);">
                                        <span></span>
                                    </div>
                                    <div class="label-flex">
                                        <div class="label la-top">
                                            <div class="bar ard color"><span></span></div>
                                            <span class="traffic-info">剩余流量</span>
                                            <code class="card-tag tag-green" id="remain">{$user->unusedTraffic()}</code>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="card-inner margin-bottom-no">
                                <p class="card-heading"><i class="icon icon-md">account_circle</i>签到</p>

                                    <p>上次签到时间：{$user->lastCheckInTime()}</p>

                                    <p id="checkin-msg"></p>

                                    {if $geetest_html != null}
                                        <div id="popup-captcha"></div>
                                    {/if}
                                    {if $recaptcha_sitekey != null && $user->isAbleToCheckin()}
                                        <div class="g-recaptcha" data-sitekey="{$recaptcha_sitekey}"></div>
                                    {/if}

                                    <div class="card-action">
                                        <div class="usercheck pull-left">
                                            {if $user->isAbleToCheckin() }
                                                <div id="checkin-btn">
                                                    <button id="checkin" class="btn btn-brand btn-flat"><span
                                                                class="icon">check</span>&nbsp;点我签到&nbsp;
                                                        <div><span class="icon">screen_rotation</span>&nbsp;或者摇动手机签到
                                                        </div>
                                                    </button>
                                                </div>
                                            {else}
                                                <p><a class="btn btn-brand disabled btn-flat" href="#"><span
                                                                class="icon">check</span>&nbsp;今日已签到</a></p>
                                            {/if}
                                        </div>
                                    </div>
                                </dl>
                            </div>

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-main">
                            <div class="card-inner margin-bottom-no">
                                <p class="card-heading"><i class="icon icon-md">notifications_active</i>公告栏</p>
                                {if $ann != null}
                                    <p>{$ann->content}</p>
                                    <br/>
                                    <strong>查看所有公告请<a href="/user/announcement">点击这里</a></strong>
                                {/if}
                                {if $config["enable_admin_contact"] == 'true'}
                                    <p class="card-heading">管理员联系方式</p>
                                    {if $config["admin_contact1"]!=null}
                                        <p>{$config["admin_contact1"]}</p>
                                    {/if}
                                    {if $config["admin_contact2"]!=null}
                                        <p>{$config["admin_contact2"]}</p>
                                    {/if}
                                    {if $config["admin_contact3"]!=null}
                                        <p>{$config["admin_contact3"]}</p>
                                    {/if}
                                {/if}
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-xx-12 col-sm-7">

                    <div class="card quickadd">
                        <div class="card-main">
                            <div class="card-inner">
                                <div class="cardbtn-edit">
                                    <div class="card-heading"><i class="icon icon-md">phonelink</i> 快速添加节点</div>
                                    <div class="reset-flex"><span>重置订阅链接</span><a class="reset-link btn btn-brand-accent btn-flat"><i class="icon">autorenew</i>&nbsp;</a></div>
                                </div>
                                <nav class="tab-nav margin-top-no">
                                    <ul class="nav nav-list">
                                        <li {if $ssr_prefer}class="active"{/if}>
                                            <a class="" data-toggle="tab" href="#all_ssr"><i class="icon icon-lg">airplanemode_active</i>&nbsp;SSR</a>
                                        </li>
                                        <li {if !$ssr_prefer}class="active"{/if}>
                                            <a class="" data-toggle="tab" href="#all_ss"><i class="icon icon-lg">flight_takeoff</i>&nbsp;SS/SSD</a>
                                        </li>

                                        <li>
                                            <a class="" data-toggle="tab" href="#all_v2ray"><i class="icon icon-lg">flight_land</i>&nbsp;V2RAY</a>
                                        </li>
                                    </ul>
                                </nav>
                                <div>
                                    <div class="tab-content">
                                        <div class="tab-pane fade {if $ssr_prefer}active in{/if}" id="all_ssr">
                                            {$pre_user = URL::cloneUser($user)}

                                                {$user = URL::getSSRConnectInfo($pre_user)}
                                                {$ssr_url_all = URL::getAllUrl($pre_user, 0, 0)}
                                                {$ssr_url_all_mu = URL::getAllUrl($pre_user, 1, 0)}
                                                {if URL::SSRCanConnect($user)}
                                                <table class="table">
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>端口</strong></td>
                                                            <td>{$user->port}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>密码</strong></td>
                                                            <td>{$user->passwd}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>自定义加密</strong></td>
                                                            <td>{$user->method}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>自定义协议</strong></td>
                                                            <td>{$user->protocol}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>自定义混淆</strong></td>
                                                            <td>{$user->obfs}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>自定义混淆参数</strong></td>
                                                            <td>{$user->obfs_param}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                <br>

                                                {if $mergeSub!='true'}
                                                <div>
                                                    <span class="icon icon-lg text-white">flash_auto</span> 普通节点订阅地址：
                                                </div>
                                                <div class="float-clear">
                                                    <input type="text" class="input form-control form-control-monospace cust-link col-xx-12 col-sm-8 col-lg-7" name="input1" value="{$subUrl}{$ssr_sub_token}?mu=0" readonly="true">
                                                    <button class="copy-text btn btn-subscription col-xx-12 col-sm-3 col-lg-2" type="button" data-clipboard-text="{$subUrl}{$ssr_sub_token}?mu=0">点击复制</button>
                                                </div>
                                                <br>
                                                <div>
                                                    <span class="icon icon-lg text-white">flash_auto</span> 单端口节点订阅地址：
                                                </div>
                                                <div class="float-clear">
                                                    <input type="text" class="input form-control form-control-monospace cust-link col-xx-12 col-sm-8 col-lg-7" name="input1" value="{$subUrl}{$ssr_sub_token}?mu=1" readonly="true">
                                                    <button class="copy-text btn btn-subscription col-xx-12 col-sm-3 col-lg-2" type="button" data-clipboard-text="{$subUrl}{$ssr_sub_token}?mu=1">点击复制</button>
                                                </div>
                                                {else}
                                                <div>
                                                    <span class="icon icon-lg text-white">flash_auto</span> 订阅地址：
                                                </div>
                                                <div class="float-clear">
                                                    <input type="text" class="input form-control form-control-monospace cust-link col-xx-12 col-sm-8 col-lg-7" name="input1" value="{$subUrl}{$ssr_sub_token}" readonly="true">
                                                    <button class="copy-text btn btn-subscription col-xx-12 col-sm-3 col-lg-2" type="button" data-clipboard-text="{$subUrl}{$ssr_sub_token}">点击复制</button>
                                                </div>
                                                {/if}

                                                <br>

                                                    {if $mergeSub!='true'}
                                                        <button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{$ssr_url_all}">点击复制 SSR 普通端口节点链接</button>
                                                        <button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{$ssr_url_all_mu}">点击复制 SSR 单端口多用户链接</button>
                                                    {else}
                                                        <button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{$ssr_url_all}">点击复制全部 SSR 节点链接</button>
                                                    {/if}

                                                {else}
                                                    <p>您好，您目前的 加密方式，混淆，或者协议设置在 ShadowsocksR 客户端下无法连接。请您选用 Shadowsocks
                                                        客户端来连接，或者到 资料编辑 页面修改后再来查看此处</p>
                                                    <p>同时, ShadowsocksR 单端口多用户的连接不受您设置的影响,您可以在此使用相应的客户端进行连接~</p>
                                                    <p>请注意，在当前状态下您的 SSR 订阅链接已经失效，您无法通过此种方式导入节点</p>
                                                {/if}

                                        </div>

                                        <div class="tab-pane fade {if !$ssr_prefer}active in{/if}" id="all_ss">
                                                {$user = URL::getSSConnectInfo($pre_user)}
                                                {$ss_url_all_mu = URL::getAllUrl($pre_user, 1, 1)}
                                                {$ss_url_all = URL::getAllUrl($pre_user, 0, 2)}
                                                {$ssd_url_all =URL::getAllSSDUrl($user)}

                                                {if URL::SSCanConnect($user)}
                                                <table class="table">
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>端口</strong></td>
                                                            <td>{$user->port}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>密码</strong></td>
                                                            <td>{$user->passwd}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>自定义加密</strong></td>
                                                            <td>{$user->method}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>自定义混淆</strong></td>
                                                            <td>{$user->obfs}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>自定义混淆参数</strong></td>
                                                            <td>{$user->obfs_param}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <br>
                                                <div>
                                                    <span class="icon icon-lg text-white">flash_auto</span> SSD 节点订阅地址
                                                </div>
                                                <div class="float-clear">
                                                    <input type="text" class="input form-control form-control-monospace cust-link col-xx-12 col-sm-8 col-lg-7" name="input1" readonly value="{$subUrl}{$ssr_sub_token}?mu=3" readonly="true">
                                                    <button class="copy-text btn btn-subscription col-xx-12 col-sm-3 col-lg-2" type="button" data-clipboard-text="{$subUrl}{$ssr_sub_token}?mu=3">点击复制
                                                    </button>
                                                </div>

                                                <br>
                                                <button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{$ssd_url_all}">点击复制全部 SSD 节点链接</button>
                                                <button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{$ss_url_all}">点击复制全部 SS 节点链接</button>
                                                
                                                {else}
                                                    <p>您好，您目前的 加密方式，混淆，或者协议设置在 SS 客户端下无法连接。请您选用 SSR 客户端来连接，或者到 资料编辑 页面修改后再来查看此处</p>
                                                    <p>同时, Shadowsocks 单端口多用户的连接不受您设置的影响,您可以在此使用相应的客户端进行连接~</p>
                                                {/if}
                                        </div>

                                        <div class="tab-pane fade" id="all_v2ray">

                                                {$v2_url_all = URL::getAllVMessUrl($user)}
                                                

                                                <div><span class="icon icon-lg text-white">flash_auto</span> 订阅地址：</div>
                                                <div class="float-clear">
                                                    <input type="text" class="input form-control form-control-monospace cust-link col-xx-12 col-sm-8 col-lg-7" name="input1" value="{$subUrl}{$ssr_sub_token}?mu=2" readonly="true"/>
                                                    <button class="copy-text btn btn-subscription col-xx-12 col-sm-3 col-lg-2" type="button" data-clipboard-text="{$subUrl}{$ssr_sub_token}?mu=2">点击复制</button>
                                                </div>
                                                <br>
                                                <button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{$v2_url_all}">点击复制全部 VMess 链接</button>

                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-main">
                            <div class="card-inner margin-bottom-no">
                                <div class="card-heading"><i class="icon icon-md">phonelink</i> 客户端下载</div>

                                <nav class="tab-nav margin-top-no">
                                    <ul class="nav nav-list">
                                        <li {if $ssr_prefer}class="active"{/if}>
                                            <a class="" data-toggle="tab" href="#all_ssr_client"><i class="icon icon-lg">airplanemode_active</i>&nbsp;SSR</a>
                                        </li>
                                        <li {if !$ssr_prefer}class="active"{/if}>
                                            <a class="" data-toggle="tab" href="#all_ss_client"><i class="icon icon-lg">flight_takeoff</i>&nbsp;SS/SSD</a>
                                        </li>
                                        <li>
                                            <a class="" data-toggle="tab" href="#all_v2ray_client"><i class="icon icon-lg">flight_land</i>&nbsp;V2RAY</a>
                                        </li>

                                        {if $display_ios_class>=0}
                                        <li>
                                            <a class="" data-toggle="tab" href="#all_appid_client"><i class="icon icon-lg">phone_iphone</i>&nbsp;iOS 公共账号</a>
                                        </li>
                                        {/if}
                                    </ul>
                                </nav>
                                <div class="card-inner">
                                    <div class="tab-content">
                                        <div class="tab-pane fade {if $ssr_prefer}active in{/if}" id="all_ssr_client">
                                            <p><i class="icon icon-lg">laptop_windows</i> Windows：下载 <a href="{if $config['subscribe_client']!='true'}/ssr-download/ssr-win.7z{else}/user/getPcClient?type=ssr-win{/if}" target="_blank">ShadowsocksRR Windows</a> 或 <a href="/ssr-download/SSTap.7z" target="_blank">SSTap</a></p>
                                            <p><i class="icon icon-lg">laptop_mac</i> macOS：<a href="/ssr-download/ssr-mac.dmg" target="_blank">下载 ShadowsocksX-NG-R8</a></p>
                                            <p><i class="icon icon-lg">laptop_windows</i> Linux（GUI）：<a href="/ssr-download/ssr-linux.AppImage" target="_blank">下载 Electron SSR</a></p>
                                            <p><i class="icon icon-lg">android</i> Android：下载 <a href="/ssr-download/ssrr-android.apk">SSRR</a> 或 <a href="/ssr-download/ssr-android.apk">SSR</a></p>
                                            <p><i class="icon icon-lg">phone_iphone</i> iOS：可以使用 Shadowrocket、Quantumult、Potatso 或 Potatso Lite</p>
                                            <p><i class="icon icon-lg">router</i> Koolshare 固件路由器/软路由：前往 <a href="https://github.com/hq450/fancyss_history_package" target="_blank">FancySS 下载页面</a></p>
                                        </div>
                                        <div class="tab-pane fade {if !$ssr_prefer}active in{/if}" id="all_ss_client">
                                            <p><i class="icon icon-lg">laptop_windows</i> Windows：下载 <a href="{if $config['subscribe_client']!='true'}/ssr-download/ssd-win.7z{else}/user/getPcClient?type=ssd-win{/if}" target="_blank">SSD Windows</a>，<a href="{if $config['subscribe_client']!='true'}/ssr-download/ss-win.zip{else}/user/getPcClient?type=ss-win{/if}" target="_blank">Shadowsocks Windows</a> 或 <a href="/ssr-download/SSTap.7z" target="_blank">SSTap</a></p>
                                            <p><i class="icon icon-lg">laptop_mac</i> macOS：<a href="/ssr-download/ss-mac.zip" target="_blank">下载 ShadowsocksX-NG</a></p>
                                            <p><i class="icon icon-lg">laptop_windows</i> Linux（GUI）：<a href="/ssr-download/ssr-linux.AppImage" target="_blank">下载 Electron SSR</a></p>
                                            <p><i class="icon icon-lg">android</i> Android：下载 <a href="/ssr-download/ss-android.apk">Shadowsocks Android</a> 或 <a href="/ssr-download/ssd-android.apk">SSD Android</a>。如果需要启用混淆还需要下载 <a href="/ssr-download/ss-android-obfs.apk">Simple-Obfs 混淆插件</a>。</p>
                                            <p><i class="icon icon-lg">phone_iphone</i> iOS：可以使用 Shadowrocket、Quantumult、Potatso 或 Potatso Lite</p>
                                            <p><i class="icon icon-lg">router</i> Koolshare 固件路由器/软路由：前往 <a href="https://github.com/hq450/fancyss_history_package" target="_blank">FancySS 下载页面</a></p>
                                        </div>
                                        <div class="tab-pane fade" id="all_v2ray_client">
                                            <p><i class="icon icon-lg">laptop_windows</i> Windows：下载 <a href="/ssr-download/v2rayn.zip" target="_blank">V2RayN</a></p>
                                            <p><i class="icon icon-lg">laptop_mac</i> macOS：下载 <a href="/ssr-download/V2rayU.dmg">V2RayU</a></p>
                                            <p><i class="icon icon-lg">android</i> Android：下载 <a href="/ssr-download/v2rayng.apk">V2RayNG</a></p>
                                            <p><i class="icon icon-lg">phone_iphone</i> iOS：可以使用 Shadowrocket</p>
                                            <p><i class="icon icon-lg">router</i> Koolshare 固件路由器/软路由：前往 <a href="https://github.com/hq450/fancyss_history_package/tree/master/fancyss_X64" target="_blank">FancySS 历史下载页面</a> 下载 v2ray 插件</p>
                                        </div>

                                        {if $display_ios_class>=0}
                                        <div class="tab-pane fade" id="all_appid_client">
                                            {if $user->class>=$display_ios_class && $user->get_top_up()>=$display_ios_topup}
                                                <div>
                                                    <span class="icon icon-lg text-white">account_box</span>本站iOS账户：
                                                </div>
                                                <div class="float-clear">
                                                    <input type="text" class="input form-control form-control-monospace cust-link col-xx-12 col-sm-8 col-lg-7" name="input1" readonly value="{$ios_account}" readonly="true">
                                                    <button class="copy-text btn btn-subscription col-xx-12 col-sm-3 col-lg-2" type="button" data-clipboard-text="{$ios_account}">点击复制</button>
                                                    <br>
                                                </div>

                                                <div>
                                                    <span class="icon icon-lg text-white">lock</span> 本站iOS密码：
                                                </div>
                                                <div class="float-clear">
                                                    <input type="text" class="input form-control form-control-monospace cust-link col-xx-12 col-sm-8 col-lg-7" name="input1" readonly value="{$ios_password}" readonly="true">
                                                    <button class="copy-text btn btn-subscription col-xx-12 col-sm-3 col-lg-2" type="button" data-clipboard-text="{$ios_password}">点击复制</button>
                                                    <br>
                                                </div>
                                                <p>
                                                    <span class="icon icon-lg text-white">error</span>
                                                    <strong>禁止将账户分享给他人！</strong>
                                                </p>
                                                <br>
                                            {else}
                                                <p class="card-heading" align="center">
                                                    <i class="icon icon-lg">visibility_off</i> <b>等级至少为<code>{$display_ios_class}</code>且累计充值大于<code>{$display_ios_topup}</code>时可见，如需升级请<a href="/user/shop">点击这里</a>升级套餐</b>
                                                </p>
                                            {/if}

                                        </div>
                                        {/if}

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            {include file='dialog.tpl'}

    </div>


    </div>
    </section>
    </div>
</main>


{include file='user/footer.tpl'}

<script src="https://cdn.jsdelivr.net/npm/shake.js@1.2.2/shake.min.js"></script>

<script>
    ;(function () {
        'use strict'

        let onekeysubBTN = document.querySelectorAll('[data-onekeyfor]');
        for (let i = 0; i < onekeysubBTN.length; i++) {
            onekeysubBTN[i].addEventListener('click', () => {
                let onekeyId = onekeysubBTN[i].dataset.onekeyfor;
                AddSub(onekeyId);
            });
        }

        function AddSub(id) {
            let url = $$getValue(id),
                tmp = window.btoa(url);

            tmp = tmp.substring(0, tmp.length);
            url = "sub://" + tmp + "#";
            window.location.href = url;
        }
    })();
</script>

<script>

    function DateParse(str_date) {
        var str_date_splited = str_date.split(/[^0-9]/);
        return new Date(str_date_splited[0], str_date_splited[1] - 1, str_date_splited[2], str_date_splited[3], str_date_splited[4], str_date_splited[5]);
    }

</script>

<script>

    $(function () {
        new Clipboard('.copy-text');
    });

    $(".copy-text").click(function () {
        $("#result").modal();
        $$.getElementById('msg').innerHTML = '已复制，请您继续接下来的操作';
    });

    $(function () {
        new Clipboard('.reset-link');
    });

    $(".reset-link").click(function () {
        $("#result").modal();
        $$.getElementById('msg').innerHTML = '已重置您的订阅链接，请变更或添加您的订阅链接！';
        window.setTimeout("location.href='/user/url_reset'", {$config['jump_delay']});
    });

    {if $user->transfer_enable-($user->u+$user->d) == 0}
    window.onload = function () {
        $("#result").modal();
        $$.getElementById('msg').innerHTML = '您的流量已经用完或账户已经过期了，如需继续使用，请进入商店选购新的套餐~';
    };
    {/if}

    {if $geetest_html == null}

    var checkedmsgGE = '<p><a class="btn btn-brand disabled btn-flat waves-attach" href="#"><span class="icon">check</span>&nbsp;已签到</a></p>';
    window.onload = function () {
        var myShakeEvent = new Shake({
            threshold: 15
        });

        myShakeEvent.start();
        window.addEventListener('shake', shakeEventDidOccur, false);

        function shakeEventDidOccur() {
            if ("vibrate" in navigator) {
                navigator.vibrate(500);
            }

            $.ajax({
                type: "POST",
                url: "/user/checkin",
                dataType: "json",{if $recaptcha_sitekey != null}
                data: {
                    recaptcha: grecaptcha.getResponse()
                },{/if}
                success: (data) => {
                    if (data.ret) {

                        $$.getElementById('checkin-msg').innerHTML = data.msg;
                        $$.getElementById('checkin-btn').innerHTML = checkedmsgGE;
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                        $$.getElementById('remain').innerHTML = data.trafficInfo['unUsedTraffic'];
                        $('.bar.remain.color').css('width', (data.unflowtraffic - ({$user->u}+{$user->d})) / data.unflowtraffic * 100 + '%');
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = `发生错误：${
                            jqXHR.status
                            }`;
                }
            });
        }
    };


    $(document).ready(function () {
        $("#checkin").click(function () {
            $.ajax({
                type: "POST",
                url: "/user/checkin",
                dataType: "json",{if $recaptcha_sitekey != null}
                data: {
                    recaptcha: grecaptcha.getResponse()
                },{/if}
                success: (data) => {
                    if (data.ret) {
                        $$.getElementById('checkin-msg').innerHTML = data.msg;
                        $$.getElementById('checkin-btn').innerHTML = checkedmsgGE;
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                        $$.getElementById('remain').innerHTML = data.trafficInfo['unUsedTraffic'];
                        $('.bar.remain.color').css('width', (data.unflowtraffic - ({$user->u}+{$user->d})) / data.unflowtraffic * 100 + '%');
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = `发生错误：${
                            jqXHR.status
                            }`;
                }
            })
        })
    })


    {else}


    window.onload = function () {
        var myShakeEvent = new Shake({
            threshold: 15
        });

        myShakeEvent.start();

        window.addEventListener('shake', shakeEventDidOccur, false);

        function shakeEventDidOccur() {
            if ("vibrate" in navigator) {
                navigator.vibrate(500);
            }

            c.show();
        }
    };


    var handlerPopup = function (captchaObj) {
        c = captchaObj;
        captchaObj.onSuccess(function () {
            var validate = captchaObj.getValidate();
            $.ajax({
                url: "/user/checkin", // 进行二次验证
                type: "post",
                dataType: "json",
                data: {
                    // 二次验证所需的三个值
                    geetest_challenge: validate.geetest_challenge,
                    geetest_validate: validate.geetest_validate,
                    geetest_seccode: validate.geetest_seccode
                },
                success: (data) => {
                    if (data.ret) {
                        $$.getElementById('checkin-msg').innerHTML = data.msg;
                        $$.getElementById('checkin-btn').innerHTML = checkedmsgGE;
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                        $$.getElementById('remain').innerHTML = data.trafficInfo['unUsedTraffic'];
                        $('.bar.remain.color').css('width', (data.unflowtraffic - ({$user->u}+{$user->d})) / data.unflowtraffic * 100 + '%');
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = `发生错误：${
                            jqXHR.status
                            }`;
                }
            });
        });
        // 弹出式需要绑定触发验证码弹出按钮
        //captchaObj.bindOn("#checkin")
        // 将验证码加到id为captcha的元素里
        captchaObj.appendTo("#popup-captcha");
        // 更多接口参考：http://www.geetest.com/install/sections/idx-client-sdk.html
    };

    initGeetest({
        gt: "{$geetest_html->gt}",
        challenge: "{$geetest_html->challenge}",
        product: "popup", // 产品形式，包括：float，embed，popup。注意只对PC版验证码有效
        offline: {if $geetest_html->success}0{else}1{/if} // 表示用户后台检测极验服务器是否宕机，与SDK配合，用户一般不需要关注
    }, handlerPopup);



    {/if}


</script>
{if $recaptcha_sitekey != null}
    <script src="https://recaptcha.net/recaptcha/api.js" async defer></script>
{/if}
