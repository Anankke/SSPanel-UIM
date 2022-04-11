{include file='admin/tabler_admin_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span style="font-size: 36px;">订阅记录</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">在最近 {$config['subscribeLog_keep_days']} 天内所有用户的订阅记录。表格仅展示最近 500
                            条记录</span>
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
                                        <th>编号</th>
                                        <th>昵称</th>
                                        <th>类型</th>
                                        <th>地址</th>
                                        <th>地址信息</th>
                                        <th>时间</th>
                                        <th>标头</th>
                                    </tr>
                                </thead>
                                <tbody id="table_content">
                                    {foreach $logs as $log}
                                        <tr>
                                            <td>{$log->id}</td>
                                            <td>{$log->user_id}</td>
                                            <td>{$log->user_name}</td>
                                            <td>{$log->subscribe_type}</td>
                                            <td>{$log->request_ip}</td>
                                            <td>{Tools::getIpInfo($log->request_ip)}</td>
                                            <td>{$log->request_time}</td>
                                            <td>{$log->request_user_agent}</td>
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
                        <label class="form-label col-3 col-form-label">用户编号</label>
                        <div class="col">
                            <input id="user_id" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">用户昵称</label>
                        <div class="col">
                            <input id="user_name" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">用户邮箱</label>
                        <div class="col">
                            <input id="email" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">发起地址</label>
                        <div class="col">
                            <input id="request_ip" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">订阅类型</label>
                        <div class="col">
                            <input id="subscribe_type" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-3 col-form-label">请求标头</label>
                        <div class="col">
                            <input id="request_user_agent" type="text" class="form-control">
                        </div>
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

        loadTable();

        $("#submit-query").click(function() {
            $.ajax({
                type: "POST",
                url: "/admin/subscribe/ajax",
                dataType: "json",
                data: {
                    email: $('#email').val(),
                    user_id: $('#user_id').val(),
                    user_name: $('#user_name').val(),
                    request_ip: $('#request_ip').val(),
                    subscribe_type: $('#subscribe_type').val(),
                    request_user_agent: $('#request_user_agent').val(),
                },
                success: function(data) {
                    if (data.ret == 1) {
                        var str = '';
                        for (var i = 0; i < data.result.length; i++) {
                            str += "<tr><td>" + data.result[i].id +
                                "</td><td>" + data.result[i].user_id +
                                "</td><td>" + data.result[i].user_name +
                                "</td><td>" + data.result[i].subscribe_type +
                                "</td><td>" + data.result[i].request_ip +
                                "</td><td>" + data.result[i].ip_info +
                                "</td><td>" + data.result[i].request_time +
                                "</td><td>" + data.result[i].request_user_agent + "</td></tr>";
                        }
                        $('#data_table').DataTable().destroy();
                        $("#table_content").html(str);
                        loadTable();
                    }
                }
            })
        });
    </script>

{include file='admin/tabler_admin_footer.tpl'}