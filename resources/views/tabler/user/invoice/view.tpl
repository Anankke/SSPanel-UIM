{include file='user/tabler_header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title my-3">账单 #{$invoice->id}</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">账单详情</span>
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-sm-12 col-md-6 col-lg-9">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">基本信息</h3>
                        </div>
                        <div class="card-body">
                            <div class="datagrid">
                                <div class="datagrid-item">
                                    <div class="datagrid-title">订单ID</div>
                                    <div class="datagrid-content">{$invoice->order_id}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">订单金额</div>
                                    <div class="datagrid-content">{$invoice->price}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">订单状态</div>
                                    <div class="datagrid-content">{$invoice->status}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">创建时间</div>
                                    <div class="datagrid-content">{$invoice->create_time}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">更新时间</div>
                                    <div class="datagrid-content">{$invoice->update_time}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">支付时间</div>
                                    <div class="datagrid-content">{$invoice->pay_time}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card my-3">
                        <div class="card-header">
                            <h3 class="card-title">账单详情</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="invoice_content_table" class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>名称</th>
                                            <th>价格</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach $invoice_content as $invoice_content_detail}
                                        <tr>
                                            <td>{$invoice_content_detail->name}</td>
                                            <td>{$invoice_content_detail->price}</td>
                                        </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">余额支付</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                当前账户可用余额：<code>{$user->money}</code> 元
                            </div>
                        </div>
                        <div class="card-footer">
                          <div class="d-flex">
                          <button id="pay-balance" class="btn" type="button">支付</button>
                          </div>
                        </div>
                    </div>
                    <div class="card my-3">
                        <div class="card-header">
                            <h3 class="card-title">网关支付</h3>
                        </div>
                        <div class="card-body">
                            {if count($payments) > 0}
                                {foreach from=$payments item=payment}
                                <div class="mb-3">
                                    {$payment::getPurchaseHTML()}
                                </div>
                                {/foreach}
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $("#pay-balance").click(function() {
            $.ajax({
                url: '/user/invoice/pay_balance',
                type: 'POST',
                dataType: "json",
                data: {
                    invoice_id: {invoice->id},
                },
                success: function(data) {
                    if (data.ret == 1) {
                        $('#success-message').text(data.msg);
                        $('#success-dialog').modal('show');
                        setTimeout(function() {
                            $(location).attr('href', '/user/invoice/');
                        }, 1500);
                    } else {
                        $('#fail-message').text(data.msg);
                        $('#fail-dialog').modal('show');
                    }
                }
            })
        });  
    </script>

{include file='user/tabler_footer.tpl'}
