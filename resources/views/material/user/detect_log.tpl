{include file='user/main.tpl'}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">审计记录查看</h1>
        </div>
    </div>
    <div class="container">
        <div class="col-lg-12 col-md-12">
            <section class="content-inner margin-top-no">
                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <p>系统中所有审计记录。</p>
                            <p>关于隐私：注意，我们仅用以下规则进行实时匹配和记录匹配到的规则，您的通信方向和通信内容我们不会做任何记录，请您放心。也请您理解我们对于这些不当行为的管理，谢谢。</p>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <div class="card-table">
                                <div class="table-responsive table-user">
                                    {$logs->render()}
                                    <table class="table">
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
                                        {foreach $logs as $log}
                                            {if $log->DetectRule() != null}
                                                <tr>
                                                    <td>#{$log->id}</td>
                                                    <td>{$log->node_id}</td>
                                                    <td>{$log->Node()->name}</td>
                                                    <td>{$log->list_id}</td>
                                                    <td>{$log->DetectRule()->name}</td>
                                                    <td>{$log->DetectRule()->text}</td>
                                                    <td>{$log->DetectRule()->regex}</td>
                                                    {if $log->DetectRule()->type == 1}
                                                        <td>数据包明文匹配</td>
                                                    {/if}
                                                    {if $log->DetectRule()->type == 2}
                                                        <td>数据包 hex 匹配</td>
                                                    {/if}
                                                    <td>{date('Y-m-d H:i:s',$log->datetime)}</td>
                                                </tr>
                                            {/if}
                                        {/foreach}
                                    </table>
                                    {$logs->render()}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</main>

{include file='user/footer.tpl'}