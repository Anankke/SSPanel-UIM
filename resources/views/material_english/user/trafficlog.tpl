





{include file='user/main.tpl'}





	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">Data Usage</h1>
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
										<p class="card-heading">Attention!</p>
										<p>Some servers do not support data logging.</p>
										<p>Only the last 72 hours of records are shown here, with a granularity of minutes.</p>
									</div>
									
								</div>
							</div>
						</div>
						
						<div class="col-lg-12 col-sm-12">
							<div class="card">
								<div class="card-main">
									<div class="card-inner">
										<div id="log_chart" style="height: 300px; width: 100%;"></div>
                                      
										<script src="/assets/js/canvasjs.min.js"> </script>
											
										<script type="text/javascript">
											window.onload = function () {
												var log_chart = new CanvasJS.Chart("log_chart",
												{
													zoomEnabled: true,
													title:{
														text: "Your last 72 hours of data usage",
														fontSize: 20
														
													},  
													animationEnabled: true,
													axisX: {
														title:"Time",
														labelFontSize: 14,
														titleFontSize: 18
													},
													axisY:{
														title: "Flow/KB",
														lineThickness: 2,
														labelFontSize: 14,
														titleFontSize: 18
													},

													data: [
													{        
														type: "scatter", 
														{literal}														
														toolTipContent: "<span style='\"'color: {color};'\"'><strong>Generate time: </strong></span>{x} <br/><span style='\"'color: {color};'\"'><strong>Flow: </strong></span>{y} KB <br/><span style='\"'color: {color};'\"'><strong>Generate node: </strong></span>{jd}",
														{/literal}
														
														dataPoints: [
														
														
														{$i=0}
														{foreach $logs as $single_log}
															{if $i==0}
																{literal}
																{
																{/literal}
																	x: new Date({$single_log->log_time*1000}), y:{$single_log->totalUsedRaw()},jd:"{$single_log->node()->name}"
																{literal}
																}
																{/literal}
																{$i=1}
															{else}
																{literal}
																,{
																{/literal}
																	x: new Date({$single_log->log_time*1000}), y:{$single_log->totalUsedRaw()},jd:"{$single_log->node()->name}"
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
