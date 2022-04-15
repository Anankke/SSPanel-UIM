{include file='user/tabler_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">资料修改</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">修改账户的部分信息</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-sm-12 col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">修改登录邮箱</h3>
                            <div class="mb-3">
                                <input id="new-email" type="email" class="form-control" placeholder="新邮箱"
                                    {if $config['enable_change_email'] != true}disabled{/if}>
                            </div>
                            {if $config['enable_email_verify'] == true && $config['enable_change_email'] == true}
                                <div class="mb-3">
                                    <input id="email-code" type="text" class="form-control" placeholder="新邮箱收到的验证码">
                                </div>
                            {/if}
                        </div>
                        <div class="card-footer">
                            <div class="d-flex">
                                {if $config['enable_email_verify'] == true && $config['enable_change_email'] == true}
                                    <a id="email-verify" class="btn btn-link">获取验证码</a>
                                {/if}
                                <button id="modify-email" class="btn btn-primary ms-auto"
                                    {if $config['enable_change_email'] != true}disabled{/if}>修改</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">修改用户名</h3>
                            <div class="mb-3">
                                <input id="new-username" type="text" class="form-control" value="{$user->user_name}">
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex">
                                <a id="modify-username" class="btn btn-primary ms-auto">修改</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">更换订阅地址</h3>
                            <p>点击会重置您的订阅链接，您需要更新客户端中所配置的订阅地址方可继续使用</p>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex">
                                <a id="reset-sub-url" class="btn btn-primary ms-auto bg-red">更换</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">修改连接密码</h3>
                            <p>重置连接密码与 UUID ，修改后需更新订阅才能继续使用</p>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex">
                                <a id="reset-passwd" class="btn btn-primary ms-auto bg-red">重置</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">修改主题</h3>
                            <div class="mb-3">
                                <select id="user-theme" class="form-select">
                                    {foreach $themes as $theme}
                                        <option value="{$theme}" {if $user->theme == $theme}selected{/if}>{$theme}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex">
                                <a id="modify-user-theme" class="btn btn-primary ms-auto">修改</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">修改每日推送</h3>
                            <div class="mb-3">
                                <select id="daily-report" class="form-select">
                                    <option value="0" {if $user->sendDailyMail == '0'}selected{/if}>不发送</option>
                                    <option value="1" {if $user->sendDailyMail == '1'}selected{/if}>邮件接收</option>
                                    <option value="2" {if $user->sendDailyMail == '2'}selected{/if}>TelegramBot接收
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex">
                                <a id="modify-daily-report" class="btn btn-primary ms-auto">修改</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">更换连接端口</h3>
                            <p>随机分配一个连接端口，这将用于 SS / SSR 客户端</p>
                            <p>当前端口是：<code>{$user->port}</code></p>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex">
                                <a id="reset-client-port" class="btn btn-red ms-auto">更换</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">修改登录密码</h3>
                            <div class="mb-3">
                                <form>
                                    <input id="password" type="password" class="form-control" placeholder="当前登录密码"
                                        autocomplete="off">
                                </form>
                            </div>
                            <div class="mb-3">
                                <form>
                                    <input id="new-password" type="password" class="form-control" placeholder="输入新密码"
                                        autocomplete="off">
                                </form>
                            </div>
                            <div class="mb-3">
                                <form>
                                    <input id="again-new-password" type="password" class="form-control"
                                        placeholder="再次输入新密码" autocomplete="off">
                                </form>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex">
                                <a id="modify-login-passwd" class="btn btn-primary ms-auto">修改</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">修改联系方式</h3>
                            <div class="mb-3">
                                <select id="imtype" class="form-select">
                                    <option value="1" {if $user->im_type == '1'}selected{/if}>WeChat</option>
                                    <option value="2" {if $user->im_type == '2'}selected{/if}>QQ</option>
                                    <option value="3" {if $user->im_type == '3'}selected{/if}>Facebook</option>
                                    <option value="4" {if $user->im_type == '4'}selected{/if}>Telegram</option>
                                    <option value="5" {if $user->im_type == '5'}selected{/if}>Discord</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <input id="wechat" type="text" class="form-control" value="{$user->im_value}"
                                    placeholder="社交账户">
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex">
                                <a id="modify-im" class="btn btn-primary ms-auto">修改</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">设置两步认证</h3>
                            <div class="col-md-12">
                                <div class="col-sm-6 col-md-6">
                                    <p>
                                        <i class="ti ti-brand-apple"></i>
                                        <a target="view_window"
                                            href="https://apps.apple.com/us/app/google-authenticator/id388497605">苹果客户端
                                        </a>
                                        &nbsp;&nbsp;&nbsp;
                                        <i class="ti ti-brand-android"></i>
                                        <a target="view_window"
                                            href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=zh&gl=US">安卓客户端
                                        </a>
                                    </p>
                                </div>
                            </div>
                            <hr />
                            <div class="row">
                                <div class="col-md-3">
                                    <p id="qrcode"></p>
                                </div>
                                <div class="col-md-9">
                                    <div class="mb-3">
                                        <select id="ga-enable" class="form-select">
                                            <option value="0">不使用</option>
                                            <option value="1" {if $user->ga_enable == '1'}selected{/if}>使用两步认证登录
                                            </option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <input id="2fa-test-code" type="text" class="form-control"
                                            placeholder="测试两步认证验证码">
                                    </div>
                                    <div class="col-md-12">
                                        <p>密钥：<code>{$user->ga_token}</code></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex">
                                <a id="reset-2fa" class="btn btn-link">重置</a>
                                <a id="test-2fa" class="btn btn-link">测试</a>
                                <a id="save-2fa" class="btn btn-primary ms-auto">设置</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">修改连接参数</h3>
                            <p>SS/SSR
                                支持的加密方式和混淆方式有所不同，请根据实际情况来进行选择。在这里选择你需要使用的客户端可以帮助你筛选加密方式和混淆方式。<code>auth_chain</code>
                                系为实验性协议，可能造成不稳定或无法使用</p>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">加密方式</label>
                                <div class="col">
                                    <select id="method" class="form-select">
                                        {$method_list = $config_service->getSupportParam('method')}
                                        {foreach $method_list as $method}
                                            <option value="{$method}" {if $user->method == $method}selected{/if}>{$method}
                                            </option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">协议</label>
                                <div class="col">
                                    <select id="protocol" class="form-select">
                                        {$protocol_list = $config_service->getSupportParam('protocol')}
                                        {foreach $protocol_list as $protocol}
                                            <option value="{$protocol}" {if $user->protocol == $protocol}selected{/if}>
                                                {$protocol}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">混淆</label>
                                <div class="col">
                                    <select id="obfs" class="form-select">
                                        {$obfs_list = $config_service->getSupportParam('obfs')}
                                        {foreach $obfs_list as $obfs}
                                            <option value="{$obfs}" {if $user->obfs == $obfs}selected{/if}>{$obfs}
                                            </option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <input id="obfs_param" type="text" class="form-control" value="{$user->obfs_param}"
                                    placeholder="混淆参数">
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex">
                                <a id="modify-config" class="btn btn-primary ms-auto">修改</a>
                            </div>
                        </div>
                    </div>
                </div>
                {if $config['enable_telegram'] == true}
                    <div class="col-sm-12 col-md-6">
                        {if $user->telegram_id != 0}
                            <div class="card">
                                <div class="card-body">
                                    <h3 class="card-title">解绑 Telegram</h3>
                                    <p>当前绑定的 Telegram 账户：<a href="https://t.me/{$user->im_value}">@{$user->im_value}</a></p>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex">
                                        <a href="/user/telegram_reset" class="btn btn-red ms-auto">解绑</a>
                                    </div>
                                </div>
                            </div>
                        {else}
                            <div class="card">
                                <div class="card-body">
                                    <h3 class="card-title">绑定 Telegram</h3>
                                    {if $config['use_new_telegram_bot'] == true}
                                        <div class="row">
                                            <div class="col-6 col-sm-2 col-md-2 col-sm mb-3">
                                                <a href="https://t.me/{$telegram_bot}?start={$bind_token}"
                                                    class="btn btn-primary w-100">
                                                    一键绑定
                                                </a>
                                            </div>
                                            <div class="col-6 col-sm-2 col-md-2 col-xl mb-3">
                                                手机电脑平板等如已安装 Telegram 可点击
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 col-sm-2 col-md-2 col-sm mb-3">
                                                <button data-clipboard-text="{$bind_token}" class="copy btn btn-primary w-100">
                                                    复制验证码
                                                </button>
                                            </div>
                                            <div class="col-6 col-sm-2 col-md-2 col-xl mb-3">
                                                向机器人 <a href="https://t.me/{$telegram_bot}">@{$telegram_bot}</a> 发送验证码绑定
                                            </div>
                                        </div>
                                    {else}
                                        <p>向机器人 <a href="https://t.me/{$telegram_bot}">@{$telegram_bot}</a> 发送图片绑定，拍照可能导致解码失败</p>
                                        <p id="qrcode-telegram"></p>
                                    {/if}
                                </div>
                            </div>
                        {/if}
                    </div>
                {/if}
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
                                <a href="#" class="btn w-100" data-bs-dismiss="modal">
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
        var qrcode = new QRCode('qrcode', {
            text: "{$user->getGAurl()}",
            width: 128,
            height: 128,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });

        {if $config['use_new_telegram_bot'] == false}
            var tgqrcode = new QRCode('qrcode-telegram', {
                text: 'mod://bind/{$bind_token}',
                width: 128,
                height: 128,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        {/if}

        var clipboard = new ClipboardJS('.copy');
        clipboard.on('success', function(e) {
            $('#success-message').text('已复制到剪切板');
            $('#success-dialog').modal('show');
        });

        $("#modify-email").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/email",
                dataType: "json",
                data: {
                    {if $config['enable_email_verify'] == true}
                        emailcode: $('#email-code').val(),
                    {/if}
                    newemail: $('#new-email').val()
                },
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

        $("#email-verify").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/send",
                dataType: "json",
                data: {
                    email: $('#new-email').val()
                },
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

        $("#modify-username").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/username",
                dataType: "json",
                data: {
                    newusername: $('#new-username').val()
                },
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

        $("#reset-sub-url").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/url_reset",
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

        $("#modify-user-theme").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/theme",
                dataType: "json",
                data: {
                    theme: $('#user-theme').val()
                },
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

        $("#modify-daily-report").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/mail",
                dataType: "json",
                data: {
                    mail: $('#daily-report').val()
                },
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

        $("#reset-client-port").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/port",
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

        $("#reset-passwd").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/sspwd",
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

        $("#modify-login-passwd").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/password",
                dataType: "json",
                data: {
                    pwd: $('#new-password').val(),
                    repwd: $('#again-new-password').val(),
                    oldpwd: $('#password').val()
                },
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

        $("#modify-im").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/wechat",
                dataType: "json",
                data: {
                    imtype: $('#imtype').val(),
                    wechat: $('#wechat').val()
                },
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

        $("#reset-2fa").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/gareset",
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

        $("#test-2fa").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/gacheck",
                dataType: "json",
                data: {
                    code: $('#2fa-test-code').val()
                },
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

        $("#save-2fa").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/gaset",
                dataType: "json",
                data: {
                    enable: $('#ga-enable').val()
                },
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

        $("#modify-config").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/ssr",
                dataType: "json",
                data: {
                    obfs: $('#obfs').val(),
                    method: $('#method').val(),
                    protocol: $('#protocol').val(),
                    obfs_param: $('#obfs_param').val()
                },
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
{include file='user/tabler_footer.tpl'}