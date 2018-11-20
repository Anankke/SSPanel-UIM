




{include file='user/main.tpl'}







	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">邀请</h1>
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
										<p class="card-heading">说明</p>
										<p>您每邀请1位用户注册：</p>
										<p>您会获得<code>{$config["invite_gift"]} G</code>流量奖励。</p>
										<p>对方将获得<code>{$config["invite_get_money"]}</code>元奖励作为初始资金。</p>
										<p>对方充值时您还会获得对方充值金额的 <code>{$config["code_payback"]} %</code> 的返利。</p>
										
									</div>

								</div>
							</div>
						</div>
					</div>

					{if $user->class!=0}

					{if $user->invite_num!=-1}
					<div class="col-xx-12 col-lg-7">
						<div class="card margin-bottom-no card-invite">
							<div class="card-main">
								<div class="card-inner">
									<div class="card-inner">
										<p class="card-heading">邀请链接</p>
										<p>剩余可邀请次数：{if $user->invite_num<0}无限{else}<code>{$user->invite_num}</code>{/if}</p>
										<p>邀请链接请给认识的需要的人，邀请他人注册时，请将以下链接发给被邀请者</p>
										<div class="invite-link">
											<input type="text" class="input form-control form-control-monospace cust-link" name="input1" readonly="" value="{$config["baseUrl"]}/auth/register?code={$code->code}">
											<button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{$config["baseUrl"]}/auth/register?code={$code->code}">点击复制</button>
											<button class="reset-invitelink btn btn-subscription" type="button"><span class="icon">autorenew</span>&nbsp;重置邀请链接</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					{/if}

					{if $config['invite_price']>=0}
					<div class="col-xx-12 col-lg-5">
						<div class="card margin-bottom-no card-invite">
							<div class="card-main">
								<div class="card-inner">
									<div class="card-inner">
									<div class="cardbtn-edit">
											<div class="card-heading">购买邀请次数</div>
											<button class="btn btn-flat waves-attach" id="buy-invite"><span class="material-icons">shopping_cart</span></button>
									</div>		
										<p>邀请次数价格：<code>{$config['invite_price']}</code>元/个</p>
										<div class="form-group form-group-label">
											<label class="floating-label" for="buy-invite-num">在这输入购买次数</label>
											<input class="form-control maxwidth-edit" id="buy-invite-num" type="num">
										</div>
										<p class="card-heading">已获得返利：<code>{$paybacks_sum}</code> 元</p>
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
                                 	<p class="card-heading">邀请链接</p>
									<h3>{$user->user_name}，您不是VIP暂时无法使用邀请链接，<a href="/user/shop">成为VIP请点击这里</a></h3>
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
													<th>被邀请用户ID</th>
													<th>获得返利</th>
												</tr>
												{foreach $paybacks as $payback}
												<tr>
					
											  <!--       <td>#{$payback->id}</td> -->
													<td>{$payback->id}</td>
													<td>{$payback->userid}</td>
													<td>{$payback->ref_get} 元</td>
					
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
		$("#msg").html("已复制到您的剪贴板，请您继续接下来的操作。");
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
					$("#msg").html("发生错误：" + jqXHR.status);
                }
            })
        })
    })
</script>

<script>
{include file='table/js_1.tpl'}

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
        	$("#msg").html(data.msg+"     出现了一些错误。");
        }
    })
});

$(document).ready(function(){
 	{include file='table/js_2.tpl'}
});

</script>

<script>

$(".reset-invitelink").click(function () {
	$("#result").modal();
	$("#msg").html("已重置您的邀请链接，复制您的邀请链接发送给其他人！");
	window.setTimeout("location.href='/user/inviteurl_reset'", {$config['jump_delay']});
});

</script>
