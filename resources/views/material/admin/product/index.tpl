{include file='admin/tabler_admin_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span style="font-size: 36px;">商品列表</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">在这里管理商店商品</span>
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal"
                            data-bs-target="#product-dialog">
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
                            data-bs-target="#product-dialog" aria-label="Create new report">
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
                                        <th>类型</th>
                                        <th>名称</th>
                                        <th>内容</th>
                                        <th>售价</th>
                                        <th>状态</th>
                                        <th>创建时间</th>
                                        <th>更新时间</th>
                                    </tr>
                                </thead>
                                <tbody id="table_content">
                                    {foreach $products as $product}
                                        <tr>
                                            <td>{$product->id}</td>
                                            <td>
                                                <a href="#" onclick="edit('{$product->id}')">编辑</a>
                                            </td>
                                            <td>{$product->type}</td>
                                            <td>{$product->name}</td>
                                            <td>{$product->translate}</td>
                                            <td>{$product->price / 100}</td>
                                            <td>{($product->status == '1') ? '销售' : '下架'}</td>
                                            <td>{date('Y-m-d H:i:s', $product->created_at)}</td>
                                            <td>{date('Y-m-d H:i:s', $product->updated_at)}</td>
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

    <div class="modal modal-blur fade" id="product-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">商品属性</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <select id="product_type" class="form-select">
                            <option value="tatp">时间流量包</option>
                            <option value="time">时间包</option>
                            <option value="traffic">流量包</option>
                            <option value="invite" disabled>邀请码（等待开发）</option>
                            <option value="device" disabled>设备限制（等待开发）</option>
                        </select>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">商品名称</label>
                        <div class="col">
                            <input id="product_name" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">商品售价</label>
                        <div class="col">
                            <input id="product_price" type="number" step="0.01" class="form-control">
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">添加时长 (Day)</label>
                        <div class="col">
                            <input id="product_time" type="text" class="form-control" placeholder="添加时间包时只填写此项">
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">添加流量 (GB)</label>
                        <div class="col">
                            <input id="product_traffic" type="text" class="form-control" placeholder="添加流量包时只填写此项">
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">设置等级</label>
                        <div class="col">
                            <input id="product_class" type="text" class="form-control" placeholder="留空时默认为0">
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">等级时长</label>
                        <div class="col">
                            <input id="product_class_time" type="text" class="form-control" placeholder="留空时默认为与添加时长相同">
                        </div>
                    </div>
                    <div class="mb-3">
                        <select id="product_status" class="form-select">
                            <option value="1">上架状态</option>
                            <option value="0">下架</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <select id="product_reset_time" class="form-select">
                            <option value="0">购买时叠加套餐时长</option>
                            <option value="1">购买时重置为套餐时长</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <select id="product_reset_traffic" class="form-select">
                            <option value="0">购买时叠加套餐流量</option>
                            <option value="1">购买时重置为套餐流量</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <select id="product_reset_class_time" class="form-select">
                            <option value="1">直接叠加等级时长</option>
                            <option value="2">直接重置为套餐等级时长</option>
                            <option value="3">用户等级与套餐等级不同时，重置为套餐等级时长；相同时叠加</option>
                            <option value="4">用户等级与套餐等级不同时，重置为套餐等级时长；相同时重置</option>
                        </select>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">限速 (Mbps)</label>
                        <div class="col">
                            <input id="product_speed" type="text" class="form-control" placeholder="不限制填0">
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">限制设备</label>
                        <div class="col">
                            <input id="product_device" type="text" class="form-control" placeholder="不限制填0">
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">商品库存</label>
                        <div class="col">
                            <input id="product_stock" type="text" class="form-control" placeholder="不限制填个大数">
                        </div>
                    </div>
                    <div class="mb-3">
                        <textarea id="product_html" class="form-control" rows="6" placeholder="自定义HTML代码"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">取消</button>
                    <button id="product-create" onclick="createOrUpdate('/admin/product', 'POST')" type="button"
                        class="btn btn-primary" data-bs-dismiss="modal">创建</button>
                    <button id="product-delete" type="button" class="btn btn-red" data-bs-dismiss="modal">删除</button>
                    <button id="product-update" type="button" class="btn btn-primary"
                        data-bs-dismiss="modal">更新</button>
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
            $("#product-update").show();
            $("#product-delete").show();
            $("#product-create").hide();

            product_id = id;

            $.ajax({
                type: "GET",
                url: "/admin/product/details/" + product_id,
                dataType: "json",
                success: function(result) {
                    $("#product_type").val(result.data.type);
                    $("#product_name").val(result.data.name);
                    $("#product_status").val(result.data.status);
                    $("#product_html").val(result.data.html);
                    $("#product_price").val(result.data.price / 100);
                    $("#product_stock").val(result.data.product_stock);
                    $("#product_time").val(result.content.product_time);
                    $("#product_traffic").val(result.content.product_traffic);
                    $("#product_speed").val(result.content.product_speed);
                    $("#product_device").val(result.content.product_device);
                    $("#product_class").val(result.content.product_class);
                    $("#product_class_time").val(result.content.product_class_time);
                    if (result.data.type == 'tatp') {
                        $("#product_reset_time").val(result.content.product_reset_time);
                        $("#product_reset_traffic").val(result.content.product_reset_traffic);
                        $("#product_reset_class_time").val(result.content.product_reset_class_time);
                    }
                }
            });

            $('#product-dialog').modal('show');
        }

        function createOrUpdate(ajax_url, ajax_method) {
            $.ajax({
                url: ajax_url,
                type: ajax_method,
                dataType: "json",
                data: {
                    product_type: $('#product_type').val(),
                    product_name: $('#product_name').val(),
                    product_price: $('#product_price').val(),
                    product_time: $('#product_time').val(),
                    product_traffic: $('#product_traffic').val(),
                    product_class: $('#product_class').val(),
                    product_class_time: $('#product_class_time').val(),
                    product_status: $('#product_status').val(),
                    product_reset_time: $('#product_reset_time').val(),
                    product_reset_traffic: $('#product_reset_traffic').val(),
                    product_reset_class_time: $('#product_reset_class_time').val(),
                    product_speed: $('#product_speed').val(),
                    product_device: $('#product_device').val(),
                    product_stock: $('#product_stock').val(),
                    product_html: $('#product_html').val()
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

        $("#product-delete").click(function() {
            $('#notice-message').text('确定要删除此项么');
            $('#notice-dialog').modal('show');
        });

        $("#notice-confirm").click(function() {
            $.ajax({
                url: '/admin/product/' + product_id,
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

        $("#product-update").click(function() {
            createOrUpdate('/admin/product/' + product_id, 'PUT');
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

        $("#product-update").hide();
        $("#product-delete").hide();

        $('#product-dialog').on('hide.bs.modal', function() {
            $("#product-update").hide();
            $("#product-delete").hide();
            $("#product-create").show();
        });
    </script>

{include file='admin/tabler_admin_footer.tpl'}