<!DOCTYPE HTML>
<html>
<head>
    <title>该网页无法正常运作 - {$config['appName']}</title>
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
    <meta charset="utf-8"/>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <link rel="bookmark" href="/favicon.ico" type="image/x-icon"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"/>
    <link rel="shortcut icon" type="image/ico" href="images/ssr.ico">
    <link rel="stylesheet" href="/assets/css/main.css"/>

    <noscript>
        <link rel="stylesheet" href="/assets/css/noscript.css"/>
    </noscript>
</head>

<body>

<div id="wrapper">
    <header id="header">
        <div class="logo">
            <span class="icon fa-rocket"></span>
        </div>
        <div class="content">
            <div class="inner">
                <h1>500 错误</h1>
                <p>服务娘崩溃了呢... TwT</p>
                <p>这件事儿不应该发生的...如果反复出现可以提交一下工单联系站主.</p>
                {if !is_null($exceptionId)}
                <p>事件 ID: {$exceptionId}</p>
                {/if}
            </div>
        </div>
        <nav>
            <ul>
                <li><a href="./#">返回首页</a></li>
            </ul>
        </nav>
    </header>
    <footer id="footer">
        <p class="copyright">&copy;{date("Y")} {$config['appName']} </p>
    </footer>
</div>
<div id="bg"></div>

<script src="https://cdn.jsdelivr.net/npm/jquery@1.11.3"></script>
<script src="https://cdn.jsdelivr.net/gh/ajlkn/skel@3.0.1/dist/skel.min.js"></script>
<script src="/assets/js/util.js"></script>
<script src="/assets/js/main.js"></script>

{if !is_null($exceptionId)}
<script src="https://cdn.jsdelivr.net/npm/@sentry/browser@5.20.1/build/bundle.min.js" integrity="sha256-EIV/iYkbXFgnuIHEdltBOK4eY58n87ADisyDI8/VJPg=" crossorigin="anonymous"></script>
<script>
    Sentry.init({
        dsn: "{$config['sentry_dsn']}"
    });
    Sentry.showReportDialog({
        eventId: '{$exceptionId}',
        user: {
            name: '{$user->user_name}',
            email: '{$user->email}'
        }
    });
</script>
{/if}

</body>

</html>
