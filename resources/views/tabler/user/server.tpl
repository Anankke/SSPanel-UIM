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
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal"
                            data-bs-target="#connect-info">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-circle"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <desc>Download more icon variants from https://tabler-icons.io/i/info-circle</desc>
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <circle cx="12" cy="12" r="9"></circle>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                <polyline points="11 12 12 12 12 16 13 16"></polyline>
                            </svg>
                            连接信息
                        </a>
                        <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal"
                            data-bs-target="#connect-info">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-circle"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <desc>Download more icon variants from https://tabler-icons.io/i/info-circle</desc>
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <circle cx="12" cy="12" r="9"></circle>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                <polyline points="11 12 12 12 12 16 13 16"></polyline>
                            </svg>
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
                                        {if $user->class < $server['class']}
                                        <div class="col-lg-12">
                                            <div class="card bg-primary-lt">
                                                <div class="card-body">
                                                    <p class="text-muted">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue"
                                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <circle cx="12" cy="12" r="9"></circle>
                                                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                                            <polyline points="11 12 12 12 12 16 13 16"></polyline>
                                                        </svg>
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
                                                                class="status-indicator status-indicator-animated">
                                                                <span class="status-indicator-circle"></span>
                                                                <span class="status-indicator-circle"></span>
                                                                <span class="status-indicator-circle"></span>
                                                            </span>
                                                        </div>
                                                        <div class="col">
                                                            <h2 class="page-title" style="font-size: 16px;">
                                                                {$server['name']}&nbsp;
                                                                <span class="card-subtitle my-2"
                                                                    style="font-size: 10px;">
                                                                    {if $server['traffic_limit'] == '0'}
                                                                        {round($server['traffic_used'] / 1073741824)} GB /
                                                                        不限
                                                                    {else}
                                                                        {round($server['traffic_used'] / 1073741824)} GB /
                                                                        {round($server['traffic_limit'] / 1073741824)}
                                                                        GB
                                                                    {/if}
                                                                </span>
                                                            </h2>
                                                            <div class="text-muted">
                                                                <ul class="list-inline list-inline-dots mb-0">
                                                                    <li class="list-inline-item">
                                                                        <i class="ti ti-users"></i>&nbsp;
                                                                        {$server['online_user']}
                                                                    </li>
                                                                    <li class="list-inline-item">
                                                                        <i class="ti ti-rocket"></i>&nbsp;
                                                                        {$server['traffic_rate']} 倍
                                                                    </li>
                                                                    {if $server->sort == '11' && $user->class >= $server->node_class}
                                                                    <li class="list-inline-item">
                                                                        <a class="ti ti-copy"
                                                                            data-clipboard-text="{URL::getV2Url($user, $server)}"
                                                                            style="text-decoration: none;">
                                                                        </a>
                                                                    </li>
                                                                    {/if}
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
        var clipboard = new ClipboardJS('.ti-copy');
        clipboard.on('success', function(e) {
            $('#success-message').text('已复制到剪切板');
            $('#success-dialog').modal('show');
        });
    </script>
{include file='user/tabler_footer.tpl'}
