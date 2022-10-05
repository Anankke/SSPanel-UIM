{include file='user/tabler_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">用户中心</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">在这里查看账户信息和最新公告</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <!-- <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">账户等级</div>
                            </div>
                            <div class="h1 mb-3">Lv.{$user->class}</div>
                            <div class="d-flex mb-2">
                                <div>到期时间：<code>{$user->class_expire}</code></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">账户余额</div>
                            </div>
                            <div class="h1 mb-3">{$user->money} 元</div>
                            <div class="d-flex mb-2">
                                <div>你可以用余额支付账单</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">设备限制</div>
                            </div>
                            <div class="h1 mb-3">
                                {if $user->node_connector != 0}
                                    <div class="h1 mb-3">{$user->node_connector}</div>
                                {else}
                                    <div class="h1 mb-3">不限制</div>
                                {/if}
                            </div>
                            <div class="d-flex mb-2">
                                <div>最近使用时间：<code>{$user->lastSsTime()}</code></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">速度限制</div>
                            </div>
                            {if $user->node_speedlimit != 0}
                                <div class="h1 mb-3">{$user->node_speedlimit}</code> Mbps</div>
                            {else}
                                <div class="h1 mb-3">不限制</div>
                            {/if}
                            <div class="d-flex mb-2">
                                <div>不同套餐对应的速度限制不同</div>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="col-12">
                    <div class="row row-cards">
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-blue text-white avatar">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="icon icon-tabler icon-tabler-star" width="24" height="24"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path
                                                        d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z">
                                                    </path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                账户等级
                                            </div>
                                            <div class="text-muted">
                                                LV. {$user->class}
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
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="icon icon-tabler icon-tabler-coin" width="24" height="24"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <circle cx="12" cy="12" r="9"></circle>
                                                    <path
                                                        d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 0 0 0 4h2a2 2 0 0 1 0 4h-2a2 2 0 0 1 -1.8 -1">
                                                    </path>
                                                    <path d="M12 6v2m0 8v2"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                账户余额
                                            </div>
                                            <div class="text-muted">
                                                {$user->money}
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
                                            <span class="bg-twitter text-white avatar">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="icon icon-tabler icon-tabler-devices-pc" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M3 5h6v14h-6z"></path>
                                                    <path d="M12 9h10v7h-10z"></path>
                                                    <path d="M14 19h6"></path>
                                                    <path d="M17 16v3"></path>
                                                    <path d="M6 13v.01"></path>
                                                    <path d="M6 16v.01"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                设备限制
                                            </div>
                                            <div class="text-muted">
                                                {if $user->node_connector != 0}
                                                    {$user->node_connector}
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
                                            <span class="bg-facebook text-white avatar">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="icon icon-tabler icon-tabler-rocket" width="24" height="24"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path
                                                        d="M4 13a8 8 0 0 1 7 7a6 6 0 0 0 3 -5a9 9 0 0 0 6 -8a3 3 0 0 0 -3 -3a9 9 0 0 0 -8 6a6 6 0 0 0 -5 3">
                                                    </path>
                                                    <path d="M7 14a6 6 0 0 0 -3 6a6 6 0 0 0 6 -3"></path>
                                                    <circle cx="15" cy="9" r="1"></circle>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                速度限制
                                            </div>
                                            <div class="text-muted">
                                                {if $user->node_speedlimit != 0}
                                                    {$user->node_speedlimit}</code> Mbps
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
                        <div class="ribbon ribbon-top bg-yellow">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-bell-ringing"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path
                                    d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6">
                                </path>
                                <path d="M9 17v1a3 3 0 0 0 6 0v-1"></path>
                                <path d="M21 6.727a11.05 11.05 0 0 0 -2.794 -3.727"></path>
                                <path d="M3 6.727a11.05 11.05 0 0 1 2.792 -3.727"></path>
                            </svg>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">最新公告 <span class="card-subtitle">{$ann->date}</span></h3>
                            <p class="text-muted">
                                {$ann->content}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-12">
                    <div class="row row-deck row-cards">
                        <div class="col-12">
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
                                        {if time() > strtotime($user->expire_in)}
                                            你的账户过期了，可以前往 <a href="/user/product">商店</a> 购买套餐
                                        {else}
                                            {$diff = round((strtotime($user->expire_in) - time()) / 86400)}
                                            你的账户大约还有 {$diff} 天到期（{$user->expire_in}）
                                        {/if}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {if $config['enable_checkin'] == true}
                    <div class="col-lg-6 col-sm-12">
                        <div class="card">
                            <div class="card-stamp">
                                <div class="card-stamp-icon bg-green">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M5 12l5 5l10 -10"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title">每日签到</h3>
                                <p class="text-muted">
                                    签到可领取
                                    {if $config['checkinMin'] != $config['checkinMax']}
                                        &nbsp;<code>{$config['checkinMin']} MB</code> 至 <code>{$config['checkinMax']} MB</code>
                                        范围内的流量，
                                    {else}
                                        <code>{$config['checkinMin']} MB</code>
                                    {/if}
                                    {if $config['checkin_add_time'] == true}
                                        流量并获得 <code>{$config['checkin_add_time_hour']}</code> 小时的时长，
                                    {/if}
                                    每日零时后就可以可签到了
                                </p>
                                <p class="text-muted">
                                    上次签到时间：<code>{$user->lastCheckInTime()}</code>
                                </p>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex">
                                    {if !$user->isAbleToCheckin()}
                                        <button id="check-in" class="btn btn-primary ms-auto" disabled>已签到</button>
                                    {else}
                                        <button id="check-in" class="btn btn-primary ms-auto">签到</button>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}
                <div class="col-lg-6">
                    <div class="row row-cards">
                        <div class="col-12">
                            <div class="card">
                                <ul class="nav nav-tabs nav-fill" data-bs-toggle="tabs">
                                    <li class="nav-item">
                                        <a href="#windows" class="nav-link active" data-bs-toggle="tab">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-brand-windows" width="24"
                                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path
                                                    d="M17.8 20l-12 -1.5c-1 -.1 -1.8 -.9 -1.8 -1.9v-9.2c0 -1 .8 -1.8 1.8 -1.9l12 -1.5c1.2 -.1 2.2 .8 2.2 1.9v12.1c0 1.2 -1.1 2.1 -2.2 1.9z">
                                                </path>
                                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                                <line x1="4" y1="12" x2="20" y2="12"></line>
                                            </svg>
                                            &nbsp;Windows
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#macos" class="nav-link" data-bs-toggle="tab">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-device-laptop" width="24"
                                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <line x1="3" y1="19" x2="21" y2="19"></line>
                                                <rect x="5" y="6" width="14" height="10" rx="1"></rect>
                                            </svg>
                                            &nbsp;Macos
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#android" class="nav-link" data-bs-toggle="tab">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-brand-android" width="24"
                                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <line x1="4" y1="10" x2="4" y2="16"></line>
                                                <line x1="20" y1="10" x2="20" y2="16"></line>
                                                <path d="M7 9h10v8a1 1 0 0 1 -1 1h-8a1 1 0 0 1 -1 -1v-8a5 5 0 0 1 10 0">
                                                </path>
                                                <line x1="8" y1="3" x2="9" y2="5"></line>
                                                <line x1="16" y1="3" x2="15" y2="5"></line>
                                                <line x1="9" y1="18" x2="9" y2="21"></line>
                                                <line x1="15" y1="18" x2="15" y2="21"></line>
                                            </svg>
                                            &nbsp;Android
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#ios" class="nav-link" data-bs-toggle="tab">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-brand-apple" width="24" height="24"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path
                                                    d="M9 7c-3 0 -4 3 -4 5.5c0 3 2 7.5 4 7.5c1.088 -.046 1.679 -.5 3 -.5c1.312 0 1.5 .5 3 .5s4 -3 4 -5c-.028 -.01 -2.472 -.403 -2.5 -3c-.019 -2.17 2.416 -2.954 2.5 -3c-1.023 -1.492 -2.951 -1.963 -3.5 -2c-1.433 -.111 -2.83 1 -3.5 1c-.68 0 -1.9 -1 -3 -1z">
                                                </path>
                                                <path d="M12 4a2 2 0 0 0 2 -2a2 2 0 0 0 -2 2"></path>
                                            </svg>
                                            &nbsp;IOS
                                        </a>
                                    </li>
                                    <!-- <li class="nav-item">
                                        <a href="#route" class="nav-link" data-bs-toggle="tab">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-router" width="24" height="24"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <rect x="3" y="13" width="18" height="8" rx="2"></rect>
                                                <line x1="17" y1="17" x2="17" y2="17.01"></line>
                                                <line x1="13" y1="17" x2="13" y2="17.01"></line>
                                                <line x1="15" y1="13" x2="15" y2="11"></line>
                                                <path d="M11.75 8.75a4 4 0 0 1 6.5 0"></path>
                                                <path d="M8.5 6.5a8 8 0 0 1 13 0"></path>
                                            </svg>
                                            &nbsp;Route
                                        </a>
                                    </li> -->
                                    <li class="nav-item">
                                        <a href="#linux" class="nav-link" data-bs-toggle="tab">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-devices-pc" width="24" height="24"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M3 5h6v14h-6z"></path>
                                                <path d="M12 9h10v7h-10z"></path>
                                                <path d="M14 19h6"></path>
                                                <path d="M17 16v3"></path>
                                                <path d="M6 13v.01"></path>
                                                <path d="M6 16v.01"></path>
                                            </svg>
                                            &nbsp;Linux
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#config" class="nav-link" data-bs-toggle="tab">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-file-text" width="24" height="24"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                                <path
                                                    d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z">
                                                </path>
                                                <line x1="9" y1="9" x2="10" y2="9"></line>
                                                <line x1="9" y1="13" x2="15" y2="13"></line>
                                                <line x1="9" y1="17" x2="15" y2="17"></line>
                                            </svg>
                                            &nbsp;Config
                                        </a>
                                    </li>
                                </ul>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="tab-pane active show" id="windows">
                                            <div>
                                                <p>
                                                    适用于 v2rayN 的订阅：<code>{$subInfo['v2ray']}</code>
                                                </p>
                                                <p>
                                                    适用于 Clash 的订阅：<code>{$subInfo['clash']}</code>
                                                </p>
                                                <a data-clipboard-text="{$subInfo['v2ray']}"
                                                    class="copy btn btn-primary ms-auto">
                                                    复制 v2rayN
                                                </a>
                                                <a data-clipboard-text="{$subInfo['clash']}"
                                                    class="copy btn btn-primary ms-auto">
                                                    复制 Clash
                                                </a>
                                                <a href="clash://install-config?url={$subInfo['clash']}&name={$config['appName']}"
                                                    class="btn btn-primary ms-auto my-2">
                                                    导入 Clash
                                                </a>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="macos">
                                            <p>
                                                适用于 v2rayU 的订阅：<code>{$subInfo['v2ray']}</code>
                                            </p>
                                            <p>
                                                适用于 ClashX 的订阅：<code>{$subInfo['clash']}</code>
                                            </p>
                                            <a data-clipboard-text="{$subInfo['v2ray']}"
                                                class="copy btn btn-primary ms-auto">
                                                复制 v2rayU
                                            </a>
                                            <a data-clipboard-text="{$subInfo['clash']}"
                                                class="copy btn btn-primary ms-auto my-2">
                                                复制 ClashX
                                            </a>
                                        </div>
                                        <div class="tab-pane" id="android">
                                            <p>
                                                适用于 v2rayNG 的订阅：<code>{$subInfo['v2ray']}</code>
                                            </p>
                                            <p>
                                                适用于 Clash 的订阅：<code>{$subInfo['clash']}</code>
                                            </p>
                                            <a data-clipboard-text="{$subInfo['v2ray']}"
                                                class="copy btn btn-primary ms-auto">
                                                复制 v2rayNG
                                            </a>
                                            <a data-clipboard-text="{$subInfo['clash']}"
                                                class="copy btn btn-primary ms-auto">
                                                复制 Clash
                                            </a>
                                            <a href="clash://install-config?url={$subInfo['clash']}&name={$config['appName']}"
                                                class="btn btn-primary ms-auto my-2">
                                                导入 Clash
                                            </a>
                                        </div>
                                        <div class="tab-pane" id="ios">
                                            <p>
                                                在安装 Shadowrocket 或 Quantumult 后，只需 <span style="color: red;">使用 Safari
                                                    浏览器</span> 点击下方按钮，然后在弹出的弹窗中点击 <b>打开</b>，即可快捷完成订阅设置
                                            </p>
                                            <p style="color: red;">
                                                如若提示无法打开，是因为需要先安装对应 APP，然后才能导入
                                            </p>
                                            <a href="sub://{base64_encode($subInfo['v2ray'])}"
                                                class="btn btn-primary ms-auto">
                                                导入 Shadowrocket
                                            </a>
                                            <a href="quantumult://configuration?server={$qt_url}"
                                                class="btn btn-primary ms-auto my-1">
                                                导入 Quantumult
                                            </a>
                                        </div>
                                        <div class="tab-pane" id="linux">
                                            <div>紧张准备中...</div>
                                        </div>
                                        <div class="tab-pane" id="config">
                                            {if $servers->where('sort', '0')->count() > '0'}
                                                <button data-clipboard-text="{$text['ss']}"
                                                    class="copy btn btn-primary ms-auto">
                                                    复制 SS 文本配置
                                                </button>
                                            {/if}
                                            {if $servers->where('sort', '1')->count() > '0'}
                                                <button data-clipboard-text="{$text['ssr']}"
                                                    class="copy btn btn-primary ms-auto">
                                                    复制 SSR 文本配置
                                                </button>
                                            {/if}
                                            {if $servers->where('sort', '11')->count() > '0'}
                                                <button data-clipboard-text="{$text['v2ray']}"
                                                    class="copy btn btn-primary ms-auto">
                                                    复制 V2ray 文本配置
                                                </button>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row row-cards">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h3 class="card-title">过去七日用量 (单位: GB)</h3>
                                    <div id="past-usage"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="success-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-success"></div>
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-green icon-lg" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <circle cx="12" cy="12" r="9" />
                        <path d="M9 12l2 2l4 -4" />
                    </svg>
                    <p id="success-message" class="text-muted">成功</p>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <a id="success-confirm" href="#" class="btn w-100" data-bs-dismiss="modal">
                                    好
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-blur fade" id="fail-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-danger"></div>
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 9v2m0 4v.01" />
                        <path
                            d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" />
                    </svg>
                    <p id="fail-message" class="text-muted">失败</p>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <a href="#" class="btn btn-danger w-100" data-bs-dismiss="modal">
                                    确认
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var clipboard = new ClipboardJS('.copy');
        clipboard.on('success', function(e) {
            $('#success-message').text('已复制到剪切板');
            $('#success-dialog').modal('show');
        });

        $("#check-in").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/checkin",
                dataType: "json",
                success: function(data) {
                    if (data.ret == 1) {
                        $('#success-message').text(data.msg);
                        $('#success-dialog').modal('show');
                    } else {
                        $('#fail-message').text(data.msg);
                        $('#fail-dialog').modal('show');
                    }
                }
            })
        });

        $("#success-confirm").click(function() {
            location.reload();
        });

        document.addEventListener("DOMContentLoaded", function() {
            window.ApexCharts && (new ApexCharts(document.getElementById('past-usage'), {
                chart: {
                    type: "line",
                    fontFamily: 'inherit',
                    height: 240,
                    parentHeightOffset: 0,
                    toolbar: {
                        show: false,
                    },
                    animations: {
                        enabled: false
                    },
                },
                fill: {
                    opacity: 1,
                },
                stroke: {
                    width: 2,
                    lineCap: "round",
                    curve: "smooth",
                },
                series: [{
                    name: "流量用量",
                    data: [{implode(',', $chart_traffic_data)}]
                }],
                grid: {
                    padding: {
                        top: -20,
                        right: 0,
                        left: -4,
                        bottom: -4
                    },
                    strokeDashArray: 4,
                },
                xaxis: {
                    labels: {
                        padding: 0,
                        format: 'MM/dd',
                    },
                    tooltip: {
                        enabled: false
                    },
                },
                yaxis: {
                    labels: {
                        padding: 4
                    },
                },
                labels: [
                    {implode(',', $chart_date_data)}
                ],
                colors: ["#206bc4"],
                legend: {
                    show: false,
                },
            })).render();
        });
    </script>
{include file='user/tabler_footer.tpl'}