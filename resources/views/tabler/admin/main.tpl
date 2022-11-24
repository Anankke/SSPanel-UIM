<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
    <meta name="theme-color" content="#4285f4">
    <title>{$config['appName']}</title>
    <!-- css -->
    <link href="/theme/tabler/css/base.min.css" rel="stylesheet">
    <link href="/theme/tabler/css/project.min.css" rel="stylesheet">
    <link href="//cdn.jsdelivr.net/npm/@mdi/font@latest/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="//cdn.jsdelivr.net/npm/material-design-lite@1.3.0/material.min.css" rel="stylesheet">
    <link href="//cdn.datatables.net/1.12.1/css/dataTables.material.min.css" rel="stylesheet">
    <!-- js -->
    <script src="//cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
    <!-- ... -->
    <style>
        body {
            position: relative;
        }

        .table-responsive {
            background: white;
        }

        .dropdown-menu.dropdown-menu-right a {
            color: #212121;
        }

        a[href='#ui_menu'] {
            color: #212121;
        }

        #custom_config {
            height: 500px;
        }
    </style>
</head>

<body class="page-brand">
<header class="header header-red header-transparent header-waterfall ui-header">
    <ul class="nav nav-list pull-left">
        <div>
            <a data-toggle="menu" href="#ui_menu">
                <span class="mdi mdi-menu icon-lg"></span>
            </a>
        </div>
    </ul>
    <ul class="nav nav-list pull-right">
        <div class="dropdown margin-right">
            <a class="dropdown-toggle padding-left-no padding-right-no" data-toggle="dropdown">
                <span class="access-hide">{$user->user_name}</span>
                <span class="avatar avatar-sm"><img src="{$user->gravatar}"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a class="waves-attach" href="/user/logout"><span
                                class="mdi mdi-exit-to-app icon-lg margin-right"></span>登出</a>
                </li>
            </ul>
        </div>
    </ul>
</header>
<nav aria-hidden="true" class="menu menu-left nav-drawer nav-drawer-md" id="ui_menu" tabindex="-1">
    <div class="menu-scroll">
        <div class="menu-content">
            <a class="menu-logo" href="/">&nbsp;管理面板</a>
            <ul class="nav">
                <li>
                    <a class="waves-attach" data-toggle="collapse" href="#ui_menu_me">我的</a>
                    <ul class="menu-collapse collapse in" id="ui_menu_me">
                        <li><a href="/admin"><i class="mdi mdi-eye icon-lg"></i>&nbsp;系统概览</a></li>
                        <li><a href="/admin/announcement"><i class="mdi mdi-bullhorn-variant icon-lg"></i>&nbsp;公告管理</a></li>
                        <li><a href="/admin/ticket"><i class="mdi mdi-comment-question icon-lg"></i>&nbsp;工单管理</a></li>
                    </ul>
                    <a class="waves-attach" data-toggle="collapse" href="#ui_menu_node">节点</a>
                    <ul class="menu-collapse collapse in" id="ui_menu_node">
                        <li><a href="/admin/node"><i class="mdi mdi-server icon-lg"></i>&nbsp;节点列表</a></li>
                    </ul>
                    <a class="waves-attach" data-toggle="collapse" href="#ui_menu_user">用户</a>
                    <ul class="menu-collapse collapse in" id="ui_menu_user">
                        <li><a href="/admin/user"><i class="mdi mdi-account-group icon-lg"></i>&nbsp;用户列表</a></li>
                        <li><a href="/admin/invite"><i class="mdi mdi-account-multiple-plus icon-lg"></i>&nbsp;邀请与返利</a></li>
                        <li><a href="/admin/subscribe"><i class="mdi mdi-file-find icon-lg"></i>&nbsp;订阅记录</a></li>
                        <li><a href="/admin/login"><i class="mdi mdi-text-account icon-lg"></i>&nbsp;登录记录</a></li>
                        <li><a href="/admin/trafficlog"><i class="mdi mdi-swap-vertical icon-lg"></i>&nbsp;流量记录</a></li>
                        <li><a href="/admin/alive"><i class="mdi mdi-account-badge icon-lg"></i>&nbsp;在线IP</a></li>
                    </ul>
                    <a class="waves-attach" data-toggle="collapse" href="#ui_menu_config">配置</a>
                    <ul class="menu-collapse collapse in" id="ui_menu_config">
                        <li><a href="/admin/setting"><i class="mdi mdi-cog icon-lg"></i>&nbsp;设置中心</a></li>
                    </ul>
                    <a class="waves-attach" data-toggle="collapse" href="#ui_menu_detect">审计</a>
                    <ul class="menu-collapse collapse in" id="ui_menu_detect">
                        <li><a href="/admin/detect"><i class="mdi mdi-account-filter icon-lg"></i>&nbsp;审计规则</a></li>
                        <li><a href="/admin/detect/log"><i class="mdi mdi-calendar-filter icon-lg"></i>&nbsp;审计记录</a></li>
                        <li><a href="/admin/detect/ban"><i class="mdi mdi-account-cancel icon-lg"></i>&nbsp;审计封禁</a></li>
                    </ul>
                    <a class="waves-attach" data-toggle="collapse" href="#ui_menu_trade">财务</a>
                    <ul class="menu-collapse collapse in" id="ui_menu_trade">
                        <li><a href="/admin/code">
                                <i class="mdi mdi-currency-usd icon-lg"></i>
                                &nbsp;充值记录</a>
                        </li>
                        <li><a href="/admin/shop"><i class="mdi mdi-shopping icon-lg"></i>&nbsp;商品</a></li>
                        <li><a href="/admin/coupon"><i class="mdi mdi-code-tags icon-lg"></i>&nbsp;优惠码</a></li>
                        <li><a href="/admin/bought"><i class="mdi mdi-shopping-search icon-lg"></i>&nbsp;购买记录</a></li>
                    </ul>
                <li><a href="/user"><i class="mdi mdi-account icon-lg"></i>&nbsp;用户中心</a></li>
            </ul>
        </div>
    </div>
</nav>
