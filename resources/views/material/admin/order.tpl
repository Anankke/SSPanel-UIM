{include file='admin/tabler_admin_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">商品订单</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">
                            注册用户的所有订单。表格仅展示最近 500 条记录
                        </span>
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal"
                            data-bs-target="#search-dialog">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <circle cx="10" cy="10" r="7"></circle>
                                <line x1="21" y1="21" x2="15" y2="15"></line>
                            </svg>
                            搜索
                        </a>
                        <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal"
                            data-bs-target="#search-dialog" aria-label="Create new report">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <circle cx="10" cy="10" r="7"></circle>
                                <line x1="21" y1="21" x2="15" y2="15"></line>
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
                                        <th>#</th>
                                        <th>订单号</th>
                                        <th>提交用户</th>
                                        <th>名称</th>
                                        <th>类型</th>
                                        <th>售价</th>
                                        <th>优惠码</th>
                                        <th>订单金额</th>
                                        <th>订单状态</th>
                                        <th>支付方式</th>
                                        <th>创建时间</th>
                                        <th>支付时间</th>
                                        <th>执行状态</th>
                                    </tr>
                                </thead>
                                <tbody id="table_content">
                                    {foreach $logs as $log}
                                        <tr>
                                            <td>{$log->id}</td>
                                            <td>{$log->no}</td>
                                            <td>{$log->user_id}</td>
                                            <td>{$log->product_name}</td>
                                            <td>{$log->product_type}</td>
                                            <td>{sprintf("%.2f", $log->product_price / 100)}</td>
                                            <td>{(empty($log->order_coupon)) ? 'null' : $log->order_coupon}</td>
                                            <td>{sprintf("%.2f", $log->order_price / 100)}</td>
                                            {if $log->order_status == 'paid'}
                                                <td>已支付</td>
                                            {else}
                                                {if time() > $log->expired_at}
                                                    <td>超时</td>
                                                {else}
                                                    <td>等待支付</td>
                                                {/if}
                                            {/if}
                                            <td>{$log->order_payment}</td>
                                            <td>{date('Y-m-d H:i:s', $log->created_at)}</td>
                                            {if $log->order_status == 'paid'}
                                                <td>{date('Y-m-d H:i:s', $log->paid_at)}</td>
                                            {else}
                                                <td>null</td>
                                            {/if}
                                            <td>{($log->execute_status == '0') ? '未执行' : '已执行'}</td>
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

    <div class="modal modal-blur fade" id="search-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">搜索条件</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">订单号</label>
                        <div class="col">
                            <input id="no" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">提交用户</label>
                        <div class="col">
                            <input id="user_id" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">商品名称</label>
                        <div class="col">
                            <input id="product_name" type="text" class="form-control" placeholder="模糊搜索">
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">优惠码</label>
                        <div class="col">
                            <input id="order_coupon" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">商品类型</label>
                        <select id="product_type" class="col form-select">
                            <option value="all">所有类型</option>
                            <option value="tatp">时间流量包</option>
                            <option value="time">时间包</option>
                            <option value="traffic">流量包</option>
                            <option value="other">自定义商品</option>
                            <option value="invite" disabled>邀请码（等待开发）</option>
                            <option value="device" disabled>设备限制（等待开发）</option>
                        </select>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">支付方式</label>
                        <select id="order_payment" class="col form-select">
                            <option value="all">所有方式</option>
                            <option value="balance">余额支付</option>
                            {foreach $config['active_payments'] as $key => $value}
                                <option value="{$value['name']}">{$value['name']}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">订单状态</label>
                        <select id="order_status" class="col form-select">
                            <option value="all">所有状态</option>
                            <option value="paid">已支付</option>
                            <option value="pending_payment">未支付</option>
                        </select>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">执行状态</label>
                        <select id="execute_status" class="col form-select">
                            <option value="all">所有状态</option>
                            <option value="1">已执行</option>
                            <option value="0">未执行</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">取消</button>
                    <button id="submit-query" type="button" class="btn btn-primary" data-bs-dismiss="modal">搜索</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function loadTable() {
            $('#data_table').DataTable({
                'iDisplayLength': 25,
                'scrollX': true,
                'order': [
                    [0, 'desc']
                ],
                "dom": "<'row px-3 py-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row card-footer d-flex align-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                language: {
                    "sProcessing": "处理中...",
                    "sLengthMenu": "显示 _MENU_ 条",
                    "sZeroRecords": "没有匹配结果",
                    "sInfo": "第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
                    "sInfoEmpty": "第 0 至 0 项结果，共 0 项",
                    "sInfoFiltered": "(在 _MAX_ 项中查找)",
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
        }

        function adjustStyle() {
            $("td:contains('已支付')").css("color", "green");
            $("td:contains('等待支付')").css("color", "orange");
            $("td:contains('已执行')").css("color", "green");
            $("td:contains('未执行')").css("color", "orange");
            $("td:contains('null')").css("font-style", "italic");
        }

        loadTable();

        $("#submit-query").click(function() {
            $.ajax({
                type: "POST",
                url: "/admin/order/ajax",
                dataType: "json",
                data: {
                    no: $('#no').val(),
                    user_id: $('#user_id').val(),
                    product_name: $('#product_name').val(),
                    order_coupon: $('#order_coupon').val(),
                    product_type: $('#product_type').val(),
                    order_status: $('#order_status').val(),
                    order_payment: $('#order_payment').val(),
                    execute_status: $('#execute_status').val(),
                },
                success: function(data) {
                    if (data.ret == 1) {
                        var str = '';
                        for (var i = 0; i < data.result.length; i++) {
                            str += "<tr><td>" + data.result[i].id +
                                "</td><td>" + data.result[i].no +
                                "</td><td>" + data.result[i].user_id +
                                "</td><td>" + data.result[i].product_name +
                                "</td><td>" + data.result[i].product_type +
                                "</td><td>" + data.result[i].product_price +
                                "</td><td>" + data.result[i].order_coupon +
                                "</td><td>" + data.result[i].order_price +
                                "</td><td>" + data.result[i].order_status +
                                "</td><td>" + data.result[i].order_payment +
                                "</td><td>" + data.result[i].created_at +
                                "</td><td>" + data.result[i].paid_at +
                                "</td><td>" + data.result[i].execute_status + "</td></tr>";
                        }
                        $('#data_table').DataTable().destroy();
                        $("#table_content").html(str);
                        loadTable();
                        adjustStyle();
                    }
                }
            })
        });

        adjustStyle();
    </script>

{include file='admin/tabler_admin_footer.tpl'}