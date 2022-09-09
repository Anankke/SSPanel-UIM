﻿{include file='user/main.tpl'}

<script type="application/x-javascript"> addEventListener("load", function () {
        setTimeout(hideURLbar, 0);
    }, false);

    function hideURLbar() {
        window.scrollTo(0, 1);
    }
</script>

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
                                    <div class="card-inner ui-switch-inner">
                                        <div class="switch-btn" id="switch-cards">
                                            <a href="#" onclick="return false">
                                                <i class="mdi mdi-apps"></i>
                                            </a>
                                        </div>
                                        <div class="switch-btn" id="switch-table">
                                            <a href="#" onclick="return false">
                                                <i class="mdi mdi-format-list-bulleted"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
{*                    *}{$index = 0}
                        <div class="node-cardgroup">
{*                    *}{foreach $nodes as $node_class => $class_group}
                            <div class="nodetitle">
                                <a class="waves-effect waves-button" data-toggle="collapse" href="#cardgroup{$node_class - 1000}" aria-expanded="true" aria-controls="cardgroup{$node_class - 1000}">
                                    <span>{if $node_class < 1000}试用{elseif $node_class == 1000}免费{else}等级 {$node_class - 1000} {/if}用户节点</span>
                                    <i class="mdi mdi-chevron-down"></i>
                                </a>
                            </div>
                            <div class="card-row collapse in" id="cardgroup{$node_class - 1000}">
{*                        *}{foreach $class_group as $node}
                                <div class="node-card node-flex" cardindex="{$index}">
                                    <div class="nodemain">
                                        <div class="nodehead node-flex" title="节点名称">
{*                                        *}{if $config['enable_flag'] === true}
                                            <div class="flag">
                                                <img src="https://countryflagsapi.com/svg/{$node['flag']}" width="71" height="50"/>
                                            </div>
{*                                        *}{/if}
                                            <div class="nodename">{$node['name']}</div>
                                        </div>
                                        <div class="nodemiddle node-flex">
                                            <!-- 在线人数 -->
                                            <div class="onlinemember node-flex" title="在线人数">
                                                <i class="mdi mdi-account-group node-icon"></i>
                                                <span>{if $node['online_user'] == -1} N/A{else} {$node['online_user']}{/if}</span>
                                            </div>
                                            <!-- 节点类型 -->
                                            <div class="nodetype node-flex" title="节点类型">
                                                <span>  
                                                {if $node['sort'] == 0}
                                                Shadowsocks
                                                {elseif $node['sort'] == 9}
                                                Shadowsocksr
                                                {elseif $node['sort'] == 11}
                                                V2ray
                                                {elseif $node['sort'] == 14}
                                                Trojan
                                                {/if}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="nodeinfo node-flex">  
                                            <!-- 节点已用/剩余可用流量 -->
                                            <div class="nodetraffic node-flex" title="节点已用/剩余可用流量">
                                                <i class="mdi mdi-swap-vertical-circle node-icon"></i>
                                                <span>{if $node['traffic_limit']>0}{$node['traffic_used']}/{$node['traffic_limit']}GB{else}{$node['traffic_used']}GB{/if}</span>
                                            </div>
                                            <!-- 节点流量倍率 -->
                                            <div class="nodecheck node-flex" title="节点流量倍率">
                                                <i class="mdi mdi-chart-line-stacked node-icon"></i>
                                                <span>{$node['traffic_rate']}x</span>
                                            </div>
                                            <!-- 节点系统负载 -->
                                            <div class="nodeband node-flex" title="节点系统负载">
                                                <i class="mdi mdi-server-network node-icon"></i>
                                                <span>{$node['load']}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="nodestatus">
                                        <div class="{if $node['online'] == '1'}nodeonline{elseif $node['online'] == '0'}nodeunset{else}nodeoffline{/if}">
                                            {if $node['online']=='1'}<span class="mdi mdi-cloud">{elseif $node['online']=='0'}<span class="mdi mdi-cloud-alert">{else}<span class="mdi mdi-cloud-question">{/if}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="node-tip cust-model" tipindex="{$index++}">
{*                            *}{if $node['class'] > $user->class}
                                    <p class="card-heading" align="center">
                                        <b>
                                            <i class="mdi mdi-currency-usd-off icon-lg"></i>您当前等级不足以使用该节点，如需升级请<a href="/user/shop">点击这里</a>升级套餐
                                        </b>
                                    </p>
{*                            *}{else}
{*                                *}{foreach $node['connect'] as $mu_port}
                                    <div class="tiptitle">
                                        <a href="javascript:void(0);" onClick="urlChange('{$node['id']}',{$mu_port})">{$node['name']}{if $mu_port != 0} - {$mu_port} 端口{/if}</a>
                                        <div class="nodeload">
                                            <div class="label label-brand-accent">↑↑点击节点查看↑↑</div>
{*                                        *}{if $mu_port != 0}
                                            <div>
                                                <span class="node-icon"><i class="mdi mdi-cloud icon-lg"></i></span>
                                                <span class="node-load">单端口：<code>{$mu_port}</code></span>
                                            </div>
{*                                        *}{/if}
                                        </div>
                                    </div>
                                    <hr/>
{*                                *}{/foreach}
                                    <div class="tipmiddle">
                                        <div>
                                            <span class="node-icon"><i class="mdi mdi-chat icon-lg"></i> </span>{$node['info']}
                                        </div>
                                    </div>
{*                            *}{/if}
                                </div>
{*                        *}{/foreach}
                            </div>
{*                    *}{/foreach}
                        </div>
                        <div class="card node-table">
                            <div class="card-main">
                                <div class="card-inner margin-bottom-no">
                                    <div class="tile-wrap">
{*                                *}{$index = 0}
{*                                *}{foreach $nodes as $node_class => $class_group}
                                        <p class="card-heading">{if $node_class < 1000}试用{elseif $node_class == 1000}免费{else}等级 {$node_class - 1000} {/if}用户节点</p>
{*                                    *}{foreach $class_group as $node}
                                        <div class="tile tile-collapse">
                                            <div data-toggle="tile" data-target="#heading{$node['id']}">
                                                <div class="tile-side pull-left" data-ignore="tile">
                                                    <div class="avatar avatar-sm {if $node['online']=='1'}nodeonline{elseif $node['online']=='0'}nodeunset{else}nodeoffline{/if}">
                                                    {if $node['online']=='1'}<span class="mdi mdi-cloud">{elseif $node['online']=='0'}<span class="mdi mdi-cloud-alert">{else}<span class="mdi mdi-cloud-question">{/if}</span>
                                                    </div>
                                                </div>
                                                <div class="tile-inner">
                                                    <div class="text-overflow node-textcolor">
                                                        <span class="enable-flag" title="节点名称">
{*                                                        *}{if $config['enable_flag'] === true}
                                                            <img src="https://countryflagsapi.com/svg/{$node['flag']}" width="36" height="25"/>
{*                                                        *}{/if}
                                                            {$node['name']}
                                                        </span>
                                                        |
                                                        <span class="node-icon"><i class="mdi mdi-account-group icon-lg"></i></span>
                                                        <b><span class="node-alive" title="在线人数">{if $node['online_user'] == -1}N/A{else}{$node['online_user']}{/if}</span></b>
                                                        |
                                                        <span class="node-type" title="节点类型">
                                                        {if $node['sort'] == 0}
                                                        Shadowsocks
                                                        {elseif $node['sort'] == 9}
                                                        Shadowsocksr
                                                        {elseif $node['sort'] == 11}
                                                        V2ray
                                                        {elseif $node['sort'] == 14}
                                                        Trojan
                                                        {/if}
                                                        </span>
                                                        |
                                                        <span class="node-icon"><i class="mdi mdi-swap-vertical-circle icon-lg"></i></span>
{*                                                    *}<span class="node-band" title="节点已用/剩余可用流量">{if $node['traffic_limit']>0}{$node['traffic_used']}/{$node['traffic_limit']}GB{else}{$node['traffic_used']}GB{/if}</span>
                                                        |
                                                        <span class="node-icon"><i class="mdi mdi-chart-line-stacked icon-lg"></i></span>
                                                        <span class="node-tr" title="节点流量倍率">{$node['traffic_rate']}x</span>
                                                        |
                                                        <span class="node-icon"><i class="mdi mdi-server-network icon-lg"></i></span>
                                                        <span class="node-load" title="节点系统负载">负载：{$node['load']}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="collapsible-region collapse" id="heading{$node['id']}">
                                                <div class="tile-sub">
                                                    <br>
{*                                                *}{if $node['class'] > $user->class}
                                                    <div class="card">
                                                        <div class="card-main">
                                                            <div class="card-inner">
                                                                <p class="card-heading" align="center">
                                                                <b><i class="mdi mdi-currency-usd-off icon-lg"></i> 您当前等级不足以使用该节点，如需升级请<a href="/user/shop">点击这里</a>升级套餐</b>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
{*                                                *}{else}
                                                    <div class="card nodetip-table">
                                                        <div class="card-main">
                                                            <div class="card-inner">
                                                                <!-- 用户等级不小于节点等级 -->
{*                                                        *}{foreach $node['connect'] as $mu_port}
                                                                <p class="card-heading">
                                                                    <a href="javascript:void(0);" onClick="urlChange('{$node['id']}',{$mu_port})">{$node['name']}{if $mu_port != 0} - {$mu_port} 端口{/if}</a>
                                                                    <span class="label label-brand-accent">←点击节点查看配置信息</span>
                                                                </p>
                                                                <hr/>
{*                                                        *}{/foreach}
                                                                <div><i class="mdi mdi-chat icon-lg node-icon"></i> {$node['info']}</div>
                                                            </div>
                                                        </div>
                                                    </div>
{*                                                *}{/if}
                                                </div>
                                            </div>
                                        </div>
{*                                    *}{/foreach}
{*                                *}{/foreach}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {include file='dialog.tpl'}
                    <div aria-hidden="true" class="modal modal-va-middle fade" id="nodeinfo" role="dialog"
                        tabindex="-1">
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
    function urlChange(id, is_mu) {
        var site = `./node/${
                id
                }?ismu=${
                is_mu
                }`;
        if (id == 'guide') {
            var doc = document.getElementById('infoifram').contentWindow.document;
            doc.open();
            doc.write('<img src="../images/node.gif" style="width: 100%;height: 100%; border: none;"/>');
            doc.close();
        } else {
            document.getElementById('infoifram').src = site;
        }
        $("#nodeinfo").modal();
    }
    $(function () {
        new ClipboardJS('.copy-text');
    });
    $(".copy-text").click(function () {
        $("#result").modal();
        $$.getElementById('msg').innerHTML = '已复制，请进入软件添加。';
    });
    {literal}
    ;(function () {
        'use strict'
        //箭头旋转
        let rotateTrigger = document.querySelectorAll('a[href^="#cardgroup"]');
        let arrows = document.querySelectorAll('a[href^="#cardgroup"] i')

        for (let i = 0; i < rotateTrigger.length; i++) {
            rotatrArrow(rotateTrigger[i], arrows[i]);
        }
        //UI切换
        let elNodeCard = $$.querySelectorAll(".node-cardgroup");
        let elNodeTable = $$.querySelectorAll(".node-table");
        let switchToCard = new UIswitch('switch-cards', elNodeTable, elNodeCard, 'grid', 'tempnode');
        switchToCard.listenSwitch();
        let switchToTable = new UIswitch('switch-table', elNodeCard, elNodeTable, 'flex', 'tempnode');
        switchToTable.listenSwitch();
        switchToCard.setDefault();
        switchToTable.setDefault();
        //遮罩
        let buttongroup = document.querySelectorAll('.node-card');
        let modelgroup = document.querySelectorAll('.node-tip');
        for (let i = 0; i < buttongroup.length; i++) {
            custModal(buttongroup[i], modelgroup[i]);
        }
    })();
    {/literal}
</script>
