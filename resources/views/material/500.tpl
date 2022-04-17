<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
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
