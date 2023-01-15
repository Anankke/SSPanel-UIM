{include file='user/tabler_header.tpl'}

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
            <div class="row row-cards">
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
                                    <div class="row row-cards">
                                        <div class="col-sm-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">登录邮箱</h3>
                                                    <p>当前邮箱：<code>{$user->email}</code></p>
                                                    <div class="mb-3">
                                                        <input id="new-email" type="email" class="form-control"
                                                            placeholder="新邮箱" {if $config['enable_change_email'] == false}disabled=""{/if}>
                                                    </div>
                                                    {if $config['enable_email_verify'] == true && $config['enable_change_email'] == true}
                                                    <div class="mb-3">
                                                        <input id="email-code" type="text" class="form-control"
                                                            placeholder="验证码">
                                                    </div>
                                                    {/if}
                                                </div>
                                                <div class="card-footer">
                                                    <div class="d-flex">
                                                        {if $config['enable_email_verify'] == true && $config['enable_change_email'] == true}
                                                        <a id="email-verify" class="btn btn-link">获取验证码</a>
                                                        <button id="modify-email"
                                                            class="btn btn-primary ms-auto">修改</button>
                                                        {elseif $config['enable_change_email'] == true}
                                                        <button id="modify-email"
                                                            class="btn btn-primary ms-auto">修改</button>
                                                        {else}
                                                        <button id="modify-email" class="btn btn-primary ms-auto"
                                                            disabled>暂不允许修改</button>
                                                        {/if}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">联系方式</h3>
                                                    <div class="mb-3">
                                                        <select id="imtype" class="form-select">
                                                            <option value="1" {if $user->im_type == '1'}selected{/if}>
                                                                WeChat</option>
                                                            <option value="2" {if $user->im_type == '2'}selected{/if}>
                                                                QQ</option>
                                                            <option value="3" {if $user->im_type == '3'}selected{/if}>
                                                                Facebook</option>
                                                            <option value="4" {if $user->im_type == '4'}selected{/if}>
                                                                Telegram</option>
                                                            <option value="5" {if $user->im_type == '5'}selected{/if}>
                                                                Discord</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <input id="imvalue" type="text" class="form-control" 
                                                            {if $user->im_type == '4'} disabled="" {/if}
                                                            value="{$user->im_value}" placeholder="社交账户">
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
                                        {if $config['enable_telegram'] == true}
                                        <div class="col-sm-12 col-md-6">
                                            {if $user->telegram_id != 0}
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">解绑 Telegram</h3>
                                                    <p>当前绑定的 Telegram 账户：
                                                        {if $user->im_value === "用戶名未设置"}
                                                        <code>{$user->telegram_id}</code>
                                                        {else}
                                                        <a href="https://t.me/{$user->im_value}">@{$user->im_value}</a>
                                                        {/if}
                                                    </p>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="d-flex">
                                                        <a href="/user/telegram_reset"
                                                            class="btn btn-red ms-auto">解绑</a>
                                                    </div>
                                                </div>
                                            </div>
                                            {else}
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">绑定 Telegram</h3>
                                                    <div class="row">
                                                        <div class="col-6 col-sm-2 col-md-2 col-xl mb-3">
                                                            手机电脑平板等如已安装 Telegram 可点击
                                                        </div>
                                                        <div class="col-6 col-sm-2 col-md-2 col-sm mb-3">
                                                            <a href="https://t.me/{$telegram_bot}?start={$bind_token}"
                                                                class="btn btn-primary w-100">
                                                                一键绑定
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-6 col-sm-2 col-md-2 col-xl mb-3">
                                                            向机器人 <a
                                                                href="https://t.me/{$telegram_bot}">@{$telegram_bot}</a>
                                                            发送验证码绑定
                                                        </div>
                                                        <div class="col-6 col-sm-2 col-md-2 col-sm mb-3">
                                                            <button data-clipboard-text="{$bind_token}"
                                                                class="copy btn btn-primary w-100">
                                                                复制验证码
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {/if}
                                        </div>
                                        {/if}
                                    </div>
                                </div>
                                <div class="tab-pane" id="login_security" role="tabpanel">
                                    <div class="row row-cards">
                                        <div class="col-sm-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">两步认证</h3>
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
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <p id="qrcode"></p>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="mb-3">
                                                                <select id="ga-enable" class="form-select">
                                                                    <option value="0">不使用</option>
                                                                    <option value="1"
                                                                        {if $user->ga_enable == '1'}selected{/if}>
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
                                    <div class="row row-cards">
                                        <div class="col-sm-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">更换加密方式</h3>
                                                    <p>不同的客户端支持的加密方式可能会有所不同，请参考客户端支持列表进行设置</p>
                                                    <div class="mb-3">
                                                        <select id="user-method" class="form-select">
                                                            {foreach $methods as $method}
                                                            <option value="{$method}"
                                                                {if $user->method == $method}selected{/if}
                                                            >{$method}
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
                                                    <h3 class="card-title">更换订阅地址</h3>
                                                    <p>更换订阅地址后，旧的订阅地址将无法获取配置，但节点配置仍能使用。如果希望旧的节点配置不能使用，请配合修改连接密码操作</p>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="d-flex">
                                                        <a id="reset-sub-url"
                                                            class="btn btn-primary ms-auto bg-red">更换</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">更换连接端口</h3>
                                                    <p>随机分配一个连接端口，这将用于 Shadowsocks 客户端</p>
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
                                                    <h3 class="card-title">重置连接密码</h3>
                                                    <p>重置连接密码与UUID ，重置后需更新订阅，才能继续使用</p>
                                                    <p>当前连接密码：<code>{$user->passwd}</code></p>
                                                    <p>当前UUID：<code>{$user->uuid}</code></p>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="d-flex">
                                                        <a id="reset-passwd"
                                                            class="btn btn-primary ms-auto bg-red">重置</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="other_settings" role="tabpanel">
                                    <div class="row row-cards">
                                        <div class="col-sm-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">每日用量推送</h3>
                                                    <div class="mb-3">
                                                        <select id="daily-report" class="form-select">
                                                            <option value="0"
                                                                {if $user->sendDailyMail == '0'}selected{/if}>不发送
                                                            </option>
                                                            <option value="1"
                                                                {if $user->sendDailyMail == '1'}selected{/if}>邮件接收
                                                            </option>
                                                            <option value="2"
                                                                {if $user->sendDailyMail == '2'}selected{/if}>Telegram
                                                                Bot 接收
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
                                                    <h3 class="card-title">修改主题</h3>
                                                    <div class="mb-3">
                                                        <select id="user-theme" class="form-select">
                                                            {foreach $themes as $theme}
                                                                <option value="{$theme}"
                                                                    {if $user->theme == $theme}selected{/if}>{$theme}
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
                                        {if $config['enable_kill'] == true}
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
                                                    <a href="#" class="btn btn-red d-none d-sm-inline-block" data-bs-toggle="modal"
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

    {if $config['enable_kill'] == true}
    <div class="modal modal-blur fade" id="destroy-account" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-danger"></div>
                <div class="modal-body text-center py-4">
                    <i class="ti ti-alert-circle icon mb-2 text-danger icon-lg" style="font-size:3.5rem;"></i>
                    <h3>删除确认</h3>
                    <div class="text-muted">请确认是否真的要删除你的账户，此操作无法撤销，你的所有账户数据将会被从服务器上彻底删除</div>
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
                    <p id="success-message" class="text-muted">删除成功</p>
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
                    <p id="error-message" class="text-muted">删除失败</p>
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
            text: "{$user->getGAurl()}",
            width: 128,
            height: 128,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });

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
                    newusername: $('#new-nickname').val()
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

        $("#modify-user-method").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/method",
                dataType: "json",
                data: {
                    method: $('#user-method').val()
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
                        window.setTimeout("location.reload()", {$config['jump_delay']});
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
                url: "/user/contact_update",
                dataType: "json",
                data: {
                    imtype: $('#imtype').val(),
                    imvalue: $('#imvalue').val()
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
        
        {if $config['enable_kill'] == true}
        $("#confirm-destroy").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/kill",
                dataType: "json",
                data: {
                    passwd: $('#confirm-passwd').val(),
                },
                success: function(data) {
                    if (data.ret == 1) {
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
    </script>
    
{include file='user/tabler_footer.tpl'}