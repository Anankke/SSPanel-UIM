{include file='user/tabler_header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">     
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">                   
                    <h2 class="page-title" style="line-height: unset;">
                        <span class="home-title">审计规则</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">当浏览的地址可以被下列规则匹配时，连接将强制中断</span>
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
                                        <th>规则名称</th>
                                        <th>正则表达式</th>
                                        <th>匹配类型</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $rules as $rule}
                                        <tr>
                                            <td>{$rule->id}</td>
                                            <td>{$rule->name}</td>
                                            <td>{$rule->regex}</td>
                                            <td>{($rule->type == '1') ? '数据包明文匹配' : '数据包 hex 匹配'}</td>
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