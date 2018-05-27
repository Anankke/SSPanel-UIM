




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

					<div class="col-lg-12 col-md-12">
						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
									<div class="card-inner">
										<p class="card-heading">说明</p>
										<p>您每邀请一位用户注册，对方充值时您就会获得对方充值金额的 <code>{$config["code_payback"]} %</code> 的提成。</p>
										<p class="card-heading">已获得返利：<code>{$paybacks_sum}</code> 元</p>
										<p class="card-heading">当前余额：<code>{$user->money}</code> 元</p>
									</div>

								</div>
							</div>
						</div>
					</div>

					{if $user->class!=0}

					{if $user->invite_num!=-1}
					<div class="col-lg-12 col-md-12">
						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
									<div class="card-inner">
										<p class="card-heading">注意！</p>
										<p>邀请链接请给认识的需要的人。</p>
										<p>剩余可邀请次数：{if $user->invite_num<0}无限{else}<code>{$user->invite_num}</code>{/if}</p>
										<p><a class="btn btn-brand" data-toggle="modal" data-target="#buyCode_modal">购买邀请次数</a></p>
									</div>
								</div>
							</div>
						</div>
					</div>
					{/if}

					<div aria-hidden="true" class="modal modal-va-middle fade" id="buyCode_modal" role="dialog" tabindex="-1">
						<div class="modal-dialog modal-xs">
							<div class="modal-content">
							
								<div class="modal-heading">
									<a class="modal-close" data-dismiss="modal">×</a>
									<h2 class="modal-title">是否花费2元余额购买邀请码？</h2>
								</div>
								
								<div class="modal-inner">
									<p>邀请次数+1</p>
								</div>
								
								<div class="modal-footer">
									<p class="text-right"><a class="btn btn-flat btn-brand-accent waves-attach" data-toggle="modal" id="buyCode_btn">确定</a></p>
								</div>
							</div>
						</div>
					</div>
					
					<div aria-hidden="true" class="modal modal-va-middle fade" id="buyCode_success_modal" role="dialog" tabindex="-1">
						<div class="modal-dialog modal-xs">
							<div class="modal-content">
							
								<div class="modal-inner">
									<p>购买成功!</p>
								</div>
								
								<div class="modal-footer">
									<p class="text-right"><button class="btn btn-flat btn-brand-accent waves-attach" data-dismiss="modal" type="button" onClick="window.location.reload();">确定</button></p>
								</div>
							</div>
						</div>
					</div>
					
					
					<div aria-hidden="true" class="modal modal-va-middle fade" id="buyCode_fail_modal" role="dialog" tabindex="-1">
						<div class="modal-dialog modal-xs">
							<div class="modal-content">
							
								<div class="modal-inner">
									<p>余额不足请先充值!</p>
								</div>
								
								<div class="modal-footer">
									<p class="text-right"><button class="btn btn-flat btn-brand-accent waves-attach" data-dismiss="modal" type="button" onClick="window.location.reload();">确定</button></p>
								</div>
							</div>
						</div>
					</div>
					
                  	<div class="col-lg-12 col-md-12">
						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
									<div class="card-inner">
												<p class="card-heading">邀请链接</p>
												<p><a>{$config["baseUrl"]}/auth/register?code={$code->code}</a></p>
												<p><button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{$config["baseUrl"]}/auth/register?code={$code->code}">点击拷贝邀请链接</button></p>
									</div>
								</div>
							</div>
						</div>
					</div>

                   	{else}

                  	<div class="col-lg-12 col-md-12">
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
					<div class="table-responsive">
						{$paybacks->render()}
						<table class="table ">
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
	
	$("#buyCode_btn").click(function () {
	
			$.ajax({
				type: "POST",
                url: "/user/buyInvite",
                dataType: "json",
                success: function (data) {
					if(data.ret == 1){
						$('#buyCode_success_modal').modal("show");
					}
					if(data.ret == 0){
						$('#buyCode_fail_modal').modal("show");
					}
					
                    //window.location.reload();
                },
                error: function (jqXHR) {
                    $("#result").modal();
					$("#msg").html("发生错误：" + jqXHR.status);
                }
			})
		});
	
</script>
