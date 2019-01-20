{$load=$point_node->getNodeLoad()}

<div id="load{$id}_chart" style="height: 300px; width: 100%;"></div>
	<div id="up{$id}_chart" style="height: 300px; width: 100%;"></div>
	<div id="alive{$id}_chart" style="height: 300px; width: 100%;"></div>
	<div id="speedtest{$id}_chart" style="height: 300px; width: 100%;"></div>
	<div id="speedtest{$id}_ping_chart" style="height: 300px; width: 100%;"></div>
				
	<script type="text/javascript">
		$().ready(function(){
			chart{$id} = new CanvasJS.Chart("load{$id}_chart",
			{
				title:{
					text: "Server load {$prefix}"
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
								,{
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
				title:{
					text: "Server uptime during the last 24 hours for {$prefix} - Online for {$point_node->getNodeUptime()}"
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
								y: {$point_node->getNodeUpRate()*100}, label: "Online rate",legendText:"Online rate {number_format($point_node->getNodeUpRate()*100,2)}%", indexLabel: "Online rate {number_format($point_node->getNodeUpRate()*100,2)}%"
							},
							{
								y: {(1-$point_node->getNodeUpRate())*100}, label: "Offline rate",legendText:"Offline rate {number_format((1-$point_node->getNodeUpRate())*100,2)}%", indexLabel: "Offline rate {number_format((1-$point_node->getNodeUpRate())*100,2)}%"
							}
						]
					}
					]
			});
			
			{$load=$point_node->getNodeAlive()}
			alive_chart{$id} = new CanvasJS.Chart("alive{$id}_chart",
			{
				title:{
					text: "Number of connected users during the last 24 hours for {$prefix}"
				},
				data: [
				{
					type: "line", 
                  yValueFormatString: "##0\"user\"",
					dataPoints: [
						{$i=0}
						{foreach $load as $single_load}
							{if $i==0}
								{literal}
								{
								{/literal}
									x: new Date({$single_load->log_time*1000}), y:{$single_load->online_user},label: "Number of connected users at the same time"
								{literal}
								}
								{/literal}
								{$i=1}
							{else}
								{literal}
								,{
								{/literal}
									x: new Date({$single_load->log_time*1000}), y:{$single_load->online_user},label: "Number of connected users at the same time"
								{literal}
								}
								{/literal}
							{/if}
						{/foreach}
						
					]
				}
				]
			});
			
			
			
			{$speedtests=$point_node->getSpeedtestResult()}
			speedtest_chart{$id} = new CanvasJS.Chart("speedtest{$id}_chart",
			{
				title:{
					text: "Ping from major ISPs {$prefix}"
				},
				axisY: {				
					suffix: " ms"
				},
				data: [
				{
					type: "line", 
					showInLegend: true,
					legendText: "China Telecom ping",
                    yValueFormatString: "##0\"ms\"",
					dataPoints: [
						{$i=0}
						{foreach $speedtests as $single_speedtest}
							{if $i==0}
								{literal}
								{
								{/literal}
									x: new Date({$single_speedtest->datetime*1000}), y:{$single_speedtest->getTelecomPing()},label: "China Telecom ping"
								{literal}
								}
								{/literal}
								{$i=1}
							{else}
								{literal}
								,{
								{/literal}
									x: new Date({$single_speedtest->datetime*1000}), y:{$single_speedtest->getTelecomPing()},label: "China Telecom ping"
								{literal}
								}
								{/literal}
							{/if}
						{/foreach}
						
					]
				},
				{
					type: "line", 
					showInLegend: true,
					legendText: "China Unicom ping",
                  yValueFormatString: "##0\"ms\"",
					dataPoints: [
						{$i=0}
						{foreach $speedtests as $single_speedtest}
							{if $i==0}
								{literal}
								{
								{/literal}
									x: new Date({$single_speedtest->datetime*1000}), y:{$single_speedtest->getUnicomPing()},label: "China Unicom ping"
								{literal}
								}
								{/literal}
								{$i=1}
							{else}
								{literal}
								,{
								{/literal}
									x: new Date({$single_speedtest->datetime*1000}), y:{$single_speedtest->getUnicomPing()},label: "China Unicom ping"
								{literal}
								}
								{/literal}
							{/if}
						{/foreach}
						
					]
				},
				{
					type: "line", 
					showInLegend: true,
					legendText:"China Mobile ping",
                  yValueFormatString: "##0\"ms\"",
					dataPoints: [
						{$i=0}
						{foreach $speedtests as $single_speedtest}
							{if $i==0}
								{literal}
								{
								{/literal}
									x: new Date({$single_speedtest->datetime*1000}), y:{$single_speedtest->getCmccPing()},label: "China Mobile ping"
								{literal}
								}
								{/literal}
								{$i=1}
							{else}
								{literal}
								,{
								{/literal}
									x: new Date({$single_speedtest->datetime*1000}), y:{$single_speedtest->getCmccPing()},label: "China Mobile ping"
								{literal}
								}
								{/literal}
							{/if}
						{/foreach}
						
					]
				}
				]
			});
			
			speedtest_ping_chart{$id} = new CanvasJS.Chart("speedtest{$id}_ping_chart",
			{
				title:{
					text: "Connection ​​Speed {$prefix}"
				},
				axisY: {
					includeZero: false,
					suffix: " Mbps"
					},
              	toolTip:{
	     			shared: true
	                   },
				data: [
				{
					type: "line", 
					showInLegend: true,
					legendText: "China Telecom upload speed",
                  	name: "China Telecom upload",
                  yValueFormatString: "##0.00\"Mb\"",
					dataPoints: [
						{$i=0}
						{foreach $speedtests as $single_speedtest}
							{if $i==0}
								{literal}
								{
								{/literal}
									x: new Date({$single_speedtest->datetime*1000}), y:{$single_speedtest->getTelecomUpload()},label: "China Telecom upload"
								{literal}
								}
								{/literal}
								{$i=1}
							{else}
								{literal}
								,{
								{/literal}
									x: new Date({$single_speedtest->datetime*1000}), y:{$single_speedtest->getTelecomUpload()},label: "China Telecom upload"
								{literal}
								}
								{/literal}
							{/if}
						{/foreach}
						
					]
				},
				{
					type: "line", 
					showInLegend: true,
					legendText: "China Telecom download speed",
                  name: "China Telecom download",
                  yValueFormatString: "##0.00\"Mb\"",
					dataPoints: [
						{$i=0}
						{foreach $speedtests as $single_speedtest}
							{if $i==0}
								{literal}
								{
								{/literal}
									x: new Date({$single_speedtest->datetime*1000}), y:{$single_speedtest->getTelecomDownload()},label: "China Telecom download"
								{literal}
								}
								{/literal}
								{$i=1}
							{else}
								{literal}
								,{
								{/literal}
									x: new Date({$single_speedtest->datetime*1000}), y:{$single_speedtest->getTelecomDownload()},label: "China Telecom download"
								{literal}
								}
								{/literal}
							{/if}
						{/foreach}
						
					]
				},
				{
					type: "line", 
					showInLegend: true,
					legendText: "China Unicom upload speed",
                  name: "China Unicom upload",
                  yValueFormatString: "##0.00\"Mb\"",
					dataPoints: [
						{$i=0}
						{foreach $speedtests as $single_speedtest}
							{if $i==0}
								{literal}
								{
								{/literal}
									x: new Date({$single_speedtest->datetime*1000}), y:{$single_speedtest->getUnicomUpload()},label: "China Unicom upload"
								{literal}
								}
								{/literal}
								{$i=1}
							{else}
								{literal}
								,{
								{/literal}
									x: new Date({$single_speedtest->datetime*1000}), y:{$single_speedtest->getUnicomUpload()},label: "China Unicom upload"
								{literal}
								}
								{/literal}
							{/if}
						{/foreach}
						
					]
				},
				{
					type: "line", 
					showInLegend: true,
					legendText: "China Unicom download speed",
                  name: "China Unicom download",
                  yValueFormatString: "##0.00\"Mb\"",
					dataPoints: [
						{$i=0}
						{foreach $speedtests as $single_speedtest}
							{if $i==0}
								{literal}
								{
								{/literal}
									x: new Date({$single_speedtest->datetime*1000}), y:{$single_speedtest->getUnicomDownload()},label: "China Unicom download"
								{literal}
								}
								{/literal}
								{$i=1}
							{else}
								{literal}
								,{
								{/literal}
									x: new Date({$single_speedtest->datetime*1000}), y:{$single_speedtest->getUnicomDownload()},label: "China Unicom download"
								{literal}
								}
								{/literal}
							{/if}
						{/foreach}
						
					]
				},
				{
					type: "line", 
					showInLegend: true,
					legendText:"China Mobile download speed",
                  name: "China Mobile download",
                  yValueFormatString: "##0.00\"Mb\"",
					dataPoints: [
						{$i=0}
						{foreach $speedtests as $single_speedtest}
							{if $i==0}
								{literal}
								{
								{/literal}
									x: new Date({$single_speedtest->datetime*1000}), y:{$single_speedtest->getCmccDownload()},label: "China Mobile download"
								{literal}
								}
								{/literal}
								{$i=1}
							{else}
								{literal}
								,{
								{/literal}
									x: new Date({$single_speedtest->datetime*1000}), y:{$single_speedtest->getCmccDownload()},label: "China Mobile download"
								{literal}
								}
								{/literal}
							{/if}
						{/foreach}
						
					]
				},
				{
					type: "line", 
					showInLegend: true,
					legendText:"China Mobile upload speed",
                  name: "China Mobile upload",
                  yValueFormatString: "##0.00\"Mb\"",
					dataPoints: [
						{$i=0}
						{foreach $speedtests as $single_speedtest}
							{if $i==0}
								{literal}
								{
								{/literal}
									x: new Date({$single_speedtest->datetime*1000}), y:{$single_speedtest->getCmccUpload()},label: "China Mobile upload"
								{literal}
								}
								{/literal}
								{$i=1}
							{else}
								{literal}
								,{
								{/literal}
									x: new Date({$single_speedtest->datetime*1000}), y:{$single_speedtest->getCmccUpload()},label: "China Mobile upload"
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
			speedtest_chart{$id}.render();
			speedtest_ping_chart{$id}.render();
			
			
		});
		
		
		
		
			
	</script>
