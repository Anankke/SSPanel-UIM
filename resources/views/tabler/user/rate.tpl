{include file='user/header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">流量倍率</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">查看节点的每小时流量倍率</span>
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
                            <div class="d-flex">
                                <h3 class="card-title">流量倍率图表</h3>
                                <div class="ms-auto">
                                    <div class="dropdown">
                                        <a id="dropdown-toggle" class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false">{$node_list[0]['name']}</a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            {foreach $node_list as $node}
                                            <a class="dropdown-item" hx-post="/user/rate" hx-swap="none"
                                                hx-vals='{ "node_id": "{$node['id']}" }'>
                                                {$node['name']}
                                            </a>
                                            {/foreach}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="rate-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.body.addEventListener("drawChart", function(evt) {
            let chart = window.ApexCharts && new ApexCharts(document.getElementById('rate-chart'), {
                chart: {
                    type: "bar",
                    fontFamily: 'inherit',
                    height: '250%',
                    parentHeightOffset: 0,
                    toolbar: {
                        show: false,
                    },
                    animations: {
                        enabled: false,
                    },
                },
                plotOptions: {
                    bar: {
                        columnWidth: '70%',
                        borderRadius: 5,
                        dataLabels: {
                            position: 'top'
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        fontSize: '13px',
                    }
                },
                fill: {
                    opacity: 1,
                },
                series: [{
                    name: "倍率",
                    data: []
                }],
                tooltip: {
                    theme: 'dark'
                },
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
                    title: {
                        text: '小时',
                    },
                    labels: {
                        padding: 0,
                    },
                    tooltip: {
                        enabled: false
                    },
                    axisBorder: {
                        show: false,
                    },
                    categories: ['00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12',
                        '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'],
                },
                yaxis: {
                    title: {
                        text: '倍率',
                        rotate: 0,
                    },
                    labels: {
                        padding: 4,
                    },
                },
                colors: [tabler.getColor("azure")],
                legend: {
                    show: false,
                },
            });
            document.getElementById('dropdown-toggle').innerHTML = evt.detail.msg;
            chart.render();
            chart.updateOptions({
                series: [{
                    name: "倍率",
                    data: evt.detail.data
                }],
            });
        })
    </script>

    <script src="//{$config['jsdelivr_url']}/npm/@tabler/core@latest/dist/libs/apexcharts/dist/apexcharts.min.js"></script>

{include file='user/footer.tpl'}
