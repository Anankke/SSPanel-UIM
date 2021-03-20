<div class="card-inner">
    <div class="row">
        <div class="col-lg-6 col-md-6">
            <p class="card-heading">输入充值金额后，点击下方的图标进行充值</p>
            常用充值金额：
            <button onclick="bitpayDeposit(10)">10元</button>
            <button onclick="bitpayDeposit(20)">20元</button>
            <button onclick="bitpayDeposit(30)">30元</button>
            <div class="form-group form-group-label">
                <label class="floating-label" for="bitpayx-amount">金额</label>
                <input class="form-control" id="bitpayx-amount" type="number" value="10">
            </div>
            <div id="bitpayx-qrarea">
                <button class="btn btn-flat waves-attach" id="bitpayx-crypto-submit" name="type" onclick="selectPayment('CRYPTO')"><img
                            src="https://dcdn.mugglepay.com/pay/crypto.jpg"
                            height="64"></button>
            </div>
        </div>
        <div class="col-lg-6 col-md-6">
            <div id="bitpayx-qrcode" style="padding-left: 20px;"></div>
        </div>
    </div>
</div>

<script>
    var pid = 0;

    var isMobile = /Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent);
    if (isMobile) {
        $("#bitpayx-wechat-submit").hide();
    }

    function bitpayDeposit(amount) {
        $("#bitpayx-amount").val(amount);
    }

    function selectPayment(type) {
        var price = parseFloat($("#bitpayx-amount").val());

        console.log("将要使用 " + type + " 充值" + price + "元");
        if (isNaN(price) || price < 10 || price >= 500) {
            $("#readytopay").modal('hide');
            $("#result").modal();
            if (isNaN(price)) {
                $("#msg").html("请输入正确的金额!");
            }
            else if (price < 10) {
                $("#msg").html("请不要充值低于10元。");
            }
            else if (price > 500) {
                $("#msg").html("请不要充值超过500元。");
            }
            return;
        }

        var isMobile = /Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent);
        $('#readytopay').modal();
        // $("#readytopay").on('shown.bs.modal', function () {
            $.ajax({
                'url': "/user/payment/purchase",
                'data': {
                    'price': price,
                    'type': type,
                    'mobile': isMobile,
                },
                'dataType': 'json',
                'type': "POST",
                success: function (data) {
                    if (data.errcode == 0) {
                        $("#readytopay").modal('hide');
                        pid = data.pid;
                            $("#msg").html("正在跳转到支付页面...");
                            window.location.href = data.url;
                    } else {
                        $("#result").modal();
                        $("#msg").html(data.errmsg);
                    }
                }
            });
        // });
    }

    function bitpayStatus() {
        tid = setTimeout(bitpayStatus, 3000); //循环调用触发setTimeout
        if (pid === 0) {
            return;
        }
        $.ajax({
            type: "POST",
            url: "/payment/status",
            dataType: "json",
            data: {
                pid: pid
            },
            success: function (data) {
                if (data.result) {
                    console.log(data);
                    $("#result").modal();
                    $("#msg").html("充值成功！");
                    window.setTimeout("location.href=window.location.href", {$config['jump_delay']});
                }
            },
            error: function (jqXHR) {
                console.log(jqXHR);
            }
        });
    }
    bitpayStatus();
</script>