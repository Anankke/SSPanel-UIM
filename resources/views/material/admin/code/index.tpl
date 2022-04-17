{include file='admin/main.tpl'}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">充值码{if $config['enable_donate']===true}与捐赠{/if}管理</h1>
        </div>
    </div>
    <div class="container">
        <div class="col-lg-12 col-md-12">
            <section class="content-inner margin-top-no">
                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <p>系统中金额流转记录。</p>
                            <p>
                                今日流水：￥{$user->calIncome("today")}<br/>
                                昨日流水：￥{$user->calIncome("yesterday")}<br/>
                                这月流水：￥{$user->calIncome("this month")}<br/>
                                上月流水：￥{$user->calIncome("last month")}<br/>
                                总共流水：￥{$user->calIncome("total")}
                            </p>
                            <p>显示表项:
                                {include file='table/checkbox.tpl'}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    {include file='table/table.tpl'}
                </div>
                <div class="fbtn-container">
                    <div class="fbtn-inner">
                        <a class="fbtn fbtn-lg fbtn-brand-accent waves-attach waves-circle waves-light"
                           href="/admin/code/create">+</a>
                    </div>
                </div>
                <div class="fbtn-container">
                    <div class="fbtn-inner">
                        <a class="fbtn fbtn-lg fbtn-brand-accent waves-attach waves-circle waves-light"
                           data-toggle="dropdown"><span class="fbtn-ori icon">add</span><span class="fbtn-sub icon">close</span></a>
                        <div class="fbtn-dropup">
                            <a class="fbtn fbtn-brand waves-attach waves-circle waves-light"
                               href="/admin/code/create"><span class="fbtn-text fbtn-text-left">充值码</span><span
                                        class="icon">code</span></a> {if $config['enable_donate']===true}
                                <a class="fbtn fbtn-green waves-attach waves-circle waves-light"
                                   href="/admin/donate/create"><span class="fbtn-text fbtn-text-left">捐赠和支出</span><span
                                            class="icon">attach_money</span></a>
                            {/if}
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>

{include file='admin/footer.tpl'}

<script>
    {include file='table/js_1.tpl'}
    $(document).ready(function () {
        {include file='table/js_2.tpl'}
    });
</script>