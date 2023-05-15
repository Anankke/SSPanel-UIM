{include file='user/header.tpl'}

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
                <div class="col-auto">
                    <div class="btn-list">
                        <a href="#" class="btn btn-primary" data-bs-toggle="modal"
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
                    <div class="card">
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

    <div class="modal modal-blur fade" id="apply-giftcard-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">兑换礼品卡</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3 row">
                        <div class="col">
                            <input id="giftcard" type="text" class="form-control" placeholder="输入礼品卡卡号并点击兑换">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">取消</button>
                    <button id="apply-giftcard"
                            type="button" class="btn btn-primary" data-bs-dismiss="modal">兑换</button>
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

{include file='user/footer.tpl'}