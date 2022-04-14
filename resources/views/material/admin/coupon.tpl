{include file='admin/tabler_admin_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">优惠码</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">在这里管理优惠码</span>
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal"
                            data-bs-target="#coupon-dialog">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            添加
                        </a>
                        <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal"
                            data-bs-target="#coupon-dialog" aria-label="Create new report">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
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
                                        <th>操作</th>
                                        <th>优惠码</th>
                                        <th>状态</th>
                                        <th>折扣额度</th>
                                        <th>商品范围限制</th>
                                        <th>单用户使用限制</th>
                                        <th>所有用户使用限制</th>
                                        <th>使用计数</th>
                                        <th>折扣计数</th>
                                        <th>创建时间</th>
                                        <th>过期时间</th>
                                    </tr>
                                </thead>
                                <tbody id="table_content">
                                    {foreach $coupons as $coupon}
                                        <tr>
                                            <td>{$coupon->id}</td>
                                            <td>
                                                <a href="#" onclick="edit('{$coupon->id}')">编辑</a>
                                            </td>
                                            <td>{$coupon->coupon}</td>
                                            {if time() > $coupon->expired_at}
                                                <td>已过期</td>
                                            {else}
                                                {if $coupon->use_count >= $coupon->total_limit}
                                                    <td>已用尽</td>
                                                {else}
                                                    <td>可用</td>
                                                {/if}
                                            {/if}
                                            <td>{$coupon->discount}</td>
                                            <td>{$coupon->product_limit}</td>
                                            <td>{$coupon->user_limit}</td>
                                            <td>{$coupon->total_limit}</td>
                                            <td>{$coupon->use_count}</td>
                                            <td>{$coupon->amount_count}</td>
                                            <td>{date('Y-m-d H:i:s', $coupon->created_at)}</td>
                                            <td>{date('Y-m-d H:i:s', $coupon->expired_at)}</td>
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

    <div class="modal modal-blur fade" id="coupon-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">优惠码属性</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">优惠码</label>
                        <div class="col">
                            <input id="coupon" type="text" class="form-control" placeholder="自定义文本">
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">折扣额度</label>
                        <div class="col">
                            <input id="discount" type="number" step="0.01" class="form-control" placeholder="九折填0.9">
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">时间限制 (Hour)</label>
                        <div class="col">
                            <input id="time_limit" type="number" step="1" class="form-control" placeholder="从当前时间向后推">
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">商品限制</label>
                        <div class="col">
                            <input id="product_limit" type="text" class="form-control"
                                placeholder="不限制填0，限制填商品编号，英文逗号分隔">
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">单用户限制</label>
                        <div class="col">
                            <input id="user_limit" type="text" class="form-control" placeholder="不限制填个大数">
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">所有用户限制</label>
                        <div class="col">
                            <input id="total_limit" type="text" class="form-control" placeholder="不限制填个大数">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">取消</button>
                    <button id="coupon-create" onclick="createOrUpdate('/admin/coupon', 'POST')" type="button"
                        class="btn btn-primary" data-bs-dismiss="modal">创建</button>
                    <button id="coupon-delete" type="button" class="btn btn-red" data-bs-dismiss="modal">删除</button>
                    <button id="coupon-update" type="button" class="btn btn-primary" data-bs-dismiss="modal">更新</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="success-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-success"></div>
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-green icon-lg" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <circle cx="12" cy="12" r="9" />
                        <path d="M9 12l2 2l4 -4" />
                    </svg>
                    <p id="success-message" class="text-muted">成功</p>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <a id="success-confirm" href="#" class="btn btn-success w-100" data-bs-dismiss="modal">
                                    确认
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="fail-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-danger"></div>
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 9v2m0 4v.01" />
                        <path
                            d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" />
                    </svg>
                    <p id="fail-message" class="text-muted">失败</p>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <a href="#" class="btn btn-danger w-100" data-bs-dismiss="modal">
                                    确认
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="notice-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-yellow"></div>
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-yellow icon-lg" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <circle cx="12" cy="12" r="9"></circle>
                        <line x1="12" y1="17" x2="12" y2="17.01"></line>
                        <path d="M12 13.5a1.5 1.5 0 0 1 1 -1.5a2.6 2.6 0 1 0 -3 -4"></path>
                    </svg>
                    <p id="notice-message" class="text-muted">注意</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">取消</button>
                    <button id="notice-confirm" type="button" class="btn btn-yellow" data-bs-dismiss="modal">确认</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function edit(id) {
            $("#coupon-update").show();
            $("#coupon-delete").show();
            $("#coupon-create").hide();

            coupon_id = id;

            $.ajax({
                type: "GET",
                url: "/admin/coupon/details/" + coupon_id,
                dataType: "json",
                success: function(result) {
                    $("#coupon").val(result.data.coupon);
                    $("#discount").val(result.data.discount);
                    $("#time_limit").val(result.data.time_limit);
                    $("#product_limit").val(result.data.product_limit);
                    $("#user_limit").val(result.data.user_limit);
                    $("#total_limit").val(result.data.total_limit);
                }
            });

            $('#time_limit').attr('disabled', 'disabled');
            $('#coupon-dialog').modal('show');
        }

        function createOrUpdate(ajax_url, ajax_method) {
            $.ajax({
                url: ajax_url,
                type: ajax_method,
                dataType: "json",
                data: {
                    coupon: $('#coupon').val(),
                    discount: $('#discount').val(),
                    time_limit: $('#time_limit').val(),
                    product_limit: $('#product_limit').val(),
                    user_limit: $('#user_limit').val(),
                    total_limit: $('#total_limit').val()
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
        };

        $("#coupon-delete").click(function() {
            $('#notice-message').text('确定要删除此项么');
            $('#notice-dialog').modal('show');
        });

        $("#notice-confirm").click(function() {
            $.ajax({
                url: '/admin/coupon/' + coupon_id,
                type: 'DELETE',
                dataType: "json",
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

        $("#coupon-update").click(function() {
            createOrUpdate('/admin/coupon/' + coupon_id, 'PUT');
        });

        $("#success-confirm").click(function() {
            location.reload();
        });

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

        $("#coupon-update").hide();
        $("#coupon-delete").hide();

        $('#coupon-dialog').on('hide.bs.modal', function() {
            $("#coupon-update").hide();
            $("#coupon-delete").hide();
            $("#coupon-create").show();
            $('#time_limit').removeAttr('disabled');
        });

        $("td:contains('可用')").css("color", "green");
        $("td:contains('已用尽')").css("color", "orange");
        $("td:contains('已过期')").css("color", "red");
    </script>

{include file='admin/tabler_admin_footer.tpl'}