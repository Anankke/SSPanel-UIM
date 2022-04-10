<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.0.0-beta9
* @link https://tabler.io
* Copyright 2018-2022 The Tabler Authors
* Copyright 2018-2022 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->
<html lang="zh">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{$config['appName']}</title>
    <!-- CSS files -->
    <link href="/theme/tabler/css/tabler.min.css" rel="stylesheet" />
    <link href="/theme/tabler/css/tabler-flags.min.css" rel="stylesheet" />
    <link href="/theme/tabler/css/tabler-payments.min.css" rel="stylesheet" />
    <link href="/theme/tabler/css/tabler-vendors.min.css" rel="stylesheet" />
    <link href="/theme/tabler/css/demo.min.css" rel="stylesheet" />
    <link href="https://unpkg.com/@tabler/icons@latest/iconfont/tabler-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.11.5/datatables.min.css" />
    <!-- JS files -->
    <script src="/theme/tabler/js/qrcode.min.js"></script>
    <script src="/theme/tabler/js/clipboard.min.js"></script>
    <script src="/theme/tabler/js/jquery-3.6.0.min.js"></script>
    <script src="/theme/tabler/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <style>
        .home-subtitle{
            font-size: 14px;
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
                        </div>
                    </div>
                </div>
                <div class="collapse navbar-collapse" id="navbar-menu">
                    <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="/user">
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
                                        主页
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" role="button" aria-expanded="false">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/package -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <polyline points="12 3 20 7.5 20 16.5 12 21 4 16.5 4 7.5 12 3" />
                                            <line x1="12" y1="12" x2="20" y2="7.5" />
                                            <line x1="12" y1="12" x2="12" y2="21" />
                                            <line x1="12" y1="12" x2="4" y2="7.5" />
                                            <line x1="16" y1="5.25" x2="8" y2="9.75" />
                                        </svg>
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
                                            <a class="dropdown-item" href="/user/ticket">
                                                <i class="ti ti-ticket"></i>&nbsp;
                                                工单系统
                                            </a>
                                            <a class="dropdown-item" href="/user/invite">
                                                <i class="ti ti-friends"></i>&nbsp;
                                                邀请链接
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" role="button" aria-expanded="false">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/star -->
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-lifebuoy" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <circle cx="12" cy="12" r="4"></circle>
                                            <circle cx="12" cy="12" r="9"></circle>
                                            <line x1="15" y1="15" x2="18.35" y2="18.35"></line>
                                            <line x1="9" y1="15" x2="5.65" y2="18.35"></line>
                                            <line x1="5.65" y1="5.65" x2="9" y2="9"></line>
                                            <line x1="18.35" y1="5.65" x2="15" y2="9"></line>
                                        </svg>
                                    </span>
                                    <span class="nav-link-title">
                                        使用
                                    </span>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="/user/announcement">
                                        <i class="ti ti-flag"></i>&nbsp;
                                        站点公告
                                    </a>
                                    <a class="dropdown-item" href="/user/node">
                                        <i class="ti ti-server-2"></i>&nbsp;
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
                                        商店
                                    </span>
                                </a>
                                <div class="dropdown-menu">
                                    <div class="dropdown-menu-columns">
                                        <div class="dropdown-menu-column">
                                            <a class="dropdown-item" href="/user/code">
                                                <i class="ti ti-coin"></i>&nbsp;
                                                账户充值
                                            </a>
                                            <a class="dropdown-item" href="/user/shop">
                                                <i class="ti ti-shopping-cart"></i>&nbsp;
                                                商品列表
                                            </a>
                                            <a class="dropdown-item" href="/user/bought">
                                                <i class="ti ti-checklist"></i>&nbsp;
                                                订单记录
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
</header>