{include file="user/tabler_header.tpl"}
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
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal"
                            data-bs-target="#connect-info">
                            <i class="ti ti-info-circle icon"></i>
                            连接信息
                        </a>
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
                                <p class="my-2">指示灯为绿色表示正常运行；为黄色表示当月流量用尽；为橙色表示未配置成功；为红色表示已离线，不可使用</p>
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
                                        {if $user->class < $server["class"]}
                                        <div class="col-lg-12">
                                            <div class="card bg-primary-lt">
                                                <div class="card-body">
                                                    <p class="text-muted">
                                                        <i class="ti ti-info-circle icon text-blue"></i>
                                                        你当前的账户等级小于下列节点等级，因此仅能查看公开信息而无法使用。可前往 <a
                                                            href="/user/product">商店</a> 订购相应等级套餐
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        {/if}
                                        <div class="col-md-4 col-sm-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="row g-3 align-items-center">
                                                        <div class="col-auto">
                                                            <span
                                                                class="status-indicator
                                                                {if $server["traffic_used"] >= $server["traffic_limit"]}
                                                                status-yellow 
                                                                {elseif $server["online"] =="1"}
                                                                status-green 
                                                                {elseif $node["online"] =="0"}
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
                                                                    {if $server["traffic_limit"] == "0"}
                                                                        {round($server["traffic_used"])} GB /
                                                                        不限
                                                                    {else}
                                                                        {round($server["traffic_used"])} GB /
                                                                        {round($server["traffic_limit"])} GB
                                                                    {/if}
                                                                </span>
                                                            </h2>
                                                            <div class="text-muted">
                                                                <ul class="list-inline list-inline-dots mb-0">
                                                                    <li class="list-inline-item">
                                                                        <i class="ti ti-users"></i>&nbsp;
                                                                        {$server["online_user"]}
                                                                    </li>
                                                                    <li class="list-inline-item">
                                                                        <i class="ti ti-rocket"></i>&nbsp;
                                                                        {$server["traffic_rate"]} 倍
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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
    </div>

    <div class="modal modal-blur fade" id="connect-info" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">连接信息</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>项目</th>
                                        <th>内容</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>端口</td>
                                        <td>{$user->port}</td>
                                    </tr>
                                    <tr>
                                        <td>加密</td>
                                        <td>{$user->method}</td>
                                    </tr>
                                    <tr>
                                        <td>协议</td>
                                        <td>{$user->protocol}</td>
                                    </tr>
                                    <tr>
                                        <td>混淆</td>
                                        <td>{$user->obfs}</td>
                                    </tr>
                                    <tr>
                                        <td>混淆参数</td>
                                        <td>{$user->obfs_param}</td>
                                    </tr>
                                    <tr>
                                        <td>连接密码</td>
                                        <td>{$user->passwd}</td>
                                    </tr>
                                    <tr>
                                        <td>UUID</td>
                                        <td>{$user->uuid}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">确认</button>
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
{include file="user/tabler_footer.tpl"}
