{include file='user/main.tpl'}
{$ssr_prefer = URL::SSRCanConnect($user, 0)}
{$pre_user = URL::cloneUser($user)}

<style>
    .table {
        box-shadow: none;
    }

    table tr td:first-child {
        text-align: left;
        font-weight: bold;
    }

    #connection-info {
        overflow: auto;
        width: 100%;
    }

    #connection-info-table {
        width: 100%;
        table-layout: fixed;
        word-break: break-all;
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
                                    <span><i class="mdi mdi-cached icon-md"></i>到期流量清空</span>
                                {else}
                                    <span><i class="mdi mdi-arrow-up-circle icon-md"></i>升级解锁 VIP 节点</span>
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
                                    <div style="font-size: 14px">账户有效时间：{substr($user->expire_in, 0, 10)}</div>
                                </div>
                            </div>
                        </div>
                        <div class="user-info-bottom">
                            <div class="nodeinfo node-flex">
                                <span><i class="mdi mdi-account icon-md"></i>到期账户自动删除</span>
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
                                            <dd>{$user->onlineIpCount()} / {$user->node_connector}</dd>
                                        {else}
                                            <dd>{$user->onlineIpCount()} / 不限制</dd>
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
                                <span><i class="mdi mdi-devices icon-md"></i>在线设备/设备限制数</span>
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
                                <span><i class="mdi mdi-signal icon-md"></i>账户最高下行网速</span>
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
                            <p class="card-heading" style="margin-bottom: 0;"><i class="mdi mdi-account-circle icon-md"></i>流量使用情况</p>
                                {if $user->validUseLoop() != '未购买套餐.'}
                                <p>下次流量重置时间：{$user->validUseLoop()}</p>
                                {/if}
                                <div class="progressbar">
                                    <div class="before"></div>
                                    <div class="bar tuse color3"
                                         style="width:calc({($user->transfer_enable==0)?0:($user->u+$user->d-$user->last_day_t)/$user->transfer_enable*100}%);"></div>
                                    <div class="label-flex">
                                        <div class="label la-top">
                                            <div class="bar ard color3"></div>
                                            <span class="traffic-info">今日已用</span>
                                            <code class="card-tag tag-red">{$user->todayUsedTraffic()}</code>
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
                                            <code class="card-tag tag-orange">{$user->lastUsedTraffic()}</code>
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
                            {if $config['enable_checkin'] == true}
                            <div class="card-inner margin-bottom-no">
                                <p class="card-heading"><i class="mdi mdi-account-circle icon-md"></i> 签到</p>
                                <p>上次签到时间：{$user->lastCheckInTime()}</p>
                                <p id="checkin-msg"></p>
                                {if $geetest_html != null}
                                    <div id="popup-captcha"></div>
                                {/if}
                                {if $config['enable_checkin_captcha'] == true && $config['captcha_provider'] == 'recaptcha' && $user->isAbleToCheckin()}
                                    <div class="g-recaptcha" data-sitekey="{$recaptcha_sitekey}"></div>
                                {/if}
                                <div class="card-action">
                                    <div class="usercheck pull-left">
                                        {if $user->isAbleToCheckin() }
                                            <div id="checkin-btn">
                                                <button id="checkin" class="btn btn-brand btn-flat"><span class="mdi mdi-check"></span>&nbsp;点我签到&nbsp;
                                                    <div><span class="mdi mdi-screen-rotation"></span>&nbsp;或者摇动手机签到</div>
                                                    </button>
                                            </div>
                                        {else}
                                            <p><a class="btn btn-brand disabled btn-flat" href="#"><span class="mdi mdi-check"></span>&nbsp;今日已签到</a></p>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                            {/if}
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-main">
                            <div class="card-inner margin-bottom-no">
                                <p class="card-heading"><i class="mdi mdi-bell-badge icon-md"></i> 最新公告</p>
                                {if $ann != null}
                                    <p>{$ann->content}</p>
                                    <br/>
                                    <strong>查看所有公告请<a href="/user/announcement">点击这里</a></strong>
                                {/if}
                                {if $config['enable_admin_contact'] == true}
                                    <p class="card-heading">如需帮助，请联系：</p>
                                    {if $config['admin_contact1'] != ''}
                                        <p>{$config['admin_contact1']}</p>
                                    {/if}
                                    {if $config['admin_contact2'] != ''}
                                        <p>{$config['admin_contact2']}</p>
                                    {/if}
                                    {if $config['admin_contact3'] != ''}
                                        <p>{$config['admin_contact3']}</p>
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
                                    <div class="card-heading"><i class="mdi mdi-monitor-cellphone-star icon-md"></i>&nbsp;快速使用</div>
                                </div>
                                <nav class="tab-nav margin-top-no">
                                    <ul class="nav nav-list">
                                        <li class="active">
                                            <a class="" data-toggle="tab" href="#sub_center"><i class="mdi mdi-information-variant icon-lg"></i>&nbsp;订阅中心</a>
                                        </li>
                                        <li>
                                            <a class="" data-toggle="tab" href="#info_center"><i class="mdi mdi-list-box-outline icon-lg"></i>&nbsp;连接信息</a>
                                        </li>
                                    </ul>
                                </nav>
                                <div class="card-inner">
                                    <div class="tab-content">
                                        <div class="tab-pane fade" id="info_center">
                                            <p>您的连接信息：</p>
                                            <div id="connection-info">
                                                <table id="connection-info-table" class="table">
                                                    <tbody>
                                                    <tr>
                                                        <td><strong>端口</strong></td>
                                                        <td>{$user->port}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>连接密码</strong></td>
                                                        <td>{$user->passwd}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>UUID</strong></td>
                                                        <td>{$user->uuid}</td>
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
                                            </div>
                                        </div>
                                        <div class="tab-pane fade active in" id="sub_center">
                                            <nav class="tab-nav margin-top-no">
                                                <ul class="nav nav-list">
                                                    <li class="active">
                                                        <a class="" data-toggle="tab" href="#sub_center_universal_subscription"><i class="mdi mdi-star icon-lg"></i>&nbsp;通用订阅</a>
                                                    </li>
                                                    <li>
                                                        <a class="" data-toggle="tab" href="#sub_center_general"><i class="mdi mdi-apps icon-lg"></i>&nbsp;协议/客户端专用订阅</a>
                                                    </li>
                                                    <li>
                                                        <a class="" data-toggle="tab" href="#sub_center_windows"><i class="mdi mdi-microsoft icon-lg"></i>&nbsp;Windows</a>
                                                    </li>
                                                    <li>
                                                        <a class="" data-toggle="tab" href="#sub_center_mac"><i class="mdi mdi-apple-finder icon-lg"></i>&nbsp;macOS</a>
                                                    </li>
                                                    <li>
                                                        <a class="" data-toggle="tab" href="#sub_center_ios"><i class="mdi mdi-apple-ios icon-lg"></i>&nbsp;iOS</a>
                                                    </li>
                                                    <li>
                                                        <a class="" data-toggle="tab" href="#sub_center_android"><i class="mdi mdi-android icon-lg"></i>&nbsp;Android</a>
                                                    </li>
                                                    <li>
                                                        <a class="" data-toggle="tab" href="#sub_center_linux"><i class="mdi mdi-linux icon-lg"></i>&nbsp;Linux Desktop</a>
                                                    </li>
                                                    <li>
                                                        <a class="" data-toggle="tab" href="#sub_center_router"><i class="mdi mdi-router-wireless icon-lg"></i>&nbsp;Router</a>
                                                    </li>
                                                </ul>
                                            </nav>
                                            <div class="tab-pane fade active in" id="sub_center_universal_subscription">
                                                <p>此处为通用订阅，适用于多种应用的订阅。</p>
                                                <hr/>
                                                <p>[ 所有节点 ]：
                                                    <a class="copy-text btn-dl" data-clipboard-text="{$getUniversalSub}/all"><i class="mdi mdi-send icon-sm"></i> 拷贝链接</a>
                                                </p>
                                            </div>
                                            <div class="tab-pane fade" id="sub_center_general">
                                                <p>此处的订阅为协议/客户端专用订阅，可能不适用于所有类型的客户端。</p>
                                                <hr/>
                                                <p>[ Shadowsocks ]：
                                                    <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['ss']}"><i class="mdi mdi-send icon-sm"></i> 拷贝订阅链接</a>.<a id="general_ss" class="copy-config btn-dl" onclick=Copyconfig("/user/getUserAllURL?type=ss","#general_ss","")><i class="mdi mdi-send icon-sm"></i> 拷贝全部节点 URL</a>
                                                </p>
                                                <hr/>
                                                <p>[ ShadowsocksR ]：
                                                    <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['ssr']}"><i class="mdi mdi-send icon-sm"></i> 拷贝订阅链接</a>.<a id="general_ssr" class="copy-config btn-dl" onclick=Copyconfig("/user/getUserAllURL?type=ssr","#general_ssr","")><i class="mdi mdi-send icon-sm"></i> 拷贝全部节点 URL</a>
                                                </p>
                                                <hr/>
                                                <p>[ V2Ray ]：
                                                    <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['v2ray']}"><i class="mdi mdi-send icon-sm"></i> 拷贝订阅链接</a>.<a id="general_v2ray" class="copy-config btn-dl" onclick=Copyconfig("/user/getUserAllURL?type=v2ray","#general_v2ray","")><i class="mdi mdi-send icon-sm"></i> 拷贝全部节点 URL</a>
                                                </p>
                                                <hr/>
                                                <p>[ Trojan ]：
                                                    <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['trojan']}"><i class="mdi mdi-send icon-sm"></i> 拷贝订阅链接</a>
                                                </p>
                                                <hr/>
                                                <p>[ Clash ]：
                                                    <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['clash']}"><i class="mdi mdi-send icon-sm"></i> 拷贝订阅链接</a>
                                                </p>
                                            </div>
                                            <div class="tab-pane fade" id="sub_center_windows">
                                                <p>Shadowsocks Windows - [ SS ]：</p>
                                                    <p>
                                                        应用下载：
                                                        <a class="btn-dl" href="/clients/Shadowsocks.zip"><i class="mdi mdi-tray-arrow-down icon-sm"></i> 本站下载</a>
                                                        .
                                                        <a class="btn-dl" href="https://github.com/shadowsocks/shadowsocks-windows/releases"><i class="mdi mdi-tray-arrow-down icon-sm"></i> GitHub 下载</a>
                                                    </p>
                                                    <p>
                                                        使用方式：
                                                        <a id="win_ss" class="copy-config btn-dl" onclick=Copyconfig("/user/getUserAllURL?type=ss","#win_ss","")><i class="mdi mdi-send icon-sm"></i> 拷贝全部节点 URL</a>
                                                    </p>
                                                <hr/>
                                                <p>ShadowsocksR Windows - [ SS/SSR ]：</p>
                                                    <p>
                                                        应用下载：
                                                        <a class="btn-dl" href="/clients/ShadowsocksR.7z"><i class="mdi mdi-tray-arrow-down icon-sm"></i> 本站下载</a>
                                                    </p>
                                                    <p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['ssr']}"><i class="mdi mdi-send icon-sm"></i> 拷贝订阅链接</a>
                                                        .
                                                        <a id="win_ssr" class="copy-config btn-dl" onclick=Copyconfig("/user/getUserAllURL?type=ssr","#win_ssr","")><i class="mdi mdi-send icon-sm"></i> 拷贝全部节点 URL</a>
                                                    </p>
                                                <hr/>
                                                <p>V2RayN - [ SS/VMess ]：</p>
                                                    <p>
                                                        应用下载：
                                                        <a class="btn-dl" href="/clients/v2rayN.zip"><i class="mdi mdi-tray-arrow-down icon-sm"></i> 本站下载 </a>
                                                        .
                                                        <a class="btn-dl" href="https://github.com/2dust/v2rayN/releases"><i class="mdi mdi-tray-arrow-down icon-sm"></i> GitHub 下载</a>
                                                    </p>
                                                    <p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['v2ray']}"><i class="mdi mdi-send icon-sm"></i> 拷贝订阅链接</a>
                                                        .
                                                        <a id="win_v2rayn" class="copy-config btn-dl" onclick=Copyconfig("/user/getUserAllURL?type=v2ray","#win_v2rayn","")><i class="mdi mdi-send icon-sm"></i> 拷贝全部节点 URL</a>
                                                    </p>
                                                <hr/>
                                                <p>Clash for Windows - [ SS/VMess/Trojan ]：</p>
                                                    <p>
                                                        应用下载：
                                                        <a class="btn-dl" href="/clients/Clash-Windows.exe"><i class="mdi mdi-tray-arrow-down icon-sm"></i> 本站下载</a>
                                                        .
                                                        <a class="btn-dl" href="https://github.com/Fndroid/clash_for_windows_pkg/releases"><i class="mdi mdi-tray-arrow-down icon-sm"></i> GitHub 下载</a>
                                                    </p>
                                                    <p>
                                                        使用方式：
                                                        <a class="btn-dl" href="{$subInfo['clash']}"><i class="mdi mdi-send icon-sm"></i> 配置文件下载</a>
                                                        .
                                                        <a class="btn-dl" href="clash://install-config?url={urlencode($subInfo['clash'])}"><i class="mdi mdi-send icon-sm"></i> 配置一键导入</a>
                                                    </p>
                                                <hr/>
                                            </div>
                                            <div class="tab-pane fade" id="sub_center_mac">
                                                <p>Surge - [ SS/VMess ]：</p>
                                                    <p>
                                                        应用下载：
                                                        <a class="btn-dl" href="https://nssurge.com/mac/v3/Surge-latest.zip"><i class="mdi mdi-tray-arrow-down icon-sm"></i> 官方网站下载</a>
                                                    </p>
                                                    <p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['surge4']}"><i class="mdi mdi-send icon-sm"></i> 拷贝 4.x 托管链接</a>
                                                        .
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['surge3']}"><i class="mdi mdi-send icon-sm"></i> 拷贝 3.x 托管链接</a>
                                                        .
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['surge_node']}"><i class="mdi mdi-send icon-sm"></i> 拷贝 3.x 节点链接</a>
                                                        .
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['surge2']}"><i class="mdi mdi-send icon-sm"></i> 拷贝 2.x 托管链接</a>
                                                    </p>
                                                <hr/>
                                                <p>ClashX - [ SS/VMess ]：</p>
                                                    <p>
                                                        应用下载：
                                                        <a class="btn-dl" href="/clients/ClashX.dmg"><i class="mdi mdi-tray-arrow-down icon-sm"></i> 本站下载</a>
                                                        .
                                                        <a class="btn-dl" href="https://github.com/yichengchen/clashX/releases"><i class="mdi mdi-tray-arrow-down icon-sm"></i> GitHub 下载</a>
                                                    </p>
                                                    <p>
                                                        使用方式：
                                                        <a class="btn-dl" href="{$subInfo['clash']}"><i class="mdi mdi-send icon-sm"></i> 配置文件下载</a>
                                                        .
                                                        <a class="btn-dl" href="clash://install-config?url={urlencode($subInfo['clash'])}"><i class="mdi mdi-send icon-sm"></i> 配置一键导入</a>
                                                    </p>
                                                <hr/>
                                                <p>V2RayU - [ SS/VMess ]：</p>
                                                    <p>
                                                        应用下载：
                                                        <a class="btn-dl" href="/clients/V2rayU.dmg"><i class="mdi mdi-tray-arrow-down icon-sm"></i> 本站下载(x86_64)</a>
                                                        .
                                                        <a class="btn-dl" href="/clients/V2rayU-arm64.dmg"><i class="mdi mdi-tray-arrow-down icon-sm"></i> 本站下载(arm64)</a>
                                                        .
                                                        <a class="btn-dl" href="https://github.com/yanue/V2rayU/releases"><i class="mdi mdi-tray-arrow-down icon-sm"></i> GitHub 下载</a>
                                                    </p>
                                                    <p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['v2ray']}"><i class="mdi mdi-send icon-sm"></i> 拷贝订阅链接</a>
                                                    </p>
                                                <hr/>
                                                <p>Clash for Windows - [ SS/VMess/Trojan ]：</p>
                                                    <p>
                                                        应用下载：
                                                        <a class="btn-dl" href="/clients/Clash-Windows.dmg"><i class="mdi mdi-tray-arrow-down icon-sm"></i> 本站下载</a>
                                                        .
                                                        <a class="btn-dl" href="https://github.com/Fndroid/clash_for_windows_pkg/releases"><i class="mdi mdi-tray-arrow-down icon-sm"></i> GitHub 下载</a>
                                                    </p>
                                                    <p>
                                                        使用方式：
                                                        <a class="btn-dl" href="{$subInfo['clash']}"><i class="mdi mdi-send icon-sm"></i> 配置文件下载</a>
                                                        .
                                                        <a class="btn-dl" href="clash://install-config?url={urlencode($subInfo['clash'])}"><i class="mdi mdi-send icon-sm"></i> 配置一键导入</a>
                                                    </p>
                                                <hr/>
                                            </div>
                                            <div class="tab-pane fade" id="sub_center_ios">
                                            {if $display_ios_class>=0}
                                                {if $user->class>=$display_ios_class && $user->getTopUp()>=$display_ios_topup}
                                                <div><span class="mdi mdi-account-box icon-lg text-white"></span> 本站iOS账户：</div>
                                                <div class="float-clear">
                                                    <input type="text" class="input form-control form-control-monospace cust-link col-xx-12 col-sm-8 col-lg-7" name="input1" readonly value="{$ios_account}" readonly="true">
                                                    <button class="copy-text btn btn-subscription col-xx-12 col-sm-3 col-lg-2" type="button" data-clipboard-text="{$ios_account}">点击复制</button>
                                                    <br>
                                                </div>
                                                <div><span class="mdi mdi-account-lock icon-lg text-white"></span> 本站iOS密码：</div>
                                                <div class="float-clear">
                                                    <input type="text" class="input form-control form-control-monospace cust-link col-xx-12 col-sm-8 col-lg-7" name="input1" readonly value="{$ios_password}" readonly="true">
                                                    <button class="copy-text btn btn-subscription col-xx-12 col-sm-3 col-lg-2" type="button" data-clipboard-text="{$ios_password}">点击复制</button>
                                                    <br>
                                                </div>
                                                <p><span class="mdi mdi-alert icon-lg text-white"></span><strong>禁止将账户分享给他人或登录 iCloud！</strong></p>
                                                <hr/>
                                                {/if}
                                            {/if}
                                                <p>Surge - [ SS/VMess ]：</p>
                                                    <p>
                                                        应用下载：
                                                        <a class="btn-dl" href="https://itunes.apple.com/us/app/surge-3/id1442620678?ls=1&mt=8"><i class="mdi mdi-currency-usd icon-sm"></i> Appstore 购买</a>
                                                    </p>
                                                    <p>
                                                        相关说明：
                                                        Surge 4 托管配置中可能含有 VMess 节点，如您未订阅 Surge 4 请使用 3.x 一键.
                                                        其中 2 & 3 & 4 代表 Surge 的版本.
                                                    </p>
                                                    <p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['surge4']}"><i class="mdi mdi-send icon-sm"></i> 拷贝 4.x 托管链接</a>
                                                        .
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['surge3']}"><i class="mdi mdi-send icon-sm"></i> 拷贝 3.x 托管链接</a>
                                                        .
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['surge_node']}"><i class="mdi mdi-send icon-sm"></i> 拷贝 3.x 节点链接</a>
                                                        .
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['surge2']}"><i class="mdi mdi-send icon-sm"></i> 拷贝 2.x 托管链接</a>
                                                    </p>
                                                <hr/>
                                                <p>Kitsunebi - [ SS/VMess ]：</p>
                                                    <p>
                                                        应用下载：
                                                        <a class="btn-dl" href="https://itunes.apple.com/us/app/kitsunebi-proxy-utility/id1446584073?ls=1&mt=8"><i class="mdi mdi-currency-usd icon-sm"></i> Appstore 购买</a>
                                                    </p>
                                                    <p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['kitsunebi']}"><i class="mdi mdi-send icon-sm"></i> 拷贝订阅链接</a>
                                                    </p>
                                                <hr/>
                                                <p>QuantumultX - [ SS/SSR/VMess ]：</p>
                                                    <p>
                                                        应用下载：
                                                        <a class="btn-dl" href="https://apps.apple.com/us/app/quantumult-x/id1443988620"><i class="mdi mdi-currency-usd icon-sm"></i> Appstore 购买</a>
                                                    </p>
                                                    <p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['quantumultx']}"><i class="mdi mdi-send icon-sm"></i> 拷贝订阅链接</a>
                                                    </p>
                                                <hr/>
                                                <p>Shadowrocket - [ SS/SSR/VMess/Trojan ]：</p>
                                                    <p>
                                                        应用下载：
                                                        <a class="btn-dl" href="https://itunes.apple.com/us/app/shadowrocket/id932747118?mt=8"><i class="mdi mdi-currency-usd icon-sm"></i> Appstore 购买</a>
                                                    </p>
                                                    <p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['shadowrocket']}"><i class="mdi mdi-send icon-sm"></i> 拷贝订阅链接</a>
                                                        .
                                                        <a class="btn-dl" onclick=AddSub("{$subInfo['shadowrocket']}","shadowrocket://add/sub://")><i class="mdi mdi-send icon-sm"></i> 配置一键导入</a>
                                                    </p>
                                                <hr/>
                                                <p>Stash - [ SS/SSR/VMess/Trojan ]：</p>
                                                    <p>
                                                        应用下载：
                                                        <a class="btn-dl" href="https://apps.apple.com/app/stash/id1596063349"><i class="mdi mdi-currency-usd icon-sm"></i> Appstore 购买</a>
                                                    </p>
                                                    <p>
                                                        使用方式：
                                                        <a class="btn-dl" href="stash://install-config?url={urlencode($subInfo['clash'])}"><i class="mdi mdi-send icon-sm"></i> 配置一键导入</a>
                                                    </p>
                                                <hr/>
                                            </div>
                                            <div class="tab-pane fade" id="sub_center_android">
                                                <p>Shadowsocks Android - [ SS ]：</p>
                                                    <p>
                                                        应用下载：
                                                        <a class="btn-dl" href="/clients/Shadowsocks.apk"><i class="mdi mdi-tray-arrow-down icon-sm"></i> 本站下载</a>
                                                        .
                                                        <a class="btn-dl" href="https://github.com/shadowsocks/shadowsocks-android/releases"><i class="mdi mdi-tray-arrow-down icon-sm"></i> GitHub 下载</a>
                                                    </p>
                                                    <p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['ssa']}"><i class="mdi mdi-send icon-sm"></i> 拷贝订阅链接</a>
                                                    </p>
                                                <hr/>
                                                <p>ShadowsocksR Android - [ SSR ]：</p>
                                                    <p>
                                                        应用下载：
                                                        <a class="btn-dl" href="/clients/ShadowsocksR.apk"><i class="mdi mdi-tray-arrow-down icon-sm"></i> 本站下载</a>
                                                    </p>
                                                    <p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['ssr']}"><i class="mdi mdi-send icon-sm"></i> 拷贝订阅链接</a>
                                                    </p>
                                                <hr/>
                                                <p>V2RayNG - [ SS/VMess ]：</p>
                                                    <p>
                                                        应用下载：
                                                        <a class="btn-dl" href="/clients/v2rayng.apk"><i class="mdi mdi-tray-arrow-down icon-sm"></i> 本站下载</a>
                                                        .
                                                        <a class="btn-dl" href="https://github.com/2dust/v2rayNG/releases"><i class="mdi mdi-tray-arrow-down icon-sm"></i> GitHub 下载</a>
                                                    </p>
                                                    <p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['v2ray']}"><i class="mdi mdi-send icon-sm"></i> 拷贝订阅链接</a>
                                                    </p>
                                                <hr/>
                                                <p>Clash for Android - [ SS/VMess ]：</p>
                                                    <p>
                                                        应用下载：
                                                        <a class="btn-dl" href="/clients/Clash-Android.apk"><i class="mdi mdi-tray-arrow-down icon-sm"></i> 本站下载</a>
                                                        .
                                                        <a class="btn-dl" href="https://github.com/Kr328/ClashForAndroid/releases"><i class="mdi mdi-tray-arrow-down icon-sm"></i> GitHub 下载</a>
                                                    </p>
                                                    <p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['clash']}"><i class="mdi mdi-send icon-sm"></i> 拷贝订阅链接</a>
                                                        .
                                                        <a class="btn-dl" href="clash://install-config?url={urlencode($subInfo['clash'])}"><i class="mdi mdi-send icon-sm"></i> 配置一键导入</a>
                                                    </p>
                                                <hr/>
                                            </div>
                                            <div class="tab-pane fade" id="sub_center_linux">
                                                <p>Clash for Windows - [ SS/VMess/Trojan ]：</p>
                                                    <p>
                                                        应用下载：
                                                        <a class="btn-dl" href="/clients/Clash-Windows.tar.gz"><i class="mdi mdi-tray-arrow-down icon-sm"></i> 本站下载</a>
                                                        .
                                                        <a class="btn-dl" href="https://github.com/Fndroid/clash_for_windows_pkg/releases"><i class="mdi mdi-tray-arrow-down icon-sm"></i> GitHub 下载</a>
                                                    </p>
                                                    <p>
                                                        使用方式：
                                                        <a class="btn-dl" href="{$subInfo['clash']}"><i class="mdi mdi-send icon-sm"></i> 配置文件下载</a>
                                                        .
                                                        <a class="btn-dl" href="clash://install-config?url={urlencode($subInfo['clash'])}"><i class="mdi mdi-send icon-sm"></i> 配置一键导入</a>
                                                    </p>
                                                <hr/>
                                            </div>
                                            <div class="tab-pane fade" id="sub_center_router">
                                                <p>Fancyss [ SS/SSR/VMess/Trojan ]：</p>
                                                    <p>
                                                        应用下载：
                                                        <a class="btn-dl" href="https://github.com/hq450/fancyss_history_package"><i class="mdi mdi-tray-arrow-down icon-sm"></i> GitHub 下载</a>
                                                    </p>
                                                <hr/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {include file='dialog.tpl'}
        </section>
    </div>
