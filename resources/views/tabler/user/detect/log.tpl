{include file='user/tabler_header.tpl'}

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
                                        <th>ID</th>
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
                                    {assign var="rule" value=$log->rule()}
                                    {if $rule != null}
                                        <tr>
                                            <td>#{$log->id}</td>
                                            <td>{$log->node_id}</td>
                                            <td>{$log->Node()->name}</td>
                                            <td>{$log->list_id}</td>
                                            <td>{$rule->name}</td>
                                            <td>{$rule->text}</td>
                                            <td>{$rule->regex}</td>
                                            {if $rule->type == 1}
                                                <td>数据包明文匹配</td>
                                            {/if}
                                            {if $rule->type == 2}
                                                <td>数据包 hex 匹配</td>
                                            {/if}
                                            <td>{date('Y-m-d H:i:s',$log->datetime)}</td>
                                        </tr>
                                    {/if}
                                {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

{include file='user/tabler_footer.tpl'}
