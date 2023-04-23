{include file='user/tabler_header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">       
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">余额记录</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">查看账户余额变动记录</span>
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal"
                           data-bs-target="#apply-giftcard-dialog">
                            <i class="icon ti ti-cash-banknote"></i>
                            兑换礼品卡
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck">
                <div class="col-sm-12 col-lg-12">
                    <div class="card my-3">
                        <div class="card-header">
                            <h3 class="card-title">账户余额记录</h3>
                        </div>
                        {if $moneylogs->count() !== 0}
                            <div class="table-responsive">
                                <table class="table card-table table-vcenter text-nowrap datatable">
                                    <thead>
                                    <tr>
                                        <th>事件ID</th>
                                        <th>变动前余额</th>
                                        <th>变动后余额</th>
                                        <th>变动金额</th>
                                        <th>备注</th>
                                        <th>变动时间</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {foreach $moneylogs as $moneylog}
                                        <tr>
                                            <td>{$moneylog->id}</td>
                                            <td>{$moneylog->before}</td>
                                            <td>{$moneylog->after}</td>
                                            <td>{$moneylog->amount}</td>
                                            <td>{$moneylog->remark}</td>
                                            <td>{$moneylog->create_time}</td>
                                        </tr>
                                    {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        {else}
                            <div class="card-body">
                                <p>没有找到记录</p>
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $("#apply-giftcard").click(function() {
            $.ajax({
                url: '/user/giftcard',
                type: 'POST',
                dataType: "json",
                data: {
                    giftcard: $('#giftcard').val(),
                },
                success: function(data) {
                    if (data.ret === 1) {
                        $('#success-message').text(data.msg);
                        $('#success-dialog').modal('show');
                    } else {
                        $('#fail-message').text(data.msg);
                        $('#fail-dialog').modal('show');
                    }
                }
            })
        });
    </script>

{include file='user/tabler_footer.tpl'}