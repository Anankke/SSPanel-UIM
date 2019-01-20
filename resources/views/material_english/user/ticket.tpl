


{include file='user/main.tpl'}







	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">Support Tickets</h1>
			</div>
		</div>
		<div class="container">
			<div class="col-lg-12 col-sm-12">
				<section class="content-inner margin-top-no">
					
					<div class="card">
						<div class="card-main">
							<div class="card-inner">
								<p>If you have any questions, please submit a new ticket.</p>
							</div>
						</div>
					</div>
					
					<div class="card">
						<div class="card-main">
							<div class="card-inner">
								<div class="card-table">
									<div class="table-responsive table-user">
										{$tickets->render()}
										<table class="table">
											<tr>
												
											  <!--  <th>ID</th>   -->
												<th>Datetime</th>
												<th>title</th>
												<th>Status</th>
											  <th>Action</th>
											</tr>
											{foreach $tickets as $ticket}
												<tr>
													
												 <!--   <td>#{$ticket->id}</td>  -->
													<td>{$ticket->datetime()}</td>
													<td>{$ticket->title}</td>
													{if $ticket->status==1}
													<td>Serving</td>
													{else}
													<td>Closed</td>
													{/if}
													 <td>
														<a class="btn btn-brand" href="/user/ticket/{$ticket->id}/view">View</a>
													</td>
												</tr>
											{/foreach}
										</table>
										{$tickets->render()}
									</div>
								</div>
							</div>
						</div>
					</div>
					
					
					<div class="fbtn-container">
						<div class="fbtn-inner">
							<a class="fbtn fbtn-lg fbtn-brand-accent" href="/user/ticket/create">+</a>
							
						</div>
					</div>

							
			</div>
			
			
			
		</div>
	</main>






{include file='user/footer.tpl'}










