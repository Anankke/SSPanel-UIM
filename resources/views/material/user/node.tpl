







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
								<p class="card-heading">免费用户节点</p>
											{$id=0}
											{foreach $node_prefix as $prefix => $nodes}
{if $node_isv6[$prefix] == 0 && $node_class[$prefix]==0}
												{$id=$id+1}
                                           {foreach $nodes as $node}
													<div class="tile tile-collapse">
														<div data-toggle="tile" data-target="#heading{$node_order->$prefix}">
															<div class="tile-side pull-left" data-ignore="tile">
																<div class="avatar avatar-sm">
																	<span class="icon {if $node_heartbeat[$prefix]=='在线'}text-green{else}{if $node_heartbeat[$prefix]=='暂无数据'}text-green{else}text-red{/if}{/if}">{if $node_heartbeat[$prefix]=="在线"}backup{else}{if $node_heartbeat[$prefix]=='暂无数据'}toll{else}warning{/if}{/if}</span>
                                                              </div>
                                                              
															</div>
                                                         
														<div class="tile-inner">
																<div class="text-overflow">
                                                                  <font color="#383838"><img src="/images/prefix/{$prefix}.jpg" height="22" width="40"> {$prefix}</font> | <font color="#ff9000"><i class="icon icon-lg">flight_takeoff</i></font> <strong><b><font color="#474747">{$node_alive[$prefix]}</font></b></strong> | <font color="#ff9000"><i class="icon icon-lg">import_export</i></font>  <font color="#828282">{$node_method[$prefix]}</font> | <font color="#ff9000"><i class="icon icon-lg">equalizer</i></font> {if isset($node_bandwidth[$prefix])==true}<font color="#aaaaaa">{$node_bandwidth[$prefix]}</font>{else}N/A{/if} | <font color="#ff9000"><i class="icon icon-lg">network_check</i></font> <font color="#a5a5a5">{$node->traffic_rate} 倍率</font> | <font color="#ff9000"><i class="icon icon-lg">notifications_none</i></font> <font color="#c4c4c4">{$node->status}</font>
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
																			<p class="card-heading"><b><i class="icon icon-lg">visibility_off</i>您无查看权限,如需购买VIP请<a href="/user/shop">点击这里</a>。</b></p>
																			</div></div></div>
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
																				<a href="javascript:void(0);" onClick="urlChange('{$node->id}',0,{if $relay_rule != null}{$relay_rule->id}{else}0{/if})">{$node->name}{if $relay_rule != null} - {$relay_rule->dist_node()->name}{/if}</a>
																				<span class="label label-brand-accent"><!--{$node->status}-->←点击节点查看配置信息</span>
																			</p>
                                                                                                                                                                             
																	
																	<!--{if $node->sort > 2 && $node->sort != 5 && $node->sort != 10}
																				<p>地址：<span class="label" >
																				<a href="javascript:void(0);" onClick="urlChange('{$node->id}',0,0)">请点这里进入查看详细信息</a>
																			{else}
																				<p>地址：<span class="label label-brand-accent">
																				{$node->server}
																			{/if}

																				</span></p>

																			{if $node->sort == 0||$node->sort==7||$node->sort==8||$node->sort==10}


																				<p>流量比例：<span class="label label-red">
																					{$node->traffic_rate}
																				</span></p>



																				{if ($node->sort==0||$node->sort==7||$node->sort==8||$node->sort==10)&&($node->node_speedlimit!=0||$user->node_speedlimit!=0)}
																					<p>节点限速：<span class="label label-green">
																						{if $node->node_speedlimit>$user->node_speedlimit}
																							{$node->node_speedlimit}Mbps
																						{else}
																							{$user->node_speedlimit}Mbps
																						{/if}
																					</span></p>
																				{/if}
																			{/if}-->

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

																			<div class="card-main">
 																					<div class="card-inner">
 																					<p class="card-heading" >
																						<a href="javascript:void(0);" onClick="urlChange('{$node->id}',{$single_muport['server']->server},{if $relay_rule != null}{$relay_rule->id}{else}0{/if})">{$prefix} {if $relay_rule != null} - {$relay_rule->dist_node()->name}{/if} - 单 - {$single_muport['server']->server} 端口</a>
 																						<span class="label label-brand-accent">{$node->status}</span>
 																					</p>


																					<p>地址：<span class="label label-brand-accent">
																					{$node->server}

																					</span></p>

																					<p>端口：<span class="label label-brand-red">
																						{$single_muport['user']['port']}
																					</span></p>

																					<p>加密方式：<span class="label label-brand">
																						{$single_muport['user']['method']}
																					</span></p>

																					<p>协议：<span class="label label-brand-accent">
																						{$single_muport['user']['protocol']}
																					</span></p>

																					{if $single_muport['user']['is_multi_user'] != 0}
																					<p>协议参数：<span class="label label-green">
																						{$user->id}:{$user->passwd}
																					</span></p>
																					{/if}

																					<p>混淆方式：<span class="label label-brand">
																						{$single_muport['user']['obfs']}
																					</span></p>

																					{if $single_muport['user']['is_multi_user'] == 1}
																					<p>混淆参数：<span class="label label-green">
																						{$single_muport['user']['obfs_param']}
																					</span></p>
																					{/if}

																					<p>流量比例：<span class="label label-red">
																						{$node->traffic_rate}
																					</span></p>

																					<p>{$node->info}</p>

																					 </div>

																				</div>
                                                                              
																			</div>
																		{/foreach}
                                                                      
																	{/if}
																	{/if}
																{/foreach}



																{if isset($point_node)}
																	{if $point_node!=null}

																		<div class="card">
																			<div class="card-main">
																				<div class="card-inner" id="info{$id}">

																				</div>
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
												</div></div>
												{/if}
                                                          

											{/foreach}


								<p class="card-heading">VIP用户节点</p>

											{$id=1000}
											{foreach $node_prefix as $prefix => $nodes}
										{if $node_isv6[$prefix] == 0 && $node_class[$prefix]==1}
												{$id=$id+1}
                                                          	{foreach $nodes as $node}

													<div class="tile tile-collapse">
														<div data-toggle="tile" data-target="#heading{$node_order->$prefix}">
															<div class="tile-side pull-left" data-ignore="tile">
																<div class="avatar avatar-sm">
																	<span class="icon {if $node_heartbeat[$prefix]=='在线'}text-green{else}{if $node_heartbeat[$prefix]=='暂无数据'}text-orange{else}text-red{/if}{/if}">{if $node_heartbeat[$prefix]=="在线"}backup{else}{if $node_heartbeat[$prefix]=='暂无数据'}report{else}warning{/if}{/if}</span>
																</div>
															</div>
															<div class="tile-inner">
																<div class="text-overflow">
                                                                  <font color="#383838"><img src="/images/prefix/{$prefix}.jpg" height="22" width="40"> {$prefix}</font> | {if $user->class!=0}<font color="#ff9000"><i class="icon icon-lg">flight_takeoff</i></font> <strong>{else}{/if}<b><font color="#474747">{$node_alive[$prefix]}</font></b></strong> | <font color="#ff9000"><i class="icon icon-lg">import_export</i></font>  <font color="#828282">{$node_method[$prefix]}</font> | <font color="#ff9000"><i class="icon icon-lg">equalizer</i></font> {if isset($node_bandwidth[$prefix])==true}<font color="#aaaaaa">{$node_bandwidth[$prefix]}</font>{else}N/A{/if} | <font color="#ff9000"><i class="icon icon-lg">network_check</i></font> <font color="#a5a5a5">{$node->traffic_rate} 倍率</font> | <font color="#ff9000"><i class="icon icon-lg">notifications_none</i></font> <font color="#c4c4c4">{$node->status}</font>
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
</div></div></div>
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
																				<a href="javascript:void(0);" onClick="urlChange('{$node->id}',0,{if $relay_rule != null}{$relay_rule->id}{else}0{/if})">{$node->name}{if $relay_rule != null} - {$relay_rule->dist_node()->name}{/if}</a>
																				<span class="label label-brand-accent"><!--{$node->status}-->←点击节点查看配置信息</span>
																			</p>
                                                                                                                                                                             
																	
																			<!--{if $node->sort > 2 && $node->sort != 5 && $node->sort != 10}
																				<p>地址：<span class="label" >
																				<a href="javascript:void(0);" onClick="urlChange('{$node->id}',0,0)">请点这里进入查看详细信息</a>
																			{else}
																				<p>地址：<span class="label label-brand-accent">
																				{$node->server}
																			{/if}

																				</span></p>

																			{if $node->sort == 0||$node->sort==7||$node->sort==8||$node->sort==10}


																				<p>流量比例：<span class="label label-red">
																					{$node->traffic_rate}
																				</span></p>



																				{if ($node->sort==0||$node->sort==7||$node->sort==8||$node->sort==10)&&($node->node_speedlimit!=0||$user->node_speedlimit!=0)}
																					<p>节点限速：<span class="label label-green">
																						{if $node->node_speedlimit>$user->node_speedlimit}
																							{$node->node_speedlimit}Mbps
																						{else}
																							{$user->node_speedlimit}Mbps
																						{/if}
																					</span></p>
																				{/if}
																			{/if}-->

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

																			<div class="card">
																				<div class="card-main">
																					<div class="card-inner">
																					<p class="card-heading" >
																						<a href="javascript:void(0);" onClick="urlChange('{$node->id}',{$single_muport['server']->server},{if $relay_rule != null}{$relay_rule->id}{else}0{/if})">{$prefix} {if $relay_rule != null} - {$relay_rule->dist_node()->name}{/if} - 单端口 Shadowsocks - {$single_muport['server']->server} 端口</a>
																						<span class="label label-brand-accent">{$node->status}</span>
																					</p>


																					<p>地址：<span class="label label-brand-accent">
																					{$node->server}

																					</span></p>

																					<p>端口：<span class="label label-brand-red">
																						{$single_muport['user']['port']}
																					</span></p>

																					<p>加密方式：<span class="label label-brand">
																						{$single_muport['user']['method']}
																					</span></p>

																					<p>协议：<span class="label label-brand-accent">
																						{$single_muport['user']['protocol']}
																					</span></p>

																					{if $single_muport['user']['is_multi_user'] != 0}
																					<p>协议参数：<span class="label label-green">
																						{$user->id}:{$user->passwd}
																					</span></p>
																					{/if}

																					<p>混淆方式：<span class="label label-brand">
																						{$single_muport['user']['obfs']}
																					</span></p>

																					{if $single_muport['user']['is_multi_user'] == 1}
																					<p>混淆参数：<span class="label label-green">
																						{$single_muport['user']['obfs_param']}
																					</span></p>
																					{/if}

																					<p>流量比例：<span class="label label-red">
																						{$node->traffic_rate}
																					</span></p>

																					<p>{$node->info}</p>

																					 </div>

																				</div>
																			</div>
																		{/foreach}
																	{/if}
																	{/if}
																{/foreach}



																	{if isset($point_node)}
																	{if $point_node!=null}

																		<div class="card">
																			<div class="card-main">
																				<div class="card-inner" id="info{$id}">

																				</div>
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
												</div>
												{/if}

											{/foreach}

										</div>
									</div>

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
			</section>
		</div>
	</main>







{include file='user/footer.tpl'}


<script>


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
