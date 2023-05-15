{include file='user/header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">审计记录</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">系统中所有审计记录</span>
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
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>事件ID</th>
                                        <th>节点ID</th>
                                        <th>节点名称</th>
                                        <th>规则ID</th>
                                        <th>名称</th>
                                        <th>描述</th>
                                        <th>正则表达式</th>
                                        <th>类型</th>
                                        <th>时间</th>
                                    </tr>
                                </thead>
                                <tbody>
                                {foreach $logs as $log}
                                <tr>
                                    <td>#{$log->id}</td>
                                    <td>{$log->node_id}</td>
                                    <td>{$log->node_name}</td>
                                    <td>{$log->list_id}</td>
                                    <td>{$log->rule->name}</td>
                                    <td>{$log->rule->text}</td>
                                    <td>{$log->rule->regex}</td>
                                    <td>{$log->rule->type}</td>
                                    <td>{$log->datetime}</td>
                                </tr>
                                {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

{include file='user/footer.tpl'}
