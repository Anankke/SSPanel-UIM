


{include file='user/main.tpl'}






	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading"> 添加中转规则</h1>
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
										<label class="floating-label" for="source_node">起源节点</label>
										<select id="source_node" class="form-control" name="source_node">
											<option value="0">请选择起源节点</option>
											{foreach $source_nodes as $source_node}
												<option value="{$source_node->id}">{$source_node->name}</option>
											{/foreach}
										</select>
									</div>


									<div class="form-group form-group-label">
										<label class="floating-label" for="dist_node">目标节点</label>
										<select id="dist_node" class="form-control" name="dist_node">
											<option value="-1">不进行中转</option>
											{foreach $dist_nodes as $dist_node}
												<option value="{$dist_node->id}">{$dist_node->name}</option>
											{/foreach}
										</select>
									</div>

									<div class="form-group form-group-label">
										<label class="floating-label" for="port">端口</label>
										<select id="port" class="form-control" name="port">
											{foreach $ports as $port}
												<option value="{$port}">{$port}</option>
											{/foreach}
										</select>
									</div>



									<div class="form-group form-group-label">
										<label class="floating-label" for="priority">优先级</label>
										<input class="form-control" id="priority" name="priority" type="text" value="0">
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
												<button id="submit" type="submit" class="btn btn-block btn-brand waves-attach waves-light">添加</button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>
					{include file='dialog.tpl'}
				<section>

			</div>



		</div>
	</main>











{include file='user/footer.tpl'}


{literal}
<script>

	$('#main_form').validate({
		rules: {
		  priority: {required: true}
		},


		submitHandler: function() {



		$.ajax({

				type: "POST",
				url: "/user/relay",
				dataType: "json",
				{/literal}
				data: {
						source_node: $("#source_node").val(),
						dist_node: $("#dist_node").val(),
						port: $("#port").val(),
						priority: $("#priority").val()
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
