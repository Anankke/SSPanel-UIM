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
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="m-0 my-2">
                                <p>描述中分别表述为：该节点的在线人数，该节点的流量倍率，该节点的类型</p>
                                <p>指示灯为绿色表示正常运行；为黄色表示当月流量用尽；为橙色表示未配置成功；为红色表示已离线，不可使用</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="row row-cards">
                                    {foreach $servers as $server}
                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                            <div class="card">
                                                {if $server["class"] === 0}
                                                <div class="ribbon bg-blue">免费</div>
                                                {else}
                                                <div class="ribbon bg-blue">LV. {$server["class"]}</div>
                                                {/if}
                                                <div class="card-body">
                                                    <div class="row g-3 align-items-center">
                                                        <div class="col-auto">
                                                            <span
                                                                class="status-indicator
                                                                {if $server["traffic_limit"] !== 0 && $server["traffic_used"] >= $server["traffic_limit"]}
                                                                status-yellow 
                                                                {elseif $server["online"] === 1}
                                                                status-green 
                                                                {elseif $server["online"] === 0}
                                                                status-orange 
                                                                {else}
                                                                status-red 
                                                                {/if}
                                                                status-indicator-animated">
                                                                <span class="status-indicator-circle"></span>
                                                                <span class="status-indicator-circle"></span>
                                                                <span class="status-indicator-circle"></span>
                                                            </span>
                                                        </div>
                                                        <div class="col">
                                                            <h2 class="page-title" style="font-size: 16px;">
                                                                {$server["name"]}&nbsp;
                                                                <span class="card-subtitle my-2"
                                                                    style="font-size: 10px;">
                                                                    {if $server["traffic_limit"] === 0}
                                                                        {round($server["traffic_used"])} GB /
                                                                        不限
                                                                    {else}
                                                                        {round($server["traffic_used"])} GB /
                                                                        {round($server["traffic_limit"])} GB
                                                                    {/if}
                                                                </span>
                                                            </h2>
                                                            <div class="text-secondary">
                                                                <ul class="list-inline list-inline-dots mb-0">
                                                                    <li class="list-inline-item">
                                                                        <i class="ti ti-users"></i>&nbsp;
                                                                        {$server["online_user"]}
                                                                    </li>
                                                                    <li class="list-inline-item">
                                                                        <i class="ti ti-rocket"></i>&nbsp;
                                                                        {$server["traffic_rate"]} 倍
                                                                    </li>
                                                                    <li class="list-inline-item">
                                                                        <i class="ti ti-server-2"></i>&nbsp;
                                                                        {if $server['sort'] === 0}
                                                                        Shadowsocks
                                                                        {elseif $server['sort'] === 11}
                                                                        V2Ray
                                                                        {elseif $server['sort'] === 14}
                                                                        Trojan
                                                                        {/if}
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {if $user->class < $server["class"]}
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

    <script>
        var clipboard = new ClipboardJS(".ti-copy");
        clipboard.on("success", function(e) {
            $("#success-message").text("已复制到剪切板");
            $("#success-dialog").modal("show");
        });
    </script>
    
{include file="user/footer.tpl"}
