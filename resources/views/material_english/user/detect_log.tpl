


{include file='user/main.tpl'}







	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">Filtering Logs</h1>
			</div>
		</div>
		<div class="container">
			<div class="col-lg-12 col-md-12">
				<section class="content-inner margin-top-no">
					
					<div class="card">
						<div class="card-main">
							<div class="card-inner">
								<p>All logs</p>
								<p>Privacy: We only check for the below traffic types. We do not in any way log your connection details, the websites you visit, or any other internet traffic information. Thank you for your understanding.</p>
							</div>
						</div>
					</div>
					

					<div class="card">
						<div class="card-main">
							<div class="card-inner">
								<div class="card-table">
									<div class="table-responsive table-user">
										{$logs->render()}
										<table class="table">
											<tr>
												<th>ID</th>
												<th>Server ID</th>
												<th>Server Name</th>
												<th>Rule ID</th>
												<th>Name</th>
												<th>Description</th>
											<th>Rules</th>
											<th>Type</th>
											<th>Time</th>
												
											</tr>
											{foreach $logs as $log}
												{if $log->DetectRule() != null}
													<tr>
													<td>#{$log->id}</td>
													<td>{$log->node_id}</td>
													<td>{$log->Node()->name}</td>
													<td>{$log->list_id}</td>
													<td>{$log->DetectRule()->name}</td>
													<td>{$log->DetectRule()->text}</td>
													<td>{$log->DetectRule()->regex}</td>
													{if $log->DetectRule()->type == 1}
														<td>Matching by data in plain text</td>
													{/if}		
													{if $log->DetectRule()->type == 2}
														<td>Matching by hex code</td>
													{/if}
													<td>{date('Y-m-d H:i:s',$log->datetime)}</td>						
													</tr>
												{/if}
											{/foreach}
										</table>
										{$logs->render()}
									</div>				
								</div>
							</div>
						</div>
					</div>
					
					

							
			</div>
			
			
			
		</div>
	</main>






{include file='user/footer.tpl'}








