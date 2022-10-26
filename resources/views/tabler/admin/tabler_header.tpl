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
    <link href="//fastly.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css" rel="stylesheet" />
    <link href="//fastly.jsdelivr.net/npm/@tabler/icons@latest/iconfont/tabler-icons.min.css" rel="stylesheet" />
    <link href="//cdn.datatables.net/v/dt/dt-1.12.1/datatables.min.css" rel="stylesheet" />
    <!-- JS files -->
    <script src="//fastly.jsdelivr.net/gh/davidshimjs/qrcodejs@master/qrcode.min.js"></script>
    <script src="//cdn.staticfile.org/clipboard.js/2.0.11/clipboard.min.js"></script>
    <script src="//cdn.staticfile.org/jquery/3.6.1/jquery.min.js"></script>
    <script src="//cdn.datatables.net/v/dt/dt-1.12.1/datatables.min.js"></script>
    <style>
        .home-subtitle {
            font-size: 14px;
        }

        .home-title {
            font-size: 36px;
        }
    </style>
</head>

<body>
    <div class="page">
        <header class="navbar navbar-expand-md navbar-dark navbar-overlap d-print-none">
            <div class="container-xl" style="background-image: none;">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                    <img src="/theme/tabler/static/logo-white.svg" width="110" height="32" alt="Tabler"
                        class="navbar-brand-image">
                </h1>
                <div class="navbar-nav flex-row order-md-last">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
                            aria-label="Open user menu">
                            <span class="avatar avatar-sm"
                                style="background-image: url(/theme/tabler/static/avatars/000m.jpg)"></span>
                            <div class="d-none d-xl-block ps-2">
                                <div>{$user->email}</div>
                                <div class="mt-1 small text-muted">{$user->user_name}</div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <a href="/user/logout" class="dropdown-item">登出</a>
                            <a href="/user" class="dropdown-item">用户中心</a>
                        </div>
                    </div>
                </div>
                <div class="collapse navbar-collapse" id="navbar-menu">
                    <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="/admin">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/home -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <polyline points="5 12 3 12 12 3 21 12 19 12" />
                                            <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                            <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                                        </svg>
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
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-settings" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path
                                                d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z">
                                            </path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
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
                                            <a class="dropdown-item" href="/admin/giftcard">
                                                <i class="ti ti-gift"></i>&nbsp;
                                                礼品卡
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" role="button" aria-expanded="false">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-brand-hipchat" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path
                                                d="M17.802 17.292s.077 -.055 .2 -.149c1.843 -1.425 2.998 -3.49 2.998 -5.789c0 -4.286 -4.03 -7.764 -8.998 -7.764c-4.97 0 -9.002 3.478 -9.002 7.764c0 4.288 4.03 7.646 9 7.646c.424 0 1.12 -.028 2.088 -.084c1.262 .82 3.104 1.493 4.716 1.493c.499 0 .734 -.41 .414 -.828c-.486 -.596 -1.156 -1.551 -1.416 -2.29z">
                                            </path>
                                            <path d="M7.5 13.5c2.5 2.5 6.5 2.5 9 0"></path>
                                        </svg>
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
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-report-analytics" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <desc>Download more icon variants from
                                                https://tabler-icons.io/i/report-analytics</desc>
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path
                                                d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2">
                                            </path>
                                            <rect x="9" y="3" width="6" height="4" rx="2"></rect>
                                            <path d="M9 17v-5"></path>
                                            <path d="M12 17v-1"></path>
                                            <path d="M15 17v-3"></path>
                                        </svg>
                                    </span>
                                    <span class="nav-link-title">
                                        报表
                                    </span>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item"
                                        href="/admin/chart/index">
                                        <i class="ti ti-timeline"></i>&nbsp;
                                        总览
                                    </a>
                                    <a class="dropdown-item"
                                        href="/admin/chart/finance">
                                        <i class="ti ti-businessplan"></i>&nbsp;
                                        财务
                                    </a>
                                    <a class="dropdown-item"
                                        href="/admin/chart/user/{date("Ymd", strtotime("-1 day"))}">
                                        <i class="ti ti-sort-ascending-2"></i>&nbsp;
                                        用户流量
                                    </a>
                                    <a class="dropdown-item"
                                        href="/admin/chart/node/{date("Ymd", strtotime("-1 day"))}">
                                        <i class="ti ti-sort-descending-2"></i>&nbsp;
                                        节点流量
                                    </a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" role="button" aria-expanded="false">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-address-book" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <desc>Download more icon variants from
                                                https://tabler-icons.io/i/address-book</desc>
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path
                                                d="M20 6v12a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2z">
                                            </path>
                                            <path d="M10 16h6"></path>
                                            <circle cx="13" cy="11" r="2"></circle>
                                            <path d="M4 8h3"></path>
                                            <path d="M4 12h3"></path>
                                            <path d="M4 16h3"></path>
                                        </svg>
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
                                    <a class="dropdown-item" href="/admin/payback">
                                        <i class="ti ti-friends"></i>&nbsp;
                                        返利
                                    </a>
                                    <a class="dropdown-item" href="/admin/alive">
                                        <i class="ti ti-router"></i>&nbsp;
                                        在线
                                    </a>
                                    <a class="dropdown-item" href="/admin/log">
                                        <i class="ti ti-book-download"></i>&nbsp;
                                        自定义
                                    </a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" role="button" aria-expanded="false">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/star -->
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-shield-check" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M9 12l2 2l4 -4"></path>
                                            <path
                                                d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3">
                                            </path>
                                        </svg>
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
                                        <!-- Download SVG icon from http://tabler-icons.io/i/layout-2 -->
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-building-store" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <line x1="3" y1="21" x2="21" y2="21"></line>
                                            <path
                                                d="M3 7v1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1h-18l2 -4h14l2 4">
                                            </path>
                                            <line x1="5" y1="21" x2="5" y2="10.85"></line>
                                            <line x1="19" y1="21" x2="19" y2="10.85"></line>
                                            <path d="M9 21v-4a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v4"></path>
                                        </svg>
                                    </span>
                                    <span class="nav-link-title">
                                        商品
                                    </span>
                                </a>
                                <div class="dropdown-menu">
                                    <div class="dropdown-menu-columns">
                                        <div class="dropdown-menu-column">
                                            <a class="dropdown-item" href="/admin/product">
                                                <i class="ti ti-list-details"></i>&nbsp;
                                                列表
                                            </a>
                                            <a class="dropdown-item" href="/admin/order">
                                                <i class="ti ti-receipt"></i>&nbsp;
                                                订单
                                            </a>
                                            <a class="dropdown-item" href="/admin/coupon">
                                                <i class="ti ti-ticket"></i>&nbsp;
                                                优惠码
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/user">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-arrow-back-up" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M9 13l-4 -4l4 -4m-4 4h11a4 4 0 0 1 0 8h-1"></path>
                                        </svg>
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