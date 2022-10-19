{include file='header.tpl'}

<!-- 新的 -->
<div class="authpage">
    <div class="container">
        <div class="auth-main auth-row auth-col-one">
            <div class="auth-top auth-row">
                <a class="boardtop-left" href="/">
                    <div>首 页</div>
                </a>
                <div class="auth-logo">
                    <img src="/images/uim-logo-round.png">
                </div>
                <a href="/auth/login" class="boardtop-right">
                    <div>登 录</div>
                </a>
            </div>
            <div class="auth-row">
                <div class="form-group-label auth-row row-login">
                    <label class="floating-label" for="email">邮箱</label>
                    <input class="form-control maxwidth-auth" id="email" type="email" inputmode="email" autocomplete="username">
                </div>
            </div>
            {if $config['enable_reset_password_captcha'] == true && $config['captcha_provider'] == 'turnstile'}
            <div class="form-group-label auth-row">
                <div class="row">
                    <div align="center" class="cf-turnstile" data-sitekey="{$captcha['turnstile_sitekey']}" data-theme="light"></div>
                </div>
            </div>
            {/if}
            <div class="btn-auth auth-row">
                <button id="reset" type="submit" class="btn btn-block btn-brand waves-attach waves-light">重置密码</button>
            </div>
        </div>
        <div class="card auth-tg">
            <div class="card-main"></div>
        </div>
    </div>
</div>

{include file='dialog.tpl'}

{include file='footer.tpl'}

<script>
    $(document).ready(function () {
        function reset() {
            $("#result").modal();
            $$.getElementById('msg').innerHTML = '发送中，请等待'
            $.ajax({
                type: "POST",
                url: location.pathname,
                dataType: "json",
                data: {
                    email: $$getValue('email'),
                    {if $config['enable_reset_password_captcha'] == true && $config['captcha_provider'] == 'turnstile'}
                    turnstile: turnstile.getResponse(),
                    {/if}
                },
                success: (data) => {
                    if (data.ret == 1) {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                        window.setTimeout("location.href='/auth/login'", 2000);
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                }
            });
        }

        $("html").keydown(function (event) {
            if (event.keyCode === 13) {
                reset();
            }
        });
        $("#reset").click(function () {
            reset();
        });
    })
</script>

{if $config['enable_reset_password_captcha'] == true && $config['captcha_provider'] == 'turnstile'}
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js?compat=recaptcha" async defer></script>
{/if}
