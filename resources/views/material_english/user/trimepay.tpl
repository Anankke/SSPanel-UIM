

    <div class="card-inner">
            <p class="card-heading">After inputing the recharge amount, click the icon below to recharge</p>
            <div class="form-group form-group-label">
                <label class="floating-label" for="amount">Amount</label>
                <input class="form-control" id="amount" type="text" >
            </div>
        </div>
        <div id="qrarea">
            <button class="btn btn-flat waves-attach" id="btnSubmit" name="type" onclick="pay('Alipay')"><img src="/images/alipay.jpg" width="50px" height="50px" /></button>
            <button class="btn btn-flat waves-attach" id="btnSubmit" name="type" onclick="pay('WEPAY_JSAPI')"><img src="/images/weixin.jpg" width="50px" height="50px" /></button>
        </div>
            
      
<script>
    var pid = 0;

    function pay(type){
        if (type==='Alipay'){
            if(/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
                type = 'ALIPAY_WAP';
            } else {
                type = 'ALIPAY_WEB';
            }
        }

        var price = parseFloat($("#amount").val());

        console.log("Will use "+ type + " to recharge " + price + " CNY");
        if (isNaN(price)) {
            $("#readytopay").modal('hide');
            $("#result").modal();
            $("#msg").html("Illegal amount!");
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
                    if (data.code == 0) {
                        console.log(data);
                        $("#readytopay").modal('hide');
                        if(type === 'ALIPAY_WAP' || type ==='ALIPAY_WEB'){
                            window.location.href = data.data;
                        } else {
                            pid = data.pid;
                            $("#qrarea").html('<div class="text-center"><p>Use WeChat to scan QR code to pay.</p><div align="center" id="qrcode" style="padding-top:10px;"></div><p>After the recharge is completed, it will automatically jump</p></div>');
                            var qrcode = new QRCode("qrcode", {
                                render: "canvas",
                                width: 200,
                                height: 200,
                                text: encodeURI(data.data)
                            });
                            tid = setTimeout(f, 1000); //循环调用触发setTimeout
                        }
                    } else {
                        $("#result").modal();
                        $("#msg").html(data.msg);
                        console.log(data);
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
                if (data.result) {
                    console.log(data);
                    $("#result").modal();
                    $("#msg").html("Success!");
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
