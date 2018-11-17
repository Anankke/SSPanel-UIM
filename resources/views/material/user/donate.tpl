








{include file='user/main.tpl'}







	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">捐赠公示</h1>
			</div>
		</div>
		<div class="container">
			<section class="content-inner margin-top-no">
				<div class="row">
				

					<div class="col-lg-12 col-md-12">
						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
									<p>感谢各位捐赠来支撑服务器的日常支出！您可以在<a href="/user/code">充值界面</a>进行充值，这样就等同于捐赠了。</p>
									{if $user->isAdmin()}
									<p>总收入：{$total_in} 元</p>
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
										<p class="card-heading">匿名捐赠</p>
										<p>当前设置：<code data-default="hide">{if $user->is_hide==1} 匿名 {else} 不匿名 {/if}</code></p>
										<div class="form-group form-group-label control-highlight-custom dropdown">
											<label class="floating-label" for="hide">匿名设置</label>
											<button id="hide" class="form-control maxwidth-edit" data-toggle="dropdown" value="{$user->is_hide}">
												
											</button>
											<ul class="dropdown-menu" aria-labelledby="hide">
												<li><a href="#" class="dropdown-option" onclick="return false;" val="1" data="hide">匿名</a> </li>
												<li><a href="#" class="dropdown-option" onclick="return false;" val="0" data="hide">不匿名</a></li>
											</ul>
										</div>
										
									</div>
									<div class="card-action">
										<div class="card-action-btn pull-left">
											<button class="btn btn-flat waves-attach" id="hide-update" ><span class="icon">check</span>&nbsp;提交</button>
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
														<th>用户名</th>
														<th>类型</th>
														<th>操作</th>
														<th>备注</th>
														<th>时间</th>
														
													</tr>
													{foreach $codes as $code}
														<tr>
															<td>#{$code->id}</td>
															{if $code->user() != null && $code->user()->is_hide == 0}
															<td>{$code->user()->user_name}</td>
															{else}
															<td>用户匿名或已注销</td>
															{/if}
															{if $code->type == -1}
															<td>充值捐赠</td>
															{/if}
															{if $code->type == -2}
															<td>财务支出</td>
															{/if}
															{if $code->type == -1}
															<td>捐赠 {$code->number} 元</td>
															{/if}
															{if $code->type == -2}
															<td>支出 {$code->number} 元</td>
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
					$("#msg").html(data.msg+"     出现了一些错误。");
                }
            })
        })
    })
</script>
