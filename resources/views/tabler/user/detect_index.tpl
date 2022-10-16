{include file='user/tabler_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <h2 class="page-title">
                        <span class="home-title">审计规则</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">目前站点中所使用的审计规则</span>
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
                            <div class="m-0 my-2">
                                <p>为了防止滥用与确保站点可以稳定运行，特制定了如下过滤规则，当您使用节点执行这些动作时，您的通信就会被截断。</p>
                                {if $config['enable_auto_detect_ban']===true}
                                    {if $config['auto_detect_ban_type']==1}
                                    <p>每当您触发审计规则达 <strong>{$config['auto_detect_ban_number']}</strong> 次，将会被停止使用服务 <strong>{$config    ['auto_detect_ban_time']}</strong> 分钟</p>
                                    {else}
                                    <p>审计将以以下规则执行：</p>
                                    {foreach $config['auto_detect_ban'] as $key => $value}
                                        {if $value['type']=='kill'}
                                        <p><i class="mdi mdi-information-outline icon-sm"></i> 当您触碰审计规则达 <strong>{$key}</strong> 次，将会被直接 <strong>删除   账号</strong></p>
                                        {else}
                                        <p><i class="mdi mdi-information-outline icon-sm"></i> 当您触碰审计规则达 <strong>{$key}</strong> 次，将会被封禁 <strong>   {$value['time']}</strong> 分钟</p>
                                        {/if}
                                    {/foreach}
                                    {/if}
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>名称</th>
                                        <th>描述</th>
                                        <th>正则表达式</th>
                                        <th>类型</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $rules as $rule}
                                    <tr>
                                        <td>#{$rule->id}</td>
                                        <td>{$rule->name}</td>
                                        <td>{$rule->text}</td>
                                        <td>{$rule->regex}</td>
                                        {if $rule->type == 1}
                                            <td>数据包明文匹配</td>
                                        {/if}
                                        {if $rule->type == 2}
                                            <td>数据包 hex 匹配</td>
                                        {/if}
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
{include file='user/tabler_footer.tpl'}
