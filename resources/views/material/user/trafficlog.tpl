{include file='user/main.tpl'}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">流量记录</h1>
        </div>
    </div>
    <div class="container">
        <section class="content-inner margin-top-no">
            <div class="ui-card-wrap">
                <div class="row">
                    <div class="col-lg-12 col-sm-12">
                        <div class="card">
                            <div class="card-main">
                                <div class="card-inner margin-bottom-no">
                                    <p class="card-heading">注意!</p>
                                    <p>部分节点不支持流量记录.</p>
                                    <p>此处只展示最近 72 小时的记录，粒度为分钟。</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-sm-12">
                        <div class="card">
                            <div class="card-main">
                                <div class="card-inner">
                                    <div id="log_chart" style="height: 300px; width: 100%;"></div>
                                    <script src="//cdn.jsdelivr.net/gh/M1Screw/canvasjs.js@v3.2/canvasjs.min.js"></script>
                                    <script type="text/javascript">
                                        window.onload = function () {
                                            var log_chart = new CanvasJS.Chart("log_chart",
                                                    {
                                                        zoomEnabled: true,
                                                        title: {
                                                            text: "您的最近72小时流量消耗",
                                                            fontSize: 20
                                                        },
                                                        animationEnabled: true,
                                                        axisX: {
                                                            title: "时间",
                                                            labelFontSize: 14,
                                                            titleFontSize: 18
                                                        },
                                                        axisY: {
                                                            title: "流量/KB",
                                                            lineThickness: 2,
                                                            labelFontSize: 14,
                                                            titleFontSize: 18
                                                        },
                                                        data: [
                                                            {
                                                                type: "scatter",
                                                                {literal}
                                                                toolTipContent: "<span style='\"'color: {color};'\"'><strong>产生时间: </strong></span>{x} <br/><span style='\"'color: {color};'\"'><strong>流量: </strong></span>{y} KB <br/><span style='\"'color: {color};'\"'><strong>产生节点: </strong></span>{jd}",
                                                                {/literal}
                                                                dataPoints: [
                                                                    {$i=0}
                                                                    {foreach $logs as $single_log}
                                                                    {if $i==0}
                                                                    {literal}
                                                                    {
                                                                        {/literal}
                                                                        x: new Date({$single_log->log_time*1000}),
                                                                        y:{$single_log->totalUsedRaw()},
                                                                        jd: "{$single_log->node()->name}"
                                                                        {literal}
                                                                    }
                                                                    {/literal}
                                                                    {$i=1}
                                                                    {else}
                                                                    {literal}
                                                                    , {
                                                                        {/literal}
                                                                        x: new Date({$single_log->log_time*1000}),
                                                                        y:{$single_log->totalUsedRaw()},
                                                                        jd: "{$single_log->node()->name}"
                                                                        {literal}
                                                                    }
                                                                    {/literal}
                                                                    {/if}
                                                                    {/foreach}
                                                                ]
                                                            }
                                                        ]
                                                    });
                                            log_chart.render();
                                        }
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

{include file='user/footer.tpl'}