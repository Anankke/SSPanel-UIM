







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
			<!--	<div class="row">
					<div class="col-lg-12 col-md-12">
						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
									<a href="javascript:void(0);" onClick="urlChange('guide',0,0,0)">如不知道如何查看节点的详细信息和二维码，请点我。</a>
									<p>新注册用户账号有效期7天，可免费使用<code>七天</code>洛杉矶免费节点，不计流量，其余节点正常统计流量，要使用高速和游戏节点请购买等级1套餐，请勿在任何地方公开节点地址！</p>
									<p>流量比例为2即使用1MB按照2MB流量记录记录结算。</p>
									<p>标有太阳标志的为等级1节点。</p>

								</div>
							</div>
						</div>
					</div>
				</div>-->
			
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
													<div class="tile tile-collapse">
														<div data-toggle="tile" data-target="#heading{$node_order->$prefix}">
															<div class="tile-side pull-left" data-ignore="tile">
																<div class="avatar avatar-sm">
																	<span class="icon {if $node_heartbeat[$prefix]=='在线'}text-green{else}{if $node_heartbeat[$prefix]=='暂无数据'}text-green{else}text-red{/if}{/if}">{if $node_heartbeat[$prefix]=="在线"}backup{else}{if $node_heartbeat[$prefix]=='暂无数据'}toll{else}warning{/if}{/if}</span>
																</div>
															</div>
														<div class="tile-inner">
																<div class="text-overflow">
                                                                  <font color="#B7B7B7"><img src="/images/prefix/{$prefix}.jpg" height="22" width="40"> {$prefix}</font> | {if $user->isAdmin()} <i class="icon icon-lg">flight_takeoff</i> <strong><b><font color="red">{$node_alive[$prefix]}</font></b></strong> |{/if} <i class="icon icon-lg">import_export</i>  <font color="#CD96CD">{$node_method[$prefix]}</font> | <i class="icon icon-lg">equalizer</i> {if isset($node_bandwidth[$prefix])==true}<font color="#C1CDC1">{$node_bandwidth[$prefix]}</font>{else}N/A{/if}</div>
															</div>
														</div>
														<div class="collapsible-region collapse" id="heading{$node_order->$prefix}">
															<div class="tile-sub">

																<br>

																{foreach $nodes as $node}

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
																				<span class="label label-brand-accent">{$node->status}</span>
																			</p>
                                                                                                                                                                             
																	
																			{if $node->sort > 2 && $node->sort != 5 && $node->sort != 10}
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
																			{/if}

																			<p>{$node->info}</p>
																			
																				 	
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
																						<a href="javascript:void(0);" onClick="urlChange('{$node->id}',{$single_muport['server']->server},{if $relay_rule != null}{$relay_rule->id}{else}0{/if})">{$prefix} {if $relay_rule != null} - {$relay_rule->dist_node()->name}{/if} - 单端口多用户 Shadowsocks - {$single_muport['server']->server} 端口</a>
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


								<p class="card-heading">VIP用户节点</p>

											{$id=1000}
											{foreach $node_prefix as $prefix => $nodes}
										{if $node_isv6[$prefix] == 0 && $node_class[$prefix]==1}
												{$id=$id+1}

													<div class="tile tile-collapse">
														<div data-toggle="tile" data-target="#heading{$node_order->$prefix}">
															<div class="tile-side pull-left" data-ignore="tile">
																<div class="avatar avatar-sm">
																	<span class="icon {if $node_heartbeat[$prefix]=='在线'}text-green{else}{if $node_heartbeat[$prefix]=='暂无数据'}text-orange{else}text-red{/if}{/if}">{if $node_heartbeat[$prefix]=="在线"}backup{else}{if $node_heartbeat[$prefix]=='暂无数据'}report{else}warning{/if}{/if}</span>
																</div>
															</div>
															<div class="tile-inner">
																<div class="text-overflow">
                                                                  <font color="#B7B7B7"><img src="/images/prefix/{$prefix}.jpg" height="22" width="40"> {$prefix}</font> |{if $user->isAdmin()} <i class="icon icon-lg">flight_takeoff</i> <strong><b><font color="red">{$node_alive[$prefix]}</font></b></strong> |{/if} <i class="icon icon-lg">import_export</i>  <font color="#CD96CD">{$node_method[$prefix]}</font> | <i class="icon icon-lg">equalizer</i> {if isset($node_bandwidth[$prefix])==true}<font color="#C1CDC1">{$node_bandwidth[$prefix]}</font>{else}N/A{/if}</div>
															</div>
														</div>
														<div class="collapsible-region collapse" id="heading{$node_order->$prefix}">
															<div class="tile-sub">

																<br>

																{foreach $nodes as $node}

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
																				<span class="label label-brand-accent">{$node->status}</span>
																			</p>
                                                                                                                                                                             
																	
																			{if $node->sort > 2 && $node->sort != 5 && $node->sort != 10}
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
																			{/if}

																			<p>{$node->info}</p>
																			
																				 	
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
																						<a href="javascript:void(0);" onClick="urlChange('{$node->id}',{$single_muport['server']->server},{if $relay_rule != null}{$relay_rule->id}{else}0{/if})">{$prefix} {if $relay_rule != null} - {$relay_rule->dist_node()->name}{/if} - 单端口多用户 Shadowsocks - {$single_muport['server']->server} 端口</a>
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



			
		<!--		<div class="ui-card-wrap">
					<div class="row">
						<div class="col-lg-12 col-sm-12">
							<div class="card">

								<div class="card-main">

									<div class="card-inner margin-bottom-no">
										<div class="tile-wrap">
								<p class="card-heading">教育网ipv6节点-等级0</p>
											{$id=0}
											{foreach $node_prefix as $prefix => $nodes}
{if $node_isv6[$prefix] == 1 && $node_class[$prefix]==0}
												{$id=$id+1}
													<div class="tile tile-collapse">
														<div data-toggle="tile" data-target="#heading{$node_order->$prefix}">
															<div class="tile-side pull-left" data-ignore="tile">
																<div class="avatar avatar-sm">
																	<span class="icon {if $node_heartbeat[$prefix]=='在线'}text-green{else}{if $node_heartbeat[$prefix]=='暂无数据'}text-green{else}text-red{/if}{/if}">{if $node_heartbeat[$prefix]=="在线"}backup{else}{if $node_heartbeat[$prefix]=='暂无数据'}toll{else}warning{/if}{/if}</span>
																</div>
															</div>
															<div class="tile-inner">
																<div class="text-overflow">{$prefix}</font></div>
															</div>
														</div>
														<div class="collapsible-region collapse" id="heading{$node_order->$prefix}">
															<div class="tile-sub">

																<br>

																{foreach $nodes as $node}

 																{if $node->node_class > $user->class}

																		<div class="card">
																		<div class="card-main">
																			<div class="card-inner">
																			<p class="card-heading"><b>无查看权限，请升级等级1</b></p>
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
																				<span class="label label-brand-accent">{$node->status}</span>
																			</p>
                                                                                                                                                                             
																	
																			{if $node->sort > 2 && $node->sort != 5 && $node->sort != 10}
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
																			{/if}

																			<p>{$node->info}</p>
																			
																				 	
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
																						<a href="javascript:void(0);" onClick="urlChange('{$node->id}',{$single_muport['server']->server},{if $relay_rule != null}{$relay_rule->id}{else}0{/if})">{$prefix} {if $relay_rule != null} - {$relay_rule->dist_node()->name}{/if} - 单端口多用户 Shadowsocks - {$single_muport['server']->server} 端口</a>
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
								<p class="card-heading">教育网ipv6节点-等级1</p>

											{$id=0}
											{foreach $node_prefix as $prefix => $nodes}
{if $node_isv6[$prefix] == 1 && $node_class[$prefix]==1}
												{$id=$id+1}

													<div class="tile tile-collapse">
														<div data-toggle="tile" data-target="#heading{$node_order->$prefix}">
															<div class="tile-side pull-left" data-ignore="tile">
																<div class="avatar avatar-sm">
																	<span class="icon {if $node_heartbeat[$prefix]=='在线'}text-green{else}{if $node_heartbeat[$prefix]=='暂无数据'}text-green{else}text-red{/if}{/if}">{if $node_heartbeat[$prefix]=="在线"}backup{else}{if $node_heartbeat[$prefix]=='暂无数据'}toll{else}warning{/if}{/if}</span>
																</div>
															</div>
															<div class="tile-inner">
																<div class="text-overflow">{$prefix}</font></div>
															</div>
														</div>
														<div class="collapsible-region collapse" id="heading{$node_order->$prefix}">
															<div class="tile-sub">

																<br>

																{foreach $nodes as $node}

 																{if $node->node_class > $user->class}

																		<div class="card">
																		<div class="card-main">
																			<div class="card-inner">
																			<p class="card-heading"><b>无查看权限，请升级等级1</b></p>
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
																				<span class="label label-brand-accent">{$node->status}</span>
																			</p>
                                                                                                                                                                             
																	
																			{if $node->sort > 2 && $node->sort != 5 && $node->sort != 10}
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
																			{/if}

																			<p>{$node->info}</p>
																			
																				 	
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
																						<a href="javascript:void(0);" onClick="urlChange('{$node->id}',{$single_muport['server']->server},{if $relay_rule != null}{$relay_rule->id}{else}0{/if})">{$prefix} {if $relay_rule != null} - {$relay_rule->dist_node()->name}{/if} - 单端口多用户 Shadowsocks - {$single_muport['server']->server} 端口</a>
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
							</div> -->





		<!--		<div class="row">
					<div class="col-lg-12 col-md-12">
						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
										<p class="card-heading">注意!</p>
									<p>★如需购买等级1请<a href="/user/shop">点击这里</a>。</p>
                                         ★若不购买等级1是无法在节点列表中看到等级1专属的节点信息的，请您知悉。                              
										 <p>★使用ipv6免校园网流量的同学注意，只有标注有教育网ipv6的节点才支持ipv6，确认是否支持ipv6可访问<a href="http://bt.byr.cn" target="_blank">北邮人pt</a>，如果能够打开说明您的网络支持ipv6，打不开的话说明您的网络只支持ipv4无法使用ipv6节点。</p>
								</div>
							</div>
						</div>
					</div>
				</div>
						</div> -->

						<div aria-hidden="true" class="modal modal-va-middle fade" id="nodeinfo" role="dialog" tabindex="-1">
							<div class="modal-dialog modal-full">
								<div class="modal-content">
									<iframe class="iframe-seamless" title="Modal with iFrame" id="infoifram"></iframe>
								</div>
							</div>
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
		doc.write('<img src="https://www.zhaoj.in/wp-content/uploads/2016/07/1469595156fca44223cf8da9719e1d084439782b27.gif" style="width: 100%;height: 100%; border: none;"/>');
		doc.close();
	}
	else
	{
		document.getElementById('infoifram').src = site;
	}
	$("#nodeinfo").modal();
}
</script>
