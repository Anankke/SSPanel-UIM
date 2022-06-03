{include file='admin/tabler_admin_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">财务报表</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">上个月的财务报表</span>
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
                            <table class="table table-vcenter text-nowrap card-table">
                                <thead>
                                    <tr>
                                        <th>支付网关</th>
                                        <th>成交金额</th>
                                        <th>余额抵扣</th>
                                        <th>成交数</th>
                                        <th>客单价</th>
                                        <th>手续费</th>
                                        <th>净收入</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $result as $key => $value}
                                        <tr>
                                            <td>{$key}</td>
                                            <td>{$value['deal_amount']}</td>
                                            <td>{$value['balance_payment_amount']}</td>
                                            <td>{$value['deal_order_count']}</td>
                                            <td>{$value['customer_price']}</td>
                                            <td>{$value['fee']}</td>
                                            <td>{$value['net_income']}</td>
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
{include file='admin/tabler_admin_footer.tpl'}