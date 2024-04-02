<div class="card-inner">
    <h4>
        EPay 在线充值
    </h4>
    <p class="card-heading"></p>
    <form class="epay" name="epay" method="post">
        {if $public_setting['epay_alipay']}
            <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value="alipay">
                <img src="/images/alipay.png" height="50px"/>
            </button>
        {/if}
        {if $public_setting['epay_wechat']}
            <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value="wxpay">
                <img src="/images/wechat.png" height="50px"/>
            </button>
        {/if}
        {if $public_setting['epay_qq']}
            <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value="qqpay">
                <img src="/images/qqpay.png" height="50px"/>
            </button>
        {/if}
        {if $public_setting['epay_usdt']}
            <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value="usdt">
                <img src="/images/usdt.png" height="50px"/>
            </button>
        {/if}
    </form>
</div>
<div class="modal modal-blur fade" id="processing-dialog" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-status bg-primary"></div>
            <div class="modal-body text-center py-4">
                <i class="ti ti-loader icon mb-2 text-primary icon-lg" style="font-size:3.5rem;"></i>
                <p id="processing-message" class="text-secondary">
                    <i class="fas fa-spinner fa-spin"></i> 正在处理你的请求，请稍候...
                </p>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('.epay').on('submit', function (e) {
            e.preventDefault();
            $('#processing-dialog').modal('show');
            var formData = {
                price: {$invoice->price},
                invoice_id: {$invoice->id},
                type: $('button[name="type"]:focus').val()
            };

            $.ajax({
                url: '/user/payment/purchase/epay',
                type: 'POST',
                data: formData,
                dataType: "json",
                success: function (data) {
                    if (data.ret === 1) {
                        $('#success-message').text(data.msg);
                        $('#success-dialog').modal('show');
                        window.location.href = data.url;
                    } else {
                        $('#fail-message').text(data.msg);
                        $('#fail-dialog').modal('show');
                    }
                },
                error: function (data) {
                    $('#fail-message').text(data.msg);
                    $('#fail-dialog').modal('show');
                },
                complete: function () {
                    $('#processing-dialog').modal('hide');
                }
            });
        });
    });
</script>
