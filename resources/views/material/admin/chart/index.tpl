{include file='admin/tabler_admin_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">报表总览</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">查看站点运营的各项指标数据</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                签到数 <span class="card-subtitle">近 {$range['checkin']} 天内的数据</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="check-in"></div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                流量用量 <span class="card-subtitle">近 {$range['traffic']} 天内的数据</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="total-traffic"></div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                注册用户 <span class="card-subtitle">近 {$range['register']} 天内的数据</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="register"></div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                订单额 <span class="card-subtitle">近 {$range['sale']} 天内的数据</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="order"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            window.ApexCharts && (new ApexCharts(document.getElementById('order'), {
                chart: {
                    type: "area",
                    fontFamily: 'inherit',
                    height: 300,
                    parentHeightOffset: 0,
                    toolbar: {
                        show: false,
                    },
                    animations: {
                        enabled: false
                    },
                },
                dataLabels: {
                    enabled: false,
                },
                fill: {
                    opacity: .16,
                    type: 'solid'
                },
                stroke: {
                    width: 2,
                    lineCap: "round",
                    curve: "smooth",
                },
                series: [{
                    name: "订单额",
                    data: [{implode(', ', $order_amount['y'])}]
                }, {
                    name: "成交额",
                    data: [{implode(', ', $deal_amount['y'])}]
                }],
                grid: {
                    padding: {
                        top: -20,
                        right: 0,
                        left: -4,
                        bottom: -4
                    },
                    strokeDashArray: 4,
                },
                xaxis: {
                    labels: {
                        padding: 0,
                    },
                    tooltip: {
                        enabled: false
                    },
                    axisBorder: {
                        show: false,
                    },
                },
                yaxis: {
                    labels: {
                        padding: 4
                    },
                },
                labels: [
                    {implode(', ', $order_amount['x'])}
                ],
                colors: ["#206bc4", "#74a800"],
                legend: {
                    show: false,
                },
            })).render();
        });
    </script>

    {foreach $charts as $key => $value}
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                window.ApexCharts && (new ApexCharts(document.getElementById('{$value['element_id']}'), {
                    chart: {
                        type: "line",
                        fontFamily: 'inherit',
                        height: 300,
                        parentHeightOffset: 0,
                        toolbar: {
                            show: false,
                        },
                        animations: {
                            enabled: false
                        },
                    },
                    fill: {
                        opacity: 1,
                    },
                    stroke: {
                        width: 2,
                        lineCap: "round",
                        curve: "smooth",
                    },
                    series: [{
                        name: "{$value['series_name']}",
                        data: [{implode(', ', $value['y'])}]
                    }],
                    grid: {
                        padding: {
                            top: -20,
                            right: 0,
                            left: -4,
                            bottom: -4
                        },
                        strokeDashArray: 4,
                    },
                    xaxis: {
                        labels: {
                            padding: 0,
                        },
                        tooltip: {
                            enabled: false
                        },
                    },
                    yaxis: {
                        labels: {
                            padding: 4
                        },
                    },
                    labels: [
                        {implode(', ', $value['x'])}
                    ],
                    colors: ["#206bc4"],
                    legend: {
                        show: false,
                    },
                })).render();
            });
        </script>
    {/foreach}
{include file='admin/tabler_admin_footer.tpl'}