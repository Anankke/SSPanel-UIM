<div class="card-inner">
    <p class="card-heading">TrimePay 支付宝充值</p>
    <div class="form-group form-group-label">
        <label class="floating-label" for="amount">金额</label>
        <input class="form-control" id="amount" type="text">
    </div>
</div>
<div class="card-action">
    <div class="card-action-btn pull-left">
        <button class="btn btn-flat waves-attach" id="submit" ><span class="icon">check</span>&nbsp;充值</button>
    </div>
</div>
<script>
    var pid = 0;
    var isWap = 0;
    var type = 'alipay';

    if(/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
        isWap = 1;
    } else {
        isWap = 0;
    }

    if (type=='alipay' && isWap == 1){
        type = 'ALIPAY_WAP';
    } else {
        type = 'ALIPAY_WEB';
    }

    window.onload = function(){
        $("#submit").click(function() {
            var price = parseFloat($("#amount").val());
            console.log("将要使用 TrimePay 方法充值" + price + "元");
            if (isNaN(price)) {
                $("#result").modal();
                $("#msg").html("非法的金额!");
            }
            $('#readytopay').modal();
            $("#readytopay").on('shown.bs.modal', function () {
                $.ajax({
                    'url': "/user/payment/purchase",
                    'data': {
                        'price': price,
                        'type': type,
                    },
                    'dataType': 'json',
                    'type': "POST",
                    success: function (data) {
                        if (data.code == 0) {
                            $("#result").modal();
                            $("#msg").html("正在跳转到支付宝...");
                            console.log(data);
                            window.location.href = data.data;
                        } else {
                            $("#result").modal();
                            $("#msg").html(data.msg);
                            console.log(data);
                        }
                    }
                });
            });
        });
    };
</script>