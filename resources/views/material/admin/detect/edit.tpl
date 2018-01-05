


{include file='admin/main.tpl'}







	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">   编辑规则 #{$rule->id}</h1>
			</div>
		</div>
		<div class="container">
			<div class="col-lg-12 col-sm-12">
				<section class="content-inner margin-top-no">
					<form id="main_form">
						<div class="card">
							<div class="card-main">
								<div class="card-inner">
									<div class="form-group form-group-label">
										<label class="floating-label" for="name">规则名称</label>
										<input class="form-control" id="name" name="name" type="text" value="{$rule->name}">
									</div>
									
									
									<div class="form-group form-group-label">
										<label class="floating-label" for="text">规则描述</label>
										<input class="form-control" id="text" name="text" type="text" value="{$rule->text}">
									</div>
									
									<div class="form-group form-group-label">
										<label class="floating-label" for="regex">规则正则表达式</label>
										<input class="form-control" id="regex" name="regex" type="text"  value="{$rule->regex}">
									</div>
									
									
									
									<div class="form-group form-group-label">
										<div class="form-group form-group-label">
												<label class="floating-label" for="type">规则类型</label>
												<select id="type" class="form-control" name="type">
													<option value="1" {if $rule->type == 1}selected{/if}>数据包明文匹配</option>
													<option value="2" {if $rule->type == 2}selected{/if}>数据包 hex 匹配</option>
												</select>
											</div>
									</div>
									
									
								</div>
							</div>
						</div>
						
						
						<div class="card">
							<div class="card-main">
								<div class="card-inner">
									
									<div class="form-group">
										<div class="row">
											<div class="col-md-10 col-md-push-1">
												<button id="submit" type="submit" class="btn btn-block btn-brand waves-attach waves-light">修改</button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>	
					{include file='dialog.tpl'}

			</div>
			
			
			
		</div>
	</main>

	
	
	
	






{include file='admin/footer.tpl'}


{literal}
<script>

	$('#main_form').validate({
		rules: {
		  name: {required: true},
		  text: {required: true},
		  regex: {required: true}
		},


		submitHandler: function() {
		{/literal}	
			
			
		$.ajax({

				type: "PUT",
				url: "/admin/detect/{$rule->id}",
				dataType: "json",
				
				data: {
					    name: $("#name").val(),
					    text: $("#text").val(),
					    regex: $("#regex").val(),
					    type: $("#type").val()
				{literal}
					},
					success: function (data) {
					    if (data.ret) {
						$("#result").modal();
						$("#msg").html(data.msg);
									{/literal}
						window.setTimeout("location.href=top.document.referrer", {$config['jump_delay']});
									{literal}
					    } else {
						$("#result").modal();
						$("#msg").html(data.msg);
					    }
					},
					error: function (jqXHR) {
					    $("#result").modal();
					    $("#msg").html(data.msg+"  发生错误了。");
					}
					});
				}
		});

</script>

{/literal}

