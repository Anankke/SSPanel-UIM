<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
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