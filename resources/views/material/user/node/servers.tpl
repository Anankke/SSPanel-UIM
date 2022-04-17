{include file='user/tabler_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">节点列表</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">查看节点在线情况</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="m-0 my-2">
                                描述中分别表述为：该节点的在线人数，该节点的流量倍率
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <ul class="nav nav-tabs nav-fill" data-bs-toggle="tabs">
                            {foreach $class as $grade}
                                <li class="nav-item">
                                    <a href="#class-{$grade['node_class']}"
                                        class="nav-link {if $grade['node_class'] == '0'}active{/if}" data-bs-toggle="tab">
                                        <i class="ti ti-box-multiple-{$grade['node_class']}"></i>&nbsp;
                                        等级 {$grade['node_class']}
                                    </a>
                                </li>
                            {/foreach}
                        </ul>
                        <div class="card-body">
                            <div class="tab-content">
                                {foreach $class as $grade}
                                    <div class="tab-pane {if $grade['node_class'] == '0'}active show{/if}"
                                        id="class-{$grade['node_class']}">
                                        <div class="row row-cards">
                                            {foreach $servers as $server}
                                                {if $server->node_class == $grade['node_class']}
                                                    <div class="col-md-3 col-sm-12">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div class="row g-3 align-items-center">
                                                                    <div class="col-auto">
                                                                        <span
                                                                            class="status-indicator status-{if ($server->get_node_online_status() == '1')}green{else}red{/if} status-indicator-animated">
                                                                            <span class="status-indicator-circle"></span>
                                                                            <span class="status-indicator-circle"></span>
                                                                            <span class="status-indicator-circle"></span>
                                                                        </span>
                                                                    </div>
                                                                    <div class="col">
                                                                        <h2 class="page-title" style="font-size: 16px;">
                                                                            {$server->name}&nbsp;
                                                                            <span class="card-subtitle my-2"
                                                                                style="font-size: 10px;">
                                                                                {if $server->node_bandwidth_limit == '0'}
                                                                                    不限流量
                                                                                {else}
                                                                                    {round($server->node_bandwidth / 1073741824)}
                                                                                    GB /
                                                                                    {round($server->node_bandwidth_limit / 1073741824)}
                                                                                    GB
                                                                                {/if}
                                                                            </span>
                                                                        </h2>
                                                                        <div class="text-muted">
                                                                            <ul class="list-inline list-inline-dots mb-0">
                                                                                {if ($server->get_node_online_status() == '1')}
                                                                                    <li class="list-inline-item"><span
                                                                                            class="text-green">Up</span></li>
                                                                                {else}
                                                                                    <li class="list-inline-item"><span
                                                                                            class="text-red">Down</span></li>
                                                                                {/if}
                                                                                <li class="list-inline-item">
                                                                                    <i class="ti ti-users"></i>&nbsp;
                                                                                    {$server->get_node_online_user_count()}
                                                                                </li>
                                                                                <li class="list-inline-item">
                                                                                    <i class="ti ti-rocket"></i>&nbsp;
                                                                                    {$server->traffic_rate}x
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                {/if}
                                            {/foreach}
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{include file='user/tabler_footer.tpl'}