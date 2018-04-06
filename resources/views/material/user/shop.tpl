





{include file='user/main.tpl'}





<main class="content">
	<div class="content-header ui-content-header">
		<div class="container">
			<h1 class="content-heading">商品列表</h1>
		</div>
	</div>
	<div class="container">
		<div class="col-lg-12 col-sm-12">
			<section class="content-inner margin-top-no">
				
				<div class="card">
					<div class="card-main">
						<div class="card-inner">
							<p>所有商品可以叠加购买，VIP时间会叠加</p>
							<p>当前余额：{$user->money} 元</p>
						</div>
					</div>
				</div>
				
				
				<!--
				<div class="table-responsive">
					{$shops->render()}
					<table class="table ">
						<tr>
							
							    <th>ID</th>    
							<th>套餐</th>
							<th>价格</th>
							<th>套餐详情</th>
                                  <th>自动续费天数</th>
                           	<th>续费时重置流量</th>     
                           	<th>操作</th>
                           	
                           </tr>
                           {foreach $shops as $shop}
                           <tr>
                           	
                           	     <td>#{$shop->id}</td>    
                           	<td>{$shop->name}</td>
                           	<td>{$shop->price} 元</td>
                           	<td>{$shop->content()}</td>
							  	{if $shop->auto_renew==0}
                                <td>不能自动续费</td>
								{else}
								<td>可选 在 {$shop->auto_renew} 天后自动续费</td>
								{/if}
								
								{if $shop->auto_reset_bandwidth==0}
                                <td>不自动重置</td>
								{else}
								<td>自动重置</td>
								{/if}  
								<td>
									<a class="btn btn-brand-accent" href="javascript:void(0);" onClick="buy('{$shop->id}',{$shop->auto_renew},{$shop->auto_reset_bandwidth})">购买</a>
								</td>
							</tr>
							{/foreach}
						</table>
						{$shops->render()}
					</div>
				-->
					{$shops->render()}
					{foreach $shops as $shop}
					<div class="tile tile-collapse">
						<div data-toggle="tile" data-target="#heading{$shop->id}">
							<div class="tile-inner">
								<div class="text-overflow">{$shop->name}&nbsp;<span class="label label-brand">{$shop->price} 元</span></div>
							</div>
						</div>
						<div class="collapsible-region collapse" id="heading{$shop->id}" style="height: 0px;">
							<div style="padding:18px">
								<p class="card-heading">
									<span>{$shop->name}</span>
								</p>
								<hr>
								<h4 style="margin-top:12px">商品内容</h4>
								<p></p><ul>
									{if $shop->speedlimit()==0}
									<li>不限速</li>
									{else}
									<li>{$shop->speedlimit()}Mbps 速率</li>
									{/if}
									<li>每{$shop->reset_exp()}天{$shop->bandwidth()}GB套餐流量</li>
									<li>套餐有效期 {$shop->expire()} 天</li>
									{if $shop->connector()==0}
									<li>不限制设备数</li>
									{else}
									<li>最多支持 {$shop->connector()} 个IP同时使用</li>
									{/if}
								</ul><p></p>

								<h4 style="margin-top:12px">续费</h4>
								{if $shop->auto_renew==0}
								<span class="label label-brand-accent">不能自动续费</span>
								{else}
								<span class="label label-brand-accent">每 {$shop->auto_renew} 天自动续费</span>
								{/if}

								<h4 style="margin-top:12px">续费时重置流量</h4>
								{if $shop->auto_reset_bandwidth==0}
								<span class="label label-brand-accent">不自动重置</span>
								{else}
								<span class="label label-brand-accent">自动重置</span>
								{/if}

								<h4 style="margin-top:12px">订购总额</h4>
								<span class="label label-brand-accent">{$shop->price} 元</span>

								<hr>
								<a class="btn btn-brand" href="javascript:void(0);" onclick="buy('{$shop->id}',{$shop->auto_renew},{$shop->auto_reset_bandwidth})" style="background-color: #4cae4c;padding-right:16px">
									<span style="margin-left:8px;margin-right:8px" class="icon">local_grocery_store</span>立即购买</a>
									<a class="btn btn-brand" href="/user/code" style="background-color: #337ab7;padding-right:16px;margin-left:8px"><span style="margin-left:8px;margin-right:8px" class="icon">local_gas_station</span>充值</a>
									<br>
								</div>
							</div>
						</div>
						{/foreach}
						{$shops->render()}


						<div aria-hidden="true" class="modal modal-va-middle fade" id="coupon_modal" role="dialog" tabindex="-1">
							<div class="modal-dialog modal-xs">
								<div class="modal-content">
									<div class="modal-heading">
										<a class="modal-close" data-dismiss="modal">×</a>
										<h2 class="modal-title">您有优惠码吗？</h2>
									</div>
									<div class="modal-inner">
										<div class="form-group form-group-label">
											<label class="floating-label" for="coupon">有的话，请在这里输入。没有的话，直接确定吧</label>
											<input class="form-control" id="coupon" type="text">
										</div>
									</div>
									<div class="modal-footer">
										<p class="text-right"><button class="btn btn-flat btn-brand waves-attach" data-dismiss="modal" id="coupon_input" type="button">确定</button></p>
									</div>
								</div>
							</div>
						</div>


						<div aria-hidden="true" class="modal modal-va-middle fade" id="order_modal" role="dialog" tabindex="-1">
							<div class="modal-dialog modal-xs">
								<div class="modal-content">
									<div class="modal-heading">
										<a class="modal-close" data-dismiss="modal">×</a>
										<h2 class="modal-title">订单确认</h2>
									</div>
									<div class="modal-inner">
										<p id="name">商品名称：</p>
										<p id="credit">优惠额度：</p>
										<p id="total">总金额：</p>
										<p id="auto_reset">在到期时自动续费</p>

										<div class="checkbox switch" id="autor">
											<label for="autorenew">
												<input checked class="access-hide" id="autorenew" type="checkbox"><span class="switch-toggle"></span>自动续费
											</label>
										</div>

									</div>

									<div class="modal-footer">
										<p class="text-right"><button class="btn btn-flat btn-brand waves-attach" data-dismiss="modal" id="order_input" type="button">确定</button></p>
									</div>
								</div>
							</div>
						</div>

						{include file='dialog.tpl'}

					</div>



				</div>
			</main>









			{include file='user/footer.tpl'}


			<script>
				function buy(id,auto,auto_reset) {
					auto_renew=auto;
					if(auto==0)
					{
						document.getElementById('autor').style.display="none";
					}
					else
					{
						document.getElementById('autor').style.display="";
					}

					if(auto_reset==0)
					{
						document.getElementById('auto_reset').style.display="none";
					}
					else
					{
						document.getElementById('auto_reset').style.display="";
					}

					shop=id;
					$("#coupon_modal").modal();
				}


				$("#coupon_input").click(function () {
					$.ajax({
						type: "POST",
						url: "coupon_check",
						dataType: "json",
						data: {
							coupon: $("#coupon").val(),
							shop: shop
						},
						success: function (data) {
							if (data.ret) {
								$("#name").html("商品名称："+data.name);
								$("#credit").html("优惠额度："+data.credit);
								$("#total").html("总金额："+data.total);
								$("#order_modal").modal();
							} else {
								$("#result").modal();
								$("#msg").html(data.msg);
							}
						},
						error: function (jqXHR) {
							$("#result").modal();
							$("#msg").html(data.msg+"  发生了错误。");
						}
					})
				});

				$("#order_input").click(function () {

					if(document.getElementById('autorenew').checked)
					{
						var autorenew=1;
					}
					else
					{
						var autorenew=0;
					}

					$.ajax({
						type: "POST",
						url: "buy",
						dataType: "json",
						data: {
							coupon: $("#coupon").val(),
							shop: shop,
							autorenew: autorenew
						},
						success: function (data) {
							if (data.ret) {
								$("#result").modal();
								$("#msg").html(data.msg);
								window.setTimeout("location.href='/user/shop'", {$config['jump_delay']});
							} else {
								$("#result").modal();
								$("#msg").html(data.msg);
							}
						},
						error: function (jqXHR) {
							$("#result").modal();
							$("#msg").html(data.msg+"  发生了错误。");
						}
					})
				});

			</script>