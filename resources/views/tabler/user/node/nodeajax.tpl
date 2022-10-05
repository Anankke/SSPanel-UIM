{$load=$point_node->getNodeLoad()}

<div id="load{$id}_chart" style="height: 300px; width: 100%;"></div>
<div id="up{$id}_chart" style="height: 300px; width: 100%;"></div>
<div id="alive{$id}_chart" style="height: 300px; width: 100%;"></div>

<script type="text/javascript">
    $().ready(function () {
        chart{$id} = new CanvasJS.Chart("load{$id}_chart",
                {
                    title: {
                        text: "节点负载情况 {$prefix}"
                    },
                    data: [
                        {
                            type: "line",
                            dataPoints: [
                                {$i=0}
                                {foreach $load as $single_load}
                                {if $i==0}
                                {literal}
                                {
                                    {/literal}
                                    x: new Date({$single_load->log_time*1000}), y:{$single_load->getNodeLoad()}
                                    {literal}
                                }
                                {/literal}
                                {$i=1}
                                {else}
                                {literal}
                                , {
                                    {/literal}
                                    x: new Date({$single_load->log_time*1000}), y:{$single_load->getNodeLoad()}
                                    {literal}
                                }
                                {/literal}
                                {/if}
                                {/foreach}
                            ]
                        }
                    ]
                });
        up_chart{$id} = new CanvasJS.Chart("up{$id}_chart",
                {
                    title: {
                        text: "最近一天节点在线情况 {$prefix} - 在线 {$point_node->getNodeUptime()}"
                    },
                    data: [
                        {
                            //startAngle: 45,
                            indexLabelFontSize: 20,
                            indexLabelFontFamily: "Garamond",
                            indexLabelFontColor: "darkgrey",
                            indexLabelLineColor: "darkgrey",
                            yValueFormatString: "##0.00\"%\"",
                            indexLabelPlacement: "outside",
                            type: "doughnut",
                            showInLegend: true,
                            dataPoints: [
                                {
                                    y: {$point_node->getNodeUpRate()*100},
                                    label: "在线率",
                                    legendText: "在线率 {number_format($point_node->getNodeUpRate()*100,2)}%",
                                    indexLabel: "在线率 {number_format($point_node->getNodeUpRate()*100,2)}%"
                                },
                                {
                                    y: {(1-$point_node->getNodeUpRate())*100},
                                    label: "离线率",
                                    legendText: "离线率 {number_format((1-$point_node->getNodeUpRate())*100,2)}%",
                                    indexLabel: "离线率 {number_format((1-$point_node->getNodeUpRate())*100,2)}%"
                                }
                            ]
                        }
                    ]
                });
        {$load=$point_node->getNodeAlive()}
        alive_chart{$id} = new CanvasJS.Chart("alive{$id}_chart",
                {
                    title: {
                        text: "最近一天节点在线人数情况 {$prefix}"
                    },
                    data: [
                        {
                            type: "line",
                            yValueFormatString: "##0\"人\"",
                            dataPoints: [
                                {$i=0}
                                {foreach $load as $single_load}
                                {if $i==0}
                                {literal}
                                {
                                    {/literal}
                                    x: new Date({$single_load->log_time*1000}),
                                    y:{$single_load->online_user},
                                    label: "同时在线人数"
                                    {literal}
                                }
                                {/literal}
                                {$i=1}
                                {else}
                                {literal}
                                , {
                                    {/literal}
                                    x: new Date({$single_load->log_time*1000}),
                                    y:{$single_load->online_user},
                                    label: "同时在线人数"
                                    {literal}
                                }
                                {/literal}
                                {/if}
                                {/foreach}
                            ]
                        }
                    ]
                });
        chart{$id}.render();
        up_chart{$id}.render();
        alive_chart{$id}.render();
    });
</script>
