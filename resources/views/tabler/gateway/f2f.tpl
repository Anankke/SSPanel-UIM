<div class="card-inner">
    <h4>
        支付宝当面付
    </h4>
    <p class="card-heading"></p>
    <input hidden id="amount-f2fpay" name="amount-f2fpay" value="{$invoice->price}">
    <input hidden id="invoice_id" name="invoice_id" value="{$invoice->id}">
    <div id="qrarea"></div>
    <button class="btn btn-flat waves-attach" id="f2fpay" onclick="f2fpay();">
        生成付款QR Code
    </button>
</div>

<script>
    var pid = 0; 
    var flag = false;
    function f2fpay() {
        $("#readytopay").modal('show');
        $.ajax({
            type: "POST",
            url: "/user/payment/purchase/f2fpay",
            dataType: "json",
            data: {
                amount: $$getValue('amount-f2fpay'),
                invoice_id: $$getValue('invoice_id')
            },
            success: (data) => {
                if (data.ret) {
                    //console.log(data);
                    pid = data.pid;
                    $$.getElementById('qrarea').innerHTML = '<div class="text-center"><p>请使用手机支付宝扫描二维码支付</p><a id="qrcode" style="padding-top:10px;display:inline-block"></a><p>手机可点击二维码唤起支付宝支付</p></div>'
                    $("#readytopay").modal('hide');
                    new QRCode("qrcode", {
                        render: "canvas",
                        width: 200,
                        height: 200,
                        text: encodeURI(data.qrcode)
                    });
                    $$.getElementById('qrcode').setAttribute('href', data.qrcode);
                    if(flag == false){
                        setTimeout(ff2f, 1000);
                        flag = true;
                    }else{
                        return 0;
                    }
                } else {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                }
            },
            error: (jqXHR) => {
                //console.log(jqXHR);
                $("#readytopay").modal('hide');
                $("#result").modal();
                $$.getElementById('msg').innerHTML = `${
                        jqXHR
                        } 发生错误了`;
            }
        })
    }
    function ff2f() {
        $.ajax({
            type: "POST",
            url: "/payment/status/f2fpay",
            dataType: "json",
            data: {
                pid: pid
            },
            success: (data) => {
                if (data.result) {
                    //console.log(data);
                    $("#alipay").modal('hide');
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = '充值成功';
                    window.setTimeout("location.href=window.location.href", {$config['jump_delay']});
                }
            },
            error: (jqXHR) => {
                //console.log(jqXHR);
            }
        });
        tid = setTimeout(f, 1000); //循环调用触发setTimeout
    }
</script>