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
                <div class="col-12">
                    <div class="row row-cards">
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-blue text-white avatar">
                                                <i class="ti ti-star icon"></i>
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
                                                <i class="ti ti-coin icon"></i>
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
                                                <i class="ti ti-devices-pc icon"></i>
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
                                                <i class="ti ti-rocket icon"></i>
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
                <div class="col-lg-6">
                    <div class="row row-cards">
                        <div class="col-12">
                            <div class="card">
                                <ul class="nav nav-tabs nav-fill" data-bs-toggle="tabs">
                                    <li class="nav-item">
                                        <a href="#sub" class="nav-link active" data-bs-toggle="tab">
                                            <i class="ti ti-rss icon"></i>
                                            &nbsp;通用订阅
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
                                            &nbsp;Macos
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
                                            &nbsp;IOS
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#linux" class="nav-link" data-bs-toggle="tab">
                                            <i class="ti ti-brand-debian icon"></i>
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
                                                    通用订阅（json）：<code>{$getUniversalSub}/json</code>
                                                </p>
                                                <p>
                                                    通用订阅（clash）：<code>{$subInfo['clash']}</code>
                                                </p>
                                                <a data-clipboard-text="{$getUniversalSub}/json"
                                                    class="copy btn btn-primary ms-auto">
                                                    复制通用订阅（json）
                                                </a>
                                                <a data-clipboard-text="{$subInfo['clash']}"
                                                    class="copy btn btn-primary ms-auto">
                                                    复制通用订阅（clash）
                                                </a>
                                                <a href="clash://install-config?url={$subInfo['clash']}&name={$config['appName']}"
                                                    class="btn btn-primary ms-auto my-2">
                                                    导入 Clash
                                                </a>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="windows">
                                            <div>
                                                <p>
                                                    适用于 Clash 的订阅：<code>{$subInfo['clash']}</code>
                                                </p>
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
                                                适用于 Clash 的订阅：<code>{$subInfo['clash']}</code>
                                            </p>
                                            <a data-clipboard-text="{$subInfo['clash']}"
                                                class="copy btn btn-primary ms-auto my-2">
                                                复制 Clash
                                            </a>
                                            <a href="clash://install-config?url={$subInfo['clash']}&name={$config['appName']}"
                                                class="btn btn-primary ms-auto my-2">
                                                导入 Clash
                                            </a>
                                        </div>
                                        <div class="tab-pane" id="android">
                                            <p>
                                                适用于 Clash 的订阅：<code>{$subInfo['clash']}</code>
                                            </p>
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
                                                在安装 Shadowrocket 后，只需 <span style="color: red;">使用 Safari
                                                    浏览器</span> 点击下方按钮，然后在弹出的弹窗中点击 <b>打开</b>，即可快捷完成订阅设置
                                            </p>
                                            <p style="color: red;">
                                                如若提示无法打开，是因为需要先安装对应 APP，然后才能导入
                                            </p>
                                            <a data-clipboard-text="{$subInfo['clash']}"
                                                class="copy btn btn-primary ms-auto">
                                                复制 Clash
                                            </a>
                                            <a href="sub://{base64_encode($subInfo['clash'])}"
                                                class="btn btn-primary ms-auto">
                                                导入 Shadowrocket
                                            </a>
                                        </div>
                                        <div class="tab-pane" id="linux">
                                            <p>
                                                适用于 Clash 的订阅：<code>{$subInfo['clash']}</code>
                                            </p>
                                            <a data-clipboard-text="{$subInfo['clash']}"
                                                class="copy btn btn-primary ms-auto">
                                                复制 Clash
                                            </a>
                                            <a href="clash://install-config?url={$subInfo['clash']}&name={$config['appName']}"
                                                class="btn btn-primary ms-auto my-2">
                                                导入 Clash
                                            </a>
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
                <div class="col-lg-6 col-sm-12">
                    <div class="card">
                        <div class="ribbon ribbon-top bg-yellow">
                            <i class="ti ti-bell-ringing icon"></i>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">最新公告 <span class="card-subtitle">{$ann->date}</span></h3>
                            <p class="text-muted">
                                {$ann->content}
                            </p>
                        </div>
                    </div>
                </div>
                {if $config['enable_checkin'] == true}
                    <div class="col-lg-6 col-sm-12">
                        <div class="card">
                            <div class="card-stamp">
                                <div class="card-stamp-icon bg-green">
                                    <i class="ti ti-check"></i>
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
                                </p>
                                <p class="text-muted">
                                    上次签到时间：<code>{$user->lastCheckInTime()}</code>
                                </p>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex">
                                    {if $config['enable_checkin_captcha'] == true && $config['captcha_provider'] == 'turnstile' && $user->isAbleToCheckin()}
                                    <div class="cf-turnstile" data-sitekey="{$turnstile_sitekey}" data-theme="light"></div>
                                    {/if}
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
    </script>

    {if $config['enable_checkin_captcha'] == true && $config['captcha_provider'] == 'turnstile'}
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?compat=recaptcha" async defer></script>
    {/if}
{include file='user/tabler_footer.tpl'}