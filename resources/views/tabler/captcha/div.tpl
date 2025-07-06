{if $public_setting['captcha_provider'] === 'turnstile'}
    <div id="cf-turnstile" class="cf-turnstile" data-sitekey="{$captcha['turnstile_sitekey']}"></div>
{/if}
{if $public_setting['captcha_provider'] === 'geetest'}
    <div id="geetest"></div>
{/if}
{if $public_setting['captcha_provider'] === 'hcaptcha'}
    <div class="h-captcha" data-sitekey="{$captcha['hcaptcha_sitekey']}"></div>
{/if}
{if $public_setting['captcha_provider'] === 'recaptcha_enterprise'}
    <div id="recaptcha"></div>
{/if}
