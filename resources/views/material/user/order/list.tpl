{include file='user/tabler_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <h2 class="page-title">
                        <span class="home-title">账单列表</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">在这里查看账单列表</span>
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
                            <table id="data_table" class="table card-table table-vcenter text-nowrap datatable">
                                <thead>
                                    <tr>
                                        <th>操作</th>
                                        <th>商品名称</th>
                                        <th>账单状态</th>
                                        <th>账单编号</th>
                                        <th>商品类型</th>
                                        <th>商品售价</th>
                                        <th>优惠码</th>
                                        <th>账单金额</th>
                                        <th>创建时间</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $orders as $order}
                                        <tr>
                                            <td>
                                                <a href="/user/order/{$order->no}">详情</a>
                                            </td>
                                            <td>{$order->product_name}</td>
                                            {if $order->order_status == 'paid'}
                                                <td>已支付</td>
                                            {else}
                                                {if time() > $order->expired_at}
                                                    <td>超时</td>
                                                {else}
                                                    <td>等待支付</td>
                                                {/if}
                                            {/if}
                                            <td>{$order->no}</td>
                                            <td>{$order->product_type}</td>
                                            <td>{sprintf("%.2f", $order->product_price / 100)}</td>
                                            <td>{(empty($order->order_coupon)) ? 'null' : $order->order_coupon}</td>
                                            <td>{sprintf("%.2f", $order->order_price / 100)}</td>
                                            <td>{date('Y-m-d H:i:s', $order->created_at)}</td>
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

    <script>
        $('#data_table').DataTable({
            'iDisplayLength': 25,
            'scrollX': true,
            'order': [
                [8, 'desc']
            ],
            "dom": "<'row px-3 py-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row card-footer d-flex align-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            language: {
                "sProcessing": "处理中...",
                "sLengthMenu": "显示 _MENU_ 条",
                "sZeroRecords": "没有匹配结果",
                "sInfo": "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
                "sInfoEmpty": "显示第 0 至 0 项结果，共 0 项",
                "sInfoFiltered": "(由 _MAX_ 项结果过滤)",
                "sInfoPostFix": "",
                "sSearch": "<i class=\"ti ti-search\"></i> ",
                "sUrl": "",
                "sEmptyTable": "表中数据为空",
                "sLoadingRecords": "载入中...",
                "sInfoThousands": ",",
                "oPaginate": {
                    "sFirst": "首页",
                    "sPrevious": "<i class=\"ti ti-arrow-left\"></i>",
                    "sNext": "<i class=\"ti ti-arrow-right\"></i>",
                    "sLast": "末页"
                },
                "oAria": {
                    "sSortAscending": ": 以升序排列此列",
                    "sSortDescending": ": 以降序排列此列"
                }
            }
        });

        $("td:contains('已支付')").css("color", "green");
        $("td:contains('等待支付')").css("color", "orange");
        $("td:contains('null')").css("font-style", "italic");
    </script>

{include file='user/tabler_footer.tpl'}