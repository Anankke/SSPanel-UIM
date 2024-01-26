{include file='user/header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">流量记录</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">本日内的流量使用记录</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div id="traffic-log"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let chart = window.ApexCharts && new ApexCharts(document.getElementById('traffic-log'), {
                chart: {
                    type: "line",
                    fontFamily: "inherit",
                    height: '250%',
                    parentHeightOffset: 0,
                    toolbar: {
                        show: false
                    },
                    animations: {
                        enabled: false
                    }
                },
                stroke: {
                    curve: "smooth"
                },
                fill: {
                    opacity: 1
                },
                series: [
                    {
                        name: "使用流量（MB）",
                        data: {$logs}
                    }
                ],
                tooltip: {
                    theme: "dark"
                },
                grid: {
                    padding: {
                        top: -20,
                        right: 0,
                        left: 0,
                        bottom: 0
                    },
                    strokeDashArray: 4
                },
                xaxis: {
                    title: {
                        text: "小时"
                    },
                    labels: {
                        padding: 0
                    },
                    tooltip: {
                        enabled: false
                    },
                    axisBorder: {
                        show: false
                    },
                    categories: [
                        "00",
                        "01",
                        "02",
                        "03",
                        "04",
                        "05",
                        "06",
                        "07",
                        "08",
                        "09",
                        "10",
                        "11",
                        "12",
                        "13",
                        "14",
                        "15",
                        "16",
                        "17",
                        "18",
                        "19",
                        "20",
                        "21",
                        "22",
                        "23"
                    ]
                },
                yaxis: {
                    title: {
                        text: "使用流量（MB）",
                        rotate: -90
                    },
                    labels: {
                        padding: 14
                    }
                },
                colors: [tabler.getColor("azure")],
                legend: {
                    show: false
                }
            });
            chart.render();
        });
    </script>

    <script src="//{$config['jsdelivr_url']}/npm/@tabler/core@latest/dist/libs/apexcharts/dist/apexcharts.min.js"></script>

{include file='user/footer.tpl'}
