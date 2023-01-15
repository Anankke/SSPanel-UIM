{include file='admin/tabler_header.tpl'}

<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="//cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="//cdn.jsdelivr.net/npm/flatpickr/dist/l10n/zh.js"></script>

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">优惠码</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">
                            查看并管理优惠码
                        </span>
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal"
                            data-bs-target="#create-dialog">
                            <i class="icon ti ti-plus"></i>
                            创建
                        </a>
                        <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal"
                            data-bs-target="#create-dialog">
                            <i class="icon ti ti-plus"></i>
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
                                        {foreach $details['field'] as $key => $value}
                                            <th>{$value}</th>
                                        {/foreach}
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="create-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">优惠码内容</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {foreach $details['create_dialog'] as $detail}
                        {if $detail['type'] == 'input'}
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">{$detail['info']}</label>
                                <div class="col">
                                    <input id="{$detail['id']}" type="text" class="form-control"
                                        placeholder="{$detail['placeholder']}">
                                </div>
                            </div>
                        {/if}
                        {if $detail['type'] == 'textarea'}
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">{$detail['info']}</label>
                                <textarea id="{$detail['id']}" class="col form-control" rows="{$detail['rows']}"
                                    placeholder="{$detail['placeholder']}"></textarea>
                            </div>
                        {/if}
                        {if $detail['type'] == 'select'}
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">{$detail['info']}</label>
                                <div class="col">
                                    <select id="{$detail['id']}" class="col form-select">
                                        {foreach $detail['select'] as $key => $value}
                                            <option value="{$key}">{$value}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        {/if}
                    {/foreach}
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">过期时间</label>
                        <div class="col">
                            <input id="expire_time" type="text" class="form-control"
                                placeholder="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">取消</button>
                    <button id="create-button" onclick="createCoupon()"
                        type="button" class="btn btn-primary" data-bs-dismiss="modal">创建</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        flatpickr("#expire_time", {
            enableTime: true,
            dateFormat: "U",
            time_24hr: true,
            minDate: "today",
            locale: "zh"
        });

        var table = $('#data_table').DataTable({
            ajax: {
                url: '/admin/coupon/ajax',
                type: 'POST',
                dataSrc: 'coupons'
            },
            "autoWidth":false,
            'iDisplayLength': 10,
            'scrollX': true,
            'order': [
                [1, 'desc']
            ],
            columns: [
                {foreach $details['field'] as $key => $value}
                { data: '{$key}' },
                {/foreach}
            ],
            "columnDefs":[
                { targets:[0],orderable:false }
            ],
            "dom": "<'row px-3 py-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row card-footer d-flex d-flexalign-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            language: {
                "sProcessing": "处理中...",
                "sLengthMenu": "显示 _MENU_ 条",
                "sZeroRecords": "没有匹配结果",
                "sInfo": "第 _START_ 至 _END_ 项结果，共 _TOTAL_项",
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
                    "sPrevious": "<i class=\"titi-arrow-left\"></i>",
                    "sNext": "<i class=\"ti ti-arrow-right\"><i>",
                    "sLast": "末页"
                },
                "oAria": {
                    "sSortAscending": ": 以升序排列此列",
                    "sSortDescending": ": 以降序排列此列"
                }
            },
        });

        function loadTable() {
            table;
        }

        function createCoupon() {
            $.ajax({
                url: '/admin/coupon',
                type: 'POST',
                dataType: "json",
                data: {
                    {foreach $details['create_dialog'] as $detail}
                        {$detail['id']}: $('#{$detail['id']}').val(),
                    {/foreach}
                    expire_time: $('#expire_time').val(),
                },
                success: function(data) {
                    if (data.ret == 1) {
                        $('#success-message').text(data.msg);
                        $('#success-dialog').modal('show');
                        reloadTableAjax();
                    } else {
                        $('#fail-message').text(data.msg);
                        $('#fail-dialog').modal('show');
                    }
                }
            })
        };

        function deleteCoupon(coupon_id) {
            $('#notice-message').text('确定删除此优惠码？');
            $('#notice-dialog').modal('show');
            $('#notice-confirm').on('click', function() {
                $.ajax({
                    url: "/admin/coupon/" + coupon_id,
                    type: 'DELETE',
                    dataType: "json",
                    success: function(data) {
                        if (data.ret == 1) {
                            $('#success-message').text(data.msg);
                            $('#success-dialog').modal('show');
                            reloadTableAjax();
                        } else {
                            $('#fail-message').text(data.msg);
                            $('#fail-dialog').modal('show');
                        }
                    }
                })
            });
        };

        function reloadTableAjax() {
            table.ajax.reload(null, false);
        }

        loadTable();
    </script>

{include file='admin/tabler_footer.tpl'}
