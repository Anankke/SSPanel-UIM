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
                            <hr />
                            <p class="text-muted">
                                {$ann->content}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
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
                                            <span>过去用量</span>
                                            <span
                                                class="d-none d-md-inline d-lg-none d-xxl-inline ms-2 text-muted">{$user->LastusedTraffic()}</span>
                                        </div>
                                        <div class="col-auto d-flex align-items-center px-2">
                                            <span class="legend me-2 bg-success"></span>
                                            <span>今日用量</span>
                                            <span
                                                class="d-none d-md-inline d-lg-none d-xxl-inline ms-2 text-muted">{$user->TodayusedTraffic()}</span>
                                        </div>
                                        <div class="col-auto d-flex align-items-center ps-2">
                                            <span class="legend me-2"></span>
                                            <span>剩余流量</span>
                                            <span
                                                class="d-none d-md-inline d-lg-none d-xxl-inline ms-2 text-muted">{$user->unusedTraffic()}</span>
                                        </div>
                                    </div>
                                    <p class="my-3">
                                        {if time() > strtotime($user->expire_in)}
                                            你的账户过期了，可以前往 <a href="/user/product">商店</a> 购买套餐
                                        {else}
                                            {$diff = round((strtotime($user->expire_in) - time()) / 86400)}
                                            你的账户大约还有 {$diff} 天到期
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
                                    签到可领取 <code>{$config['checkinMin']} MB</code> 至 <code>{$config['checkinMax']} MB</code>
                                    范围内的流量，每日零时后就可以可签到了
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
                                <div class="card-body">
                                    <h3 class="card-title">过去七日用量（正在开发）</h3>
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
                    data: [155, 65, 465, 265, 225, 325, 80]
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
                    },
                    tooltip: {
                        enabled: false
                    },
                    type: 'datetime',
                },
                yaxis: {
                    labels: {
                        padding: 4
                    },
                },
                labels: [
                    '2020-06-20', '2020-06-21', '2020-06-22', '2020-06-23', '2020-06-24',
                    '2020-06-25', '2020-06-26'
                ],
                colors: ["#206bc4"],
                legend: {
                    show: false,
                },
            })).render();
        });
    </script>
{include file='user/tabler_footer.tpl'}