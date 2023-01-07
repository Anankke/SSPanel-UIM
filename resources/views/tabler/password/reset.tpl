{include file='tabler_header.tpl'}

<body class="border-top-wide border-primary d-flex flex-column">
    <div class="page page-center">
        <div class="container-tight py-4">
            <div class="text-center mb-4">
                <a href="#" class="navbar-brand navbar-brand-autodark">
                    <img src="/images/uim-logo-round_96x96.png" height="64" alt="">
                </a>
            </div>
            <div class="card card-md">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">忘记密码</h2>
                    <p class="text-muted mb-4">
                        我们将向你的注册邮箱发送一封邮件，邮件内容中包含一个可以重设密码的链接
                    </p>
                    <div class="mb-3">
                        <label class="form-label">注册邮箱</label>
                        <input id="email" type="email" class="form-control">
                    </div>
                    {if $config['enable_reset_password_captcha'] === true && $config['captcha_provider'] === 'turnstile'}
                    <div class="mb-3">
                        <div class="input-group mb-3">
                            <div class="cf-turnstile" data-sitekey="{$captcha['turnstile_sitekey']}" data-theme="light"></div>
                        </div>
                    </div>
                    {/if}
                    {if $config['enable_reset_password_captcha'] === true && $config['captcha_provider'] === 'geetest'}
                    <div class="mb-3">
                        <div class="input-group mb-3">
                            <div id="geetest"></div>
                        </div>
                    </div>    
                    {/if}
                    <div class="form-footer">
                        <button id="send" class="btn btn-primary w-100">
                            <i class="ti ti-brand-telegram icon"></i>
                            发送邮件
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center text-muted mt-3">
            已有账户？ <a href="/auth/login" tabindex="-1">点击登录</a>
        </div>
    </div>

    <script>
        $("#send").click(function() {
            $.ajax({
                type: 'POST',
                url: '/password/reset',
                dataType: "json",
                data: {
                    email: $('#email').val(),
                    {if $config['enable_reset_password_captcha'] === true && $config['captcha_provider'] === 'turnstile'}
                    turnstile: turnstile.getResponse(),
                    {/if}
                    {if $config['enable_reset_password_captcha'] === true && $config['captcha_provider'] === 'geetest'}
                    geetest: geetest_result,
                    {/if}
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

        $("#success-confirm").click(function() {
            location.reload();
        });
    </script>

    {if $config['enable_reset_password_captcha'] === true && $config['captcha_provider'] === 'turnstile'}
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?compat=recaptcha" async defer></script>
    {/if}
    {if $config['enable_reset_password_captcha'] === true && $config['captcha_provider'] === 'geetest'}
    <script src="http://static.geetest.com/v4/gt4.js"></script>
    <script>
        var geetest_result = '';
        initGeetest4({
            captchaId: '{$captcha['geetest_id']}',
            product: 'float',
            language: "zho",
            riskType:'slide'
        }, function (geetest) {
            geetest.appendTo("#geetest");
            geetest.onSuccess(function() {
                geetest_result = geetest.getValidate();
            });
        });
    </script>    
    {/if}    
{include file='tabler_footer.tpl'}
