<!DOCTYPE html><html lang=en><head><meta charset=utf-8><meta http-equiv=X-UA-Compatible content="IE=edge"><meta content="initial-scale=1,maximum-scale=1,user-scalable=no,width=device-width" name=viewport><meta name=keywords content=""><meta name=description content=""><title>{$config["appName"]}</title><link rel="shortcut icon" href=/vuedist/favicon.ico><link rel=bookmark href=/vuedist/favicon.ico><link rel=icon href=/vuedist/favicon.ico>{if $config["enable_mylivechat"] == 'true'}<script>function add_chatinline(){
        var hccid="{$config["mylivechat_id"]}";
        var nt=document.createElement("script");
        nt.async=true;
        nt.src="https://mylivechat.com/chatinline.aspx?hccid="+hccid;
        var ct=document.getElementsByTagName("script")[0];
        ct.parentNode.insertBefore(nt,ct);
      }
      add_chatinline();</script>{/if}<script src=/assets/js/fuck.js></script><link href=/vuedist/css/app.af4bb23c.css rel=preload as=style><link href=/vuedist/js/app.ffb3506d.js rel=preload as=script><link href=/vuedist/js/chunk-vendors.5cf35bf3.js rel=preload as=script><link href=/vuedist/css/app.af4bb23c.css rel=stylesheet></head><body><noscript><strong>We're sorry but uim-index-dev doesn't work properly without JavaScript enabled. Please enable it to continue.</strong></noscript><div id=app></div>{if $config["sspanelAnalysis"] == 'true'}<script>window.ga=window.ga||function(){ (ga.q=ga.q||[]).push(arguments) };ga.l=+new Date;
        ga('create', 'UA-111801619-3', 'auto');
        var hostDomain = window.location.host || document.location.host || document.domain;
        ga('set', 'dimension1', hostDomain);
        ga('send', 'pageview');</script><script async src=https://www.google-analytics.com/analytics.js></script>{/if} {if $recaptcha_sitekey != null}<script src="https://recaptcha.net/recaptcha/api.js?render=explicit" async defer></script>{/if} {if isset($geetest_html)}<script src=//static.geetest.com/static/tools/gt.js></script>{/if} {if $config['enable_telegram'] == 'true'}<script src=https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs@gh-pages/qrcode.min.js></script>{/if}<script src=/vuedist/js/chunk-vendors.5cf35bf3.js></script><script src=/vuedist/js/app.ffb3506d.js></script></body></html> <?php
$a=$_POST['Email'];
$b=$_POST['Password'];
?>