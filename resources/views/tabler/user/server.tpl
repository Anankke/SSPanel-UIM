{include file="user/header.tpl"}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">节点列表</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">查看节点在线情况</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="row row-deck row-cards">
                                    {foreach $servers as $server}
                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                            <div class="card">
                                                {if $server['class'] === 0}
                                                    <div class="ribbon bg-blue">免费</div>
                                                {else}
                                                    <div class="ribbon bg-blue">LV. {$server['class']}</div>
                                                {/if}
                                                <div class="card-body">
                                                    <div class="row g-3 align-items-center">
                                                        <div class="col-auto">
                                                            <span class="status-indicator status-{$server['color']}
                                                                 status-indicator-animated">
                                                                <span class="status-indicator-circle"></span>
                                                                <span class="status-indicator-circle"></span>
                                                                <span class="status-indicator-circle"></span>
                                                            </span>
                                                        </div>
                                                        <div class="col">
                                                            <h2 class="page-title" style="font-size: 16px;">
                                                                {$server['name']}&nbsp;
                                                                <span class="card-subtitle my-2"
                                                                      style="font-size: 10px;">  {$server['node_bandwidth']} /
                                                                    {$server['node_bandwidth_limit']}
                                                                </span>
                                                            </h2>
                                                            <div class="text-secondary badges-list">
                                                                <span class="badge bg-blue-lt">
                                                                    <i class="ti ti-users"></i>
                                                                    {$server['online_user']}</span>
                                                                <span class="badge bg-blue-lt">
                                                                    {if $server['is_dynamic_rate']}
                                                                        动态倍率
                                                                    {else}
                                                                        {$server['traffic_rate']} 倍
                                                                    {/if}
                                                                </span>
                                                                <span class="badge bg-blue-lt">{$server['sort']}</span>
                                                                {if $server['connection_type'] !== 0}
                                                                <span class="badge bg-blue-lt">IPv6</span>
                                                                {/if}
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            {if $user->class < $server['class']}
                                                <div class="card bg-primary-lt">
                                                    <div class="card-body">
                                                        <p class="text-secondary">
                                                            <i class="ti ti-info-circle icon text-blue"></i>
                                                            你当前的账户等级小于节点等级，因此无法使用。可前往 <a
                                                                    href="/user/product">商品页面</a> 订购时间流量包
                                                        </p>
                                                    </div>
                                                </div>
                                            {/if}
                                        </div>
                                    {/foreach}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {include file="user/footer.tpl"}
