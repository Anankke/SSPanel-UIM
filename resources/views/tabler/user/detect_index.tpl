{include file='user/tabler_header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title" style="line-height: unset;">
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
