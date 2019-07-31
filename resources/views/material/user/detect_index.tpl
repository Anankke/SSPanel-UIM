{include file='user/main.tpl'}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">审计规则公示</h1>
        </div>
    </div>
    <div class="container">
        <div class="col-lg-12 col-md-12">
            <section class="content-inner margin-top-no">

                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <p>为了爱与和平，也同时为了系统的正常运行，特制定了如下过滤规则，当您使用节点执行这些动作时，您的通信就会被截断。</p>
                            <p>关于隐私：注意，我们仅用以下规则进行实时匹配和记录匹配到的规则，您的通信方向和通信内容我们不会做任何记录，请您放心。也请您理解我们对于这些不当行为的管理，谢谢。</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <div class="card-table">
                                <div class="table-responsive table-user">
                                    {$rules->render()}
                                    <table class="table">
                                        <tr>
                                            <th>ID</th>
                                            <th>名称</th>
                                            <th>描述</th>
                                            <th>正则表达式</th>
                                            <th>类型</th>
                                        </tr>
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
                                    </table>
                                    {$rules->render()}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

        </div>

    </div>
</main>


{include file='user/footer.tpl'}
