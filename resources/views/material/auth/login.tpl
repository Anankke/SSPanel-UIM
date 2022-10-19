{include file='header.tpl'}

<div class="authpage">
    <div class="container">
        <form action="javascript:void(0);" method="POST">
            <div class="auth-main auth-row auth-col-one">
                <div class="auth-top auth-row">
                    <a class="boardtop-left" href="/">
                        <div>首 页</div>
                    </a>
                    <div class="auth-logo">
                        <img src="/images/uim-logo-round.png">
                    </div>
                    <a href="/auth/register" class="boardtop-right">
                        <div>注 册</div>
                    </a>
                </div>
                <div class="auth-row">
                    <div class="form-group-label auth-row row-login">
                        <label class="floating-label" for="email">邮箱</label>
                        <input class="form-control maxwidth-auth" id="email" type="email" name="Email" inputmode="email" autocomplete="username">
                    </div>
                </div>
                <div class="auth-row">
                    <div class="form-group-label auth-row row-login">
                        <label class="floating-label" for="passwd">密码</label>
                        <input class="form-control maxwidth-auth" id="passwd" type="password" name="Password" autocomplete="current-password">
                    </div>
                </div>
                <div class="auth-row">
                    <div class="form-group-label auth-row row-login">
                        <label class="floating-label" for="code">两步验证码（未设置请忽略）</label>
                        <input class="form-control maxwidth-auth" id="code" type="number" name="Code" inputmode="numeric" autocomplete="one-time-code">
                    </div>
                </div>

                {if $config['enable_login_captcha'] == true && $config['captcha_provider'] == 'turnstile'}
                    <div class="form-group-label auth-row">
                        <div class="row">
                            <div align="center" class="cf-turnstile" data-sitekey="{$captcha['turnstile_sitekey']}" data-theme="light"></div>
                        </div>
                    </div>
                {/if}

                <div class="btn-auth auth-row">
                    <button id="login" type="submit" class="btn btn-block btn-brand waves-attach waves-light">
                        确认登录
                    </button>
                </div>
                <div class="auth-help auth-row">
                    <div class="auth-help-table auth-row">
                        <div class="checkbox checkbox-adv">
                            <label for="remember_me">
                                <input class="access-hide" value="week" id="remember_me" name="remember_me"
                                       type="checkbox">记住我</input>
                                <span class="checkbox-circle"></span>
                                <span class="checkbox-circle-check"></span>
                                <span class="checkbox-circle-icon mdi mdi-check"></span>
                            </label>
                        </div>
                        <a href="/password/reset">忘记密码？</a>
                    </div>
                </div>
                {if $config['enable_telegram_login'] === true}
                    <div class="auth-bottom auth-row">
                        <div class="tgauth">
                            <span>Telegram</span>
                            <button class="btn" id="calltgauth"><i class="mdi mdi-send-circle icon-lg"></i></button>
                            <span>快捷登录</span>
                        </div>
                    </div>
                {/if}
            </div>
        </form>
        {if $config['enable_telegram_login'] === true}
            {include file='./telegram_modal.tpl'}
        {/if}
    </div>
</div>

{include file='dialog.tpl'}

{include file='footer.tpl'}

{if $config['enable_telegram_login'] === true}
    {include file='./telegram.tpl'}
{/if}

{literal}
    <script>
        let calltgbtn = document.querySelector('#calltgauth');
        let tgboard = document.querySelector('.card.auth-tg.cust-model');
        if (calltgbtn && tgboard) {
            custModal(calltgbtn, tgboard);
        }
    </script>
{/literal}

<script>
    $(document).ready(function () {
        function login() {
            document.getElementById("login").disabled = true;

            $.ajax({
                type: "POST",
                url: location.pathname,
                dataType: "json",
                data: {
                    {if $config['enable_login_captcha'] == true && $config['captcha_provider'] == 'turnstile'}
                    turnstile: turnstile.getResponse(),
                    {/if}
                    code: $$getValue('code'),
                    email: $$getValue('email'),
                    passwd: $$getValue('passwd'),
                    remember_me: $("#remember_me:checked").val()
                },
                success: (data) => {
                    if (data.ret == 1) {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                        window.setTimeout("location.href='/user'", {$config['jump_delay']});
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                        document.getElementById("login").disabled = false;
                    }
                },
                error: (jqXHR) => {
                    $("#msg-error").hide(10);
                    $("#msg-error").show(100);
                    $$.getElementById('msg').innerHTML = `发生错误：${
                        jqXHR.status
                    }`;
                    document.getElementById("login").disabled = false;
                }
            });
        }

        $("html").keydown(function (event) {
            if (event.keyCode == 13) {
                login();
            }
        });
        $("#login").click(function () {
            login();
        });

        $('div.modal').on('shown.bs.modal', function () {
            $("div.gt_slider_knob").hide();
        });

        $('div.modal').on('hidden.bs.modal', function () {
            $("div.gt_slider_knob").show();
        });
    })
</script>

{if $config['enable_login_captcha'] == true && $config['captcha_provider'] == 'turnstile'}
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js?compat=recaptcha" async defer></script>
{/if}