</main>

{include file='user/footer.tpl'}

<script src="https://fastly.jsdelivr.net/npm/shake.js@1.2.2/shake.min.js"></script>
<script>
    function DateParse(str_date) {
        var str_date_splited = str_date.split(/[^0-9]/);
        return new Date(str_date_splited[0], str_date_splited[1] - 1, str_date_splited[2], str_date_splited[3], str_date_splited[4], str_date_splited[5]);
    }
</script>
<script>
    $(function () {
        new ClipboardJS('.copy-text');
    });
    $(".copy-text").click(function () {
        $("#result").modal();
        $$.getElementById('msg').innerHTML = '已复制，请您继续接下来的操作';
    });
    function AddSub(url,jumpurl="") {
        let tmp = window.btoa(url);
        window.location.href = jumpurl + tmp;
    }
    function Copyconfig(url,id,jumpurl="") {
        $.ajax({
            url: url,
            type: 'get',
            async: false,
            success: function(res) {
                if(res) {
                    $("#result").modal();
                    $("#msg").html("获取成功");
                    $(id).data('data', res);
                    console.log(res);
                } else {
                    $("#result").modal();
                   $("#msg").html("获取失败，请稍后再试");
               }
            }
        });
        const clipboard = new ClipboardJS('.copy-config', {
            text: function() {
                return $(id).data('data');
            }
        });
        clipboard.on('success', function(e) {
                    $("#result").modal();
                    if (jumpurl != "") {
                        $("#msg").html("复制成功，即将跳转到 APP");
                        window.setTimeout(function () {
                            window.location.href = jumpurl;
                        }, 1000);

                    } else {
                        $("#msg").html("复制成功");
                    }
                }
        );
        clipboard.on("error",function(e){
            console.error('Action:', e.action);
            console.error('Trigger:', e.trigger);
            console.error('Text:', e.text);
            }
        );
    }
    {if $user->transfer_enable-($user->u+$user->d) == 0}
    window.onload = function () {
        $("#result").modal();
        $$.getElementById('msg').innerHTML = '您的流量已经用完或账户已经过期了，如需继续使用，请进入商店选购新的套餐~';
    };
    {/if}
    {if $geetest_html == null}
    var checkedmsgGE = '<p><a class="btn btn-brand disabled btn-flat waves-attach" href="#"><span class="mdi mdi-check"></span>&nbsp;已签到</a></p>';
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
                dataType: "json",
                {if $config['enable_checkin_captcha'] == true && $config['captcha_provider'] == 'recaptcha'}
                data: {
                    recaptcha: grecaptcha.getResponse()
                },
                {/if}
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
                dataType: "json",
                {if $config['enable_checkin_captcha'] == true && $config['captcha_provider'] == 'recaptcha'}
                data: {
                    recaptcha: grecaptcha.getResponse()
                },
                {/if}
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
    var checkedmsgGE = '<p><a class="btn btn-brand disabled btn-flat waves-attach" href="#"><span class="mdi mdi-check"></span>&nbsp;已签到</a></p>';
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

{if $config['enable_checkin_captcha'] == true && $config['captcha_provider'] == 'recaptcha'}
    <script src="https://recaptcha.net/recaptcha/api.js" async defer></script>
{/if}
