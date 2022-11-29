<!DOCTYPE HTML>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <title>{$config['appName']}</title>
    {if $user->isLogin}
    <script>
        window.location.href = "/user"
    </script>
    {else}
    <script>
        window.location.href = "/auth/login"
    </script>
    {/if}
</head>
</html>