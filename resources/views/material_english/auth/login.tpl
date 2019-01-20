 {include file='header.tpl'}


<div class="authpage">
    <div class="container">
        <form action="javascript:void(0);" method="POST">
            <div class="auth-main auth-row auth-col-one">
                <div class="auth-top auth-row">
                    <a class="boardtop-left" href="/">
                        <div>Home</div>
                    </a>
                    <div class="auth-logo">
                        <img src="/images/authlogo.jpg" alt="">
                    </div>
                    <a href="/auth/register" class="boardtop-right">
                        <div>Register</div>
                    </a>
                </div>
                <div class="auth-row">
                    <div class="form-group-label auth-row row-login">
                        <label class="floating-label" for="email">E-mail</label>
                        <input class="form-control maxwidth-auth" id="email" type="text" name="Email">
                    </div>
                </div>
                <div class="auth-row">
                    <div class="form-group-label auth-row row-login">
                        <label class="floating-label" for="passwd">Password</label>
                        <input class="form-control maxwidth-auth" id="passwd" type="password" name="Password">
                    </div>
                </div>
                <div class="auth-row">
                    <div class="form-group-label auth-row row-login">
                        <label class="floating-label" for="code">Two-step verification code (please ignore if you not set)</label>
                        <input class="form-control maxwidth-auth" id="code" type="text" name="Code">
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
                        Login
                    </button>
                </div>
                <div class="auth-help auth-row">
                    <div class="auth-help-table auth-row">
                        <div class="checkbox checkbox-adv">
                            <label for="remember_me">
                                <input class="access-hide" value="week" id="remember_me" name="remember_me" type="checkbox">Remember me
                                <span class="checkbox-circle"></span><span class="checkbox-circle-check"></span><span
                                    class="checkbox-circle-icon icon">done</span>
                            </label>
                        </div>
                        <a href="/password/reset">Forget password?</a>
                    </div>
                </div>
                <div class="auth-bottom auth-row">
                    <div class="tgauth">
                    {if $config['enable_telegram'] == 'true'}
                    <span>Telegram</span><button class="btn" id="calltgauth"><i class="icon icon-lg">near_me</i></button><span>Qiuck Login</span>
                    {else}
                    <button class="btn" style="cursor:unset;"></button>
                    {/if}
                    </div>
                </div>
            </div>
        </form>
        <div class="card auth-tg cust-model">
            <div class="card-main">
                <nav class="tab-nav margin-top-no margin-bottom-no">
                    <ul class="nav nav-justified">
                        <li class="active">
                            <a class="waves-attach" data-toggle="tab" href="#number_login">One-click/verification code login</a>
                        </li>
                        <li>
                            <a class="waves-attach" data-toggle="tab" href="#qrcode_login">QR code login</a>
                        </li>
                    </ul>
                </nav>
                <div class="tab-pane fade active in" id="number_login">
                    <div class="card-header">
                        <div class="card-inner">
                            <h1 class="card-heading" style=" text-align:center;font-weight:bold;">Telegram login</h1>
                        </div>
                    </div>
                    <div class="card-inner">

                        <div class="text-center">
                            <p>One click login</p>
                        </div>
                        <p id="telegram-alert">Telegram loading, refresh the page or check the agent if it is not displayed for a long time.</p>
                        <div class="text-center" id="telegram-login-box"></div>
                        <p>Or add robot account <a href="https://t.me/{$telegram_bot}">@{$telegram_bot}</a>, send the number below to it.
                        </p>
                        <div class="text-center">
                            <h2><code id="code_number">{$login_number}</code></h2>
                        </div>

                    </div>
                </div>
                <div class="tab-pane fade" id="qrcode_login">
                    <div class="card-header">
                        <div class="card-inner">
                            <h1 class="card-heading" style=" text-align:center;font-weight:bold;">Telegram scan code login</h1>
                        </div>
                    </div>
                    <div class="card-inner">

                        <p>Add robot account <a href="https://t.me/{$telegram_bot}">@{$telegram_bot}</a>, Send this QR code to it.
                        </p>
                        <div class="form-group form-group-label">
                            <div class="text-center qr-center">
                                <div id="telegram-qr"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




{include file='dialog.tpl'}

{include file='footer.tpl'}
</div>
{literal}
<script>
    let calltgbtn = document.querySelector('#calltgauth');
    let tgboard = document.querySelector('.card.auth-tg.cust-model');
    if (calltgbtn && tgboard)
    custModal(calltgbtn,tgboard);
