{if $public_setting['captcha_provider'] === 'turnstile'}
    <div id="cf-turnstile" class="cf-turnstile" data-sitekey="{$captcha['turnstile_sitekey']}" data-theme="light"></div>
{/if}
{if $public_setting['captcha_provider'] === 'geetest'}
    <div id="geetest"></div>
{/if}
