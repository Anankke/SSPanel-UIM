








{include file='user/main.tpl'}







	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">Donations</h1>
			</div>
		</div>
		<div class="container">
			<section class="content-inner margin-top-no">
				<div class="row">
				

					<div class="col-lg-12 col-md-12">
						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
									<p>Thank you for donating to support the daily expenses of the servers! You can also recharge your account <a href="/user/code">here</a>, which is equivalent to donating.</p>
									{if $user->isAdmin()}
									<p>Total donated so far: {$total_in} CNY</p>
									{/if}
								
								</div>
							</div>
						</div>
					</div>
				
					<div class="col-lg-12 col-md-12">
						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
									<div class="card-inner">
										<p class="card-heading">Anonymous donations</p>
										<p>Current settings:<code data-default="hide">{if $user->is_hide==1} Anonymous {else} Not anonymous {/if}</code></p>
										<div class="form-group form-group-label control-highlight-custom dropdown">
											<label class="floating-label" for="hide">Anonymous settings</label>
											<button id="hide" class="form-control maxwidth-edit" data-toggle="dropdown" value="{$user->is_hide}">
												
											</button>
											<ul class="dropdown-menu" aria-labelledby="hide">
												<li><a href="#" class="dropdown-option" onclick="return false;" val="1" data="hide">Anonymous</a> </li>
												<li><a href="#" class="dropdown-option" onclick="return false;" val="0" data="hide">Not anonymous</a></li>
											</ul>
										</div>
										
									</div>
									<div class="card-action">
										<div class="card-action-btn pull-left">
											<button class="btn btn-flat waves-attach" id="hide-update" ><span class="icon">check</span>&nbsp;submit</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					
					<div class="col-lg-12 col-md-12">
						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
								
										<div class="card-table">
											<div class="table-responsive table-user">
												{$codes->render()}
												<table class="table table-hover">
													<tr>
														<th>ID</th>
														<th>Username</th>
														<th>Type</th>
														<th>Action</th>
														<th>Code</th>
														<th>Date</th>
														
													</tr>
													{foreach $codes as $code}
														<tr>
															<td>#{$code->id}</td>
															{if $code->user() != null && $code->user()->is_hide == 0}
															<td>{$code->user()->user_name}</td>
															{else}
															<td>Anonymous</td>
															{/if}
															{if $code->type == -1}
															<td>Recharge Account</td>
															{/if}
															{if $code->type == -2}
															<td>Donations</td>
															{/if}
															{if $code->type == -1}
															<td>Recharged {$code->number} CNY</td>
															{/if}
															{if $code->type == -2}
															<td>Total donated {$code->number} CNY</td>
															{/if}
															<td>{$code->code}</td>
															<td>{$code->usedatetime}</td>
														</tr>
													{/foreach}
												</table>
												{$codes->render()}
											</div>
										</div>
							
									
								</div>
							</div>
						</div>
					</div>
					
					{include file='dialog.tpl'}
				</div>
			</section>
		</div>
	</main>







{include file='user/footer.tpl'}

<script>
    $(document).ready(function () {
        $("#hide-update").click(function () {
            $.ajax({
                type: "POST",
                url: "hide",
                dataType: "json",
                data: {
                    hide: $("#hide").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#result").modal();
						$("#msg").html(data.msg);
                    } else {
                        $("#result").modal();
						$("#msg").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    $("#result").modal();
					$("#msg").html(data.msg+"     An error occurred.");
                }
            })
        })
    })
</script>
