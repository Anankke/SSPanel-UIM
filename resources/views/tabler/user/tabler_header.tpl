<!doctype html>
<html lang="zh">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="referrer" content="never">
    <title>{$config['appName']}</title>
    <!-- CSS files -->
    <link href="//cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css" rel="stylesheet" />
    <link href="//cdn.jsdelivr.net/npm/@tabler/icons@latest/iconfont/tabler-icons.min.css" rel="stylesheet" />
    <!-- JS files -->
    <script src="/assets/js/fuck.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/qrcode_js@1.0.0/qrcode.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/clipboard@2.0.11/dist/clipboard.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
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
                                <a class="nav-link" href="/user">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-home icon"></i>
                                    </span>
                                    <span class="nav-link-title">
                                        主页
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" role="button" aria-expanded="false">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-user icon"></i>
                                    </span>
                                    <span class="nav-link-title">
                                        我的
                                    </span>
                                </a>
                                <div class="dropdown-menu">
                                    <div class="dropdown-menu-columns">
                                        <div class="dropdown-menu-column">
                                            <a class="dropdown-item" href="/user/profile">
                                                <i class="ti ti-info-square"></i>&nbsp;
                                                账户信息
                                            </a>
                                            <a class="dropdown-item" href="/user/edit">
                                                <i class="ti ti-edit"></i>&nbsp;
                                                资料修改
                                            </a>
                                            {if $config['enable_ticket'] == true}
                                                <a class="dropdown-item" href="/user/ticket">
                                                    <i class="ti ti-ticket"></i>&nbsp;
                                                    工单系统
                                                </a>
                                            {/if}
                                            <a class="dropdown-item" href="/user/invite">
                                                <i class="ti ti-friends"></i>&nbsp;
                                                邀请注册
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" role="button" aria-expanded="false">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-brand-telegram icon"></i>
                                    </span>
                                    <span class="nav-link-title">
                                        使用
                                    </span>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="/user/server">
                                        <i class="ti ti-server"></i>&nbsp;
                                        节点列表
                                    </a>
                                    <a class="dropdown-item" href="/user/media">
                                        <i class="ti ti-key"></i>&nbsp;
                                        流媒体解锁
                                    </a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" role="button" aria-expanded="false">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-dots-circle-horizontal icon"></i>
                                    </span>
                                    <span class="nav-link-title">
                                        更多
                                    </span>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="/user/announcement">
                                        <i class="ti ti-speakerphone"></i>&nbsp;
                                        站点公告
                                    </a>
                                    {if $config['subscribeLog_show'] == true && $config['subscribeLog'] == true}
                                        <a class="dropdown-item" href="/user/subscribe_log">
                                            <i class="ti ti-rss"></i></i>&nbsp;
                                            订阅日志
                                        </a>
                                    {/if}
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
                                    <a class="dropdown-item" href="/user/detect">
                                        <i class="ti ti-barrier-block"></i>&nbsp;
                                        审计规则
                                    </a>
                                    <a class="dropdown-item" href="/user/detect/log">
                                        <i class="ti ti-notes"></i>&nbsp;
                                        审计日志
                                    </a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#navbar-layout" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" role="button" aria-expanded="false">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-building-store icon"></i>
                                    </span>
                                    <span class="nav-link-title">
                                        商店
                                    </span>
                                </a>
                                <div class="dropdown-menu">
                                    <div class="dropdown-menu-columns">
                                        <div class="dropdown-menu-column">
                                            <a class="dropdown-item" href="/user/shop">
                                                <i class="ti ti-shopping-cart"></i>&nbsp;
                                                套餐购买
                                            </a>
                                            <a class="dropdown-item" href="/user/code">
                                                <i class="ti ti-checklist"></i>&nbsp;
                                                账户充值
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            {if $user->is_admin}
                                <li class="nav-item">
                                    <a class="nav-link" href="/admin">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <i class="ti ti-settings icon"></i>
                                        </span>
                                        <span class="nav-link-title">
                                            管理
                                        </span>
                                    </a>
                                </li>
                            {/if}
                        </ul>
                    </div>
                </div>
            </div>
</header>