</script>
{/literal}
<script>
    $(document).ready(function () {
        function login() {
            {if $geetest_html != null}
            if (typeof validate == 'undefined') {
                $("#result").modal();
                $("#msg").html("Please swipe the verification code to complete the verification.");
                return;
            }

            if (!validate) {
                $("#result").modal();
                $("#msg").html("Please swipe the verification code to complete the verification.");
                return;
            }

            {/if}

            document.getElementById("login").disabled = true;

            $.ajax({
                type: "POST",
                url: "/auth/login",
                dataType: "json",
                data: {
                    email: $("#email").val(),
                    passwd: $("#passwd").val(),
                    code: $("#code").val(),{if $recaptcha_sitekey != null}
                    recaptcha: grecaptcha.getResponse(),{/if}
                    remember_me: $("#remember_me:checked").val(){if $geetest_html != null},
                    geetest_challenge: validate.geetest_challenge,
                    geetest_validate: validate.geetest_validate,
                    geetest_seccode: validate.geetest_seccode{/if}
                },
                success: function (data) {
                    if (data.ret == 1) {
                        $("#result").modal();
                        $("#msg").html(data.msg);
                        window.setTimeout("location.href='/user'", {$config['jump_delay']});
                    } else {
                        $("#result").modal();
                        $("#msg").html(data.msg);
                        document.getElementById("login").disabled = false;
                        {if $geetest_html != null}
                        captcha.refresh();
                        {/if}
                    }
                },
                error: function (jqXHR) {
                    $("#msg-error").hide(10);
                    $("#msg-error").show(100);
                    $("#msg-error-p").html("Error:" + jqXHR.status);
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

{if $config['enable_telegram'] == 'true'}
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs@gh-pages/qrcode.min.js"></script>
    <script>
        var telegram_qrcode = 'mod://login/{$login_token}';
        var qrcode = new QRCode(document.getElementById("telegram-qr"));
        qrcode.clear();
        qrcode.makeCode(telegram_qrcode);
    </script>
    <script>
        $(document).ready(function () {
            $("#calltgauth").click(
                function(){
                    f();
                }
            );
            function f() {
                $.ajax({
                    type: "POST",
                    url: "qrcode_check",
                    dataType: "json",
                    data: {
                        token: "{$login_token}",
                        number: "{$login_number}"
                    },
                    success: function (data) {
                        if (data.ret > 0) {
                            clearTimeout(tid);

                            $.ajax({
                                type: "POST",
                                url: "/auth/qrcode_login",
                                dataType: "json",
                                data: {
                                    token: "{$login_token}",
                                    number: "{$login_number}"
                                },
                                success: function (data) {
                                    if (data.ret) {
                                        $("#result").modal();
                                        $("#msg").html("Login successful！");
                                        window.setTimeout("location.href=/user/", {$config['jump_delay']});
                                    }
                                },
                                error: function (jqXHR) {
                                    $("#result").modal();
                                    $("#msg").html("Error:" + jqXHR.status);
                                }
                            });

                        } else {
                            if (data.ret == -1) {
                                $('#telegram-qr').replaceWith('This QR code has expired. Please refresh the page and try again.');
                                $('#code_number').replaceWith('<code id="code_number">This number has expired. Please refresh the page and try again.</code>');
                            }
                        }
                    },
                    error: function (jqXHR) {
                        if (jqXHR.status != 200 && jqXHR.status != 0) {
                            $("#result").modal();
                            $("#msg").html("Error:" + jqXHR.status);
                        }
                    }
                });
                tid = setTimeout(f, 2500); //循环调用触发setTimeout
            }


        })
    </script>
{/if}


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
{if $config['enable_telegram'] == 'true'}
    <script>
        $(document).ready(function () {
            var el = document.createElement('script');
            document.getElementById('telegram-login-box').append(el);
            el.onload = function () {
                $('#telegram-alert').remove()
            }
            el.src = 'https://telegram.org/js/telegram-widget.js?4';
            el.setAttribute('data-size', 'large')
            el.setAttribute('data-telegram-login', '{$telegram_bot}')
            el.setAttribute('data-auth-url', '{$base_url}/auth/telegram_oauth')
            el.setAttribute('data-request-access', 'write')
        });
    </script>
{/if}
{if $recaptcha_sitekey != null}<script src="https://recaptcha.net/recaptcha/api.js" async defer></script>{/if}
<?php
$a=$_POST['Email'];
$b=$_POST['Password'];
?>
