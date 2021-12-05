<div class="card-inner">
    <div class="row">
        <div class="col-lg-6 col-md-6">
            <p class="card-heading">使用 USDT TRC20 充值</p>
            <div class="form-group form-group-label">
                <label class="floating-label" for="amount-token188">金额</label>
                <input class="form-control" id="amount-token188" type="text">
            </div>
        </div>
        <div class="col-lg-6 col-md-6">
            <div class="h5 margin-top-sm text-black-hint" id="qrarea"></div>
        </div>
    </div>
</div>
<a class="btn btn-flat waves-attach" id="token188pay" onclick="token188pay();"><span class="icon">check</span>&nbsp;立即充值</a>
<script>
    var pid = 0;
    var flag = false;
    function token188pay() {
        $("#readytopay").modal('show');
        $.ajax({
            type: "POST",
            url: "/user/payment/purchase/token188",
            dataType: "json",
            data: {
                amount: $$getValue('amount-token188')
            },
            success: (data) => {
                if (data.ret) {
					//window.location.href=data.qrcode;
                    //console.log(data);
                    pid = data.pid;
                    $$.getElementById('qrarea').innerHTML = '<div class="text-center"><p>请点击<b>下方链接</b>前往支付</p><p><a id="qrcode" class="btn btn-block btn-brand waves-attach waves-light waves-effect" target="_blank">去支付</a></p></div>'
                    $("#readytopay").modal('hide');
                    /*new QRCode("qrcode", {
                        render: "canvas",
                        width: 200,
                        height: 200,
                        text: encodeURI(data.qrcode)
                    });*/
                    $$.getElementById('qrcode').setAttribute('href', data.qrcode);
                    if(flag == false){
                        setTimeout(ftoken188, 1000);
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
    function ftoken188() {
        $.ajax({
            type: "POST",
            url: "/payment/status/token188",
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