



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
                                    <p>充值完成后需刷新网页以查看余额，通常一分钟内到账。</p>
										{if $config["enable_admin_contact"] == 'true'}
											<p class="card-heading">如果没有到账请立刻联系站长：</p>
											{if $config["admin_contact1"]!=null}
												<li>{$config["admin_contact1"]}</li>
											{/if}
											{if $config["admin_contact2"]!=null}
												<li>{$config["admin_contact2"]}</li>
											{/if}
											{if $config["admin_contact2"]!=null}
												<li>{$config["admin_contact3"]}</li>
											{/if}
										{/if}
										<br/>
										<p><i class="icon icon-lg">monetization_on</i>当前余额：<font color="red" size="5">{$user->money}</font> 元</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

					
				    {if $pmw!=''}
					<div class="col-lg-12 col-md-12">
						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
									{$pmw}
								</div>
							</div>
						</div>
					</div>
					{/if}

					<div class="col-lg-12 col-md-12">
						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
									<div class="card-inner">
										<p class="card-heading">充值码</p>									
										<div class="form-group form-group-label">
											<label class="floating-label" for="code">充值码</label>
											<input class="form-control" id="code" type="text">
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
													<!--<th>ID</th> -->
														<th>代码</th>
														<th>类型</th>
														<th>操作</th>
														<th>使用时间</th>

													</tr>
													{foreach $codes as $code}
														{if $code->type!=-2}
															<tr>
															<!--	<td>#{$code->id}</td>  -->
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
					<div aria-hidden="true" class="modal modal-va-middle fade" id="readytopay" role="dialog" tabindex="-1">
						<div class="modal-dialog modal-xs">
							<div class="modal-content">
								<div class="modal-heading">
									<a class="modal-close" data-dismiss="modal">×</a>
									<h2 class="modal-title">正在连接支付宝</h2>
								</div>
								<div class="modal-inner">
									<p id="title">感谢您对我们的支持，请耐心等待</p>
                                   <img src="/images/qianbai-2.png" height="200" width="200" />
								</div>
							</div>
						</div>
					</div>

					<div aria-hidden="true" class="modal modal-va-middle fade" id="alipay" role="dialog" tabindex="-1">
						<div class="modal-dialog modal-xs">
							<div class="modal-content">
							<div class="modal-heading">
									<a class="modal-close" data-dismiss="modal">×</a>
                              <h2 class="modal-title">请使用支付宝App扫码充值：</h2>
								</div>
								<div class="modal-inner">
                                   <div class="text-center">
                                    <p id="divide">-------------------------------------------------------------</p>
									<p id="title">手机端点击二维码即可转跳app支付</p>
									<p id="divide">-------------------------------------------------------------</p>
									<p id="qrcode"></p>
									<p id="info"></p>
								</div>
                                  </div>

								<div class="modal-footer">
									<p class="text-right"><button class="btn btn-flat btn-brand waves-attach" data-dismiss="modal" id="alipay_cancel" type="button">取消</button></p>
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
		$("#code-update").click(function () {
			$.ajax({
				type: "POST",
				url: "code",
				dataType: "json",
				data: {
					code: $("#code").val()
				},
				success: function (data) {
					if (data.ret) {
						$("#result").modal();
						$("#msg").html(data.msg);
						window.setTimeout("location.href=window.location.href", {$config['jump_delay']});
					} else {
						$("#result").modal();
						$("#msg").html(data.msg);
						window.setTimeout("location.href=window.location.href", {$config['jump_delay']});
					}
				},
				error: function (jqXHR) {
					$("#result").modal();
					$("#msg").html("发生错误：" + jqXHR.status);
				}
			})
		})

	$("#urlChange").click(function () {
			$.ajax({
				type: "GET",
				url: "code/f2fpay",
				dataType: "json",
				data: {
					time: timestamp
				},
				success: function (data) {
					if (data.ret) {
						$("#readytopay").modal();
					}
				}

			})
		});

		$("#readytopay").on('shown.bs.modal', function () {
			$.ajax({
				type: "POST",
				url: "code/f2fpay",
				dataType: "json",
				data: {
						amount: $("#type").val()
					},
				success: function (data) {
					$("#readytopay").modal('hide');
					if (data.ret) {
						$("#qrcode").html(data.qrcode);
						$("#info").html("您的订单金额为："+data.amount+"元。");
						$("#alipay").modal();
					} else {
						$("#result").modal();
						$("#msg").html(data.msg);
					}
				},
				error: function (jqXHR) {
					$("#readytopay").modal('hide');
					$("#result").modal();
					$("#msg").html(data.msg+"  发生了错误。");
				}
			})
		});
	timestamp = {time()};


	function f(){
		$.ajax({
			type: "GET",
			url: "code_check",
			dataType: "json",
			data: {
				time: timestamp
			},
			success: function (data) {
				if (data.ret) {
					clearTimeout(tid);
					$("#alipay").modal('hide');
					$("#result").modal();
					$("#msg").html("充值成功！");
					window.setTimeout("location.href=window.location.href", {$config['jump_delay']});
				}
			}
		});
		tid = setTimeout(f, 1000); //循环调用触发setTimeout
	}
	setTimeout(f, 1000);
})
</script>
