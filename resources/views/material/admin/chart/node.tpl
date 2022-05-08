{include file='admin/tabler_admin_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">{$date}</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">当日累计用量：{round($logs->sum('value') / 1024 / 1024, 2)} TB</span>
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="/admin/chart/node/{$previous_day}" class="btn btn-primary d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <line x1="5" y1="12" x2="11" y2="18"></line>
                                <line x1="5" y1="12" x2="11" y2="6"></line>
                            </svg>
                            上一日
                        </a>
                        <a href="/admin/chart/node/{$previous_day}" class="btn btn-primary d-sm-none btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <line x1="5" y1="12" x2="11" y2="18"></line>
                                <line x1="5" y1="12" x2="11" y2="6"></line>
                            </svg>
                        </a>
                        <a href="/admin/chart/node/{$next_day}" class="btn btn-primary d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-right"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <line x1="13" y1="18" x2="19" y2="12"></line>
                                <line x1="13" y1="6" x2="19" y2="12"></line>
                            </svg>
                            下一日
                        </a>
                        <a href="/admin/chart/node/{$next_day}" class="btn btn-primary d-sm-none btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-right"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <line x1="13" y1="18" x2="19" y2="12"></line>
                                <line x1="13" y1="6" x2="19" y2="12"></line>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="table-responsive">
                            <table id="data_table" class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>节点编号</th>
                                        <th>节点名称</th>
                                        <th>流量用量(GB)</th>
                                        <th>记录时间</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $logs as $log}
                                        <tr>
                                            <td>{$log->node_id}</td>
                                            <td>{$names[$log->node_id]}</td>
                                            <td>{sprintf("%.2f", $log->value / 1024)}</td>
                                            <td>{date('Y-m-d H:i:s', $log->created_at)}</td>
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
            'order': [
                [2, 'desc']
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
            },
        });
    </script>
{include file='admin/tabler_admin_footer.tpl'}