{include file='user/main.tpl'}
{$ssr_prefer = URL::SSRCanConnect($user, 0)}
{$pre_user = URL::cloneUser($user)}

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
                                    <div style="font-size: 14px">账户有效时间：{substr($user->expire_in, 0, 10)}</div>
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
                                {if $user->valid_use_loop() != '未购买套餐.'}
                                <p>下次流量重置时间：{$user->valid_use_loop()}</p>
                                {/if}
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
                                <p class="card-heading"><i class="icon icon-md">account_circle</i> 签到</p>
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
                                <p class="card-heading"><i class="icon icon-md">notifications_active</i> 公告栏</p>
                                {if $ann != null}
                                    <p>{$ann->content}</p>
                                    <br/>
                                    <strong>查看所有公告请<a href="/user/announcement">点击这里</a></strong>
                                {/if}
                                {if $config['enable_admin_contact'] === true}
                                    <p class="card-heading">管理员联系方式</p>
                                    {if $config['admin_contact1']!=''}
                                        <p>{$config['admin_contact1']}</p>
                                    {/if}
                                    {if $config['admin_contact2']!=''}
                                        <p>{$config['admin_contact2']}</p>
                                    {/if}
                                    {if $config['admin_contact3']!=''}
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
                                    <div class="card-heading"><i class="icon icon-md">phonelink</i> 快速使用</div>
                                </div>
								<nav class="tab-nav margin-top-no">
									<ul class="nav nav-list">
										<li class="active">
											<a class="" data-toggle="tab" href="#sub_center"><i class="icon icon-lg">info_outline</i>&nbsp;订阅中心</a>
										</li>
										<li>
											<a class="" data-toggle="tab" href="#info_center"><i class="icon icon-lg">flight_takeoff</i>&nbsp;连接信息</a>
										</li>
									</ul>
								</nav>
								<div class="card-inner">
									<div class="tab-content">
										<div class="tab-pane fade" id="info_center">
											<p>您的链接信息：</p>
											{if URL::SSRCanConnect($user)}
												{$user = URL::getSSRConnectInfo($pre_user)}
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
												<hr/>
												<p>您好，您目前的 加密方式，混淆或协议 适用于 SSR 客户端，请您选用支持 SSR 的客户端来连接，或者到 <a href="/user/edit">资料编辑</a> 页面修改后再来查看此处。</p>
                                                <p>同时, ShadowsocksR 单端口多用户的连接不受您设置的影响，您可以在此使用相应的客户端进行连接</p>
											{elseif URL::SSCanConnect($user)}
                                                {$user = URL::getSSConnectInfo($pre_user)}
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
												<hr/>
                                                <p>您好，您目前的 加密方式，混淆或协议 适用于 SS 客户端，请您选用支持 SS 协议的客户端来连接，或者到 <a href="/user/edit">资料编辑</a> 页面修改后再来查看此处。</p>
                                                <p>同时, Shadowsocks 单端口多用户的连接不受您设置的影响，您可以在此使用相应的客户端进行连接</p>
                                            {else}
                                                <p>您的账户连接信息存在异常，请联系管理员</p>
											{/if}
										</div>
										<div class="tab-pane fade active in" id="sub_center">
											<nav class="tab-nav margin-top-no">
												<ul class="nav nav-list">
													<li class="active">
														<a class="" data-toggle="tab" href="#sub_center_general"><i class="icon icon-lg">star</i>&nbsp;General</a>
													</li>
													<li>
														<a class="" data-toggle="tab" href="#sub_center_windows"><i class="icon icon-lg">desktop_windows</i>&nbsp;Windows</a>
													</li>
													<li>
														<a class="" data-toggle="tab" href="#sub_center_mac"><i class="icon icon-lg">laptop_mac</i>&nbsp;macOS</a>
													</li>
													<li>
														<a class="" data-toggle="tab" href="#sub_center_ios"><i class="icon icon-lg">phone_iphone</i>&nbsp;iOS</a>
													</li>
													<li>
														<a class="" data-toggle="tab" href="#sub_center_android"><i class="icon icon-lg">android</i>&nbsp;Android</a>
													</li>
													<li>
														<a class="" data-toggle="tab" href="#sub_center_linux"><i class="icon icon-lg">devices_other</i>&nbsp;Linux</a>
													</li>
													<li>
														<a class="" data-toggle="tab" href="#sub_center_router"><i class="icon icon-lg">router</i>&nbsp;Router</a>
													</li>
												</ul>
											</nav>
                                            {function name=printClient items=null}
                                                {foreach $items as $item}
                                                    <hr/>
												    <p><span class="icon icon-lg text-white">filter_9_plus</span> {$item['name']} - [ {$item['support']} ]：</p>
													<p>
                                                        应用下载：
                                                        {foreach $item['download_urls'] as $download_url}
                                                        {if !$download_url@first}.{/if}
                                                        <a class="btn-dl" href="{$download_url['url']}"><i class="material-icons icon-sm">cloud_download</i> {$download_url['name']}</a>
                                                        {/foreach}
                                                    </p>
													<p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}{$item['tutorial_url']}{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
                                                    {if isset($item['description'])}
													<p>
                                                        相关说明：
                                                        {$item['description']}
                                                    </p>
                                                    {/if}
													<p>
                                                        使用方式：
                                                    {foreach $item['subscribe_urls'] as $subscribe_url}
                                                        {if !$subscribe_url@first}.{/if}
                                                        {$url=$subscribe_url['url']|replace:'%userUrl%':$subInfo['link']}
                                                        {if $subscribe_url['type'] == 'href'}
                                                        <a class="btn-dl" href="{$url}"><i class="material-icons icon-sm">send</i> {$subscribe_url['name']}</a>
                                                        {else}
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$url}"><i class="material-icons icon-sm">send</i> {$subscribe_url['name']}</a>
                                                        {/if}
                                                    {/foreach}
                                                    </p>
                                                {/foreach}
                                            {/function}
											<div class="tab-pane fade active in" id="sub_center_general">
												<p>此处为通用订阅，适用于多种应用的订阅，如您使用的客户端不在各平台列举的名单中则在此使用订阅服务.</p>
                                                <hr/>
												<p><span class="icon icon-lg text-white">filter_1</span> [ SS ]：
													<a id="general_ss" class="copy-config btn-dl" onclick=Copyconfig("/user/getUserAllURL?type=ss","#general_ss","")><i class="material-icons icon-sm">send</i> 拷贝全部节点 URL</a>
												</p>
												<hr/>
												<p><span class="icon icon-lg text-white">filter_2</span> [ SSR ]：
													<a class="copy-text btn-dl" data-clipboard-text="{$subInfo['ssr']}"><i class="material-icons icon-sm">send</i> 拷贝订阅链接</a>.<a id="general_ssr" class="copy-config btn-dl" onclick=Copyconfig("/user/getUserAllURL?type=ssr","#general_ssr","")><i class="material-icons icon-sm">send</i> 拷贝全部节点 URL</a>
												</p>
												<hr/>
												<p>如您使用 V2Ray/Trojan 订阅，请确认您的服务内包含该类协议的节点，若您所使用的客户端不在我们的支持内，那么请您考虑更换客户端或与我们的客服联系.</p>
												<p><span class="icon icon-lg text-white">filter_3</span> [ V2RayN ]：
													<a class="copy-text btn-dl" data-clipboard-text="{$subInfo['v2ray']}"><i class="material-icons icon-sm">send</i> 拷贝订阅链接</a>.<a id="general_v2ray" class="copy-config btn-dl" onclick=Copyconfig("/user/getUserAllURL?type=v2ray","#general_v2ray","")><i class="material-icons icon-sm">send</i> 拷贝全部节点 URL</a>
												</p>
												<hr/>
												<p><span class="icon icon-lg text-white">filter_4</span> [ Trojan ]：
													<a class="copy-text btn-dl" data-clipboard-text="{$subInfo['trojan']}"><i class="material-icons icon-sm">send</i> 拷贝订阅链接</a>
												</p>
												<hr/>
												<p><span class="icon icon-lg text-white">filter_5</span> [ Clash ]：
													<a class="copy-text btn-dl" data-clipboard-text="{$subInfo['clash']}"><i class="material-icons icon-sm">send</i> 拷贝订阅链接</a>
												</p>
											</div>
											<div class="tab-pane fade" id="sub_center_windows">
												<p><span class="icon icon-lg text-white">filter_1</span> SS - [ SS ]：</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="{if $config["subscribe_client"]===true}{if $config["subscribe_client_url"]==''}/user/getPcClient{else}{$config["subscribe_client_url"]}/getClient/{$getClient}{/if}?type=ss-win{else}/ssr-download/ss-win.zip{/if}"><i class="material-icons icon-sm">cloud_download</i> 本站下载【高速】</a>
                                                        .
                                                        <a class="btn-dl" href="https://github.com/shadowsocks/shadowsocks-windows/releases"><i class="material-icons icon-sm">cloud_download</i> 官方下载</a>
                                                    </p>
													<p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/Windows/Shadowsocks{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a id="win_ss" class="copy-config btn-dl" onclick=Copyconfig("/user/getUserAllURL?type=ss","#win_ss","")><i class="material-icons icon-sm">send</i> 拷贝全部节点 URL</a>
                                                    </p>
												<hr/>											
												<p><span class="icon icon-lg text-white">filter_2</span> SSR(R) - [ SS/SSR ]：</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="{if $config["subscribe_client"]===true}{if $config["subscribe_client_url"]==''}/user/getPcClient{else}{$config["subscribe_client_url"]}/getClient/{$getClient}{/if}?type=ssr-win{else}/ssr-download/ssr-win.7z{/if}"><i class="material-icons icon-sm">cloud_download</i> 本站下载【高速】</a>
                                                        .
                                                        <a class="btn-dl" href="https://github.com/shadowsocksrr/shadowsocksr-csharp/releases"><i class="material-icons icon-sm">cloud_download</i> 官方下载</a>
                                                    </p>
                                                    <p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/Windows/ShadowsocksR{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['ssr']}"><i class="material-icons icon-sm">send</i> 拷贝订阅链接</a>
                                                        .
                                                        <a id="win_ssr" class="copy-config btn-dl" onclick=Copyconfig("/user/getUserAllURL?type=ssr","#win_ssr","")><i class="material-icons icon-sm">send</i> 拷贝全部节点 URL</a>
                                                    </p>
												<hr/>
												<p><span class="icon icon-lg text-white">filter_3</span> SSTap - [ SS/SSR ]：</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="/ssr-download/SSTap.7z"><i class="material-icons icon-sm">cloud_download</i> 本站下载【高速】</a>
                                                    </p>
                                                    <p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/Windows/SSTap{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['ssr']}"><i class="material-icons icon-sm">send</i> 拷贝订阅链接</a>
                                                    </p>
												<hr/>
												<p><span class="icon icon-lg text-white">filter_4</span> V2RayN - [ SS/VMess ]：</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="{if $config["subscribe_client"]===true}{if $config["subscribe_client_url"]==''}/user/getPcClient{else}{$config["subscribe_client_url"]}/getClient/{$getClient}{/if}?type=v2rayn-win{else}/ssr-download/v2rayn.zip{/if}"><i class="material-icons icon-sm">cloud_download</i> 本站下载【高速】</a>
                                                        .
                                                        <a class="btn-dl" href="https://github.com/2dust/v2rayN/releases"><i class="material-icons icon-sm">cloud_download</i> 官方下载</a>
                                                    </p>
                                                    <p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/Windows/V2RayN{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['v2ray']}"><i class="material-icons icon-sm">send</i> 拷贝订阅链接</a>
                                                        .
                                                        <a id="win_v2rayn" class="copy-config btn-dl" onclick=Copyconfig("/user/getUserAllURL?type=v2ray","#win_v2rayn","")><i class="material-icons icon-sm">send</i> 拷贝全部节点 URL</a>
                                                    </p>
												<hr/>
												<p><span class="icon icon-lg text-white">filter_5</span> Clash for Windows - [ SS/VMess/Trojan ]：</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="/ssr-download/Clash-Windows.7z"><i class="material-icons icon-sm">cloud_download</i> 本站下载【高速】</a>
                                                        .
                                                        <a class="btn-dl" href="https://github.com/Fndroid/clash_for_windows_pkg/releases"><i class="material-icons icon-sm">cloud_download</i> 官方下载</a>
                                                    </p>
                                                    <p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/Windows/Clash-for-Windows{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a class="btn-dl" href="{$subInfo['clash']}"><i class="material-icons icon-sm">send</i> 配置文件下载</a>
                                                        .
                                                        <a class="btn-dl" href="clash://install-config?url={urlencode($subInfo['clash'])}"><i class="material-icons icon-sm">send</i> 配置一键导入</a>
                                                    </p>
                                            	<hr/>
												<p><span class="icon icon-lg text-white">filter_6</span> ClashR for Windows - [ SS/SSR/VMess ]：</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="#"><i class="material-icons icon-sm">cloud_download</i> 暂无下载</a>
                                                    </p>
                                                    <p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/Windows/Clash-for-Windows{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a class="btn-dl" href="{$subInfo['clashr']}"><i class="material-icons icon-sm">send</i> 配置文件下载</a>
                                                        .
                                                        <a class="btn-dl" href="clash://install-config?url={urlencode($subInfo['clashr'])}"><i class="material-icons icon-sm">send</i> 配置一键导入</a>
                                                    </p>
                                            {if array_key_exists('Windows',$config['userCenterClient'])}
                                                {if count($config['userCenterClient']['Windows']) != 0}
                                                    {printClient items=$config['userCenterClient']['Windows']}
                                                {/if}
                                            {/if}
											</div>
											<div class="tab-pane fade" id="sub_center_mac">
												<p><span class="icon icon-lg text-white">filter_1</span> Surge - [ SS/VMess ]：</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="https://nssurge.com/mac/v3/Surge-latest.zip"><i class="material-icons icon-sm">cloud_download</i> 官方下载</a>
                                                    </p>
                                                    <p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/macOS/Surge{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['surge4']}"><i class="material-icons icon-sm">send</i> 拷贝 4.x 托管链接</a>
                                                        .
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['surge3']}"><i class="material-icons icon-sm">send</i> 拷贝 3.x 托管链接</a>
                                                        .
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['surge_node']}"><i class="material-icons icon-sm">send</i> 拷贝 3.x 节点链接</a>
                                                        .
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['surge2']}"><i class="material-icons icon-sm">send</i> 拷贝 2.x 托管链接</a>
                                                    </p>
												<hr/>
												<p><span class="icon icon-lg text-white">filter_2</span> ClashX - [ SS/VMess ]：</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="/ssr-download/ClashX.dmg"><i class="material-icons icon-sm">cloud_download</i> 本站下载【高速】</a>
                                                        .
                                                        <a class="btn-dl" href="https://github.com/yichengchen/clashX/releases"><i class="material-icons icon-sm">cloud_download</i> 官方下载</a>
                                                    </p>
                                                    <p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/macOS/ClashX{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a class="btn-dl" href="{$subInfo['clash']}"><i class="material-icons icon-sm">send</i> 配置文件下载</a>
                                                        .
                                                        <a class="btn-dl" href="clash://install-config?url={urlencode($subInfo['clash'])}"><i class="material-icons icon-sm">send</i> 配置一键导入</a>
                                                    </p>
                                                <hr/>
												<p><span class="icon icon-lg text-white">filter_3</span> ClashXR - [ SS/SSR/VMess ]：</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="#"><i class="material-icons icon-sm">cloud_download</i> 暂无下载</a>
                                                    </p>
                                                    <p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/macOS/ClashX{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a class="btn-dl" href="{$subInfo['clashr']}"><i class="material-icons icon-sm">send</i> 配置文件下载</a>
                                                        .
                                                        <a class="btn-dl" href="clash://install-config?url={urlencode($subInfo['clashr'])}"><i class="material-icons icon-sm">send</i> 配置一键导入</a>
                                                    </p>
                                                <hr/>
												<p><span class="icon icon-lg text-white">filter_4</span> V2RayU - [ SS/VMess ]：</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="/ssr-download/V2rayU.dmg"><i class="material-icons icon-sm">cloud_download</i> 本站下载【高速】</a>
                                                    </p>
                                                    <p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/macOS/V2RayU{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['v2ray']}"><i class="material-icons icon-sm">send</i> 拷贝订阅链接</a>
                                                    </p>
                                                <hr/>
												<p><span class="icon icon-lg text-white">filter_5</span> ShadowsocksX-NG - [ SS ]：</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="/ssr-download/ss-mac.zip"><i class="material-icons icon-sm">cloud_download</i> 本站下载【高速】</a>
                                                    </p>
                                                    <p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/macOS/ShadowsocksX-NG{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a id="mac_ss" class="copy-config btn-dl" onclick=Copyconfig("/user/getUserAllURL?type=ss","#mac_ss","")><i class="material-icons icon-sm">send</i> 拷贝全部节点 URL</a>
                                                    </p>
                                                <hr/>
												<p><span class="icon icon-lg text-white">filter_6</span> ShadowsocksX-NG-R8 - [ SSR ]：</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="/ssr-download/ssr-mac.dmg"><i class="material-icons icon-sm">cloud_download</i> 本站下载【高速】</a>
                                                    </p>
                                                    <p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/macOS/ShadowsocksX-NG-R8{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['ssr']}"><i class="material-icons icon-sm">send</i> 拷贝订阅链接</a>
                                                    </p>
                                            {if array_key_exists('macOS',$config['userCenterClient'])}
                                                {if count($config['userCenterClient']['macOS']) != 0}
                                                    {printClient items=$config['userCenterClient']['macOS']}
                                                {/if}
                                            {/if}
											</div>
											<div class="tab-pane fade" id="sub_center_ios">
											{if $display_ios_class>=0}
												{if $user->class>=$display_ios_class && $user->get_top_up()>=$display_ios_topup}
												<div><span class="icon icon-lg text-white">account_box</span> 本站iOS账户：</div>
												<div class="float-clear">
													<input type="text" class="input form-control form-control-monospace cust-link col-xx-12 col-sm-8 col-lg-7" name="input1" readonly value="{$ios_account}" readonly="true">
													<button class="copy-text btn btn-subscription col-xx-12 col-sm-3 col-lg-2" type="button" data-clipboard-text="{$ios_account}">点击复制</button>
                                                    <br>
												</div>
												<div><span class="icon icon-lg text-white">lock</span> 本站iOS密码：</div>
												<div class="float-clear">
													<input type="text" class="input form-control form-control-monospace cust-link col-xx-12 col-sm-8 col-lg-7" name="input1" readonly value="{$ios_password}" readonly="true">
													<button class="copy-text btn btn-subscription col-xx-12 col-sm-3 col-lg-2" type="button" data-clipboard-text="{$ios_password}">点击复制</button>
                                                    <br>
												</div>
												<p><span class="icon icon-lg text-white">error</span><strong>禁止将账户分享给他人！</strong></p>
												<hr/>
												{/if}
											{/if}
												<p><span class="icon icon-lg text-white">filter_1</span> Surge - [ SS/VMess ]：</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="https://itunes.apple.com/us/app/surge-3/id1442620678?ls=1&mt=8"><i class="material-icons icon-sm">cloud_download</i> 官方下载</a>
                                                    </p>
													<p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/iOS/Surge{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        相关说明：
                                                        Surge 4 托管配置中可能含有 VMess 节点，如您未订阅 Surge 4 请使用 3.x 一键.
                                                        其中 2 & 3 & 4 代表 Surge 的版本.
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a class="btn-dl" href="surge3:///install-config?url={urlencode($subInfo['surge4'])}"><i class="material-icons icon-sm">send</i> 4.x 一键</a>
                                                        .
                                                        <a class="btn-dl" href="surge3:///install-config?url={urlencode($subInfo['surge3'])}"><i class="material-icons icon-sm">send</i> 3.x 一键</a>
                                                        .
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['surge_node']}"><i class="material-icons icon-sm">send</i> 节点 List</a>
                                                        .
                                                        <a class="btn-dl" href="surge:///install-config?url={urlencode($subInfo['surge2'])}"><i class="material-icons icon-sm">send</i> 2.x 一键</a>
                                                    </p>
												<hr/>
												<p><span class="icon icon-lg text-white">filter_2</span> Kitsunebi - [ SS/VMess ]：</p>
												    <p>该客户端专属订阅链接支持同时订阅 SS/V2Ray 节点.</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="https://itunes.apple.com/us/app/kitsunebi-proxy-utility/id1446584073?ls=1&mt=8"><i class="material-icons icon-sm">cloud_download</i> 官方下载</a>
                                                    </p>
                                                    <p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/iOS/Kitsunebi{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['ss']}"><i class="material-icons icon-sm">send</i> 拷贝 SS 订阅链接</a>
                                                        .
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['kitsunebi']}"><i class="material-icons icon-sm">send</i> 拷贝该应用专属订阅链接</a>
                                                    </p>
												<hr/>
												<p><span class="icon icon-lg text-white">filter_3</span> Quantumult - [ SS/SSR/VMess ]：</p>
												    <p>完整策略组配置 为使用了策略组结构的配置文件.</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="https://itunes.apple.com/us/app/quantumult/id1252015438?ls=1&mt=8"><i class="material-icons icon-sm">cloud_download</i> 官方下载</a>
                                                    </p>
                                                    <p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/iOS/Quantumult_sub{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['ss']}"><i class="material-icons icon-sm">send</i> 拷贝 SS 订阅链接</a>
                                                        .
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['ssr']}"><i class="material-icons icon-sm">send</i> 拷贝 SSR 订阅链接</a>
                                                        .
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['quantumult_v2']}"><i class="material-icons icon-sm">send</i> 拷贝 V2Ray 订阅链接</a>
                                                        <!--
                                                        .
                                                        <a id="quan_sub" class="copy-config btn-dl" onclick=Copyconfig("{$subInfo['quantumult_sub']}","#quan_sub","quantumult://settings?configuration=clipboard")><i class="material-icons icon-sm">send</i> 完整订阅配置</a>
                                                        -->
                                                        .
                                                        <a id="quan_conf" class="copy-config btn-dl" onclick=Copyconfig("{$subInfo['quantumult_conf']}","#quan_conf","quantumult://settings?configuration=clipboard")><i class="material-icons icon-sm">send</i> 完整策略组配置</a>
                                                    </p>
												<hr/>
												<p><span class="icon icon-lg text-white">filter_4</span> QuantumultX - [ SS/SSR/VMess ]：</p>
												    <p>该客户端专属订阅链接支持同时订阅 SS/SSR/V2Ray 节点.</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="https://apps.apple.com/us/app/quantumult-x/id1443988620"><i class="material-icons icon-sm">cloud_download</i> 官方下载</a>
                                                    </p>
                                                    <p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/iOS/QuantumultX{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['ssr']}"><i class="material-icons icon-sm">send</i> 拷贝 SSR 订阅链接</a>
                                                        .
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['quantumultx']}"><i class="material-icons icon-sm">send</i> 拷贝该应用专属订阅链接</a>
                                                    </p>
												<hr/>
												<p><span class="icon icon-lg text-white">filter_5</span> Shadowrocket - [ SS/SSR/VMess/Trojan ]：</p>
												    <p>该客户端专属订阅链接支持同时订阅 SS/SSR/V2Ray 节点.</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="https://itunes.apple.com/us/app/shadowrocket/id932747118?mt=8"><i class="material-icons icon-sm">cloud_download</i> 官方下载</a>
                                                    </p>
                                                    <p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/iOS/Shadowrocket{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['ss']}"><i class="material-icons icon-sm">send</i> 拷贝 SS 订阅链接</a>
                                                        .
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['ssr']}"><i class="material-icons icon-sm">send</i> 拷贝 SSR 订阅链接</a>
                                                        .
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['v2ray']}"><i class="material-icons icon-sm">send</i> 拷贝 V2Ray 订阅链接</a>
                                                        .
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['trojan']}"><i class="material-icons icon-sm">send</i> 拷贝 Trojan 订阅链接</a>
                                                        .
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['shadowrocket']}"><i class="material-icons icon-sm">send</i> 拷贝该应用专属订阅链接</a>
                                                        .
                                                        <a class="btn-dl" onclick=AddSub("{$subInfo['shadowrocket']}","shadowrocket://add/sub://")><i class="material-icons icon-sm">send</i> 一键导入 Shadowrocket</a>
                                                    </p>
                                            {if array_key_exists('iOS',$config['userCenterClient'])}
                                                {if count($config['userCenterClient']['iOS']) != 0}
                                                    {printClient items=$config['userCenterClient']['iOS']}
                                                {/if}
                                            {/if}
											</div>
											<div class="tab-pane fade" id="sub_center_android">
												<p><span class="icon icon-lg text-white">filter_1</span> SS - [ SS ]：</p>
												    <p>该客户端仅 v5.0 以上版本支持订阅，如您未找到订阅配置之处，请尝试升级客户端.</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="/ssr-download/ss-android.apk"><i class="material-icons icon-sm">cloud_download</i> 本站下载【高速】</a>
                                                        .
                                                        <a class="btn-dl" href="https://github.com/shadowsocks/shadowsocks-android/releases"><i class="material-icons icon-sm">cloud_download</i> 官方下载</a>
                                                    </p>
													<p>
                                                        插件下载：
                                                        <a class="btn-dl" href="/ssr-download/ss-android-obfs.apk"><i class="material-icons icon-sm">cloud_download</i> 「必须」obfs 插件本站下载【高速】</a>
                                                    </p>
                                                    <p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/Android/Shadowsocks-Android{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['ssa']}"><i class="material-icons icon-sm">send</i> 拷贝该应用专属订阅链接</a>
                                                    </p>
												<hr/>												
												<p><span class="icon icon-lg text-white">filter_2</span> SSR(R) - [ SSR ]：</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="/ssr-download/ssr-android.apk"><i class="material-icons icon-sm">cloud_download</i> SSR 本站下载【高速】</a>
                                                        .
                                                        <a class="btn-dl" href="/ssr-download/ssrr-android.apk"><i class="material-icons icon-sm">cloud_download</i> SSRR 本站下载【高速】</a>
                                                    </p>
                                                    <p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/Android/ShadowsocksR{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['ssr']}"><i class="material-icons icon-sm">send</i> 拷贝订阅链接</a>
                                                    </p>
												<hr/>
												<p><span class="icon icon-lg text-white">filter_3</span> V2RayNG - [ SS/VMess ]：</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="/ssr-download/v2rayng.apk"><i class="material-icons icon-sm">cloud_download</i> 本站下载【高速】</a>
                                                        .
                                                        <a class="btn-dl" href="https://github.com/2dust/v2rayNG/releases"><i class="material-icons icon-sm">cloud_download</i> 官方下载</a>
                                                    </p>
                                                    <p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/Android/V2RayNG{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['v2ray']}"><i class="material-icons icon-sm">send</i> 拷贝订阅链接</a>
                                                    </p>
												<hr/>
												<p><span class="icon icon-lg text-white">filter_4</span> Surfboard - [ SS/VMess ]：</p>
												    <p>该客户端新版本支持 V2Ray 节点，如您遇到配置解析错误等情况，请尝试升级客户端.</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="https://play.google.com/store/apps/details?id=com.getsurfboard"><i class="material-icons icon-sm">cloud_download</i> Google Play 下载</a>
                                                    </p>
                                                    <p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/Android/Surfboard{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['surfboard']}"><i class="material-icons icon-sm">send</i> 拷贝托管链接</a>
                                                        .
                                                        <a class="btn-dl" href="{$subInfo['surfboard']}"><i class="material-icons icon-sm">send</i> 配置文件下载</a>
                                                    </p>
												<hr/>
												<p><span class="icon icon-lg text-white">filter_5</span> Kitsunebi - [ SS/VMess ]：</p>
												    <p>该客户端专属订阅链接支持同时订阅 SS 和 V2Ray 节点.</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="https://play.google.com/store/apps/details?id=fun.kitsunebi.kitsunebi4android"><i class="material-icons icon-sm">cloud_download</i> Google Play 下载</a>
                                                    </p>
                                                    <p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/Android/Kitsunebi{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['ss']}"><i class="material-icons icon-sm">send</i> 拷贝 SS 订阅链接</a>
                                                        .
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['kitsunebi']}"><i class="material-icons icon-sm">send</i> 拷贝该应用专属订阅链接</a>
                                                    </p>
                                                <hr/>
                                                    <p><span class="icon icon-lg text-white">filter_6</span> Clash for Android - [ SS/VMess ]：</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="https://play.google.com/store/apps/details?id=com.github.kr328.clash"><i class="material-icons icon-sm">cloud_download</i> Google Play 下载</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['clash']}"><i class="material-icons icon-sm">send</i> 拷贝 Clash 订阅链接</a>
                                                        .
                                                        <a class="btn-dl" href="clash://install-config?url={urlencode($subInfo['clash'])}"><i class="material-icons icon-sm">send</i> 配置一键导入</a>
                                                    </p>
                                            {if array_key_exists('Android',$config['userCenterClient'])}
                                                {if count($config['userCenterClient']['Android']) != 0}
                                                    {printClient items=$config['userCenterClient']['Android']}
                                                {/if}
                                            {/if}
											</div>
											<div class="tab-pane fade" id="sub_center_linux">
												<p><span class="icon icon-lg text-white">filter_1</span> Electron SSR - [ SSR ]：</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="/ssr-download/ssr-linux.AppImage"><i class="material-icons icon-sm">cloud_download</i> 本站下载【高速】</a>
                                                    </p>
                                                    <p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/Linux/ElectronSSR{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['ssr']}"><i class="material-icons icon-sm">send</i> 拷贝订阅链接</a>
                                                    </p>
                                            {if array_key_exists('Linux',$config['userCenterClient'])}
                                                {if count($config['userCenterClient']['Linux']) != 0}
                                                    {printClient items=$config['userCenterClient']['Linux']}
                                                {/if}
                                            {/if}
											</div>
											<div class="tab-pane fade" id="sub_center_router">
												<p><span class="icon icon-lg text-white">filter_1</span> Koolshare 固件路由器/软路由：</p>
													<p>
                                                        应用下载：
                                                        <a class="btn-dl" href="https://github.com/hq450/fancyss_history_package"><i class="material-icons icon-sm">cloud_download</i> FancySS 下载页面</a>
                                                        .
                                                        <a class="btn-dl" href="https://github.com/hq450/fancyss_history_package/tree/master/fancyss_X64"><i class="material-icons icon-sm">cloud_download</i> FancySS 历史下载页面下载 V2Ray 插件</a>
                                                    </p>
                                                    <p>
                                                        使用教程：
                                                        <a class="btn-dl" href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/#/Router/Koolshare{/if}"><i class="material-icons icon-sm">turned_in_not</i> 点击查看</a>
                                                    </p>
													<p>
                                                        使用方式：
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['ssr']}"><i class="material-icons icon-sm">send</i> 拷贝 SSR 订阅链接</a>
                                                        .
                                                        <a class="copy-text btn-dl" data-clipboard-text="{$subInfo['v2ray']}"><i class="material-icons icon-sm">send</i> 拷贝 V2Ray 订阅链接</a>
                                                    </p>
                                            {if array_key_exists('Router',$config['userCenterClient'])}
                                                {if count($config['userCenterClient']['Router']) != 0}
                                                    {printClient items=$config['userCenterClient']['Router']}
                                                {/if}
                                            {/if}
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

<script src="https://cdn.jsdelivr.net/npm/shake.js@1.2.2/shake.min.js"></script>
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