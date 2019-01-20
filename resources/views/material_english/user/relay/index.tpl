


{include file='user/main.tpl'}







	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">Transfer rules management</h1>
			</div>
		</div>
		<div class="container">
			<div class="col-lg-12 col-md-12">
				<section class="content-inner margin-top-no">

					<div class="card">
						<div class="card-main">
							<div class="card-inner">
								<p>All your transit rules in the system.</p>
								<p>Here, you can set your transit rules to redirect data from one server to another.</p>
								<p>If its priority is higher, then that rule will take precedence over others with lower priority in the event of coexistence of conflicting rules.</p>
								<p>If a server does not have transit rules, then that server can be used as a normal server.</p>
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-main">
							<div class="card-relayinner">
								{if $is_relay_able}
								<nav class="tab-nav">
									<ul class="nav nav-justified">
										<li class="active">
											<a class="" data-toggle="tab" href="#rule_table">Rules table</a>
										</li>
										<li>
											<a class="" data-toggle="tab" href="#link_table">Link table</a>
										</li>
									</ul>
								</nav>
							</div>
									<div class="tab-content">
										<div class="tab-pane fade active in" id="rule_table">
											<div class="table-responsive table-user">
												{$rules->render()}
												<table class="table table-user">
											    <tr>
													
												<!--	<th>ID</th>   -->
													<th>Original server</th>
													<th>Target server</th>
													<th>Port</th>
													<th>Priority</th>
                                                    <th>Action</th>

													</tr>
													{foreach $rules as $rule}
														<tr>
														
												<!--		<td>#{$rule->id}</td>  -->
														{if $rule->source_node_id == 0}
															<td>All Servers</td>
														{else}
															<td>{$rule->Source_Node()->name}</td>
														{/if}
														{if $rule->Dist_Node() == null}
															<td>Not redirected</td>
														{else}
															<td>{$rule->Dist_Node()->name}</td>
														{/if}
														<td>{if $rule->port == 0}All ports{else}{$rule->port}{/if}</td>
														<td>{$rule->priority}</td>
                                                          <td>
															<a class="btn btn-brand" {if $rule->user_id == 0}disabled{else}href="/user/relay/{$rule->id}/edit"{/if}>Edit</a>
															<a class="btn btn-brand-accent" id="delete" value="{$rule->id}" {if $rule->user_id == 0}disabled{else}href="javascript:void(0);" onClick="delete_modal_show('{$rule->id}')"{/if}>Delete</a>
														</td>
												        </tr>
												    {/foreach}
												</table>
												{$rules->render()}
											</div>
										</div>
										<div class="tab-pane fade" id="link_table">
											<div class="table-responsive table-user">
												<table class="table">
											    <tr>
													<th>Port</th>
													<th>Originating Server</th>
													<th>End Server</th>
													<th>Connection Path</th>
													<th>Status</th>
													</tr>

													{foreach $pathset as $path}
													<tr>
													<td>{$path->port}</td>
													<td>{$path->begin_node->name}</td>
													<td>{$path->end_node->name}</td>
													<td>{$path->path}</td>
													<td>{$path->status}</td>
									        </tr>
											    {/foreach}
												</table>
											</div>
										</div>
									</div>
				
								{/if}
							
						</div>
					</div>

					<div class="fbtn-container">
						<div class="fbtn-inner">
							<a class="fbtn fbtn-lg fbtn-brand-accent" href="/user/relay/create">+</a>

						</div>
					</div>

					<div aria-hidden="true" class="modal modal-va-middle fade" id="delete_modal" role="dialog" tabindex="-1">
						<div class="modal-dialog modal-xs">
							<div class="modal-content">
								<div class="modal-heading">
									<a class="modal-close" data-dismiss="modal">×</a>
									<h2 class="modal-title">Are you sure you want to delete?</h2>
								</div>
								<div class="modal-inner">
									<p>Please confirm.</p>
								</div>
								<div class="modal-footer">
									<p class="text-right"><button class="btn btn-flat btn-brand-accent waves-attach waves-effect" data-dismiss="modal" type="button">Cancel</button><button class="btn btn-flat btn-brand-accent waves-attach" data-dismiss="modal" id="delete_input" type="button">Determine</button></p>
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
function delete_modal_show(id) {
	deleteid=id;
	$("#delete_modal").modal();
}


$(document).ready(function(){

	{if !$is_relay_able}
	$("#result").modal();
	$("#msg").html("In order to stabilize the transfer, you need to set the protocol to one of the following protocols at <a href='/user/edit'>data editing</a>: <br>{foreach $relay_able_protocol_list as $single_text}{$single_text}< Br>{/foreach} to set the transfer rule!");
	{/if}

	function delete_id(){
		$.ajax({
			type:"DELETE",
			url:"/user/relay",
			dataType:"json",
			data:{
				id: deleteid
			},
			success:function(data){
				if(data.ret){
					$("#result").modal();
					$("#msg").html(data.msg);
					window.setTimeout("location.href=window.location.href", {$config['jump_delay']});
				}else{
					$("#result").modal();
					$("#msg").html(data.msg);
				}
			},
			error:function(jqXHR){
				$("#result").modal();
				$("#msg").html(data.msg+"  An error occurred.");
			}
		});
	}
	$("#delete_input").click(function(){
		delete_id();
	});
})

</script>
