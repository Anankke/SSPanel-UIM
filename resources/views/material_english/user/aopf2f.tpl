 <div class="card-inner">
 <div class="row">
        <div class="col-lg-6 col-md-6">
            <p class="card-heading">Alipay recharge</p>
            <div class="form-group form-group-label">
                <label class="floating-label" for="amount">Amount</label>
                <input class="form-control" id="amount" type="text" >
            </div>
        </div>
        <div class="col-lg-6 col-md-6">
            <div class="h5 margin-top-sm text-black-hint" id="qrarea"></div>
        </div>
    </div>
</div>
    <a class="btn btn-flat waves-attach" id="pay" onclick="pay();" ><span class="icon">check</span>&nbsp;Recharge</a>
<script>
    var pid = 0;
    function pay(){
        $("#readytopay").modal();
        $("#readytopay").on('shown.bs.modal', function () {
            $.ajax({
                type: "POST",
                url: "/user/payment/purchase",
                dataType: "json",
                data: {
                    amount: $("#amount").val()
                },
                success: function (data) {
                    if (data.ret) {
                        console.log(data);
                        pid = data.pid;
                        $("#qrarea").html('<div class="text-center"><p>Please use Alipay to scan QR code to pay</p><a id="qrcode" style="padding-top:10px;display:inline-block"></a><p>The mobile phone can click on the QR code to call Alipay to pay.</p></div>');
                        $("#readytopay").modal('hide');
                        new QRCode("qrcode", {
                            render: "canvas",
                            width: 200,
                            height: 200,
                            text: encodeURI(data.qrcode)
                        });
                        $('#qrcode').attr('href',data.qrcode);
                        setTimeout(f, 1000);
                    } else {
                        $("#result").modal();
                        $("#msg").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    console.log(jqXHR);
                    $("#readytopay").modal('hide');
                    $("#result").modal();
                    $("#msg").html(jqXHR+"  An error occurred");
                }
            })
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
                    $("#alipay").modal('hide');
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
