{include file='admin/main.tpl'}

<script src="https://fastly.jsdelivr.net/npm/chart.js"></script>

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">运营总览</h1>
        </div>
    </div>
    <div class="container">
        <section class="content-inner margin-top-no">
            <div class="row">
                <div class="col-xx-12">
                    <div class="card margin-bottom-no">
                        <div class="card-main">
                            <div class="card-inner">
                                <p>下面是系统运行情况简报。</p>
                                <p>
                                    付费用户：{$user->paidUserCount()}<br/>
                                    总共用户：{$user->count()}<br/>
                                    总转换率：{round($user->paidUserCount()/$user->count()*100,2)}%
                                </p>
                                <p>
                                    今日流水：￥{$user->calIncome("today")}<br/>
                                    昨日流水：￥{$user->calIncome("yesterday")}<br/>
                                    这月流水：￥{$user->calIncome("this month")}<br/>
                                    上月流水：￥{$user->calIncome("last month")}<br/>
                                    总共流水：￥{$user->calIncome("total")}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ui-card-wrap">
                <div class="row">
                    <div class="col-xx-12 col-sm-6">
                        <div class="card">
                            <div class="card-main">
                                <div class="card-inner">
                                    <canvas id="check_chart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-main">
                                <div class="card-inner">
                                    <canvas id="alive_chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xx-12 col-sm-6">
                        <div class="card">
                            <div class="card-main">
                                <div class="card-inner">
                                    <canvas id="node_chart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-main">
                                <div class="card-inner">
                                    <canvas id="traffic_chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

{include file='admin/footer.tpl'}

<script>
    const check_chart = new Chart(
        document.getElementById('check_chart'),
        config = {
            type: 'doughnut',
            data: {
                labels: [
                    '没有签到过的用户（{$sts->getTotalUser()-$sts->getCheckinUser()}人）', 
                    '曾经签到过的用户（{$sts->getCheckinUser()-$sts->getTodayCheckinUser()}人）', 
                    '今日签到用户（{$sts->getTodayCheckinUser()}人）'
                ],
                datasets: [{
                    label: '用户签到状态',
                    data: [
                        {$sts->getTotalUser()-$sts->getCheckinUser()}, 
                        {$sts->getCheckinUser()-$sts->getTodayCheckinUser()},
                        {$sts->getTodayCheckinUser()}
                    ],
                    backgroundColor: [
                        'rgb(205, 180, 219)',
                        'rgb(255, 175, 204)',
                        'rgb(162, 210, 255)'
                    ]
                }]
            },
            options: {
                aspectRatio: 2,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        font: {
                            size: 14
                        }
                    },
                    title: {
                        display: true,
                        position: 'top',
                        text: '用户签到状态',
                        font: {
                            size: 14
                        }
                    }
                }
            }
        },
    );
</script>

<script>
    const alive_chart = new Chart(
        document.getElementById('alive_chart'),
        config = {
            type: 'doughnut',
            data: {
                labels: [
                    '从未在线的用户（{$sts->getUnusedUser()}人）', 
                    '一天以前在线的用户（{$sts->getTotalUser()-$sts->getOnlineUser(86400)-$sts->getUnusedUser()}人）', 
                    '一天内在线的用户（{$sts->getOnlineUser(86400)}人）',
                    '一小时内在线的用户（{$sts->getOnlineUser(3600)}人）',
                    '一分钟内在线的用户（{$sts->getOnlineUser(60)}人）'
                ],
                datasets: [{
                    label: '用户在线状态',
                    data: [
                        {$sts->getUnusedUser()}, 
                        {$sts->getTotalUser()-$sts->getOnlineUser(86400)-$sts->getUnusedUser()},
                        {$sts->getOnlineUser(86400)},
                        {$sts->getOnlineUser(3600)},
                        {$sts->getOnlineUser(60)}
                    ],
                    backgroundColor: [
                        'rgb(205, 180, 219)',
                        'rgb(255, 200, 221)',
                        'rgb(255, 175, 204)',
                        'rgb(189, 224, 254)',
                        'rgb(162, 210, 255)'
                    ]
                }]
            },
            options: {
                aspectRatio: 2,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        font: {
                            size: 14
                        }
                    },
                    title: {
                        display: true,
                        position: 'top',
                        text: '用户在线状态',
                        font: {
                            size: 14
                        }
                    }
                }
            }
        },
    );
</script>


<script>
    const node_chart = new Chart(
        document.getElementById('node_chart'),
        config = {
            type: 'doughnut',
            data: {
                labels: [
                    '离线节点（{$sts->getTotalNodes()-$sts->getAliveNodes()}个）', 
                    '在线节点（{$sts->getAliveNodes()}个）'
                ],
                datasets: [{
                    label: '节点状态',
                    data: [
                        {$sts->getTotalNodes()-$sts->getAliveNodes()}, 
                        {$sts->getAliveNodes()}
                    ],
                    backgroundColor: [
                        'rgb(205, 180, 219)',
                        'rgb(162, 210, 255)'
                    ]
                }]
            },
            options: {
                aspectRatio: 2,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        font: {
                            size: 14
                        }
                    },
                    title: {
                        display: true,
                        position: 'top',
                        text: '节点状态',
                        font: {
                            size: 14
                        }
                    }
                }
            }
        },
    );
</script>

<script>
    const traffic_chart = new Chart(
        document.getElementById('traffic_chart'),
        config = {
            type: 'doughnut',
            data: {
                labels: [
                    '总剩余可用（{$sts->getUnusedTrafficUsage()}）',
                    '总过去已用（{$sts->getLastTrafficUsage()}）',
                    '总今日已用（{$sts->getTodayTrafficUsage()}）'
                ],
                datasets: [{
                    label: '流量使用状态',
                    data: [
                        {$sts->getRawUnusedTrafficUsage()}, 
                        {$sts->getRawLastTrafficUsage()},
                        {$sts->getRawTodayTrafficUsage()}
                    ],
                    backgroundColor: [
                        'rgb(205, 180, 219)',
                        'rgb(255, 175, 204)',
                        'rgb(162, 210, 255)'
                    ]
                }]
            },
            options: {
                aspectRatio: 2,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        font: {
                            size: 14
                        }
                    },
                    title: {
                        display: true,
                        position: 'top',
                        text: '流量使用状态',
                        font: {
                            size: 14
                        }
                    }
                }
            }
        },
    );
</script>
