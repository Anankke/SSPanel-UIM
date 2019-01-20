


{include file='user/main.tpl'}







	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">My Account</h1>
			</div>
		</div>
		<div class="container">
			<section class="content-inner margin-top-no">
				<div class="row">
					<div class="col-lg-12 col-md-12">
						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
									<div class="card-inner">
									{if $config['enable_kill']=="true"}
									    <div class="cardbtn-edit">
												<div class="card-heading">My Account</div>
											    <div class="account-flex"><span>Delete my account</span><a class="btn btn-flat" href="kill"><span class="icon">not_interested</span>&nbsp;</a></div>
										</div>
									{/if}
										<dl class="dl-horizontal">
											<dt>Username</dt>
											<dd>{$user->user_name}</dd>
											<dt>E-mail</dt>
											<dd>{$user->email}</dd>
										</dl>
									</div>
								</div>
							</div>
						</div>

						<div class="card">
							<div class="card-main">
								<div class="card-inner">
									<div class="card-doubleinner">
											<p class="card-heading">Connected IPs in the last 5 minutes</p>
											<p>Please confirm that the IPs are yours, if you notice anything abnormal, please modify your password.</p>
									</div>
									<div class="card-table">
										<div class="table-responsive table-user">
											<table class="table table-fixed">
												<tr>

													<th>IP</th>
													<th>Location</th>
												</tr>
												{foreach $userip as $single=>$location}
													<tr>

														<td>{$single}</td>
														<td>{$location}</td>
													</tr>
												{/foreach}
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>


						<div class="card">
							<div class="card-main">
								<div class="card-inner">
									<div class="card-doubleinner">
											<p class="card-heading">Last 10 login IPs</p>
											<p>Please confirm that the IPs are yours, if you notice anything abnormal, please modify your password.</p>
									</div>
									<div class="card-table">
										<div class="table-responsive table-user">
											<table class="table table-fixed">
												<tr>

													<th>IP</th>
													<th>Location</th>
												</tr>
												{foreach $userloginip as $single=>$location}
													<tr>

														<td>{$single}</td>
														<td>{$location}</td>
													</tr>
												{/foreach}
											</table>
										</div>
									</div>
								</div>

							</div>
						</div>



						<div class="card">
							<div class="card-main">
								<div class="card-inner">
                                        <div class="card-doubleinner">
												<p class="card-heading">Rebate records</p>
										</div>
										
										<div class="card-table">
											<div class="table-responsive table-user">
											{$paybacks->render()}
												<table class="table">
													<thead>
													<tr>
														<th>###</th>
														<th>Rebate user</th>
														<th>Rebate Amount</th>
													</tr>
													</thead>
													<tbody>
													{foreach $paybacks as $payback}
														<tr>
															<td><b>{$payback->id}</b></td>
															{if $payback->user()!=null}
																<td>{$payback->user()->user_name}
																</td>
																{else}
																<td>Canceled
																</td>
															{/if}
															</td>
															<td>{$payback->ref_get} CNY</td>
														</tr>
													{/foreach}
													</tbody>
												</table>
											{$paybacks->render()}
											</div>
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
