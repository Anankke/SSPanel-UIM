{include file='user/main.tpl'}

	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">Invitation Links</h1>
			</div>
		</div>
		<div class="container">
			<section class="content-inner margin-top-no">
				<div class="row">

					<div class="col-xx-12">
						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
									<div class="card-inner">
										<p class="card-heading">Description</p>
										<p>Every time you invite 1 user to register:</p>
										<p>You will get a <code>{$config["invite_gift"]} G</code> traffic reward.</p>
										<p>The new user will receive a <code>{$config["invite_get_money"]}</code> CNY reward.</p>
										<p>Each time someone you invited recharges their account, you will get <code>{$config["code_payback"]} %</code> of the amount as a reward.</p>
										<p class="card-heading">Received rebate: <code>{$paybacks_sum}</code> CNY</p>
									</div>

								</div>
							</div>
						</div>
					</div>

					{if $user->class!=0}

					{if $user->invite_num!=-1}
					<div class="col-xx-12">
						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner margin-bottom-no">
									<div class="card-inner margin-bottom-no">
											<div class="cardbtn-edit">
													<div class="card-heading">Invitation Links</div>
													<div class="reset-flex"><span>Reset links</span><a class="reset-link btn btn-brand-accent btn-flat" ><i class="icon">autorenew</i>&nbsp;</a></div>
											</div>
										<p>Remaining invites: {if $user->invite_num<0}Unlimited{else}<code>{$user->invite_num}</code>{/if}</p>
										<p>Send invitation links to someone in need, invite others to sign up.</p>
										<div class="invite-link">
											<input type="text" class="input form-control form-control-monospace cust-link" name="input1" readonly="" value="{$config["baseUrl"]}/auth/register?code={$code->code}">
											<button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{$config["baseUrl"]}/auth/register?code={$code->code}">copy</button>				
										</div>
										<div class="invite-link">
											<input type="text" class="input form-control form-control-monospace cust-link" name="input2" readonly="" value="{$config["baseUrl"]}/#/auth/register?code={$code->code}">
											<button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{$config["baseUrl"]}/#/auth/register?code={$code->code}">copy</button>				
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					{if $config['custom_invite_price']>=0}
					<div class="col-xx-12 {if $config['invite_price']>=0}col-lg-6{/if}">
							<div class="card margin-bottom-no">
								<div class="card-main">
									<div class="card-inner margin-bottom-no">
										<div class="card-inner margin-bottom-no">
												<div class="cardbtn-edit">
													<div class="card-heading">Custom link suffix <code class="card-tag tag-green">{$config['custom_invite_price']} CNY</code></div>
													<button class="btn btn-flat" id="custom-invite-confirm"><span class="icon">check</span>&nbsp;</button>
												</div>
											<p>Example: Imput<code>vip</code>, then links change to<code>{$config["baseUrl"]}/auth/register?code=vip</code></p>
											<div class="form-group form-group-label">
												<label class="floating-label" for="custom-invite-link">Input suffix</label>
												<input class="form-control maxwidth-edit" id="custom-invite-link" type="num">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						{/if}
					{/if}

					{if $config['invite_price']>=0}
					<div class="col-xx-12 {if $config['custom_invite_price']>=0}col-lg-6{/if}">
						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
									<div class="card-inner">
									<div class="cardbtn-edit">
											<div class="card-heading">Purchase invitations <code class="card-tag tag-green">{$config['invite_price']} CNY</code></div>
											<button class="btn btn-flat" id="buy-invite"><span class="material-icons">shopping_cart</span></button>
									</div>		
										<p>Enter the number of invitations you need to purchase below</p>
										<div class="form-group form-group-label">
											<label class="floating-label" for="buy-invite-num">Input number</label>
											<input class="form-control maxwidth-edit" id="buy-invite-num" type="num">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					{/if}

                   	{else}

                  	<div class="col-xx-12">
						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
                                 	<p class="card-heading">Invitation Links</p>
									<h3>{$user->user_name}, you are not a VIP unable to use the invitation links.<a href="/user/shop">Click here to become a VIP</a></h3>
								</div>
							</div>
						</div>
					</div>

					{/if}
					<div class="col-xx-12">
                        <div class="card">
	                        <div class="card-main">
		                        <div class="card-inner">
			                        <div class="card-table">
										<div class="table-responsive bgc-fix table-user">
											{$paybacks->render()}
											<table class="table">
												<tr>
					
												 <!--   <th>ID</th> -->
													<th>ID</th>
													<th>Invited User ID</th>
													<th>Rebate</th>
												</tr>
												{foreach $paybacks as $payback}
												<tr>
					
											  <!--       <td>#{$payback->id}</td> -->
													<td>{$payback->id}</td>
													<td>{$payback->userid}</td>
													<td>{$payback->ref_get} CNY</td>
					
												</tr>
												{/foreach}
											</table>
											{$paybacks->render()}
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
	$(function(){
		new Clipboard('.copy-text');
	});

	$(".copy-text").click(function () {
		$("#result").modal();
		$("#msg").html("It has been copied to your clipboard, please proceed to the next step.");
	});

    $(document).ready(function () {
        $("#invite").click(function () {
            $.ajax({
                type: "POST",
                url: "/user/invite",
                dataType: "json",
                success: function (data) {
                    window.location.reload();
                },
                error: function (jqXHR) {
                    $("#result").modal();
					$("#msg").html("Error:" + jqXHR.status);
                }
            })
        })
    })
</script>

<script>

$("#buy-invite").click(function () {
    $.ajax({
        type: "POST",
        url: "/user/buy_invite",
        dataType: "json",
        data: {
            num: $("#buy-invite-num").val(),
        },
        success: function (data) {
             if (data.ret) {
     			$("#result").modal();
				$("#msg").html(data.msg);
				window.setTimeout("location.href='/user/invite'", {$config['jump_delay']});
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
});

$("#custom-invite-confirm").click(function () {
    $.ajax({
        type: "POST",
        url: "/user/custom_invite",
        dataType: "json",
        data: {
            customcode: $("#custom-invite-link").val(),
        },
        success: function (data) {
             if (data.ret) {
     			$("#result").modal();
				$("#msg").html(data.msg);
				window.setTimeout("location.href='/user/invite'", {$config['jump_delay']});
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
});

</script>

<script>

$(".reset-link").click(function () {
	$("#result").modal();
	$("#msg").html("Your invitation link has been reset, copy your invitation link and send it to others!");
	window.setTimeout("location.href='/user/inviteurl_reset'", {$config['jump_delay']});
});

</script>