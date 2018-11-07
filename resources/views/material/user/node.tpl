﻿{include file='user/main.tpl'}

<script src="//cdn.jsdelivr.net/gh/YihanH/canvasjs.js@v2.2/canvasjs.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.3.1"></script>
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>

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
					<div class="col-lg-12 col-sm-12 nodelist">
							
							<div class="ui-switch node-switch">
								<div class="card">
									<div class="card-main">
										<div class="card-inner ui-switch">
											<div class="switch-btn" id="switch-cards"><a href="#" onclick="return false"><i class="mdui-icon material-icons">apps</i></a></div>
											<div class="switch-btn" id="switch-table"><a href="#" onclick="return false"><i class="mdui-icon material-icons">dehaze</i></a></div>
										</div>
									</div>
								</div>
							</div>
						
                    <div class="card-row node-card">
                        {$class=-1}
						{foreach $nodes as $node}
						{if $node['class']!=$class}
						    {$class=$node['class']}
							<div class="nodetitle">
								<div>
								    <span>{if $class == 0}普通{else}VIP {$node['class']} {/if}用户节点</span>	
								</div>
							</div>	
						{/if}
						<div class="card-node node-flex">
                            <div class="nodemain">
                                <div class="nodehead node-flex">
                                    {if $config['enable_flag']=='true'}<div class="flag"><img src="/images/prefix/{$node['flag']}" alt=""></div>{/if}
                                    <div class="nodename">{$node['name']}</div>
                                </div>
                                <div class="nodemiddle node-flex">
                                    <div class="onlinemember node-flex"><i class="material-icons">flight_takeoff</i><span>{if $node['online_user'] == -1}N/A{else}{$node['online_user']}{/if}</span></div>
                                    <div class="nodetype">{$node['info']}</div>
                                </div>
                                <div class="nodeinfo node-flex">
                                    <div class="nodetraffic node-flex"><i class="material-icons">equalizer</i><span>{if $node['traffic_limit']>0}{$node['traffic_used']}/{$node['traffic_limit']}{else}N/A{/if}</span></div>
                                    <div class="nodecheck node-flex">
                                        <i class="material-icons">network_check</i><span>{$node['traffic_rate']} 倍率</span>
                                    </div>
                                    <div class="nodeband node-flex"><i class="material-icons">flash_on</i><span>{$node['bandwidth']}</span></div>
                                </div>
                            </div>
                            <div class="nodestatus">
                                <div class="{if $node['online']=="1"}nodeonline{elseif $node['online']=='0'}nodeunset{else}nodeoffline{/if}">
                                    <i class="material-icons">{if $node['online']=="1"}cloud_queue{elseif $node['online']=='0'}wifi_off{else}flash_off{/if}</i>
                                </div>
                            </div>
                        </div>
						{/foreach}
					</div>

						<div class="card node-table">
							<div class="card-main">
								<div class="card-inner margin-bottom-no">
									<div class="tile-wrap">                                       									
										{foreach $nodes as $node}
                                       
										{if $node['class']!=$class}
											{$class=$node['class']}
											
											<p class="card-heading">{if $class == 0}普通{else}VIP {$node['class']} {/if}用户节点</p>	
										{/if}

										<div class="tile tile-collapse">
											<div data-toggle="tile" data-target="#heading{$node['id']}">
												<div class="tile-side pull-left" data-ignore="tile">
													<div class="avatar avatar-sm">
														<span class="icon {if $node['online']=='1'}text-green
																			 {elseif $node['online']=='0'}text-orange
																			    {else}text-red
																				    {/if}">
															{if $node['online']=="1"}backup
																{elseif $node['online']=='0'}report
																	{else}warning
																	    {/if}
														</span>
													</div>
												</div>
												<div class="tile-inner">
													<div class="text-overflow node-textcolor">
														<span class="enable-flag">
															{if $config['enable_flag']=='true'}
															   <img src="/images/prefix/{$node['flag']}" height="22" width="40" />
															{/if}
															   {$node['name']}
														</span>
														| {if $user->class!=0}
														<span class="node-icon"><i class="icon icon-lg">flight_takeoff</i></span>
														  {else}
														  {/if} 
														  <strong><b><span class="node-alive">{if $node['online_user'] == -1}N/A{else}{$node['online_user']}{/if}</span></b></strong> 
											            | <span class="node-icon"><i class="icon icon-lg">cloud</i></span>
														<span class="node-load">负载：{if $node['latest_load'] == -1}N/A{else}{$node['latest_load']}%{/if}</span> 
														| <span class="node-icon"><i class="icon icon-lg">import_export</i></span>
														<span class="node-mothed">{$node['bandwidth']}</span> 
														| <span class="node-icon"><i class="icon icon-lg">equalizer</i></span>
														{if $node['traffic_limit']>0}
															<span class="node-band">{$node['traffic_used']}/{$node['traffic_limit']}</span>
														{else}
															N/A
														{/if}
														| <span class="node-icon"><i class="icon icon-lg">network_check</i></span>
														<span class="node-tr">{$node['traffic_rate']} 倍率</span> 
														| <span class="node-icon"><i class="icon icon-lg">notifications_none</i></span>
														<span class="node-status">{$node['status']}</span>
													</div>
												</div>
											</div>

										    <div class="collapsible-region collapse" id="heading{$node['id']}">
												<div class="tile-sub">
													<br>
                                                {if $node['class'] > $user->class}
													<div class="card">
														<div class="card-main">
															<div class="card-inner">
																<p class="card-heading" align="center"><b> <i class="icon icon-lg">visibility_off</i>
																		{$user->user_name}，您无查看当前等级VIP节点的权限，如需购买VIP请<a href="/user/shop">点击这里</a>。</b></p>
															</div>
														</div>
													</div>
													{else}

													{$relay_rule = null}
													<!-- 用户等级不小于节点等级 -->

                                                    {if $node['sort'] == 10 && $node['sort'] != 11}
													{$relay_rule = $tools->pick_out_relay_rule($node->id, $user->port, $relay_rules)}
													{/if}

													{if $node['mu_only'] != 1}
													<div class="card">
														<div class="card-main">
															<div class="card-inner">
																<p class="card-heading">
																	<a href="javascript:void(0);" onClick="urlChange('{$node['id']}',0,{if $relay_rule != null}{$relay_rule->id}{else}0{/if})">{$node['name']}
																		{if $relay_rule != null} - {$relay_rule->dist_node()->name}{/if}</a>
																	<span class="label label-brand-accent">←点击节点查看配置信息</span>
																</p>
																<p>备注：{$node['info']}</p>
															</div>
														</div>
													</div>
												    {/if}

                                                    {if $node['sort'] == 0 || $node['sort'] == 10}
													{$point_node=$node}
													{/if}

													{if $node['sort'] == 11}
													{assign var=server_explode value=";"|explode:$node['v2ray_server']}
													<div class="card">
														<div class="card-main">
															<div class="card-inner">
																<p class="card-heading">
																	<a href="javascript:void(0);">{$node['name']}</a>
																</p>

																<p>地址：<span class="label label-brand-accent">
																		{$server_explode[0]}
																	</span></p>

																<p>端口：<span class="label label-brand-red">
																		{$server_explode[1]}
																	</span></p>

																<p>协议参数：<span class="label label-green">
																		{$server_explode[0]}
																	</span></p>

																<p>用户 UUID：<span class="label label-brand">
																		{$user->getUuid()}
																	</span></p>

																<p>流量比例：<span class="label label-red">
																		{$node['traffic_rate']}
																	</span></p>

																<p>AlterId：<span class="label label-green">
																		{$server_explode[2]}
																	</span></p>

																<p>VMess链接：
																	<a class="copy-text" data-clipboard-text="{URL::getV2Url($user, $node['name'], $node['v2ray_server'])}">点击复制</a>
																</p>

																<p>{$node['info']}</p>
															</div>
														</div>
													</div>
													{/if}


													{if ($node['sort'] == 0 || $node['sort'] == 10) && $node['mu_only'] != -1}
													{foreach $nodes_muport as $single_muport}

													{if !($single_muport['server']->node_class <= $user->class && ($single_muport['server']->node_group == 0 || $single_muport['server']->node_group == $user->node_group))}
														{continue}
													{/if}

													{if !($single_muport['user']->class >= $node['class'] && ($node->node_group == 0 || $single_muport['user']->node_group == $node->node_group))}
														{continue}
													{/if}

													{$relay_rule = null}

													{if $node['sort'] == 10 && $single_muport['user']['is_multi_user'] != 2}
														{$relay_rule = $tools->pick_out_relay_rule($node->id, $single_muport['server']->server, $relay_rules)}
													{/if}

														<div class="card">
															<div class="card-main">
																<div class="card-inner">
																	<p class="card-heading">
																		<a href="javascript:void(0);" onClick="urlChange('{$node['id']}',{$single_muport['server']->server},{if $relay_rule != null}{$relay_rule->id}{else}0{/if})">{$node['name']}
																			{if $relay_rule != null} - {$relay_rule->dist_node()->name}{/if} - 单端口 Shadowsocks -
																			{$single_muport['server']->server} 端口</a>
																		<span class="label label-brand-accent">{$node['status']}</span>
																	</p>
																	<p>{$node['info']}</p>
																</div>
															</div>
														</div>

													{/foreach}
                                                    {/if}

												{/if}

												{if isset($point_node)}
												{if $point_node!=null}
		
												<div class="card">
													<div class="card-main">
														<div class="card-inner" id="info{$node@index}">
		
														</div>
													</div>
												</div>
		
												<script>
													$().ready(function () {
														$('#heading{$node['id']}').on("shown.bs.tile", function () {
															$("#info{$node@index}").load("/user/node/{$point_node['id']}/ajax");
														});
													});
												</script>
												{/if}
												{/if}
												
                                                </div>
											</div>

										

										{$point_node=null}
											
										</div>
										{/foreach}
										
										

									</div>
								</div>
							</div>
						</div>
					</div>

					{include file='dialog.tpl'}
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
	function urlChange(id, is_mu, rule_id) {
		var site = './node/' + id + '?ismu=' + is_mu + '&relay_rule=' + rule_id;
		if (id == 'guide') {
			var doc = document.getElementById('infoifram').contentWindow.document;
			doc.open();
			doc.write('<img src="../images/node.gif" style="width: 100%;height: 100%; border: none;"/>');
			doc.close();
		}
		else {
			document.getElementById('infoifram').src = site;
		}
		$("#nodeinfo").modal();
	}

	$(function () {
		new Clipboard('.copy-text');
	});
	$(".copy-text").click(function () {
		$("#result").modal();
		$("#msg").html("已复制，请进入软件添加。");
	});
	{literal}
	$("#switch-cards").click(function (){
	    $(".node-card").css("display","flex");
	    $(".node-table").css("display","none");
		
    });

    $("#switch-table").click(function (){
         $(".node-card").css("display","none");
	     $(".node-table").css("display","flex");
    });
	{/literal}
</script>
