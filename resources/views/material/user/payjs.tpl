<!-- PAYJS -->
<p>扫码充值：</p>
<div class="input-group pay-input">
    <input class="form-control" id="amount" placeholder="请输入金额" type="number" />
    <span class="input-group-btn pay-button">
        <button class="layui-btn" id="btnSubmit" onclick="pay('wechat')">微信</button>
    </span>
</div>

<div class="modal fade" id="wepay" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
	    <div class="modal-content">
		    <div class="modal-header">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
				    &times;
			    </button>
			    <h4 class="modal-title">
				    请使用微信扫码充值：
			    </h4>
		    </div>
		    <div class="modal-body">
			    <div class="text-center">
                    <p id="qrarea"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="layui-btn" id="wepay_cancel" data-dismiss="modal">取消</button>
            </div>
	    </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>

<script type="text/javascript" src="/theme/assets/user/js/script.min.js"></script>
<script src="/theme/assets/user/js/qrcodefix.min.js"></script>
<script>
    var pid = 0;
    function pay(type){
        var price = parseFloat($("#amount").val());
        if (isNaN(price)) {
            layer.open({ content: "非法的金额!" });
            return;
        }
        var index = layer.load(2);
            $.ajax({
                'url': "/user/payment/purchase",
                'data': {
                    'price': price,
                    'type': 'wechat',
                    'way': 'payjs'
                },
                'dataType': 'json',
                'type': "POST",
                success: function (data) {
                    if(data.code === 0){
                        layer.closeAll('loading');
                        pid = data.pid;
                        $("#qrarea").html('<div class="text-center"><p>请使用<b>微信</b>扫描二维码支付</p><div align="center" id="qrcode" style="padding-top:10px;"></div><p>付款完毕后会请等待三秒，系统会自动跳转，感谢使用</p></div>');
                            var qrcode = new QRCode("qrcode", {
                                render: "canvas",
                                width: 200,
                                height: 200,
                                text: data.url
                            });
                            $("#wepay").modal();
                            tid = setTimeout(f, 1000); //循环调用触发setTimeout
                    } else {
                        layer.closeAll('loading');
                        layer.open({ content: data.msg });
                    }
                }
            });
    function f(){
        $.ajax({
            type: "POST",
            url: "/payment/status",
            dataType: "json",
            data: {
                pid: pid,
                way: 'payjs'
            },
            success: function (data) {
                if (data.result) {
                    console.log(data);
                    layer.open({ content: "充值成功！" });
                    window.setTimeout("location.href=window.location.href", {$config['jump_delay']});
                }
            },
            error: function (jqXHR) {
                console.log(jqXHR);
            }
        });
        tid = setTimeout(f, 1000); //循环调用触发setTimeout
    }
    }
</script>
<!-- PAYJS end -->