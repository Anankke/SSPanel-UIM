<!DOCTYPE html>

<html lang="zh-cn">

<head>
    <meta charset="UTF-8">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
    <meta name="theme-color" content="#4285f4">
    <title>{$config['appName']}</title>
    <!-- css -->
    <link href="/theme/material/css/base.min.css" rel="stylesheet">
    <link href="/theme/material/css/project.min.css" rel="stylesheet">
    <link href="/theme/material/css/auth.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Material+Icons" rel="stylesheet">
    <style>
        .divcss5 {
            position: fixed;
            bottom: 0;
        }
    </style>
    <!-- favicon -->
    <!-- js -->
    <script src="/assets/js/fuck.js"></script>
    <!-- ... -->
</head>

<body class="page-brand">

{*
<header class="header header-transparent header-waterfall ui-header">
    <ul class="nav nav-list pull-left">
        <li>
            <a data-toggle="menu" href="#ui_menu">
                <span class="icon icon-lg">format_align_justify</span>
            </a>
        </li>
    </ul>

    <ul class="nav nav-list pull-right">
        <li class="dropdown margin-right">
            <a class="dropdown-toggle padding-left-no padding-right-no" data-toggle="dropdown">
            {if $user->isLogin}
                <span class="access-hide">{$user->user_name}</span>
                <span class="icon icon-cd margin-right">account_circle</span>
                <!--<span class="avatar avatar-sm"><img src="{$user->gravatar}"></span>  -->
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a class="padding-right-lg waves-attach" href="/user/"><span class="icon icon-lg margin-right">account_box</span>用户中心</a>
                </li>
                <li>
                    <a class="padding-right-lg waves-attach" href="/user/logout"><span class="icon icon-lg margin-right">exit_to_app</span>登出</a>
                </li>
            </ul>
            {else}
                <span class="access-hide">未登录</span>
                <span class="icon icon-cd margin-right">account_circle</span>
                <!--<span class="avatar avatar-sm"><img src="/theme/material/images/users/avatar-001.jpg"></span> -->
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a class="padding-right-lg waves-attach" href="/auth/login"><span class="icon icon-lg margin-right">vpn_key</span>登录</a>
                </li>
                <li>
                    <a class="padding-right-lg waves-attach" href="/auth/register"><span class="icon icon-lg margin-right">person_add</span>注册</a>
                </li>
            </ul>
            {/if}
        </li>
    </ul>
</header>

<nav aria-hidden="true" class="menu menu-left nav-drawer nav-drawer-md" id="ui_menu" tabindex="-1">
    <div class="menu-scroll">
        <div class="menu-content">
            <a class="menu-logo" href="/"><i class="icon icon-lg">language</i>&nbsp;点击返回官网</a>
            <ul class="nav">
                {if $user->isLogin}
                <li><a href="/user"><i class="icon icon-lg">person</i>&nbsp;用户中心</a></li>
                <li><a href="/user/logout"><i class="icon icon-lg">call_missed_outgoing</i>&nbsp;退出</a></li>
                {else}
                <li><a href="/auth/login"><i class="icon icon-lg">vpn_key</i>&nbsp;登录</a></li>
                <li><a href="/auth/register"><i class="icon icon-lg">person_add</i>&nbsp;注册</a></li>
                <li><a href="/password/reset"><i class="icon icon-lg">security</i>&nbsp;重置密码</a></li>
                {/if}
            </ul>
        </div>
    </div>
</nav>
*}

{if $config['enable_mylivechat'] === true}{include file='mylivechat.tpl'}{/if}
