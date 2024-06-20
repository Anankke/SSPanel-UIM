{if $public_setting['captcha_provider'] === 'turnstile'}
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
{/if}
{if $public_setting['captcha_provider'] === 'geetest'}
    <script src="https://static.geetest.com/v4/gt4.js"></script>
    <script>
        let geetest_result = '';
        initGeetest4({
            captchaId: '{$captcha['geetest_id']}',
            product: 'float',
            language: "zho",
            riskType: 'slide'
        }, function (geetest) {
            geetest.appendTo("#geetest");
            geetest.onSuccess(function () {
                geetest_result = geetest.getValidate();
            });
        });
    </script>
{/if}
{if $public_setting['captcha_provider'] === 'hcaptcha'}
    <script src='https://www.hCaptcha.com/1/api.js' async defer></script>
{/if}
