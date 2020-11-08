<div class="row">
    <div class="col-lg-6 col-md-6">
        <p class="card-heading">数字货币充值</p>
        <div class="form-group form-group-label">
            <label class="floating-label" for="bitpayamount">金额</label>
            <input class="form-control" id="bitpayamount" type="text">
        </div>
    </div>
</div>
<div class="card-action">
    <div class="card-action-btn pull-left">
        <button class="btn btn-flat waves-attach" id="bitpaySubmit" name="type" onclick="bitpay('Crypto')">
            <img src="/images/crypto.jpg" height="50px"/>
        </button>
    </div>
</div>
<script>
    var pid = 0;
    function bitpay(type) {
        var price = parseFloat($$getValue('bitpayamount'));
        //console.log("将要使用 " + type + " 充值" + price + "元");
        if (isNaN(price)) {
            $("#readytopay").modal('hide');
            $("#result").modal();
            $$.getElementById('msg').innerHTML = '请输入正确的金额！'
            return;
        }
        $('#readytopay').modal();
        $.ajax({
            url: "/user/payment/bitpay/purchase",
            data: {
                price,
                type,
            },
            dataType: 'json',
            type: "POST",
            success: (data) => {
                //console.log(data);

                if (data.errcode === 0) {
                    $("#readytopay").modal('hide');
                    $$.getElementById('msg').innerHTML = '正在跳转到数字货币支付...'
                    window.location.href = data.url;
                } else {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.errmsg
                }
            }
        });
    }
    function bitpayf() {
        $.ajax({
            type: "POST",
            url: "/payments/bitpay/status",
            dataType: "json",
{literal}
            data: { pid },
{/literal}
            success: (data) => {
                //console.log(data);
                if (data.status === 200) {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = '充值成功！'
                    window.setTimeout("location.href=window.location.href", {$config['jump_delay']});
                }
            },
            error: (jqXHR) => {
                //console.log(jqXHR);
            }
        });
        tid = setTimeout(bitpayf, 1000); //循环调用触发setTimeout
    }
</script>