{include file='admin/tabler_admin_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">{$details['title']['title']}</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">
                            {$details['title']['subtitle']}
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
                            data-bs-target="#search-dialog">
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
                                        {foreach $details['field'] as $key => $value}
                                            <th>{$value}</th>
                                        {/foreach}
                                    </tr>
                                </thead>
                                <tbody id="table_content">
                                    {foreach $logs as $log}
                                        <tr>
                                            {foreach $details['field'] as $key => $value}
                                                <td>{$log->$key}</td>
                                            {/foreach}
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
                    {foreach $details['search_dialog'] as $from}
                        {if $from['type'] == 'input'}
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">{$from['info']}</label>
                                <div class="col">
                                    <input id="search-{$from['id']}" type="text" class="form-control"
                                        placeholder="{$from['placeholder']}">
                                </div>
                            </div>
                        {/if}
                        {if $from['type'] == 'textarea'}
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">{$from['info']}</label>
                                <textarea id="search-{$from['id']}" class="col form-control" rows="{$from['rows']}"
                                    placeholder="{$from['placeholder']}"></textarea>
                            </div>
                        {/if}
                        {if $from['type'] == 'select'}
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">{$from['info']}</label>
                                <select id="search-{$from['id']}" class="col form-select">
                                    {foreach $from['select'] as $key => $value}
                                        <option value="{$key}">{$value}</option>
                                    {/foreach}
                                </select>
                            </div>
                        {/if}
                    {/foreach}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">取消</button>
                    <button id="submit-query" type="button" class="btn btn-primary" data-bs-dismiss="modal">搜索</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function adjustStyle() {
            $("td:contains('成功')").css("color", "green");
            $("td:contains('失败')").css("color", "red");
        }

        function loadTable() {
            $('#data_table').DataTable({
                'iDisplayLength': 25,
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
                },
                fnRowCallback: adjustStyle,
            });
        }

        $("#submit-query").click(function() {
            $.ajax({
                type: "POST",
                url: "/admin/{$details['route']}/ajax",
                dataType: "json",
                data: {
                    {foreach $details['search_dialog'] as $from}
                        {$from['id']}: $('#search-{$from['id']}').val(),
                    {/foreach}
                },
                success: function(data) {
                    if (data.ret == 1) {
                        var str = '';
                        for (var i = 0; i < data.result.length; i++) {
                            str += "<tr><td>" + data.result[i].id +
                                {foreach $details['field'] as $key => $value}
                                    {if $key != 'id'}
                                        "</td><td>" + data.result[i].{$key} +
                                    {/if}
                                {/foreach} "</td></tr>";
                        }
                        $('#data_table').DataTable().destroy();
                        $("#table_content").html(str);
                        loadTable();
                    }
                }
            })
        });

        loadTable();
    </script>

{include file='admin/tabler_admin_footer.tpl'}