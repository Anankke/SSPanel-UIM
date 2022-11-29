<!DOCTYPE HTML>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <title>Internal Server Error - {$config['appName']}</title>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <link rel="bookmark" href="/favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="/assets/css/error-pages.min.css"/>
</head>
<body>
<div class="container">
    <div class="copy-container center-xy">
        <p>
            500, Internal Server Error.
        </p>
    </div>
</div>

{if !is_null($exceptionId)}
<script
    src="https://browser.sentry-cdn.com/7.18.0/bundle.min.js"
    integrity="sha384-YC/EVW17onWCzzbxK9vx85T6cQ8zRiMoq2PZZhMjhq1gYKyKrOAj9LuInlcfJgzn"
    crossorigin="anonymous"
></script>
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
