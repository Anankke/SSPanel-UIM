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
                        <ul class="nav nav-tabs nav-fill" data-bs-toggle="tabs">
                            {foreach $class as $grade}
                                <li class="nav-item">
                                    <a href="#class-{$grade['node_class']}"
                                        class="nav-link {if $grade['node_class'] == $min_node_class}active{/if}" data-bs-toggle="tab">
                                        <i class="ti ti-box-multiple-{$grade['node_class']}"></i>&nbsp;
                                        等级 {$grade['node_class']}
                                    </a>
                                </li>
                            {/foreach}
                        </ul>
                        <div class="card-body">
                            <div class="tab-content">
                                {foreach $class as $grade}
                                    {$display_marker = '0'}
                                    <div class="tab-pane {if $grade['node_class'] == $min_node_class}active show{/if}"
                                        id="class-{$grade['node_class']}">
                                        <div class="row row-cards">
                                            {foreach $servers as $server}
                                                {if $server->node_class == $grade['node_class']}
                                                    {if $user->class < $server->node_class}
                                                        {if $display_marker == '0'}
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
                                                            {$display_marker = $display_marker + 1}
                                                        {/if}
                                                    {/if}
                                                    <div class="col-md-4 col-sm-12">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div class="row g-3 align-items-center">
                                                                    <div class="col-auto">
                                                                        <span
                                                                            class="status-indicator status-{$server->getNodeStatusColor()} status-indicator-animated">
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
                                                                                    {round($server->node_bandwidth / 1073741824)} GB /
                                                                                    不限
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
                                                                                <!-- {if ($server->get_node_online_status() == '1')}
                                                                                    <li class="list-inline-item"><span
                                                                                            class="text-green">Up</span></li>
                                                                                {else}
                                                                                    <li class="list-inline-item"><span
                                                                                            class="text-red">Down</span></li>
                                                                                {/if} -->
                                                                                <li class="list-inline-item">
                                                                                    <i class="ti ti-users"></i>&nbsp;
                                                                                    {$server->get_node_online_user_count()}
                                                                                </li>
                                                                                <li class="list-inline-item">
                                                                                    <i class="ti ti-rocket"></i>&nbsp;
                                                                                    {$server->traffic_rate}x
                                                                                </li>
                                                                                {if $server->sort == '11' && $user->class >= $server->node_class}
                                                                                    <li class="list-inline-item">
                                                                                        <a class="ti ti-copy"
                                                                                            data-clipboard-text="{URL::getV2Url($user, $server)}"
                                                                                            style="text-decoration: none;">
                                                                                        </a>
                                                                                    </li>
                                                                                {/if}
                                                                                <li class="list-inline-item">
                                                                                    <span id="more-details" class="pop form-help"
                                                                                        data-bs-toggle="popover"
                                                                                        data-bs-placement="top" data-bs-content="
                                                                                        <p>每月 {$server->bandwidthlimit_resetday} 日重置用量</p>
                                                                                        <p>{$server->info}</p>
                                                                                        {if $user->is_admin}
                                                                                            <p>节点备注：{$server->remark}</p>
                                                                                            <a href='/admin/node/{$server->id}/edit'>编辑节点</a>
                                                                                        {/if}" data-bs-html="true"
                                                                                        data-bs-original-title="" title="">?
                                                                                    </span>
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

    <div class="modal modal-blur fade" id="success-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-success"></div>
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-green icon-lg" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <circle cx="12" cy="12" r="9" />
                        <path d="M9 12l2 2l4 -4" />
                    </svg>
                    <p id="success-message" class="text-muted">成功</p>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <a href="#" class="btn w-100" data-bs-dismiss="modal">
                                    好
                                </a>
                            </div>
                        </div>
                    </div>
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

        {literal}                     
            // https://zablog.me/2015/10/25/Popover/ 非常感谢
            $(document).ready(
                function() {
                    $(".pop").popover({placement:'left', trigger:'manual', delay: {show: 100, hide: 100}, html: true,
                    title: function() {
                        return $("#data-original-title").html();
                    },
                    content: function() {
                        return $("#data-content").html(); // 把content变成html
                    }
                });
            $('body').click(function(event) {
                var target = $(event.target); // 判断自己当前点击的内容
                if (!target.hasClass('popover') &&
                    !target.hasClass('pop') &&
                    !target.hasClass('popover-content') &&
                    !target.hasClass('popover-title') &&
                    !target.hasClass('arrow')) {
                    $('.pop').popover('hide'); // 当点击body的非弹出框相关的内容的时候，关闭所有popover
                }
            });
            $(".pop").click(function(event) {
            $('.pop').popover('hide'); // 当点击一个按钮的时候把其他的所有内容先关闭
            });
            }
            );
        {/literal}
    </script>
{include file='user/tabler_footer.tpl'}