{if $public_setting['captcha_provider'] === 'turnstile'}
    turnstile: document.querySelector("[name=cf-turnstile-response]").value,
{/if}
{if $public_setting['captcha_provider'] === 'geetest'}
    geetest: geetest_result,
{/if}
{if $public_setting['captcha_provider'] === 'hcaptcha'}
    hcaptcha: hcaptcha.getResponse(),
{/if}
