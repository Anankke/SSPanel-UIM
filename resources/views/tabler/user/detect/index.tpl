{include file='user/header.tpl'}

<!-- 审计规则是用来防止 DMCA 和邮件 Spam，不是用来给用户建墙用的，不要以为你在中国开机场同时把“违法网站”墙了，被抓了能少判哪怕一天的刑期 -->
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">审计规则</span>
                    </h2>
                    <div class="page-pretitle my-3">
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
                                {foreach $rules as $index => $rule}
                                    <tr>
                                        <td>#{$index+1}</td>
                                        <td>{$rule->name}</td>
                                        <td>{$rule->text}</td>
                                        <td>{$rule->regex}</td>
                                        {if $rule->type === 1}
                                            <td>数据包明文匹配</td>
                                        {/if}
                                        {if $rule->type === 2}
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

    {include file='user/footer.tpl'}
