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
            {if $public_setting['reg_mode'] !== 'close'}
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">注册账户</h2>
                    <div class="mb-3">
                        <input id="name" type="text" class="form-control" placeholder="昵称">
                    </div>
                    <div class="mb-3">
                        <input id="email" type="email" class="form-control" placeholder="电子邮箱">
                    </div>
                    <div class="mb-3">
                        <div class="input-group input-group-flat">
                            <input id="passwd" type="password" class="form-control" placeholder="登录密码">
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="input-group input-group-flat">
                            <input id="repasswd" type="password" class="form-control" placeholder="重复登录密码">
                        </div>
                    </div>
                    {if $public_setting['reg_mode'] !== 'close' }
                        <div class="mb-3">
                            <div class="input-group input-group-flat">
                                <input id="invite_code" type="text" class="form-control"
                                       placeholder="注册邀请码{if $public_setting['reg_mode'] === 'open'}（可选）{else}（必填）{/if}"
                                       value="{$invite_code}">
                            </div>
                        </div>
                    {/if}
                    {if $public_setting['reg_email_verify']}
                        <div class="mb-3">
                            <div class="input-group mb-2">
                                <input id="emailcode" type="text" class="form-control" placeholder="邮箱验证码">
                                <button id="send-verify-email" class="btn text-blue" type="button"
                                        hx-post="/auth/send" hx-swap="none"
                                        hx-vals='js:{ email: document.getElementById("email").value }'>
                                    获取
                                </button>
                            </div>
                        </div>
                    {/if}
                    <div class="mb-3">
                        <label class="form-check">
                            <input id="tos" type="checkbox" class="form-check-input"/>
                            <span class="form-check-label">
                                    我已阅读并同意 <a href="/tos" tabindex="-1"> 服务条款与隐私政策 </a>
                                </span>
                        </label>
                    </div>
                    <div class="mb-3">
                        <div class="input-group mb-3">
                        {if $public_setting['enable_reg_captcha']}
                            {include file='captcha_div.tpl'}
                        {/if}
                        </div>
                    </div>
                    <div class="form-footer">
                        <button id="register" class="btn btn-primary w-100"
                                hx-post="/auth/register" hx-swap="none" hx-vals='js:{
                                    {if $public_setting['reg_email_verify']}
                                        emailcode: document.getElementById("emailcode").value,
                                    {/if}
                                    {if $public_setting['enable_reg_captcha']}
                                        {if $public_setting['captcha_provider'] === 'turnstile'}
                                            turnstile: document.querySelector("[name=cf-turnstile-response]").value,
                                        {/if}
                                        {if $public_setting['captcha_provider'] === 'geetest'}
                                            geetest: geetest_result,
                                        {/if}
                                    {/if}
                                    name: document.getElementById("name").value,
                                    email: document.getElementById("email").value,
                                    passwd: document.getElementById("passwd").value,
                                    repasswd: document.getElementById("repasswd").value,
                                    invite_code: document.getElementById("invite_code").value,
                                    tos: document.getElementById("tos").checked,
                                 }'>
                            注册新账户
                        </button>
                    </div>
                </div>
            {else}
                <div class="card-body">
                    <p>还没有开放注册，过两天再来看看吧</p>
                </div>
            {/if}
        </div>
        <div class="text-center text-secondary mt-3">
            已有账户？ <a href="/auth/login" tabindex="-1">点击登录</a>
        </div>
    </div>
</div>

{if $public_setting['enable_reg_captcha']}
    {include file='captcha_js.tpl'}
{/if}

{include file='footer.tpl'}
