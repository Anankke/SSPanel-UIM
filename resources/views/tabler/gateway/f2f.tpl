<script src="//{$config['jsdelivr_url']}/npm/jquery/dist/jquery.min.js"></script>

<div class="card-inner">
    <h4>
        支付宝当面付
    </h4>
    <p class="card-heading"></p>
    <div id="f2f-qrcode"></div>
    <button class="btn btn-flat waves-attach" id="f2fpay-button" type="button" onclick="f2fpay();">
        生成付款QR Code
    </button>
</div>

<script>
    let f2fQrcode = $('#f2fpay-button');

    function f2fpay() {
        $.ajax({
            type: "POST",
            url: "/user/payment/purchase/f2f",
            dataType: "json",
            data: {
                amount: {$invoice->price},
                invoice_id: {$invoice->id},
            },
            success: (data) => {
                if (data.ret === 1) {
                    f2fQrcode.remove();
                    f2fQrcode.append('<div class="text-center"><p>手机支付宝扫描支付</p></div>');
                    new QRCode("f2f-qrcode", {
                        text: data.qrcode,
                        width: 200,
                        height: 200,
                        colorDark: '#000000',
                        colorLight: '#ffffff',
                        correctLevel: QRCode.CorrectLevel.H,
                    });
                    f2fQrcode.append('<div class="text-center my-3"><p>支付成功后请手动刷新页面</p></div>');
                    f2fQrcode.attr('href', data.qrcode);
                } else {
                    $('#fail-message').text(data.msg);
                    $('#fail-dialog').modal('show');
                }
            }
        })
    }
</script>
