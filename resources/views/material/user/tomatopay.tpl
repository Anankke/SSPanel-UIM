{include file='user/main.tpl'}
	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">充值</h1>
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
										<p class="card-heading">充值</p>
										您的余额:{$user->money}
										<h5>支付方式:</h5>
										<nav class="tab-nav margin-top-no">
											<ul class="nav nav-list">
												{if $enabled['wxpay']}
													<li>
														<a class="waves-attach waves-effect type active" data-toggle="tab" data-pay="wxpay">微信支付</a>
													</li>
												{/if}
												{if $enabled['submit']}
													<li>
														<a class="waves-attach waves-effect type" data-toggle="tab" data-pay="submit">支付宝</a>
													</li>
												{/if}
				
											</ul>
											<div class="tab-nav-indicator"></div>
										</nav>
										<div class="form-group form-group-label">
											<label class="floating-label" for="amount">金额</label>
											<input class="form-control" id="amount" type="text">
										</div>
									</div>
									<div class="card-action">
										<div class="card-action-btn pull-left">
											<button class="btn btn-flat waves-attach" id="code-update" ><span class="icon">check</span>&nbsp;充值</button>
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
									<div class="card-inner">
										<div class="card-table">
											<div class="table-responsive">
												{$codes->render()}
												<table class="table table-hover">
													<tr>
														<th>ID</th>
														<th>代码</th>
														<th>类型</th>
														<th>操作</th>
														<th>使用时间</th>

													</tr>
													{foreach $codes as $code}
														{if $code->type!=-2}
															<tr>
																<td>#{$code->id}</td>
																<td>{$code->code}</td>
																{if $code->type==-1}
																<td>金额充值</td>
																{/if}
																{if $code->type==10001}
																<td>流量充值</td>
																{/if}
																{if $code->type==10002}
																<td>用户续期</td>
																{/if}
																{if $code->type>=1&&$code->type<=10000}
																<td>等级续期 - 等级{$code->type}</td>
																{/if}
																{if $code->type==-1}
																<td>充值 {$code->number} 元</td>
																{/if}
																{if $code->type==10001}
																<td>充值 {$code->number} GB 流量</td>
																{/if}
																{if $code->type==10002}
																<td>延长账户有效期 {$code->number} 天</td>
																{/if}
																{if $code->type>=1&&$code->type<=10000}
																<td>延长等级有效期 {$code->number} 天</td>
																{/if}
																<td>{$code->usedatetime}</td>
															</tr>
														{/if}
													{/foreach}
												</table>
												{$codes->render()}
											</div>
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
<script src=" /assets/public/js/jquery.qrcode.min.js "></script>
<script>
	{if $enabled['wxpay']}
		var type = "wxpay";
	{else}
		{if $enabled['submit']}
			var type = "submit";
		{else}
			{if $enabled['qqpay']}
				var type = "qqpay";
			{/if}
		{/if}
	{/if}
	var pid = 0;
	$(".type").click(function(){
		type = $(this).data("pay");
	});
	$("#code-update").click(function(){
		var price = parseFloat($("#amount").val());
		console.log("将要使用"+type+"方法充值"+price+"元")
		if(isNaN(price)){
			$("#result").modal();
			$("#msg").html("非法的金额!");
		}
		$.ajax({
			'url':"/user/tomatopay",
			'data':{
				'price':price,
				'type':type,
			},
			'dataType':'json',
			'type':"POST",
			success:function(data){
				console.log(data);
				if(data.errcode==-1){
					$("#result").modal();
					$("#msg").html(data.errmsg);
				}
				if(data.errcode==0){
					pid = data.pid;
					if(type=="wxpay"){
						$("#result").modal();
						$("#msg").html("正在跳转到支付宝..."+data.code);
					}else if(type=="submit"){
						$("#result").modal();
						$("#msg").html("正在跳转到支付宝..."+data.code);
					}else if(type=="qqpay"){
						$("#result").modal();
						$("#msg").html("正在跳转到支付宝..."+data.code);
					}
				}
			}
		});
		function f(){
			$.ajax({
				type: "POST",
				url: "/tomatopay/status",
				dataType: "json",
				data: {
					pid:pid
				},
				success: function (data) {
					if (data.status) {
						clearTimeout(tid);
						$("#result").modal();
						$("#msg").html("充值成功！");
						window.setTimeout("location.href=window.location.href", {$config['jump_delay']});
					}
				}
			});
			tid = setTimeout(f, 1000);
		}
		setTimeout(f, 1000);
	});
</script>
