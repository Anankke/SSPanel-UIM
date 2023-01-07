<!doctype html>
<html lang="zh">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
    <meta name="format-detection" content="telephone=no" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{$config['appName']}</title>
    <!-- CSS files -->
    <link href="//cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css" rel="stylesheet" />
    <link href="//cdn.jsdelivr.net/npm/@tabler/icons@latest/iconfont/tabler-icons.min.css" rel="stylesheet" />
    <link href="//cdn.datatables.net/v/bs5/dt-1.13.1/datatables.min.css" rel="stylesheet" />
    <!-- JS files -->
    <script src="//cdn.jsdelivr.net/npm/qrcode_js@1.0.0/qrcode.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/clipboard@2.0.11/dist/clipboard.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
    <script src="//cdn.datatables.net/v/bs5/dt-1.13.1/datatables.min.js"></script>
    <style>
        .home-subtitle {
            font-size: 14px;
        }

        .home-title {
            font-size: 36px;
        }
    </style>
</head>

{if $user->is_dark_mode}
<body class='theme-dark'>
{else}
<body>
{/if}
    <div class="page">
        <header class="navbar navbar-expand-md navbar-dark navbar-overlap d-print-none">
            <div class="container-xl" style="background-image: none;">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3" style="filter: none;">
                    <img src="/images/uim-logo-round_48x48.png" height="32" alt="SSPanel-UIM" class="navbar-brand-image">
                </h1>
                <div class="navbar-nav flex-row order-md-last">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
                            aria-label="Open user menu">
                            <span class="avatar avatar-sm"
                                style="background-image: url({$user->gravatar})"></span>
                            <div class="d-none d-xl-block ps-2">
                                <div>{$user->email}</div>
                                <div class="mt-1 small text-muted">{$user->user_name}</div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            {if $user->is_dark_mode}
                            <a id="switch_theme_mode" class="dropdown-item">切换至浅色模式</a>
                            {else}
                            <a id="switch_theme_mode" class="dropdown-item">切换至深色模式</a>
                            {/if}
                            <a href="/user/logout" class="dropdown-item">登出</a>
                        </div>
                    </div>
                </div>
                <div class="collapse navbar-collapse" id="navbar-menu">
                    <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="/admin">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-home icon"></i>
                                    </span>
                                    <span class="nav-link-title">
                                        概况
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" role="button" aria-expanded="false">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-settings icon"></i>
                                    </span>
                                    <span class="nav-link-title">
                                        管理
                                    </span>
                                </a>
                                <div class="dropdown-menu">
                                    <div class="dropdown-menu-columns">
                                        <div class="dropdown-menu-column">
                                            <a class="dropdown-item" href="/admin/setting">
                                                <i class="ti ti-tool"></i>&nbsp;
                                                设置
                                            </a>
                                            <a class="dropdown-item" href="/admin/user">
                                                <i class="ti ti-users"></i>&nbsp;
                                                用户
                                            </a>
                                            <a class="dropdown-item" href="/admin/node">
                                                <i class="ti ti-server-2"></i>&nbsp;
                                                节点
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" role="button" aria-expanded="false">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-brand-hipchat icon"></i>
                                    </span>
                                    <span class="nav-link-title">
                                        运营
                                    </span>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="/admin/announcement">
                                        <i class="ti ti-speakerphone"></i>&nbsp;
                                        公告
                                    </a>
                                    <a class="dropdown-item" href="/admin/ticket">
                                        <i class="ti ti-messages"></i>&nbsp;
                                        工单
                                    </a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" role="button" aria-expanded="false">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-address-book icon"></i>
                                    </span>
                                    <span class="nav-link-title">
                                        日志
                                    </span>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="/admin/login">
                                        <i class="ti ti-login"></i>&nbsp;
                                        登录
                                    </a>
                                    <a class="dropdown-item" href="/admin/subscribe">
                                        <i class="ti ti-rss"></i>&nbsp;
                                        订阅
                                    </a>
                                    <a class="dropdown-item" href="/admin/invite">
                                        <i class="ti ti-friends"></i>&nbsp;
                                        邀请
                                    </a>
                                    <a class="dropdown-item" href="/admin/alive">
                                        <i class="ti ti-router"></i>&nbsp;
                                        在线IP
                                    </a>
                                    <a class="dropdown-item" href="/admin/trafficlog">
                                        <i class="ti ti-arrows-up-down"></i>&nbsp;
                                        流量使用
                                    </a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" role="button" aria-expanded="false">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-shield-check icon"></i>
                                    </span>
                                    <span class="nav-link-title">
                                        审计
                                    </span>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="/admin/detect">
                                        <i class="ti ti-barrier-block"></i>&nbsp;
                                        规则
                                    </a>
                                    <a class="dropdown-item" href="/admin/detect/log">
                                        <i class="ti ti-notes"></i>&nbsp;
                                        记录
                                    </a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#navbar-layout" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" role="button" aria-expanded="false">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-coin icon"></i>
                                    </span>
                                    <span class="nav-link-title">
                                        财务
                                    </span>
                                </a>
                                <div class="dropdown-menu">
                                    <div class="dropdown-menu-columns">
                                        <div class="dropdown-menu-column">
                                            <a class="dropdown-item" href="/admin/product">
                                                <i class="ti ti-list-details"></i>&nbsp;
                                                商品
                                            </a>
                                            <a class="dropdown-item" href="/admin/order">
                                                <i class="ti ti-receipt"></i>&nbsp;
                                                订单
                                            </a>
                                            <a class="dropdown-item" href="/admin/invoice">
                                                <i class="ti ti-file-dollar"></i>&nbsp;
                                                账单
                                            </a>
                                            <a class="dropdown-item" href="/admin/coupon">
                                                <i class="ti ti-ticket"></i>&nbsp;
                                                优惠码
                                            </a>
                                            <a class="dropdown-item" href="/admin/giftcard">
                                                <i class="ti ti-gift"></i>&nbsp;
                                                礼品卡
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/user">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-arrow-back-up icon"></i>
                                    </span>
                                    <span class="nav-link-title">
                                        返回
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
</header>