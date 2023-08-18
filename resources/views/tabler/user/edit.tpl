{include file='user/header.tpl'}

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
                                                    <p>当前邮箱：<code>{$user->email}</code></p>
                                                    <div class="mb-3">
                                                        <input id="new-email" type="email" class="form-control"
                                                            placeholder="新邮箱" {if ! $config['enable_change_email']}disabled=""{/if}>
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
                                                        <a id="email-verify" class="btn btn-link">获取验证码</a>
                                                        <button id="modify-email"
                                                            class="btn btn-primary ms-auto">修改</button>
                                                        {elseif $config['enable_change_email']}
                                                        <button id="modify-email"
                                                            class="btn btn-primary ms-auto">修改</button>
                                                        {else}
                                                        <button id="modify-email" class="btn btn-primary ms-auto"
                                                            disabled>不允许修改</button>
                                                        {/if}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">用戶名</h3>
                                                    <p>当前用戶名：<code>{$user->user_name}</code></p>
                                                    <div class="mb-3">
                                                        <input id="new-nickname" type="text" class="form-control"
                                                               placeholder="新用戶名" autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="d-flex">
                                                        <a id="modify-username" class="btn btn-primary ms-auto">修改</a>
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
                                                                未绑定</option>
                                                            <option value="1" {if $user->im_type === 1}selected{/if}>
                                                                Slack</option>
                                                            <option value="2" {if $user->im_type === 2}selected{/if}>
                                                                Discord</option>
                                                            <option value="4" {if $user->im_type === 4}selected{/if}>
                                                                Telegram</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <input id="imvalue" type="text" class="form-control"
                                                            disabled="" value="{$user->im_value}">
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="d-flex btn-list justify-content-end" id="oauth-provider"></div>
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
                                                        <button id="unbind-im" class="btn btn-red ms-auto">
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
                                                                href="https://apps.apple.com/us/app/google-authenticator/id388497605">iOS 客户端
                                                            </a>
                                                            &nbsp;&nbsp;&nbsp;
                                                            <i class="ti ti-brand-android"></i>
                                                            <a target="view_window"
                                                                href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2">Android 客户端
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
                                                                <input id="2fa-test-code" type="text"
                                                                    class="form-control" placeholder="测试两步认证验证码">
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
                                                    <h3 class="card-title">修改登录密码</h3>
                                                    <div class="mb-3">
                                                        <form>
                                                            <input id="password" type="password" class="form-control"
                                                                placeholder="当前登录密码" autocomplete="off">
                                                        </form>
                                                    </div>
                                                    <div class="mb-3">
                                                        <form>
                                                            <input id="new-password" type="password"
                                                                class="form-control" placeholder="输入新密码"
                                                                autocomplete="off">
                                                        </form>
                                                    </div>
                                                    <div class="mb-3">
                                                        <form>
                                                            <input id="again-new-password" type="password"
                                                                class="form-control" placeholder="再次输入新密码"
                                                                autocomplete="off">
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="d-flex">
                                                        <a id="modify-login-passwd"
                                                            class="btn btn-primary ms-auto">修改</a>
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
                                                    <p>不同的客户端支持的加密方式可能会有所不同，请参考客户端支持列表进行设置</p>
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
                                                        <a id="modify-user-method" class="btn btn-primary ms-auto">修改</a>
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
                                                        <a id="reset-sub-url"
                                                            class="btn btn-primary ms-auto bg-red">重置</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">重置连接密码</h3>
                                                    <p>重置连接密码与UUID ，重置后需更新订阅，才能继续使用</p>
                                                    <p>当前连接密码：<code>{$user->passwd}</code></p>
                                                    <p>当前UUID：<code>{$user->uuid}</code></p>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="d-flex">
                                                        <a id="reset-passwd" class="btn btn-primary ms-auto bg-red">重置</a>
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
                                                        <select id="daily-report" class="form-select">
                                                            <option value="0" {if $user->daily_mail_enable === 0}selected{/if}>
                                                                不接收
                                                            </option>
                                                            <option value="1" {if $user->daily_mail_enable === 1}selected{/if}>
                                                                邮件接收
                                                            </option>
                                                            <option value="2" {if $user->daily_mail_enable === 2}selected{/if}>
                                                                IM 接收
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="d-flex">
                                                        <a id="modify-daily-report"
                                                            class="btn btn-primary ms-auto">修改</a>
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
                                                            <option value="1" {if $user->contact_method === 1}selected{/if}>
                                                                邮件
                                                            </option>
                                                            <option value="2" {if $user->contact_method === 2}selected{/if}>
                                                                IM
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="d-flex">
                                                        <a id="modify-contact-method"
                                                           class="btn btn-primary ms-auto">修改</a>
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
                                                        <a id="modify-user-theme" class="btn btn-primary ms-auto">修改</a>
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
                                                    <a href="#" class="btn btn-red" data-bs-toggle="modal"
                                                        data-bs-target="#destroy-account">
                                                        <i class="ti ti-trash icon"></i>
                                                        确认删除
                                                    </a>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-danger"></div>
                <div class="modal-body text-center py-4">
                    <i class="ti ti-alert-circle icon mb-2 text-danger icon-lg" style="font-size:3.5rem;"></i>
                    <h3>删除确认</h3>
                    <div class="text-secondary">请确认是否真的要删除你的账户，此操作无法撤销，你的所有账户数据将会被从服务器上彻底删除</div>
                    <div class="py-3">
                        <form>
                            <input id="confirm-passwd" type="password" class="form-control" placeholder="输入登录密码"
                                autocomplete="off">
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <a href="#" class="btn w-100" data-bs-dismiss="modal">
                                    取消
                                </a>
                            </div>
                            <div class="col">
                                <a href="#" id="confirm-destroy" class="btn btn-danger w-100" data-bs-dismiss="modal">
                                    确认
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="destroy-account-success" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-success"></div>
                <div class="modal-body text-center py-4">
                    <i class="ti ti-circle-check icon mb-2 text-green icon-lg" style="font-size:3.5rem;"></i>
                    <h3>删除成功</h3>
                    <p id="success-message" class="text-secondary">删除成功</p>
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

    <div class="modal modal-blur fade" id="destroy-account-fail" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-danger"></div>
                <div class="modal-body text-center py-4">
                    <i class="ti ti-circle-x icon mb-2 text-danger icon-lg" style="font-size:3.5rem;"></i>
                    <h3>删除失败</h3>
                    <p id="error-message" class="text-secondary">删除失败</p>
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
    {/if}

    <script>
        var qrcode = new QRCode('qrcode', {
            text: "{$gaurl}",
            width: 128,
            height: 128,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });

        var clipboard = new ClipboardJS('.copy');
        clipboard.on('success', function(e) {
            $('#success-noreload-message').text('已复制到剪切板');
            $('#success-noreload-dialog').modal('show');
        });

        $("#modify-email").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/email",
                dataType: "json",
                data: {
                    {if $public_setting['reg_email_verify']}
                        emailcode: $('#email-code').val(),
                    {/if}
                    newemail: $('#new-email').val()
                },
                success: function(data) {
                    if (data.ret === 1) {
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
                    if (data.ret === 1) {
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
                    newusername: $('#new-nickname').val()
                },
                success: function(data) {
                    if (data.ret === 1) {
                        $('#success-message').text(data.msg);
                        $('#success-dialog').modal('show');
                    } else {
                        $('#fail-message').text(data.msg);
                        $('#fail-dialog').modal('show');
                    }
                }
            })
        });

        $("#modify-user-method").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/method",
                dataType: "json",
                data: {
                    method: $('#user-method').val()
                },
                success: function(data) {
                    if (data.ret === 1) {
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
                    if (data.ret === 1) {
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
                url: "/user/passwd_reset",
                dataType: "json",
                success: function(data) {
                    if (data.ret === 1) {
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
                    if (data.ret === 1) {
                        $('#success-message').text(data.msg);
                        $('#success-dialog').modal('show');
                    } else {
                        $('#fail-message').text(data.msg);
                        $('#fail-dialog').modal('show');
                    }
                }
            })
        });

        $("#unbind-im").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/unbind_im",
                dataType: "json",
                success: function(data) {
                    if (data.ret === 1) {
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
                url: "/user/ga_reset",
                dataType: "json",
                success: function(data) {
                    if (data.ret === 1) {
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
                url: "/user/ga_check",
                dataType: "json",
                data: {
                    code: $('#2fa-test-code').val()
                },
                success: function(data) {
                    if (data.ret === 1) {
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
                url: "/user/ga_set",
                dataType: "json",
                data: {
                    enable: $('#ga-enable').val()
                },
                success: function(data) {
                    if (data.ret === 1) {
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
                url: "/user/daily_mail",
                dataType: "json",
                data: {
                    mail: $('#daily-report').val()
                },
                success: function(data) {
                    if (data.ret === 1) {
                        $('#success-message').text(data.msg);
                        $('#success-dialog').modal('show');
                    } else {
                        $('#fail-message').text(data.msg);
                        $('#fail-dialog').modal('show');
                    }
                }
            })
        });

        $("#modify-contact-method").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/contact_method",
                dataType: "json",
                data: {
                    contact: $('#contact-method').val()
                },
                success: function(data) {
                    if (data.ret === 1) {
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
                    if (data.ret === 1) {
                        $('#success-message').text(data.msg);
                        $('#success-dialog').modal('show');
                        window.setTimeout("location.reload()", {$config['jump_delay']});
                    } else {
                        $('#fail-message').text(data.msg);
                        $('#fail-dialog').modal('show');
                    }
                }
            })
        });

        {if $config['enable_kill']}
        $("#confirm-destroy").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/kill",
                dataType: "json",
                data: {
                    passwd: $('#confirm-passwd').val(),
                },
                success: function(data) {
                    if (data.ret === 1) {
                        $('#success-message').text(data.msg);
                        $('#destroy-account-success').modal('show');
                    } else {
                        $('#error-message').text(data.msg);
                        $('#destroy-account-fail').modal('show');
                    }
                }
            })
        });
        {/if}

        {if $user->im_type === 0 && $user->im_value === ''}
        $("#imtype").on('change', function() {
            if ($(this).val() === '0') {
                $('#oauth-provider').empty();
            } else if ($(this).val() === '1') {
                $('#oauth-provider').empty();
                $('#oauth-provider').append(
                    "<a id='bind-slack' class='btn btn-azure ms-auto'>绑定 Slack</a>"
                );
            } else if ($(this).val() === '2') {
                $('#oauth-provider').empty();
                $('#oauth-provider').append(
                    "<a id='bind-discord' class='btn btn-indigo ms-auto'>绑定 Discord</a>"
                );
            } else if ($(this).val() === '4'){
                $('#oauth-provider').empty();
                $('#oauth-provider').append(
                    '<script async src=\"https://telegram.org/js/telegram-widget.js?22\"' +
                    ' data-telegram-login=\"' + "{$public_setting['telegram_bot']}" +
                    '\" data-size=\"large" data-onauth=\"onTelegramAuth(user)\"' +
                    ' data-request-access=\"write\"><\/script>'
                );
            }
        });

        $('#oauth-provider').on('click', '#bind-slack', function() {
            $.ajax({
                type: "POST",
                url: "/oauth/slack",
                dataType: "json",
                success: function(data) {
                    if (data.ret === 1) {
                        window.location.replace(data.redir);
                    } else {
                        $('#fail-message').text(data.msg);
                        $('#fail-dialog').modal('show');
                    }
                }
            })
        });

        $('#oauth-provider').on('click', '#bind-discord', function() {
            $.ajax({
                type: "POST",
                url: "/oauth/discord",
                dataType: "json",
                success: function(data) {
                    if (data.ret === 1) {
                        window.location.replace(data.redir);
                    } else {
                        $('#fail-message').text(data.msg);
                        $('#fail-dialog').modal('show');
                    }
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
                success: function(data) {
                    if (data.ret === 1) {
                        $('#success-message').text(data.msg);
                        $('#success-dialog').modal('show');
                    } else {
                        $('#error-message').text(data.msg);
                        $('#fail-dialog').modal('show');
                    }
                }
            })
        }
        {/if}
    </script>

{include file='user/footer.tpl'}
