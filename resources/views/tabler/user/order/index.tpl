{include file='user/tabler_header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">       
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">                   
                    <h2 class="page-title">
                        <span class="home-title my-3">账单列表</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">在这里查看账单列表</span>
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal"
                            data-bs-target="#redeem-dialog">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-gift" width="24"
                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <rect x="3" y="8" width="18" height="4" rx="1"></rect>
                                <line x1="12" y1="8" x2="12" y2="21"></line>
                                <path d="M19 12v7a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-7"></path>
                                <path
                                    d="M7.5 8a2.5 2.5 0 0 1 0 -5a4.8 8 0 0 1 4.5 5a4.8 8 0 0 1 4.5 -5a2.5 2.5 0 0 1 0 5">
                                </path>
                            </svg>
                            兑换礼品卡
                        </a>
                        <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal"
                            data-bs-target="#redeem-dialog">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-gift" width="24"
                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <rect x="3" y="8" width="18" height="4" rx="1"></rect>
                                <line x1="12" y1="8" x2="12" y2="21"></line>
                                <path d="M19 12v7a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-7"></path>
                                <path
                                    d="M7.5 8a2.5 2.5 0 0 1 0 -5a4.8 8 0 0 1 4.5 5a4.8 8 0 0 1 4.5 -5a2.5 2.5 0 0 1 0 5">
                                </path>
                            </svg>
                        </a>
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
                                        <th>支付方式</th>
                                        <th>账单编号</th>
                                        <th>商品类型</th>
                                        <th>商品售价</th>
                                        <th>优惠码</th>
                                        <th>账单金额</th>
                                        <th>创建时间</th>
                                        <th>支付时间</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $orders as $order}
                                        <tr>
                                            <td>
                                                <a href="/user/order/{$order->no}">详情</a>
                                            </td>
                                            <td>{$order->product_name}</td>
                                            <td>{$order->judgmentOrderStatus($order->order_status, $order->expired_at, true)}</td>
                                            <td>{$order->order_payment}</td>
                                            <td>{$order->no}</td>
                                            <td>{$order->product_type}</td>
                                            <td>{sprintf("%.2f", $order->product_price / 100)}</td>
                                            <td>{(empty($order->order_coupon)) ? 'null' : $order->order_coupon}</td>
                                            <td>{sprintf("%.2f", $order->order_price / 100)}</td>
                                            <td>{date('Y-m-d H:i:s', $order->created_at)}</td>
                                            {if $order->order_status == 'paid'}
                                                <td>{date('Y-m-d H:i:s', $order->paid_at)}</td>
                                            {else}
                                                <td>null</td>
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

    <div class="modal modal-blur fade" id="redeem-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">兑换礼品卡</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">礼品卡</label>
                        <div class="col">
                            <input id="card" type="text" class="form-control" placeholder="在此输入或粘贴礼品码">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">取消</button>
                    <button id="redeem-button" type="button" class="btn btn-primary" data-bs-dismiss="modal">兑换</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('#data_table').DataTable({
            'iDisplayLength': 25,
            'scrollX': true,
            'order': [
                [9, 'desc']
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
            },
            fnRowCallback: adjustStyle,
        });

        $("#redeem-button").click(function() {
            $.ajax({
                type: "POST",
                url: "/user/redeem",
                dataType: "json",
                data: {
                    card: $('#card').val()
                },
                success: function(data) {
                    if (data.ret == 1) {
                        $('#success-message').text(data.msg);
                        $('#success-dialog').modal('show');
                    } else {
                        $('#fail-message').text(data.msg);
                        $('#fail-dialog').modal('show');
                    }
                }
            })
        });

        $("#success-confirm").click(function() {
            location.reload();
        });

        function adjustStyle() {
            $("td:contains('已支付')").css("color", "green");
            $("td:contains('异常')").css("color", "red");
            $("td:contains('等待支付')").css("color", "orange");
            $("td:contains('null')").css("font-style", "italic");
        }
    </script>

{include file='user/tabler_footer.tpl'}