{include file='user/main.tpl'}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">Recharge record</h1>
        </div>
    </div>
    <div class="container">
        <div class="col-lg-12 col-sm-12">
            <section class="content-inner margin-top-no">

                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <p>Recharge History</p>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    {$orderList->render()}
                    <table class="table ">
                        <tr>
                            <th>ID</th>
                            <th>Order Number</th>
                            <th>Amount</th>
                            <th>Datetime</th>
                            <th>Stutas</th>
                        </tr>
                        {if sizeof($orderList) > 0}
                            {foreach $orderList as $order}
                                <tr>
                                    <td>#{$order->id}</td>
                                    <td>{$order->yft_order}</td>
                                    <td>{$order->price} CNY</td>
                                    <td>{$order->create_time}</td>
                                    {if ($order->state == 1)}<td>Paid</td>{else}<td>Unpaid</td>{/if}
                                </tr>
                            {/foreach}
                        {else}
                            <tr>
                                <td colspan="5">No recharge record!</td>
                            </tr>
                        {/if}
                    </table>
                </div>
                <span>Total {$countPage} page</span>
                <input type="hidden" id="countPage" value="{$countPage}">
                <span>Current {$currentPage} page</span>
                <input type="hidden" id="currentPage" value="{$currentPage}">
                <a class="btn btn-brand" href="/user/yftOrder">First</a>
                <a class="btn btn-brand" href="javascript:void(0)" id="pre" onclick="goto('pre')">Previous</a>
                <a class="btn btn-brand" href="javascript:void(0)" id="nxt" onclick="goto('next')">Next</a>
                <a class="btn btn-brand" href="javascript:void(0)" id="end" onclick="goto('end')">Last</a>
                {$orderList->render()}
        </div>
    </div>
</main>
{include file='user/footer.tpl'}
<script>
    function goto(type) {
        var countPage = $("#countPage").val();
        var currentPage = $("#currentPage").val();
        if ("pre" == type){
            if (currentPage == 1 || currentPage == ""){
                $("#pre").removeAttr('onclick');
            }else {
                window.location.href = "/user/yftOrder?page=" + currentPage - 1;
            }
        }else if ("next" == type){
            if (currentPage == countPage){
                $("#pre").removeAttr('onclick');
            }else {
                window.location.href = "/user/yftOrder?page=" + currentPage + 1;
            }
        }else if ("end" == type){
            if (countPage == currentPage){
                $("#end").removeAttr('onclick');
            }else {
                window.location.href = "/user/yftOrder?page=" + countPage;
            }
        }
    }
</script>