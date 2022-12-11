<div class="card-inner">
    <div class="row">
        <div class="col-lg-6 col-md-6">
            <p class="card-heading">支付宝在线充值</p>
            <div class="form-group form-group-label">
                <label class="floating-label" for="amount-f2fpay">金额</label>
                <input class="form-control" id="amount-f2fpay" type="text">
            </div>
        </div>
        <div class="col-lg-6 col-md-6">
            <div class="h5 margin-top-sm text-black-hint" id="qrarea"></div>
        </div>
    </div>
</div>
<a class="btn btn-flat waves-attach" id="f2fpay" onclick="f2fpay();"><span class="mdi mdi-check"></span>&nbsp;充值</a>
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
                amount: $$getValue('amount-f2fpay')
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