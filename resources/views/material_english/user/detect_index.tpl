


{include file='user/main.tpl'}







	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">Traffic Filtering Rules</h1>
			</div>
		</div>
		<div class="container">
			<div class="col-lg-12 col-md-12">
				<section class="content-inner margin-top-no">
					
					<div class="card">
						<div class="card-main">
							<div class="card-inner">
								<p>To ensure the continued availability of our services, we have set up these traffic filtering rules. Whenever a user triggers one of these rules, their connection will be cut.</p>
								<p>Privacy: We only check for the below traffic types. We do not in any way log your connection details, the websites you visit, or any other internet traffic information. Thank you for your understanding.</p>
							</div>
						</div>
					</div>
					
                    <div class="card">
	                    <div class="card-main">
		                    <div class="card-inner">
			                    <div class="card-table">
									<div class="table-responsive table-user">
										{$rules->render()}
										<table class="table">
											<tr>
												<th>ID</th>
												<th>Name</th>
												<th>Description</th>
											<th>Rules</th>
											<th>Type</th>
												
											</tr>
											{foreach $rules as $rule}
												<tr>
												<td>#{$rule->id}</td>
												<td>{$rule->name}</td>
												<td>{$rule->text}</td>
												<td>{$rule->regex}</td>
												{if $rule->type == 1}
													<td>Matching by data in plain text</td>
												{/if}		
												{if $rule->type == 2}
													<td>Matching by hex code</td>
												{/if}								
												</tr>
											{/foreach}
										</table>
										{$rules->render()}
									</div>
			                    </div>
		                    </div>
	                    </div>
                    </div>
					
							
			</div>
			
			
			
		</div>
	</main>






{include file='user/footer.tpl'}








