{include file='user/tabler_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <h2 class="page-title">
                        <span class="home-title">订阅记录</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">在最近 {$config['subscribeLog_keep_days']} 天内所有的订阅记录</span>
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
                                        <th>类型</th>
                                        <th>IP</th>
                                        <th>归属</th>
                                        <th>时间</th>
                                        <th>标识</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $logs as $log}
                                        <tr>
                                            <td>#{$log->id}</td>
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

    <script>
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
    </script>

{include file='user/tabler_footer.tpl'}