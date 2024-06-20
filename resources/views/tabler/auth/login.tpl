{include file='header.tpl'}

<body class="border-top-wide border-primary d-flex flex-column">
<div class="page page-center">
    <div class="container-tight my-auto">
        <div class="text-center mb-4">
            <a href="#" class="navbar-brand navbar-brand-autodark">
                <img src="/images/uim-logo-round_96x96.png" height="64" alt="SSPanel-UIM Logo">
            </a>
        </div>
        <div class="card card-md">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">登录到用户中心</h2>
                <div class="mb-3">
                    <label class="form-label">邮箱</label>
                    <input id="email" type="email" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label">
                        登录密码
                        <span class="form-label-description">
                                <a href="/password/reset">忘记密码</a>
                            </span>
                    </label>
                    <div class="input-group input-group-flat">
                        <input id="password" type="password" class="form-control" autocomplete="off">
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label">两步认证</label>
                    <input id="mfa_code" type="email" class="form-control" placeholder="如果没有设置两步认证可留空">
                </div>
                <div class="mb-2">
                    <label class="form-check">
                        <input id="remember_me" type="checkbox" class="form-check-input"/>
                        <span class="form-check-label">记住此设备</span>
                    </label>
                </div>
                <div class="mb-3">
                    <div class="input-group mb-3">
                    {if $public_setting['enable_login_captcha']}
                        {include file='captcha/div.tpl'}
                    {/if}
                    </div>
                </div>
                <div class="form-footer">
                    <button class="btn btn-primary w-100"
                            hx-post="/auth/login" hx-swap="none" hx-vals='js:{
                                {if $public_setting['enable_login_captcha']}
                                    {include file='captcha/ajax.tpl'}
                                {/if}
                                email: document.getElementById("email").value,
                                password: document.getElementById("password").value,
                                mfa_code: document.getElementById("mfa_code").value,
                                remember_me: document.getElementById("remember_me").checked,
                             }'>
                        登录
                    </button>
                </div>
            </div>
        </div>
        <div class="text-center text-secondary mt-3">
            还没有账户？ <a href="/auth/register" tabindex="-1">点击注册</a>
        </div>
    </div>
</div>

{if $public_setting['enable_login_captcha']}
    {include file='captcha/js.tpl'}
{/if}

{include file='footer.tpl'}
