{include file='admin/tabler_header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title" style="line-height: unset;">
                        <span class="home-title">工单列表</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">查看并回复用户工单</span>
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
                                        <th>操作</th>
                                        {foreach $details['field'] as $key => $value}
                                            <th>{$value}</th>
                                        {/foreach}
                                    </tr>
                                </thead>
                                <tbody id="table_content">
                                    {foreach $tickets as $ticket}
                                        <tr>
                                            <td>
                                                <button type="button" class="btn btn-red" id="delete-ticket" 
                                                onclick="deleteTicket({$ticket->id})">删除</button>
                                                <button type="button" class="btn btn-orange" id="close-ticket" 
                                                onclick="closeTicket({$ticket->id})">关闭</button>
                                                <a class="btn btn-blue" href="/admin/ticket/{$ticket->id}/view">查看</a>
                                            </td>
                                            {foreach $details['field'] as $key => $value}
                                                {if $key === 'status'}
                                                <td>{Tools::getTicketStatus($ticket)}</td>
                                                {/if}
                                                {if $key === 'type'}
                                                <td>{Tools::getTicketType($ticket)}</td>
                                                {/if}
                                                {if $key === 'datetime'}
                                                <td>{Tools::toDateTime($ticket->$key)}</td>
                                                {/if}
                                                <td>{$ticket->$key}</td>
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

    <script>
        function adjustStyle() {
            $("td:contains('open_wait_admin')").css("color", "green");
            $("td:contains('open_wait_user')").css("color", "blue");
            $("td:contains('closed')").css("color", "red");
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

        function closeTicket(ticket_id) {
            $('#notice-message').text('确定关闭此工单');
            $('#notice-dialog').modal('show');
            $('#notice-confirm').on('click', function () {
                $.ajax({
                    url: "/admin/ticket/" + ticket_id + '/close',
                    type: 'PUT',
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
                });
            });
        };

        function deleteTicket(ticket_id) {
            $('#notice-message').text('确定删除此工单');
            $('#notice-dialog').modal('show');
            $('#notice-confirm').on('click', function() {
                $.ajax({
                    url: "/admin/ticket/" + ticket_id,
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
        };

        $("#success-confirm").click(function() {
            location.reload();
        });

        loadTable();
    </script>

{include file='admin/tabler_footer.tpl'}