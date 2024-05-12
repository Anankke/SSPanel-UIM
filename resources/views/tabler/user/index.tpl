{include file='user/header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">用户中心</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">在这里查看账户信息和最新公告</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="row row-cards">
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-blue text-white avatar">
                                                <i class="ti ti-vip icon"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                账户等级
                                            </div>
                                            <div class="text-secondary">
                                                {if $user->class === 0}
                                                    免费
                                                {else}
                                                    Lv. {$user->class}
                                                {/if}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-green text-white avatar">
                                                <i class="ti ti-coin icon"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                账户余额
                                            </div>
                                            <div class="text-secondary">
                                                {$user->money}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <a href="/user/money" class="btn btn-primary btn-icon">
                                                <i class="ti ti-plus icon"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-azure text-white avatar">
                                                <i class="ti ti-devices-pc icon"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                同时连接IP限制
                                            </div>
                                            <div class="text-secondary">
                                                {if $user->node_iplimit !== 0}
                                                    {$user->node_iplimit}
                                                {else}
                                                    不限制
                                                {/if}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-indigo text-white avatar">
                                                <i class="ti ti-rocket icon"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                速度限制
                                            </div>
                                            <div class="text-secondary">
                                                {if $user->node_speedlimit !== 0.0}
                                                    <code>{$user->node_speedlimit}</code>
                                                    Mbps
                                                {else}
                                                    不限制
                                                {/if}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-12">
                    <div class="card">
                        <ul class="nav nav-tabs nav-fill" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#sub" class="nav-link active" data-bs-toggle="tab">
                                    <i class="ti ti-rss icon"></i>
                                    &nbsp;通用订阅
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#client-sub" class="nav-link" data-bs-toggle="tab">
                                    <i class="ti ti-rss icon"></i>
                                    &nbsp;客户端订阅
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#windows" class="nav-link" data-bs-toggle="tab">
                                    <i class="ti ti-brand-windows icon"></i>
                                    &nbsp;Windows
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#macos" class="nav-link" data-bs-toggle="tab">
                                    <i class="ti ti-brand-finder icon"></i>
                                    &nbsp;MacOS
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#android" class="nav-link" data-bs-toggle="tab">
                                    <i class="ti ti-brand-android icon"></i>
                                    &nbsp;Android
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#ios" class="nav-link" data-bs-toggle="tab">
                                    <i class="ti ti-brand-apple icon"></i>
                                    &nbsp;iOS
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#linux" class="nav-link" data-bs-toggle="tab">
                                    <i class="ti ti-brand-redhat icon"></i>
                                    &nbsp;Linux
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#config" class="nav-link" data-bs-toggle="tab">
                                    <i class="ti ti-file-text icon"></i>
                                    &nbsp;Config
                                </a>
                            </li>
                        </ul>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active show" id="sub">
                                    <div>
                                        <p>
                                            通用订阅（json）：<code>{$UniversalSub}/json</code>
                                        </p>
                                        <p>
                                            通用订阅（clash）：<code>{$UniversalSub}/clash</code>
                                        </p>
                                        <p>
                                            通用订阅（sing-box）：<code>{$UniversalSub}/singbox</code>
                                        </p>
                                        <p>
                                            通用订阅（v2rayjson）：<code>{$UniversalSub}/v2rayjson</code>
                                        </p>
                                        {if $public_setting['enable_ss_sub']}
                                            <p>
                                                通用订阅（sip008）：<code>{$UniversalSub}/sip008</code>
                                            </p>
                                        {/if}
                                        <div class="btn-list justify-content-start">
                                            <a data-clipboard-text="{$UniversalSub}/json"
                                               class="copy btn btn-primary">
                                                复制通用订阅（json）
                                            </a>
                                            <a data-clipboard-text="{$UniversalSub}/clash"
                                               class="copy btn btn-primary">
                                                复制通用订阅（clash）
                                            </a>
                                            <a data-clipboard-text="{$UniversalSub}/singbox"
                                               class="copy btn btn-primary">
                                                复制通用订阅（sing-box）
                                            </a>
                                            <a data-clipboard-text="{$UniversalSub}/v2rayjson"
                                               class="copy btn btn-primary">
                                                复制通用订阅（v2rayjson）
                                            </a>
                                            {if $public_setting['enable_ss_sub']}
                                                <a data-clipboard-text="{$UniversalSub}/sip008"
                                                   class="copy btn btn-primary">
                                                    复制通用订阅（sip008）
                                                </a>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane show" id="client-sub">
                                    <div>
                                        {if $public_setting['enable_ss_sub']}
                                            <p>
                                                客户端订阅（Shadowsocks）：<code>{$UniversalSub}/ss</code></p>
                                            <p>
                                                客户端订阅（SIP002）：<code>{$UniversalSub}/sip002</code>
                                            </p>
                                        {/if}
                                        {if $public_setting['enable_v2_sub']}
                                            <p>
                                                客户端订阅（V2Ray）：<code>{$UniversalSub}/v2ray</code>
                                            </p>
                                        {/if}
                                        {if $public_setting['enable_trojan_sub']}
                                            <p>
                                                客户端订阅（Trojan）：<code>{$UniversalSub}/trojan</code>
                                            </p>
                                        {/if}
                                        <div class="btn-list justify-content-start">
                                            {if $public_setting['enable_ss_sub']}
                                                <a data-clipboard-text="{$UniversalSub}/ss"
                                                   class="copy btn btn-primary">
                                                    复制客户端订阅（Shadowsocks）
                                                </a>
                                                <a data-clipboard-text="{$UniversalSub}/sip002"
                                                   class="copy btn btn-primary">
                                                    复制客户端订阅（SIP002）
                                                </a>
                                            {/if}
                                            {if $public_setting['enable_v2_sub']}
                                                <a data-clipboard-text="{$UniversalSub}/v2ray"
                                                   class="copy btn btn-primary">
                                                    复制客户端订阅（V2Ray）
                                                </a>
                                            {/if}
                                            {if $public_setting['enable_trojan_sub']}
                                                <a data-clipboard-text="{$UniversalSub}/trojan"
                                                   class="copy btn btn-primary">
                                                    复制客户端订阅（Trojan）
                                                </a>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="windows">
                                    <div>
                                        <p>
                                            适用于 Clash 的订阅：<code>{$UniversalSub}/clash</code>
                                        </p>
                                        <div class="btn-list justify-content-start">
                                            <a
                                                {if $config['enable_r2_client_download']}
                                                    href="/user/clients/Clash.Verge.exe"
                                                {else}
                                                    href="/clients/Clash.Verge.exe"
                                                {/if} class="btn btn-azure">
                                                下载 Clash Verge
                                            </a>
                                            <a data-clipboard-text="{$UniversalSub}/clash"
                                               class="copy btn btn-primary">
                                                复制 Clash 订阅链接
                                            </a>
                                            <a href="clash://install-config?url={$UniversalSub}/clash&name={$config['appName']}"
                                               class="btn btn-indigo">
                                                导入 Clash
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="macos">
                                    <p>
                                        适用于 Clash 的订阅：<code>{$UniversalSub}/clash</code>
                                    </p>
                                    <p>
                                        适用于 sing-box 的订阅：<code>{$UniversalSub}/singbox</code>
                                    </p>
                                    <div class="btn-list justify-content-start">
                                        <a {if $config['enable_r2_client_download']}
                                            href="/user/clients/Clash.Verge_aarch64.dmg"
                                        {else}
                                            href="/clients/Clash.Verge_aarch64.dmg"
                                        {/if} class="btn btn-azure">
                                            下载 Clash Verge (aarch64)
                                        </a>
                                        <a data-clipboard-text="{$UniversalSub}/clash"
                                           class="copy btn btn-primary">
                                            复制 Clash 订阅链接
                                        </a>
                                        <a href="clash://install-config?url={$UniversalSub}/clash&name={$config['appName']}"
                                           class="btn btn-indigo">
                                            导入 Clash
                                        </a>
                                    </div>
                                    <div class="btn-list justify-content-start my-2">
                                        <a {if $config['enable_r2_client_download']}
                                            href="/user/clients/SFM.zip"
                                        {else}
                                            href="/clients/SFM.zip"
                                        {/if} class="btn btn-azure">
                                            下载 SFM
                                        </a>
                                        <a data-clipboard-text="{$UniversalSub}/singbox"
                                           class="copy btn btn-primary">
                                            复制 sing-box 订阅链接
                                        </a>
                                        <a href="sing-box://import-remote-profile?url={$UniversalSub}/singbox#{$config['appName']}"
                                           class="btn btn-indigo">
                                            导入 SFM
                                        </a>
                                    </div>
                                </div>
                                <div class="tab-pane" id="android">
                                    <p>
                                        适用于 Clash 的订阅：<code>{$UniversalSub}/clash</code>
                                    </p>
                                    <p>
                                        适用于 sing-box 的订阅：<code>{$UniversalSub}/singbox</code>
                                    </p>
                                    <div class="btn-list justify-content-start">
                                        <a {if $config['enable_r2_client_download']}
                                            href="/user/clients/CMFA.apk"
                                        {else}
                                            href="/clients/CMFA.apk"
                                        {/if} class="btn btn-azure">
                                            下载 Clash.Meta For Android
                                        </a>
                                        <a data-clipboard-text="{$UniversalSub}/clash"
                                           class="copy btn btn-primary">
                                            复制 Clash 订阅链接
                                        </a>
                                        <a href="clash://install-config?url={$UniversalSub}/clash&name={$config['appName']}"
                                           class="btn btn-indigo">
                                            导入 Clash
                                        </a>
                                    </div>
                                    <div class="btn-list justify-content-start my-2">
                                        <a {if $config['enable_r2_client_download']}
                                            href="/user/clients/SFA.apk"
                                        {else}
                                            href="/clients/SFA.apk"
                                        {/if} class="btn btn-azure">
                                            下载 SFA
                                        </a>
                                        <a data-clipboard-text="{$UniversalSub}/singbox"
                                           class="copy btn btn-primary">
                                            复制 sing-box 订阅链接
                                        </a>
                                        <a href="sing-box://import-remote-profile?url={$UniversalSub}/singbox#{$config['appName']}"
                                           class="btn btn-indigo">
                                            导入 SFA
                                        </a>
                                    </div>
                                </div>
                                <div class="tab-pane" id="ios">
                                    <p>
                                        适用于 sing-box 的订阅：<code>{$UniversalSub}/singbox</code>
                                    </p>
                                    <div class="btn-list justify-content-start my-2">
                                        <a href="https://apps.apple.com/app/sing-box/id6451272673" target="_blank"
                                           class="btn btn-azure">
                                            安裝 sing-box
                                        </a>
                                        <a data-clipboard-text="{$UniversalSub}/singbox"
                                           class="copy btn btn-primary">
                                            复制 sing-box 订阅链接
                                        </a>
                                        <a href="sing-box://import-remote-profile?url={$UniversalSub}/singbox#{$config['appName']}"
                                           class="btn btn-indigo">
                                            导入 sing-box
                                        </a>
                                    </div>
                                </div>
                                <div class="tab-pane" id="linux">
                                    <p>
                                        适用于 Clash 的订阅：<code>{$UniversalSub}/clash</code>
                                    </p>
                                    <div class="btn-list justify-content-start">
                                        <a {if $config['enable_r2_client_download']}
                                            href="/user/clients/Clash.Verge.AppImage.tar.gz"
                                        {else}
                                            href="/clients/Clash.Verge.AppImage.tar.gz"
                                        {/if} class="btn btn-azure">
                                            下载 Clash Verge
                                        </a>
                                        <a data-clipboard-text="{$UniversalSub}/clash"
                                           class="copy btn btn-primary">
                                            复制 Clash 订阅链接
                                        </a>
                                        <a href="clash://install-config?url={$UniversalSub}/clash&name={$config['appName']}"
                                           class="btn btn-indigo">
                                            导入 Clash
                                        </a>
                                    </div>
                                </div>
                                <div class="tab-pane" id="config">
                                    <p>您的连接信息：</p>
                                    <div class="table-responsive">
                                        <table class="table table-vcenter card-table">
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
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">流量用量</h3>
                            <div class="progress progress-separated mb-3">
                                {if $user->LastusedTrafficPercent() < '1'}
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 1%"></div>
                                {else}
                                    <div class="progress-bar bg-primary" role="progressbar"
                                         style="width: {$user->LastusedTrafficPercent()}%">
                                    </div>
                                {/if}
                                {if $user->TodayusedTrafficPercent() < '1'}
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 1%"></div>
                                {else}
                                    <div class="progress-bar bg-success" role="progressbar"
                                         style="width: {$user->TodayusedTrafficPercent()}%"></div>
                                {/if}
                            </div>
                            <div class="row">
                                <div class="col-auto d-flex align-items-center pe-2">
                                    <span class="legend me-2 bg-primary"></span>
                                    <span>过去用量 {$user->LastusedTraffic()}</span>
                                </div>
                                <div class="col-auto d-flex align-items-center px-2">
                                    <span class="legend me-2 bg-success"></span>
                                    <span>今日用量 {$user->TodayusedTraffic()}</span>
                                </div>
                                <div class="col-auto d-flex align-items-center ps-2">
                                    <span class="legend me-2"></span>
                                    <span>剩余流量 {$user->unusedTraffic()}</span>
                                </div>
                            </div>
                            <p class="my-3">
                                {if $user->class === 0}
                                    前往
                                    <a href="/user/product">商店</a>
                                    购买套餐
                                {else}
                                    您的 LV. {$user->class} 账户会在 {$class_expire_days} 天后到期（{$user->class_expire}）
                                {/if}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-12">
                    <div class="card">
                        <div class="ribbon ribbon-top bg-yellow">
                            <i class="ti ti-bell-ringing icon"></i>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">
                                最新公告
                                {if $ann !== null}
                                    <span class="card-subtitle">{$ann->date}</span>
                                {/if}
                            </h3>
                            <p class="text-secondary">
                                {if $ann !== null}
                                    {$ann->content}
                                {else}
                                    暂无公告
                                {/if}
                            </p>
                        </div>
                    </div>
                </div>
                {if $public_setting['enable_checkin']}
                    <div class="col-lg-6 col-sm-12">
                        <div class="card">
                            <div class="card-stamp">
                                <div class="card-stamp-icon bg-green">
                                    <i class="ti ti-check"></i>
                                </div>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title">每日签到</h3>
                                <p>
                                    签到可领取
                                    {if $public_setting['checkin_min'] !== $public_setting['checkin_max']}
                                        &nbsp;
                                        <code>{$public_setting['checkin_min']} MB</code>
                                        至
                                        <code>{$public_setting['checkin_max']} MB</code>
                                        范围内的流量
                                    {else}
                                        <code>{$public_setting['checkin_min']} MB</code>
                                    {/if}
                                </p>
                                <p>
                                    上次签到时间：<code id="last-checkin-time">{$user->lastCheckInTime()}</code>
                                </p>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex">
                                    {if ! $user->isAbleToCheckin()}
                                        <button id="check-in" class="btn btn-primary ms-auto" disabled>已签到</button>
                                    {else}
                                        {if $public_setting['enable_checkin_captcha']}
                                            {include file='captcha/div.tpl'}
                                        {/if}
                                        <button id="check-in" class="btn btn-primary ms-auto"
                                                hx-post="/user/checkin" hx-swap="none" hx-vals='js:{
                                                {if $public_setting['enable_checkin_captcha']}
                                                    {include file='captcha/ajax.tpl'}
                                                {/if}
                                                }'>
                                            签到
                                        </button>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}
            </div>
        </div>
    </div>

    {if $public_setting['enable_checkin_captcha'] && $user->isAbleToCheckin()}
        {include file='captcha/js.tpl'}
    {/if}

    {include file='user/footer.tpl'}
