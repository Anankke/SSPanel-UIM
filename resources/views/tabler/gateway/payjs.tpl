<div class="card-inner">
    <p class="card-heading">输入充值金额后，点击下方的图标进行充值</p>
    <div class="form-group form-group-label">
        <label class="floating-label" for="amount-payjs">金额</label>
        <input class="form-control" id="amount-payjs" type="text">
    </div>
</div>
<div id="qrarea">
    <button class="btn btn-flat waves-attach" id="btnSubmit" name="type" onclick="payjs('alipay')">
        <!-- iCon by SFont.Cn -->
        <img src="data:image/png;base64,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"
             width="64" height="64">
    </button>
</div>
<script>
    var pid = 0;
    var flag = false;
    function payjs(type) {
        var price = parseFloat($$getValue('amount-payjs'));
        //console.log("将要使用 " + type + " 充值" + price + "元");
        if (isNaN(price)) {
            $("#readytopay").modal('hide');
            $("#result").modal();
            $$.getElementById('msg').innerHTML = '非法的金额！'
            return;
        }
        $('#readytopay').modal();
        $.ajax({
            url: "/user/payment/purchase/payjs",
            data: {
                price,
                type,
            },
            dataType: 'json',
            type: "POST",
            success: (data) => {
                if (data.code == 0) {
                    //console.log(data);
                    $("#readytopay").modal('hide');
                    {
                        pid = data.pid;
                        $$.getElementById('qrarea').innerHTML = '<div class="text-center"><p>使用支付宝扫描二维码支付.</p><div align="center" id="qrcode" style="padding-top:10px;"></div><p>充值完毕后会自动跳转</p></div>';
                        var qrcode = new QRCode("qrcode", {
                            correctLevel: 3,  //解决超过200字符的二维码生成问题
                            render: "canvas",
                            width: 200,
                            height: 200,
                            text: data.url      //使用encodeURI()函数报会调低至错误
                        });
                        if(flag == false){
                            tid = setTimeout(fpayjs, 1000); //循环调用触发setTimeout
                            flag = true;
                        }else{
                            return 0;
                        }
                    }
                } else {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    //console.log(data);
                }
            }
        });
    }
    function fpayjs() {
        $.ajax({
            type: "POST",
            url: "/payment/status/payjs",
            dataType: "json",
            data: {
                pid
            },
            success: (data) => {
                if (data.result) {
                    //console.log(data);
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = '充值成功！';
                    window.setTimeout("location.href=window.location.href", {$config['jump_delay']});
                }
            },
            error: (jqXHR) => {
                //console.log(jqXHR);
            }
        });
        tid = setTimeout(fpayjs, 1000); //循环调用触发setTimeout
    }
</script>