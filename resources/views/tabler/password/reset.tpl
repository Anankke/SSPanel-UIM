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
                <h2 class="card-title text-center mb-4">忘记密码</h2>
                <p class="text-secondary mb-4">
                    我们将向你的注册邮箱发送一封邮件，邮件内容中包含一个可以重设密码的链接
                </p>
                <div class="mb-3">
                    <label class="form-label">注册邮箱</label>
                    <input id="email" type="email" class="form-control">
                </div>
                <div class="mb-3">
                    <div class="input-group mb-3">
                    {if $public_setting['enable_reset_password_captcha']}
                        {include file='captcha_div.tpl'}
                    {/if}
                    </div>
                </div>
                <div class="form-footer">
                    <button id="send" class="btn btn-primary w-100"
                        hx-post="/password/reset" hx-swap="none" hx-vals='js:{
                            {if $public_setting['enable_reset_password_captcha']}
                                {if $public_setting['captcha_provider'] === 'turnstile'}
                                    turnstile: document.querySelector("[name=cf-turnstile-response]").value,
                                {/if}
                                {if $public_setting['captcha_provider'] === 'geetest'}
                                    geetest: geetest_result,
                                {/if}
                            {/if}
                            email: document.getElementById("email").value,
                         }'>
                        <i class="ti ti-brand-telegram icon"></i>
                        发送邮件
                    </button>
                </div>
            </div>
        </div>
        <div class="text-center text-secondary mt-3">
            已有账户？ <a href="/auth/login" tabindex="-1">点击登录</a>
        </div>
    </div>
</div>

{if $public_setting['enable_reset_password_captcha']}
    {include file='captcha_js.tpl'}
{/if}
{include file='footer.tpl'}
