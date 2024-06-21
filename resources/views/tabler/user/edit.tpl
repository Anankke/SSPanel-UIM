{include file='user/header.tpl'}

<script src="//{$config['jsdelivr_url']}/npm/jquery/dist/jquery.min.js"></script>

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">资料修改</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">修改账户的部分信息</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <ul class="nav nav-tabs nav-fill" data-bs-toggle="tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="#personal_information" class="nav-link active" data-bs-toggle="tab"
                                   aria-selected="true" role="tab">
                                    <i class="ti ti-chart-candle icon"></i>&nbsp;
                                    资料
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#login_security" class="nav-link" data-bs-toggle="tab" aria-selected="true"
                                   role="tab">
                                    <i class="ti ti-shield-lock icon"></i>&nbsp;
                                    登录
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#use_safety" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                                   tabindex="-1" role="tab">
                                    <i class="ti ti-brand-telegram icon"></i>&nbsp;
                                    使用
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#other_settings" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                                   tabindex="-1" role="tab">
                                    <i class="ti ti-settings icon"></i>&nbsp;
                                    其他
                                </a>
                            </li>
                        </ul>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active show" id="personal_information" role="tabpanel">
                                    <div class="row row-deck row-cards">
                                        <div class="col-sm-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">登录邮箱</h3>
                                                    <p>当前邮箱：<code id="email">{$user->email}</code></p>
                                                    <div class="mb-3">
                                                        <input id="new-email" type="email" class="form-control"
                                                               placeholder="新邮箱"
                                                               {if ! $config['enable_change_email']}disabled=""{/if}>
                                                    </div>
                                                    {if $public_setting['reg_email_verify'] && $config['enable_change_email']}
                                                    <div class="mb-3">
                                                        <input id="email-code" type="text" class="form-control"
                                                               placeholder="验证码">
                                                    </div>
                                                    {/if}
                                                </div>
                                                <div class="card-footer">
                                                    <div class="d-flex">
                                                        {if $public_setting['reg_email_verify'] && $config['enable_change_email']}
                                                        <button class="btn btn-link"
                                                                hx-post="/user/edit/send" hx-swap="none"
                                                                hx-vals='js:{ email: document.getElementById("newemail").value }'>
                                                            获取验证码
                                                        </button>
                                                        <button class="btn btn-primary ms-auto"
                                                                hx-post="/user/edit/email" hx-swap="none"
                                                                hx-vals='js:{
                                                                    newemail: document.getElementById("new-email").value,
                                                                    emailcode: document.getElementById("email-code").value
                                                                }'>
                                                            修改
                                                        </button>
                                                        {elseif $config['enable_change_email']}
                                                        <button class="btn btn-primary ms-auto"
                                                                hx-post="/user/edit/email" hx-swap="none"
                                                                hx-vals='js:{ newemail: document.getElementById("new-email").value }'>
                                                            修改
                                                        </button>
                                                        {else}
                                                        <button class="btn btn-primary ms-auto"
                                                                disabled>不允许修改
                                                        </button>
                                                        {/if}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">用戶名</h3>
                                                    <p>当前用戶名：<code id="username">{$user->user_name}</code></p>
                                                    <div class="mb-3">
                                                        <input id="new-username" type="text" class="form-control"
                                                               placeholder="新用戶名" autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="d-flex">
                                                        <button class="btn btn-primary ms-auto"
                                                           hx-post="/user/edit/username" hx-swap="none"
                                                           hx-vals='js:{ newusername: document.getElementById("new-username").value }'>
                                                            修改
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">IM 账号绑定</h3>
                                                    <div class="mb-3">
                                                        <select id="imtype" class="form-select"
                                                                {if $user->im_type !== 0 && $user->im_value !== ''}disabled=""{/if}>
                                                            <option value="0" {if $user->im_type === 0}selected{/if}>
                                                                未绑定
                                                            </option>
                                                            <option value="1" {if $user->im_type === 1}selected{/if}>
                                                                Slack
                                                            </option>
                                                            <option value="2" {if $user->im_type === 2}selected{/if}>
                                                                Discord
                                                            </option>
                                                            <option value="4" {if $user->im_type === 4}selected{/if}>
                                                                Telegram
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <input id="imvalue" type="text" class="form-control"
                                                               value="{$user->im_value}" disabled>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="d-flex btn-list justify-content-end"
                                                         id="oauth-provider"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">解绑 IM 账户</h3>
                                                    {if $user->im_type === 0}
                                                    <p>你的账户当前没有绑定任何 IM 服务</p>
                                                    {else}
                                                    <p>
                                                        当前绑定的 IM 服务：{$user->imType()}
                                                        <br>
                                                        账户 ID：<code>{$user->im_value}</code>
                                                    </p>
                                                    {/if}
                                                </div>
                                                {if $user->im_type !== 0}
                                                <div class="card-footer">
                                                    <div class="d-flex">
                                                        <button class="btn btn-red ms-auto"
                                                                hx-post="/user/edit/unbind_im" hx-swap="none">
                                                            解绑
                                                        </button>
                                                    </div>
                                                </div>
                                                {/if}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="login_security" role="tabpanel">
                                    <div class="row row-deck row-cards">
                                        <div class="col-sm-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">多因素认证</h3>
                                                    <div class="col-md-12">
                                                        <div class="col-sm-6 col-md-6">
                                                            <i class="ti ti-brand-apple"></i>
                                                            <a target="view_window"
                                                               href="https://apps.apple.com/us/app/google-authenticator/id388497605">iOS
                                                                客户端
                                                            </a>
                                                            &nbsp;&nbsp;&nbsp;
                                                            <i class="ti ti-brand-android"></i>
                                                            <a target="view_window"
                                                               href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2">Android
                                                                客户端
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <p id="qrcode"></p>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="mb-3">
                                                                <select id="ga-enable" class="form-select">
                                                                    <option value="0">不使用</option>
                                                                    <option value="1"
                                                                            {if $user->ga_enable === '1'}selected{/if}>
                                                                        使用两步认证登录
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <input id="ga-test-code" type="text"
                                                                       class="form-control"
                                                                       placeholder="测试两步认证验证码">
                                                            </div>
                                                            <div class="col-md-12">
                                                                <p>密钥：
                                                                    <code id="ga-token" class="spoiler">
                                                                        {$user->ga_token}
                                                                    </code>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="d-flex">
                                                        <button class="btn btn-link"
                                                                hx-post="/user/ga_reset" hx-swap="none" >
                                                            重置
                                                        </button>
                                                        <button class="btn btn-link"
                                                                hx-post="/user/ga_check" hx-swap="none"
                                                                hx-vals='js:{ code: document.getElementById("ga-test-code").value }'>
                                                            测试
                                                        </button>
                                                        <button class="btn btn-primary ms-auto"
                                                                hx-post="/user/ga_set" hx-swap="none"
                                                                hx-vals='js:{ enable: document.getElementById("ga-enable").value }'>
                                                            设置
                                                        </button>
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
                                                            <input id="password" type="password" class="form-control"
                                                                   placeholder="当前登录密码" autocomplete="off">
                                                        </form>
                                                    </div>
                                                    <div class="mb-3">
                                                        <form>
                                                            <input id="new_password" type="password"
                                                                   class="form-control" placeholder="输入新密码"
                                                                   autocomplete="off">
                                                        </form>
                                                    </div>
                                                    <div class="mb-3">
                                                        <form>
                                                            <input id="confirm_new_password" type="password"
                                                                   class="form-control" placeholder="再次输入新密码"
                                                                   autocomplete="off">
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="d-flex">
                                                        <button class="btn btn-primary ms-auto"
                                                                hx-post="/user/edit/password" hx-swap="none"
                                                                hx-vals='js:{
                                                                    new_password: document.getElementById("new_password").value,
                                                                    confirm_new_password: document.getElementById("confirm_new_password").value,
                                                                    password: document.getElementById("password").value
                                                                }'>
                                                            修改
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="use_safety" role="tabpanel">
                                    <div class="row row-deck row-cards">
                                        <div class="col-sm-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">更换加密方式</h3>
                                                    <p>
                                                        不同的客户端支持的加密方式可能会有所不同，请参考客户端支持列表进行设置</p>
                                                    <div class="mb-3">
                                                        <select id="user-method" class="form-select">
                                                            {foreach $methods as $method}
                                                            <option value="{$method}"
                                                                    {if $user->method === $method}selected{/if}>
                                                                {$method}
                                                            </option>
                                                            {/foreach}
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="d-flex">
                                                        <button class="btn btn-primary ms-auto"
                                                                hx-post="/user/edit/method" hx-swap="none"
                                                                hx-vals='js:{ method: document.getElementById("user-method").value }'>
                                                            修改
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">重置订阅地址</h3>
                                                    <p>重置订阅地址后，旧的订阅地址将无法获取配置，但节点配置仍能使用。
                                                        如果希望作废旧节点配置请配合重置连接密码操作</p>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="d-flex">
                                                        <button class="btn btn-primary ms-auto bg-red"
                                                                hx-post="/user/edit/url_reset" hx-swap="none">
                                                            重置
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">重置连接密码</h3>
                                                    <p>重置连接密码与UUID ，重置后需更新订阅，才能继续使用</p>
                                                    <p>当前连接密码：<code id="passwd" class="spoiler">{$user->passwd}</code></p>
                                                    <p>当前UUID：<code id="uuid" class="spoiler">{$user->uuid}</code></p>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="d-flex">
                                                        <button class="btn btn-primary ms-auto bg-red"
                                                                hx-post="/user/edit/passwd_reset" hx-swap="none">
                                                            重置
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="other_settings" role="tabpanel">
                                    <div class="row row-deck row-cards">
                                        <div class="col-sm-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">每日流量报告</h3>
                                                    <div class="mb-3">
                                                        <select id="daily-mail" class="form-select">
                                                            <option value="0"
                                                                    {if $user->daily_mail_enable === 0}selected{/if}>
                                                                不接收
                                                            </option>
                                                            <option value="1"
                                                                    {if $user->daily_mail_enable === 1}selected{/if}>
                                                                邮件接收
                                                            </option>
                                                            <option value="2"
                                                                    {if $user->daily_mail_enable === 2}selected{/if}>
                                                                IM 接收
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="d-flex">
                                                        <button class="btn btn-primary ms-auto"
                                                                hx-post="/user/edit/daily_mail" hx-swap="none"
                                                                hx-vals='js:{ mail: document.getElementById("daily-mail").value }'>
                                                            修改
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">偏好的联系方式</h3>
                                                    <p>当 IM 未绑定时站点依然会向账户邮箱发送通知信息</p>
                                                    <div class="mb-3">
                                                        <select id="contact-method" class="form-select">
                                                            <option value="1"
                                                                    {if $user->contact_method === 1}selected{/if}>
                                                                邮件
                                                            </option>
                                                            <option value="2"
                                                                    {if $user->contact_method === 2}selected{/if}>
                                                                IM
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="d-flex">
                                                        <button class="btn btn-primary ms-auto"
                                                                hx-post="/user/edit/contact_method" hx-swap="none"
                                                                hx-vals='js:{ contact: document.getElementById("contact-method").value }'>
                                                            修改
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">修改主题</h3>
                                                    <div class="mb-3">
                                                        <select id="user-theme" class="form-select">
                                                            {foreach $themes as $theme}
                                                            <option value="{$theme}"
                                                                    {if $user->theme === $theme}selected{/if}>{$theme}
                                                            </option>
                                                            {/foreach}
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="d-flex">
                                                        <button class="btn btn-primary ms-auto"
                                                                hx-post="/user/edit/theme" hx-swap="none"
                                                                hx-vals='js:{ theme: document.getElementById("user-theme").value }'>
                                                            修改
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">修改主题模式</h3>
                                                    <div class="mb-3">
                                                        <select id="theme-mode" class="form-select">
                                                            <option value="2" {if $user->is_dark_mode === 2}selected{/if}>
                                                                自动
                                                            </option>
                                                            <option value="0" {if $user->is_dark_mode === 0}selected{/if}>
                                                                浅色
                                                            </option>
                                                            <option value="1" {if $user->is_dark_mode === 1}selected{/if}>
                                                                深色
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="d-flex">
                                                        <button class="btn btn-primary ms-auto"
                                                                hx-post="/user/edit/theme_mode" hx-swap="none"
                                                                hx-vals='js:{ theme_mode: document.getElementById("theme-mode").value }'>
                                                            修改
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {if $config['enable_kill']}
                                        <div class="col-sm-12 col-md-6">
                                            <div class="card">
                                                <div class="card-stamp">
                                                    <div class="card-stamp-icon bg-red">
                                                        <i class="ti ti-circle-x"></i>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <h3 class="card-title">删除账户数据</h3>
                                                </div>
                                                <div class="card-footer">
                                                    <button class="btn btn-red" data-bs-toggle="modal"
                                                       data-bs-target="#destroy-account">
                                                        <i class="ti ti-trash icon"></i>
                                                        确认删除
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
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

    {if $config['enable_kill']}
    <div class="modal modal-blur fade" id="destroy-account" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-danger"></div>
                <div class="modal-body text-center py-4">
                    <i class="ti ti-alert-circle icon mb-2 text-danger icon-lg" style="font-size:3.5rem;"></i>
                    <h3>删除确认</h3>
                    <div class="text-secondary">
                        请确认是否真的要删除你的账户，此操作无法撤销，你的所有账户数据将会被从服务器上彻底删除
                    </div>
                    <div class="py-3">
                        <form>
                            <input id="confirm_kill_password" type="password" class="form-control"
                                   placeholder="输入登录密码" autocomplete="off">
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button class="btn w-100" data-bs-dismiss="modal">
                                    取消
                                </button>
                            </div>
                            <div class="col">
                                <button href="#" class="btn btn-danger w-100" data-bs-dismiss="modal"
                                        hx-post="/user/edit/kill" hx-swap="none"
                                        hx-vals='js:{ password: document.getElementById("confirm_kill_password").value }'>
                                    确认
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {/if}

    <script>
        let qrcode = new QRCode('qrcode', {
            text: "{$ga_url}",
            width: 128,
            height: 128,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });

        {if $user->im_type === 0 && $user->im_value === ''}
        let oauthProvider = $('#oauth-provider');

        $("#imtype").on('change', function () {
            if ($(this).val() === '0') {
                oauthProvider.empty();
            } else if ($(this).val() === '1') {
                oauthProvider.empty();
                oauthProvider.append(
                    "<a id='bind-slack' class='btn btn-azure ms-auto'>绑定 Slack</a>"
                );
            } else if ($(this).val() === '2') {
                oauthProvider.empty();
                oauthProvider.append(
                    "<a id='bind-discord' class='btn btn-indigo ms-auto'>绑定 Discord</a>"
                );
            } else if ($(this).val() === '4') {
                oauthProvider.empty();
                oauthProvider.append(
                    '<script async src=\"https://telegram.org/js/telegram-widget.js?22\"' +
                    ' data-telegram-login=\"' + "{$public_setting['telegram_bot']}" +
                    '\" data-size=\"large" data-onauth=\"onTelegramAuth(user)\"' +
                    ' data-request-access=\"write\"><\/script>'
                );
            }
        });

        oauthProvider.on('click', '#bind-slack', function () {
            $.ajax({
                type: "POST",
                url: "/oauth/slack",
                dataType: "json",
                success: function (data) {
                    handleOauthResult(data, 'slack')
                }
            })
        });

        oauthProvider.on('click', '#bind-discord', function () {
            $.ajax({
                type: "POST",
                url: "/oauth/discord",
                dataType: "json",
                success: function (data) {
                    handleOauthResult(data, 'discord')
                }
            })
        });

        function onTelegramAuth(user) {
            $.ajax({
                type: "POST",
                url: "/oauth/telegram",
                dataType: "json",
                data: {
                    user: JSON.stringify(user),
                },
                success: function (data) {
                    handleOauthResult(data, 'telegram')
                }
            })
        }

        function handleOauthResult(data, type = 'telegram') {
            if (data.ret === 1) {
                if (type === 'telegram') {
                    $('#success-message').text(data.msg);
                    $('#success-dialog').modal('show');
                } else {
                    window.location.replace(data.redir);
                }
            } else {
                $('#error-message').text(data.msg);
                $('#fail-dialog').modal('show');
            }
        }
        {/if}
    </script>

    {include file='user/footer.tpl'}
