


{include file='user/main.tpl'}


<script src="//cdn.staticfile.org/canvasjs/1.7.0/canvasjs.js"></script>
<script src="//cdn.staticfile.org/jquery/2.2.1/jquery.min.js"></script>

	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">节点列表</h1>
			</div>
		</div>

		<div class="container">
			<section class="content-inner margin-top-no">
				<div class="ui-card-wrap">
					<div class="row">
						<div class="col-lg-12 col-sm-12">
							<div class="card">
								<div class="card-main">
									<div class="card-inner margin-bottom-no">
										<div class="tile-wrap">
											{$id=0}
											{foreach $classes as $class => $node_prefix}
												<p class="card-heading">{if $class==0}免费用户节点{else}VIP{$class}用户节点{/if}</p>
												{foreach $node_prefix as $prefix => $nodes}
													{$id=$id+1}
                                           			{foreach $nodes as $node}
														<div class="tile tile-collapse">
															<div data-toggle="tile" data-target="#heading{$node_order->$prefix}">
																<div class="tile-side pull-left" data-ignore="tile">
																	<div class="avatar avatar-sm">
																		{*节点在线*}
																		<span class="icon {if $node_heartbeat[$prefix]=='在线'}text-green{else}{if $node_heartbeat[$prefix]=='暂无数据'}text-orange{else}text-red{/if}{/if}">
																			{if $node_heartbeat[$prefix]=="在线"}
																				backup
																			{else}
																				{if $node_heartbeat[$prefix]=='暂无数据'}
																					report
																				{else}
																					warning
																				{/if}
																			{/if}
																		</span>
																	</div>
																</div>

																<div class="tile-inner">
																	<div class="text-overflow">
																		{*国旗*}
	                                  									<font color="#383838"><img src="/images/prefix/{$prefix}.jpg" height="22" width="40"> {$prefix}</font> | 

	                                  									{*在线人数*}
	                                  									{if $user->class!=0}
	                                  										<font color="#ff9000"><i class="icon icon-lg">flight_takeoff</i></font> <strong>
	                                  									{/if}
	                                  									<b><font color="#474747">{$node_alive[$prefix]}</font></b></strong> | 

	                                  									{*负载*}
	                                  									<font color="#ff9000"><i class="icon icon-lg">cloud</i></font>
	                                  									<font color="#828282">负载：{$node_latestload[$prefix]}%</font> | 

	                                  									{*带宽*}
	                                  									<font color="#ff9000"><i class="icon icon-lg">import_export</i></font>
	                                  									<font color="#828282">{$node_method[$prefix]}</font> | 

	                                  									{*流量*}
	                                  									<font color="#ff9000"><i class="icon icon-lg">equalizer</i></font>
	                                  									{if isset($node_bandwidth[$prefix])==true}
	                                  										<font color="#aaaaaa">{$node_bandwidth[$prefix]}</font>
	                                  									{else}
	                                  										N/A
	                                  									{/if} | 

	                                  									{*倍率*}
	                                  									<font color="#ff9000"><i class="icon icon-lg">network_check</i></font>
	                                  									<font color="#a5a5a5">{$node->traffic_rate} 倍率</font> | 

	                                  									{*节点状态*}
	                                  									<font color="#ff9000"><i class="icon icon-lg">notifications_none</i></font>
	                                  									<font color="#c4c4c4">{$node->status}</font>
	                                   								</div>
																</div>
															</div>

															<div class="collapsible-region collapse" id="heading{$node_order->$prefix}">
																<div class="tile-sub">
																	<br>

	 																{if $node->node_class > $user->class}
																		<div class="card">
																			<div class="card-main">
																				<div class="card-inner">
																					<p class="card-heading" align="center"><b> <i class="icon icon-lg">visibility_off</i> {$user->user_name}，您无查看VIP节点权限，如需购买VIP请<a href="/user/shop">点击这里</a>。</b></p>
																				</div>
																			</div>
																		</div>
																	{else}
																		{$relay_rule = null}
																		{if $node->sort == 10}
																			{$relay_rule = $tools->pick_out_relay_rule($node->id, $user->port, $relay_rules)}
																		{/if}

																		{if $node->mu_only != 1}
																			<div class="card">
																				<div class="card-main">
																					<div class="card-inner">
																						<p class="card-heading" >
																							{if isset($node->offset)}
																								{if URL::SSCanConnect($user)}
																									{$ss_item = URL::getItem($user, $node, $mu, $relay_rule_id, 1)}
																								{/if}
																								{if URL::SSRCanConnect($user)}
																									{$ssr_item = URL::getItem($user, $node, $mu, $relay_rule_id, 0)}
																								{/if}
																								{$node->name}
																								<p>
																									服务器地址：{$ssr_item['address']}<br>
																									服务器端口：{$ssr_item['port']}<br>
																									加密方式：{$ssr_item['method']}<br>
																									密码：{$ssr_item['passwd']}<br>
																									协议：{$ssr_item['protocol']}<br>
																									协议参数：{$ssr_item['protocol_param']}<br>
																									混淆：{$ssr_item['obfs']}<br>
																									混淆参数：{$ssr_item['obfs_param']}<br>
																									{if isset($ss_item)}
																										ss://链接：<button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{URL::getItemUrl($ss_item,1)}">点击复制</button><br>
																									{/if}
																									{if isset($ssr_item)}
																										ssr://链接：<button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{URL::getItemUrl($ssr_item,0)}">点击复制</button><br>
																									{/if}
																									{$ss_item = null}
																									{$ssr_item = null}
																								</p>
																							{else}
																								<a href="javascript:void(0);" onClick="urlChange('{$node->id}',0,{if $relay_rule != null}{$relay_rule->id}{else}0{/if})">{$node->name}{if $relay_rule != null} - {$relay_rule->dist_node()->name}{/if}</a>
																								<span class="label label-brand-accent">←点击节点查看配置信息</span>
																							{/if}
																						</p>
																						<p>备注：{$node->info}</p>
																					 </div>
																				</div>
																			</div>
																		{/if}

																		{if $node->sort == 0 || $node->sort == 10}
																			{$point_node=$node}
																		{/if}

																		{if ($node->sort == 0 || $node->sort == 10) && $node->custom_rss == 1 && $node->mu_only != -1}
																			{foreach $node_muport as $single_muport}
																				{if !($single_muport['server']->node_class <= $user->class && ($single_muport['server']->node_group == 0 || $single_muport['server']->node_group == $user->node_group))}
																					{continue}
																				{/if}

																				{if !($single_muport['user']->class >= $node->node_class && ($node->node_group == 0 || $single_muport['user']->node_group == $node->node_group))}
																					{continue}
																				{/if}

																				{$relay_rule = null}
																				{if $node->sort == 10 && $single_muport['user']['is_multi_user'] != 2}
																					{$relay_rule = $tools->pick_out_relay_rule($node->id, $single_muport['server']->server, $relay_rules)}
																				{/if}

																				{if isset($node->subscribe)}
																					{if $single_muport['server']->server == $node->muport}
																						{$ssr_item = URL::getItem($user, $node, $node->muport, $relay_rule_id, 0)}
																						<div class="card">
																							<div class="card-main">
																								<div class="card-inner">
																									<p class="card-heading" >
																										{$prefix} - 单端口 Shadowsocks - {$node->subscribe} 端口</a>
																									</p>
																									<p>
																										服务器地址：{$ssr_item['address']}<br>
																										服务器端口：{$ssr_item['port']}<br>
																										加密方式：{$ssr_item['method']}<br>
																										密码：{$ssr_item['passwd']}<br>
																										协议：{$ssr_item['protocol']}<br>
																										协议参数：{$ssr_item['protocol_param']}<br>
																										混淆：{$ssr_item['obfs']}<br>
																										混淆参数：{$ssr_item['obfs_param']}<br>
																										ssr://链接：<button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{URL::getItemUrl($ssr_item,0)}">点击复制</button><br>
																										{$ssr_item = null}
																									</p>
																									<p>{$node->info}</p>
																								 </div>
																							</div>
																						</div>
																					{/if}
																					{continue}
																				{/if}

																				<div class="card">
																					<div class="card-main">
																						<div class="card-inner">
																							<p class="card-heading" >
																								<a href="javascript:void(0);" onClick="urlChange('{$node->id}',{$single_muport['server']->server},{if $relay_rule != null}{$relay_rule->id}{else}0{/if})">{$prefix} {if $relay_rule != null} - {$relay_rule->dist_node()->name}{/if} - 单端口 Shadowsocks - {$single_muport['server']->server} 端口</a>
																								<span class="label label-brand-accent">{$node->status}</span>
																							</p>
																							<p>{$node->info}</p>
																						 </div>
																					</div>
																				</div>
																			{/foreach}
																		{/if}
																	{/if}
																	
																	{if $node->sort == 11} 
																		{assign var=server_explode value=";"|explode:$node->server}
																		<div class="card">
																			<div class="card-main">
																				<div class="card-inner">
																					<p class="card-heading" >
																						<a href="javascript:void(0);" >{$node->name}</a>
																					</p>
																				</div>
																				<p>地址：<span class="label label-brand-accent">
                                                                                    {$server_explode[0]}
																				</span></p>

																				<p>端口：<span class="label label-brand-red">
																					{$server_explode[1]}
																				</span></p>

																				<p>协议：<span class="label label-brand-accent">
																					{$server_explode[2]}
																				</span></p>

																				<p>协议参数：<span class="label label-green">
																					{$server_explode[0]}
																				</span></p>

																				<p>用户 UUID：<span class="label label-brand">
																					{$user->getUuid()}
																				</span></p>

																				<p>流量比例：<span class="label label-red">
																					{$node->traffic_rate}
																				</span></p>

																				<p>AlterId：<span class="label label-green">
																					{$server_explode[3]}
																				</span></p>

																				<p>Level：<span class="label label-brand">
																					{$server_explode[4]}
																				</span></p>

																				<p>VMess链接：
																					<button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{URL::getV2Url($user, $node)}">点击复制</button>
																				</p>

																				<p>{$node->info}</p>
																			</div>
																		</div>
																	{/if}
																</div>

																{if isset($point_node)}
																	{if $point_node!=null}
																		<div class="card">
																			<div class="card-main">
																				<div class="card-inner" id="info{$id}">	</div>
																			</div>
																		</div>
																		<script>
																			$().ready(function(){
																				$('#heading{$node_order->$prefix}').on("shown.bs.tile", function() {
																						$("#info{$id}").load("/user/node/{$point_node->id}/ajax");
																				});
																			});
																		</script>
																	{/if}
																{/if}
																{$point_node=null}
															</div>
														</div>															
													{/foreach}																
												{/foreach}
											{/foreach}
										</div>
									</div>
								</div>
							</div>
						</div>


						<div aria-hidden="true" class="modal modal-va-middle fade" id="nodeinfo" role="dialog" tabindex="-1">
							<div class="modal-dialog modal-full">
								<div class="modal-content">
									<iframe class="iframe-seamless" title="Modal with iFrame" id="infoifram"></iframe>
								</div>
							</div>
                    	</div>

						{include file='dialog.tpl'}

                    </div>
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
	$("#msg").html("已复制，请您继续接下来的操作。");
});

function urlChange(id,is_mu,rule_id) {
    var site = './node/'+id+'?ismu='+is_mu+'&relay_rule='+rule_id;
	if(id == 'guide')
	{
		var doc = document.getElementById('infoifram').contentWindow.document;
		doc.open();
		doc.write('<img src="../images/node.gif" style="width: 100%;height: 100%; border: none;"/>');
		doc.close();
	}
	else
	{
		document.getElementById('infoifram').src = site;
	}
	$("#nodeinfo").modal();
}
</script>