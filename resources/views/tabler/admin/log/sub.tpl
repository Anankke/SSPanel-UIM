{include file='admin/header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title my-3">订阅记录</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">查看用户订阅记录</span>
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
                            <table id="data-table" class="table card-table table-vcenter text-nowrap datatable">
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

    <script>
        let table = new DataTable('#data-table', {
            "serverSide": true,
            "searching": true,
            "ordering": true,
            ajax: {
                url: '/admin/subscribe/ajax',
                type: 'POST',
                dataSrc: 'subscribes.data'
            },
            "autoWidth": false,
            'iDisplayLength': 100,
            'scrollX': true,
            'order': [
                [0, 'desc']
            ],
            columns: [
                {foreach $details['field'] as $key => $value}
                {
                    data: '{$key}'
                },
                {/foreach}
            ],
            columnDefs: [
                {
                    orderable: false,
                    targets: [4]
                },
            ],
            "dom": "<'row px-3 py-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row card-footer d-flex d-flexalign-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            language: {
                "sProcessing": "处理中...",
                "sLengthMenu": "显示 _MENU_ 条",
                "sZeroRecords": "没有匹配结果",
                "sInfo": "第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
                "sInfoEmpty": "第 0 至 0 项结果，共 0 项",
                "sInfoFiltered": "（在 _MAX_ 项中查找）",
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

        loadTable();
    </script>

    {include file='admin/footer.tpl'}
