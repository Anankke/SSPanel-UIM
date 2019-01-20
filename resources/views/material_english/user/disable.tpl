{include file='user/main.tpl'}
	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">NO DATA</h1>
			</div>
		</div>
		<div class="container">
			<section class="content-inner margin-top-no">
				<div class="ui-card-wrap">
						
						<div class="col-lg-12 col-md-12">
							<section class="content-inner margin-top-no">
							
							<div class="card">
								<div class="card-main">
									<div class="card-inner">
										<p>You have been restricted from using our services. Please contact the administrator for more info.</p>
										{if $config["enable_admin_contact"] == 'true'}
												<p>Administrator contact information:</p>
												{if $config["admin_contact1"]!=null}
												<li>{$config["admin_contact1"]}</li>
												{/if}
												{if $config["admin_contact2"]!=null}
												<li>{$config["admin_contact2"]}</li>
												{/if}
												{if $config["admin_contact3"]!=null}
												<li>{$config["admin_contact3"]}</li>
												{/if}
											{/if}
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


