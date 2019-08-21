<div class="card-inner">
    <p class="card-heading">请输入充值金额</p>
    <div class="form-group form-group-label">
        <label class="floating-label" for="amount">金额</label>
        <input class="form-control" id="amount" type="text">
    </div>
    <a class="btn btn-brand yyhyopay" onclick="yyhyopay('wxpay')">微信支付</a>
    <a class="btn btn-brand yyhyopay" onclick="yyhyopay('alipay')">支付宝支付</a>
    <a class="btn btn-brand yyhyopay" onclick="yyhyopay('qqpay')">QQ支付</a>
</div>

<script>
    {literal}
    let pid = 0;

    let yyhyopay = (type) => {
        let price = parseFloat($$getValue('amount'));


        if (isNaN(price) || price === 0) {
            $("#readytopay").modal('hide');
            $("#result").modal();
            $$.getElementById('msg').innerHTML = '非法的金额！';
            return
        }

        $('#readytopay').modal();
        $("#readytopay").on('shown.bs.modal', function () {
            $.ajax({
                url: "/user/payment/purchase",
                data: {
                    price,
                    type,
                },
                dataType: 'json',
                type: "POST",
                success: (data) => {
                    if (data.code == 0) {
                        pid = data.pid;
                        $("#readytopay").modal('hide');
                        window.open(data.url, "_blank", "height=600,width=800,scrollbars=no,location=no");
                        tid = setTimeout(f, 1000) //循环调用触发setTimeout
                    } else {
                        $("#readytopay").modal('hide');
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.errmsg
                        //console.log(data)
                    }
                }
            })
        });

        let f = () => {
            $.ajax({
                type: "POST",
                url: "/payment/status",
                dataType: "json",
                data: {pid},
                success: (data) => {
                    if (data.result) {
                        //console.log(data)
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = '充值成功！';
                        window.setTimeout("location.href=window.location.href", {/literal}
                                {$config['jump_delay']});
                        {literal}
                    }
                    tid = setTimeout(f, 1000);
                },
                error: (jqXHR) => {
                    //console.log(jqXHR)
                }
            });
        }
    };
    {/literal}
</script>