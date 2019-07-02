<div class="card-inner">
    <p class="card-heading">充值</p>
    您的余额:{$user->money}
    <h5>支付方式:</h5>
    <nav class="tab-nav margin-top-no">
        <ul class="nav nav-list">
            {if $enabled['wepay']}
                <li class="active">
                    <a class="waves-attach waves-effect type" data-toggle="tab" href="#" data-pay="wepay">微信支付</a>
                </li>
            {/if}
            {if $enabled['alipay']}
                <li>
                    <a class="waves-attach waves-effect type" data-toggle="tab" href="#" data-pay="alipay">支付宝</a>
                </li>
            {/if}
            {if $enabled['qqpay']}
                <li>
                    <a class="waves-attach waves-effect type" data-toggle="tab" href="#" data-pay="qqpay">QQ支付</a>
                </li>
            {/if}
        </ul>
        <div class="tab-nav-indicator"></div>
    </nav>
    <div class="form-group form-group-label">
        <label class="floating-label" for="amount">金额</label>
        <input class="form-control" id="amount" type="text">
    </div>
</div>
<div class="card-action">
    <div class="card-action-btn pull-left">
        <button class="btn btn-flat waves-attach" id="submit"><span class="icon">check</span>&nbsp;充值</button>
    </div>
</div>
<script>
    var type = "wepay";
    var pid = 0;
    window.onload = function () {
        var qrcode = new QRCode(document.getElementById("dmy"));
        $(".type").click(function () {
            type = $(this).data("pay");
        });
        type = 'alipay';
        $("#submit").click(function () {
            var price = parseFloat($$getValue('amount'));
            //console.log("将要使用" + type + "方法充值" + price + "元");
            if (isNaN(price)) {
                $("#result").modal();
                $$.getElementById('msg').innerHTML = '非法的金额！';
            }
            $.ajax({
                url: "/user/payment/purchase",
                data: {
                    price,
                    type,
                },
                dataType: 'json',
                type: "POST",
                success: (data) => {
                    //console.log(data);
                    if (data.errcode == -1) {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.errmsg;
                    }
                    if (data.errcode == 0) {
                        pid = data.pid;
                        if (type == "wepay") {
                            $("#result").modal();
                            $$.getElementById('msg').innerHTML = '<div class="text-center">使用微信扫描二维码支付.<div id="dmy" style="padding-top:  10px;"></div></div>';
                            qrcode.clear();
                            qrcode.makeCode(data.code);
                            setTimeout(f, 2000);
                        } else if (type == "alipay") {
                            $("#result").modal();
                            $.getElementById('msg').innerHTML = `正在跳转到支付宝... ${data.code}`;
                        } else if (type == "qqpay") {
                            $("#result").modal();
                            $$.getElementById('msg').innerHTML = '<div class="text-center">使用QQ扫描二维码支付.<div id="dmy"></div></div>';
                            qrcode.clear();
                            qrcode.makeCode(data.code);
                            setTimeout(f, 2000);
                        }
                    }
                }
            });
        });
    }
</script>
