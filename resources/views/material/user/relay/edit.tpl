



{include file='user/main.tpl'}





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
									<div class="form-group form-group-label control-highlight-custom dropdown">
										<label class="floating-label" for="source_node">起源节点</label>
										<button id="source_node" class="form-control maxwidth-edit" name="source_node" data-toggle="dropdown" value="{$rule->source_node_id}">
											{foreach $source_nodes as $source_node}
											{if $rule->source_node_id == $source_node->id}{$source_node->name}{/if}
											{/foreach}
										</button>
										<ul class="dropdown-menu" aria-labelledby="source_node">
											{foreach $source_nodes as $source_node}
												<li><a href="#" class="dropdown-option" onclick="return false;" val="{$source_node->id}" data="source_node">{$source_node->name}</a></li>
											{/foreach}
										</ul>
									</div>


									<div class="form-group form-group-label control-highlight-custom dropdown">
										<label class="floating-label" for="dist_node">目标节点</label>
										<button id="dist_node" class="form-control maxwidth-edit" name="dist_node" data-toggle="dropdown" value="{$rule->dist_node_id}">
											{foreach $dist_nodes as $dist_node}
												{if $rule->dist_node_id == $dist_node->id}{$dist_node->name}{/if}
											{/foreach}	
										</button>
										<ul class="dropdown-menu" aria-labelledby="dist_node">
												<li><a href="#" class="dropdown-option" onclick="return false;" val="-1" data="dist_node">不进行中转</a></li>
											{foreach $dist_nodes as $dist_node}
												<li><a href="#" class="dropdown-option" onclick="return false;" val="{$dist_node->id}" data="dist_node">{$dist_node->name}</a></li>
											{/foreach}
										</ul>
									</div>

									<div class="form-group form-group-label control-highlight-custom dropdown">
										<label class="floating-label" for="port">端口</label>
										<button id="port" class="form-control maxwidth-edit" name="port" data-toggle="dropdown" value="{$rule->port}">
											{foreach $ports as $port}
											{if $rule->port == $port}{$rule->port}{/if}
											{/foreach}
										</button>
										<ul class="dropdown-menu" aria-labelledby="port">
											{foreach $ports as $port}
												<li><a href="#" class="dropdown-option" onclick="return false;" val="{$port}" data="port">{$port}</a></li>
											{/foreach}
										</ul>
									</div>



									<div class="form-group form-group-label">
										<label class="floating-label" for="priority">优先级</label>
										<input class="form-control maxwidth-edit" id="priority" name="priority" type="text" value="{$rule->priority}">
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
												<button id="submit" type="submit" class="btn btn-block btn-brand">修改</button>
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
				{/literal}
				type: "PUT",
				url: "/user/relay/{$rule->id}",
				dataType: "json",
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
