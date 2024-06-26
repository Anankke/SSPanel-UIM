{include file='user/header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title my-3">订单列表</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">查看并管理账户中的订单</span>
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

    <script src="//{$config['jsdelivr_url']}/npm/jquery/dist/jquery.min.js"></script>

    {include file='datatable.tpl'}

    <script>
        tableConfig.ajax = {
            url: '/user/order/ajax',
            type: 'POST',
            dataSrc: 'orders'
        };
        tableConfig.order = [
            [1, 'desc']
        ];
        tableConfig.columnDefs = [
            {
                targets: [0],
                orderable: false
            }
        ];

        let table = new DataTable('#data-table', tableConfig);

        function loadTable() {
            table;
        }

        function reloadTableAjax() {
            table.ajax.reload(null, false);
        }

        loadTable();
    </script>

    {include file='user/footer.tpl'}
