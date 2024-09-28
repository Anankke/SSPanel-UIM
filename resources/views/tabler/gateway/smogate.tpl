<div class="card-inner">
    <h4>
        支付宝当面付
    </h4>
    <p class="card-heading"></p>
    <input hidden id="amount-smogate" name="amount-smogate" value="{$invoice->price}">
    <input hidden id="invoice_id" name="invoice_id" value="{$invoice->id}">
    <div id="smogate-qrcode"></div>
    <button class="btn btn-flat waves-attach" id="smogate-button" type="button" onclick="smogate();">
        充值
    </button>
</div>

<script>
    let pid = 0;
    let flag = false;
    let paymentButton = $('#smogate-button');

    function smogate() {
        paymentButton.attr('disabled', true);
        $.ajax({
            type: "POST",
            url: "/user/payment/purchase/smogate",
            dataType: "json",
            data: {
                amount: $('#amount-smogate').val(),
                invoice_id: $('#invoice_id').val(),
            },
            success: (data) => {
                paymentButton.attr('disabled', false);
                if (data.ret === 1) {
                    pid = data.pid;
                    paymentButton.remove();
                    paymentButton.append('<div class="text-center"><p>支付宝扫描支付</p></div>');
                    new QRCode("smogate-qrcode", {
                        render: "canvas",
                        width: 200,
                        height: 200,
                        text: encodeURI(data.qrcode)
                    });
                    
                    paymentButton.append('<div class="text-center my-3"><p>支付成功后请手动刷新页面</p></div>');
                    paymentButton.attr('href', data.qrcode);
                } else {
                    $('#fail-message').text(data.msg);
                    $('#fail-dialog').modal('show');
                }
            },
            error: () => {
                paymentButton.attr('disabled', false);
            }
        })
    }
</script>