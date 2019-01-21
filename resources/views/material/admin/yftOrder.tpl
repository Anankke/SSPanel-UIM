{include file='admin/main.tpl'}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">充值记录</h1>
        </div>
    </div>
    <div class="container">
        <div class="col-lg-12 col-sm-12">
            <section class="content-inner margin-top-no">

                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <p>系统中充值记录。</p>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    {$orderList->render()}
                    <table class="table ">
                        <tr>
                            <th>ID</th>
                            <th>订单号</th>
                            <th>金额</th>
                            <th>充值时间</th>
                            <th>状态</th>
                        </tr>
                        {if sizeof($orderList) > 0}
                            {foreach $orderList as $order}
                                <tr>
                                    <td>#{$order->id}</td>
                                    <td>{$order->yft_order}</td>
                                    <td>{$order->price} 元</td>
                                    <td>{$order->create_time}</td>
                                    {if ($order->state == 1)}<td>已支付</td>{else}<td>未支付</td>{/if}
                                </tr>
                            {/foreach}
                        {else}
                            <tr>
                                <td colspan="5">暂无充值记录！</td>
                            </tr>
                        {/if}
                    </table>
                </div>
                <span>总共{$countPage}页</span>
                <input type="hidden" id="countPage" value="{$countPage}">
                <span>当前第{$currentPage}页</span>
                <input type="hidden" id="currentPage" value="{$currentPage}">
                <a class="btn btn-brand" href="/admin/yftOrder">首页</a>
                <a class="btn btn-brand" href="javascript:void(0)" id="pre" onclick="goto('pre')">上一页</a>
                <a class="btn btn-brand" href="javascript:void(0)" id="nxt" onclick="goto('next')">下一页</a>
                <a class="btn btn-brand" href="javascript:void(0)" id="end" onclick="goto('end')">尾页</a>
        </div>
    </div>
</main>
{include file='admin/footer.tpl'}
<script>
    function goto(type) {
        var countPage = $$.getElementById('countPage').value,
            currentPage = $$.getElementById('currentPage').value;

        if ("pre" === type) {
            if (currentPage !== 1 && currentPage !== "") {
                window.location.href = "/admin/yftOrder?page=" + {$currentPage -1};
            }
        } else if ("next" === type) {
            if (currentPage !== countPage) {
                window.location.href = "/admin/yftOrder?page=" + {$currentPage +1};
            }
        } else if ("end" == type) {
            if (countPage !== currentPage) {
                window.location.href = "/admin/yftOrder?page=" + countPage;
            }
        }
    }
</script>
