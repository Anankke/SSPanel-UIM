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
    <link href="https://fonts.googleapis.com/css?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/theme/material/css/user.min.css">
    <!-- jquery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1"></script>
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs@master/qrcode.min.js"></script>
    <!-- js -->
    <script src="/assets/js/fuck.min.js"></script>
</head>
<body class="page-orange">
<header class="header header-orange header-transparent header-waterfall ui-header">
    <ul class="nav nav-list pull-left">
        <div>
            <a data-toggle="menu" href="#ui_menu">
                <span class="icon icon-lg text-white">menu</span>
            </a>
        </div>
    </ul>
    <ul class="nav nav-list pull-right">
        <div class="dropdown margin-right">
            <a class="dropdown-toggle padding-left-no padding-right-no" data-toggle="dropdown">
                {if $user->isLogin}
                <span class="access-hide">{$user->user_name}</span>
                <span class="avatar avatar-sm"><img src="{$user->gravatar}"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a class="waves-attach" href="/user/"><span class="icon icon-lg margin-right">account_box</span>用户中心</a>
                </li>
                <li>
                    <a class="padding-right-cd waves-attach" href="/user/logout"><span
                                class="icon icon-lg margin-right">exit_to_app</span>登出</a>
                </li>
                <li>
                    <a href="//en.gravatar.com/" target="view_window"><i class="icon icon-lg margin-right">insert_photo</i>设置头像</a>
                </li>
            </ul>
            {else}
            <span class="access-hide">未登录</span>
            <span class="icon icon-lg margin-right">account_circle</span>
            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a class="padding-right-lg waves-attach" href="/auth/login"><span class="icon icon-lg margin-right">account_box</span>登录</a>
                </li>
                <li>
                    <a class="padding-right-lg waves-attach" href="/auth/register"><span
                                class="icon icon-lg margin-right">pregnant_woman</span>注册</a>
                </li>
            </ul>
            {/if}
        </div>
    </ul>
</header>
<nav aria-hidden="true" class="menu menu-left nav-drawer nav-drawer-md" id="ui_menu" tabindex="-1">
    <div class="menu-scroll">
        <div class="menu-content">
            <a class="menu-logo" href="/"><i class="icon icon-lg">language</i>&nbsp;{$config['appName']}</a>
            <ul class="nav">
                <li>
                    <a class="waves-attach" data-toggle="collapse" href="#ui_menu_me">我的</a>
                    <ul class="menu-collapse collapse in" id="ui_menu_me">
                        <li>
                            <a href="/user"><i class="icon icon-lg">account_balance_wallet</i>&nbsp;用户中心</a>
                        </li>
                        <li>
                            <a href="/user/profile"><i class="icon icon-lg">account_box</i>&nbsp;账户信息</a>
                        </li>
                        <li>
                            <a href="/user/edit"><i class="icon icon-lg">sync_problem</i>&nbsp;资料编辑</a>
                        </li>
                        <li>
                            <a href="/user/trafficlog"><i class="icon icon-lg">hourglass_empty</i>&nbsp;流量记录</a>
                        </li>
                    {if $config['subscribeLog']===true && $config['subscribeLog_show']===true}
                        <li>
                            <a href="/user/subscribe_log"><i class="icon icon-lg">important_devices</i>&nbsp;订阅记录</a>
                        </li>
                    {/if}
                        {if $config['enable_ticket']===true}
                            <li>
                                <a href="/user/ticket"><i class="icon icon-lg">question_answer</i>&nbsp;工单系统</a>
                            </li>
                        {/if}
                        <li>
                            <a href="/user/invite"><i class="icon icon-lg">loyalty</i>&nbsp;邀请链接</a>
                        </li>
                    </ul>
                    <a class="waves-attach" data-toggle="collapse" href="#ui_menu_use">使用</a>
                    <ul class="menu-collapse collapse in" id="ui_menu_use">
                        <li>
                            <a href="/user/node"><i class="icon icon-lg">airplanemode_active</i>&nbsp;节点列表</a>
                        </li>
                        <li>
                            <a href="/user/relay"><i class="icon icon-lg">compare_arrows</i>&nbsp;中转规则</a>
                        </li>
                        <li>
                            <a href="/user/lookingglass"><i class="icon icon-lg">visibility</i>&nbsp;延迟检测</a>
                        </li>
                        <li>
                            <a href="/user/announcement"><i class="icon icon-lg">announcement</i>&nbsp;网站公告</a>
                        </li>
                        <li>
                            <a href="{if $config['use_this_doc'] === false}/user/tutorial{else}/doc/{/if}"><i class="icon icon-lg">start</i>&nbsp;使用教程</a>
                        </li>
                    </ul>
                    <a class="waves-attach" data-toggle="collapse" href="#ui_menu_detect">审计</a>
                    <ul class="menu-collapse collapse in" id="ui_menu_detect">
                        <li>
                            <a href="/user/detect"><i class="icon icon-lg">account_balance</i>&nbsp;审计规则</a>
                        </li>
                        <li>
                            <a href="/user/detect/log"><i class="icon icon-lg">assignment_late</i>&nbsp;审计记录</a>
                        </li>
                    </ul>
                    <a class="waves-attach" data-toggle="collapse" href="#ui_menu_help">商店</a>
                    <ul class="menu-collapse collapse in" id="ui_menu_help">
                        <li>
                            <a href="/user/code"><i class="icon icon-lg">code</i>&nbsp;充值</a>
                        </li>
                        <li>
                            <a href="/user/shop"><i class="icon icon-lg">shop</i>&nbsp;套餐购买</a>
                        </li>
                        <li>
                            <a href="/user/bought"><i class="icon icon-lg">shopping_cart</i>&nbsp;购买记录</a>
                        </li>
                        {if $config['enable_donate']===true}
                            <li>
                                <a href="/user/donate"><i class="icon icon-lg">attach_money</i>&nbsp;捐赠公示</a>
                            </li>
                        {/if}
                    </ul>
                    {if $user->isAdmin()}
                        <li>
                            <a href="/admin"><i class="icon icon-lg">person_pin</i>&nbsp;管理面板</a>
                        </li>
                    {/if}
                    {if $can_backtoadmin}
                        <li>
                            <a class="padding-right-cd waves-attach" href="/user/backtoadmin"><span>class="icon icon-lg margin-right">backtoadmin</span>返回管理员身份</a>
                        </li>
                    {/if}
                </li>
            </ul>
        </div>
    </div>
</nav>
{if $config['live_chat'] === 'mylivechat'}
    {include file='mylivechat.tpl'}
{elseif $config['live_chat'] === 'crisp'}
    {include file='crisp.tpl'}
{/if}