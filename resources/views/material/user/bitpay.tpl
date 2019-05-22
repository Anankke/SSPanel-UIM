<div class="row">

    <div class="col-lg-6 col-md-6">
        <p class="card-heading">BitPay 充值</p>
        <div class="form-group form-group-label">
            <label class="floating-label" for="bitpayamount">金额</label>
            <input class="form-control" id="bitpayamount" type="text" >
        </div>
    </div>
    <div class="col-lg-6 col-md-6">
        <p class="h5 margin-top-sm text-black-hint" id="qrarea"></p>
    </div>
</div>

<div class="card-action">
    <div class="card-action-btn pull-left">
        <br>
        <!-- <button class="btn btn-flat waves-attach" id="btnSubmit" name="type" onclick="pay('Alipay')"><img src="data:image/svg+xml;base64,CjxzdmcgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDEwMDAgMTAwMCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMTAwMCAxMDAwIiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPG1ldGFkYXRhPiDnn6Lph4/lm77moIfkuIvovb0gOiBodHRwOi8vd3d3LnNmb250LmNuLyA8L21ldGFkYXRhPjxnPjxwYXRoIGQ9Ik05OTAsNjgwLjlWMTk4LjVDOTkwLDk0LjQsOTA1LjcsMTAsODAxLjUsMTBIMTk4LjVDOTQuNCwxMCwxMCw5NC4zLDEwLDE5OC41djYwMy4xQzEwLDkwNS42LDk0LjMsOTkwLDE5OC41LDk5MGg2MDMuMWM5Mi44LDAsMTY5LjktNjcsMTg1LjUtMTU1LjNjLTUwLTIxLjUtMjY2LjctMTE1LjEtMzc5LjQtMTY5Yy04NS44LDEwNC0xNzUuOCwxNjYuNS0zMTEuMywxNjYuNXMtMjI2LTgzLjMtMjE1LjEtMTg1LjZjNy4xLTY3LjIsNTMuMi0xNzYuNiwyNTMtMTU3LjhjMTA1LjMsMTAsMTUzLjUsMjkuNSwyMzkuNCw1Ny45YzIyLjEtNDAuNyw0MC42LTg1LjUsNTQuNi0xMzMuMkgyNDcuNXYtMzcuN2gxODguM3YtNjcuOEgyMDZ2LTQxLjVoMjI5Ljh2LTk3LjhjMCwwLDIuMi0xNS4zLDE5LTE1LjNoOTQuM3YxMTMuMWgyNDV2NDEuNWgtMjQ1djY3LjhoMTk5LjdjLTE4LjMsNzQuOC00Ni4yLDE0My41LTgxLDIwMy41QzcyNS45LDYwMC4yLDk5MCw2ODAuOSw5OTAsNjgwLjlMOTkwLDY4MC45TDk5MCw2ODAuOXogTTI4MS40LDc2Ny42Yy0xNDMuMywwLTE2NS44LTkwLjUtMTU4LjMtMTI4LjJzNDktODYuNywxMjguNi04Ni43YzkxLjUsMCwxNzMuNSwyMy40LDI3MS44LDcxLjNDNDU0LjUsNzE0LDM2OS41LDc2Ny42LDI4MS40LDc2Ny42TDI4MS40LDc2Ny42eiIgc3R5bGU9ImZpbGw6IzU2YWJlNCI+PC9wYXRoPjwvZz48L3N2Zz4gIA==" width="64" height="64"></button> -->
        <button class="btn btn-flat waves-attach" id="btnSubmit" name="type" onclick="pay('Crypto')"><img src="https://bitpay.dev/img/mpay-zh.png" height="50px" /></button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.3.1/dist/jquery.min.js"></script>
<script src="/assets/js/qrcode.min.js"></script>
<script>
    var pid = 0;

    function pay(type){
        var price = parseFloat($("#bitpayamount").val());

        console.log("将要使用 "+ type + " 充值" + price + "元");
        if (isNaN(price)) {
            $("#readytopay").modal('hide');
            $("#result").modal();
            $("#msg").html("请输入正确的金额!");
            return;
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
                    console.log(data);

                    if (data.errcode == 0) {
                        $("#readytopay").modal('hide');
                        $("#msg").html("正在跳转到数字货币支付...");
                        window.location.href = data.url;
                    } else {
                        $("#result").modal();
                        $("#msg").html(data.errmsg);
                    }
                }
            });
        });
    }

    function f(){
        $.ajax({
            type: "POST",
            url: "/payment/status",
            dataType: "json",
            data: {
                pid:pid
            },
            success: function (data) {
                console.log(data);
                if (data.status == 200) {
                    $("#result").modal();
                    $("#msg").html("充值成功！");
                    window.setTimeout("location.href=window.location.href", {$config['jump_delay']});
                }
            },
            error: function (jqXHR) {
                console.log(jqXHR);
            }
        });
        tid = setTimeout(f, 1000); //循环调用触发setTimeout
    }

</script>
