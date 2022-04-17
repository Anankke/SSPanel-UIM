<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title>{$config['appName']}</title>
    <link rel="stylesheet" href="/assets/css/index.min.css">
</head>
<body>
<div class="container">
    <div class="copy-container center-xy">
        <div class="logo">
            <img src="/images/uim-logo-round.png">
        </div>
        <h>{$config['appName']}</h>
        <br>
        <br>
        {if $user->isLogin}
        <p>账户 {$user->email} 的使用状态</p>
        <br>
        <table>
            <tr>
                <th>账户等级</th>
                <th>当前等级到期时间</th>
                <th>已用流量</th>
                <th>剩余流量</th>
            </tr>
            <tr>
                {if $user->class!=0}
                <th>VIP{$user->class}</th>
                {else}
                <th>免费</th>
                {/if}
                {if $user->class_expire!="1989-06-04 00:05:00"}
                <th>{$user->class_expire}</th>
                {else}
                <th>不过期</th>
                {/if}
                <th>{$user->usedTraffic()}</th>
                <th>{$user->unusedTraffic()}</th>
            </tr>
        </table>
        <br>
        <ul>
            <li><button class="btn white"><a href="/user">用户中心</a></button></li>
            {if $user->is_admin}
            <li><button class="btn white"><a href="/admin">管理后台</a></button></li>
            {/if}
            <li><button class="btn white"><a href="/user/logout">退出登录</a></button></li>
        </ul>
        {else}
            <ul>
                <li><button class="btn white"><a href="/auth/login">登录</a></button></li>
                <li><button class="btn white"><a href="/auth/register">注册</a></button></li>
            </ul>
        {/if}
    </div>
</div>

</body>
{include file='live_chat.tpl'}
</html>