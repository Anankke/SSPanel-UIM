{include file='admin/tabler_header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">系统概况</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">在这里查看系统的的各项运营指标</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="row row-cards">
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-info text-white avatar">
                                                <i class="ti ti-calendar-event icon"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                ￥{$user->calIncome("today")}
                                            </div>
                                            <div class="text-muted">
                                                今日流水
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-blue text-white avatar">
                                                <i class="ti ti-calendar-minus icon"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                ￥{$user->calIncome("yesterday")}
                                            </div>
                                            <div class="text-muted">
                                                昨日流水
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-warning text-white avatar">
                                                <i class="ti ti-calendar-stats icon"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                ￥{$user->calIncome("this month")}
                                            </div>
                                            <div class="text-muted">
                                                这月流水
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-danger text-white avatar">
                                                <i class="ti ti-calendar-plus icon"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                ￥{$user->calIncome("total")}
                                            </div>
                                            <div class="text-muted">
                                                累计流水
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{$sts->getTotalUser()} 位用户的签到情况</h3>
                        </div>
                        <div class="card-body">
                            <div id="check-in"></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{$sts->getTotalNodes()} 个服务器的在线情况</h3>
                        </div>
                        <div class="card-body">
                            <div id="node-online"></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">用户在线</h3>
                        </div>
                        <div class="card-body">
                            <div id="user-online"></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">流量用量</h3>
                        </div>
                        <div class="card-body">
                            <div id="traffic-usage"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // @formatter:off
        document.addEventListener("DOMContentLoaded", function() {
            window.ApexCharts && (new ApexCharts(document.getElementById('check-in'), {
                chart: {
                    type: "donut",
                    fontFamily: 'inherit',
                    height: 300,
                    sparkline: {
                        enabled: true
                    },
                    animations: {
                        enabled: false
                    },
                },
                fill: {
                    opacity: 1,
                },
                series: [{$sts->getTotalUser()-$sts->getCheckinUser()}, {$sts->getCheckinUser()-$sts->getTodayCheckinUser()}, {$sts->getTodayCheckinUser()}],
                labels: ["没有签到", "曾经签到", "今日签到"],
                grid: {
                    strokeDashArray: 3,
                },
                colors: ["#0780cf", "#009db2", "#fa6d1d"],
                legend: {
                    show: true,
                    position: 'bottom',
                    offsetY: 12,
                    markers: {
                        width: 10,
                        height: 10,
                        radius: 100,
                    },
                    itemMargin: {
                        horizontal: 8,
                        vertical: 8
                    },
                },
                tooltip: {
                    fillSeriesColor: false
                },
            })).render();
        });
        // @formatter:on
    </script>
    <script>
        // @formatter:off
        document.addEventListener("DOMContentLoaded", function() {
            window.ApexCharts && (new ApexCharts(document.getElementById('node-online'), {
                chart: {
                    type: "donut",
                    fontFamily: 'inherit',
                    height: 300,
                    sparkline: {
                        enabled: true
                    },
                    animations: {
                        enabled: false
                    },
                },
                fill: {
                    opacity: 1,
                },
                series: [{$sts->getAliveNodes()}, {$sts->getTotalNodes()-$sts->getAliveNodes()}],
                labels: ["在线", "离线"],
                grid: {
                    strokeDashArray: 2,
                },
                colors: ["#71ae46", "#d85d2a"],
                legend: {
                    show: true,
                    position: 'bottom',
                    offsetY: 12,
                    markers: {
                        width: 10,
                        height: 10,
                        radius: 100,
                    },
                    itemMargin: {
                        horizontal: 8,
                        vertical: 8
                    },
                },
                tooltip: {
                    fillSeriesColor: false
                },
            })).render();
        });
        // @formatter:on
    </script>
    <script>
        // @formatter:off
        document.addEventListener("DOMContentLoaded", function() {
            window.ApexCharts && (new ApexCharts(document.getElementById('user-online'), {
                chart: {
                    type: "donut",
                    fontFamily: 'inherit',
                    height: 300,
                    sparkline: {
                        enabled: true
                    },
                    animations: {
                        enabled: false
                    },
                },
                fill: {
                    opacity: 1,
                },
                series: [{$sts->getUnusedUser()}, {$sts->getTotalUser()-$sts->getOnlineUser(86400)-$sts->getUnusedUser()}, {$sts->getOnlineUser(86400)}, {$sts->getOnlineUser(3600)}, {$sts->getOnlineUser(60)}],
                labels: ["从未在线", "一天前在线", "一天内在线", "一小时内在线", "一分钟内在线"],
                grid: {
                    strokeDashArray: 4,
                },
                colors: ["#0e72cc", "#6ca30f", "#f59311", "#fa4343", "#16afcc"],
                legend: {
                    show: true,
                    position: 'bottom',
                    offsetY: 12,
                    markers: {
                        width: 10,
                        height: 10,
                        radius: 100,
                    },
                    itemMargin: {
                        horizontal: 8,
                        vertical: 8
                    },
                },
                tooltip: {
                    fillSeriesColor: false
                },
            })).render();
        });
        // @formatter:on
    </script>
    <script>
        // @formatter:off
        document.addEventListener("DOMContentLoaded", function() {
            window.ApexCharts && (new ApexCharts(document.getElementById('traffic-usage'), {
                chart: {
                    type: "donut",
                    fontFamily: 'inherit',
                    height: 300,
                    sparkline: {
                        enabled: true
                    },
                    animations: {
                        enabled: false
                    },
                },
                fill: {
                    opacity: 1,
                },
                series: [{$sts->getRawGbTodayTrafficUsage()}, {$sts->getRawGbLastTrafficUsage()}, {$sts->getRawGbUnusedTrafficUsage()}],
                labels: ["今日已用({$sts->getTodayTrafficUsage()})", "过去已用({$sts->getLastTrafficUsage()})", "剩余流量({$sts->getUnusedTrafficUsage()})"],
                grid: {
                    strokeDashArray: 3,
                },
                legend: {
                    show: true,
                    position: 'bottom',
                    offsetY: 12,
                    markers: {
                        width: 10,
                        height: 10,
                        radius: 100,
                    },
                    itemMargin: {
                        horizontal: 8,
                        vertical: 8
                    },
                },
                tooltip: {
                    fillSeriesColor: false
                },
            })).render();
        });
        // @formatter:on
    </script>

    <script src="//cdn.jsdelivr.net/npm/@tabler/core@latest/dist/libs/apexcharts/dist/apexcharts.min.js"></script>

{include file='admin/tabler_footer.tpl'}