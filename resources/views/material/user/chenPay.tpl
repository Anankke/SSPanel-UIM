<style>
    .btn-price {
        margin: 5px;
        background: #fff;
        padding: 8px 15px;
        border: 1px solid #000;
        transition: .5s;
    }

    .btn-price.active {
        background: #1972f4;
        color: #fff;
        /*border: 1px solid #fff;*/
        padding: 8px 20px;
    }

    #qrcode img {
        display: initial !important;
    }
</style>
<div class="card-inner">
    <div class="form-group pull-left">
        <p class="modal-title">本站支持支付宝/微信在线充值</p>
        {if preg_match('/\|/', $config['Pay_Price'])}
        {$data = explode('|', $config['Pay_Price'])}
        <p>选择充值金额：</p>
        <div class="form-group form-group-label btnBox">
            {foreach $data as $key => $item}
                <a class="btn btn-price {if $key == 0}active{/if}" price="{$item}" type="{$key}">{$item}元</a>
            {/foreach}
            <input type="hidden" id="AliPayType" class="form-control" name="amount"/>
            {else}

            <p>输入充值金额：</p>
            <div class="form-group form-group-label btnBox"><label class="floating-label" for="price">充值金额</label>
                <input type="number" id="AliPayType" class="form-control" name="amount"/>
            </div>
            {/if}
        </div>

        {if $config['AliPay_Status']==1}
            <a class="btn btn-flat waves-attach" id="urlChangeAliPay" type="1"><img src="/images/alipay.jpg"
                                                                                    width="45"></a>
        {/if}
        {if $config['WxPay_Status']==1}
            <a class="btn btn-flat waves-attach" id="urlChangeAliPay2" type="2"><img src="/images/weixin.jpg"
                                                                                     width="45"></a>
        {/if}
    </div>
    <div class="form-group pull-right">
        <img src="/images/qianbai-2.png" height="205"/>
    </div>
</div>
<div aria-hidden="true" class="modal modal-va-middle fade" id="AliPayReadyToPay" role="dialog"
     tabindex="-1">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-heading">
                <a class="modal-close" id="AliPayReadyToPayClose" data-dismiss="modal">×</a>
                <h2 class="modal-title">扫码充值<span style="color: red;margin-left: 10px;"
                                                  id="countTime"></span>
                </h2>
            </div>
            <div class="modal-inner" style="text-align: center">

                <div class="text-center">
                    <p id="title" class="textShow"></p>
                    <p id="qrcode">
                        <a class="pay"
                           href="">
                            {*<img src="/images/loading.gif"*}
                            {*width="300px"/>*}
                            <div id="qrcode" style="text-align: center"></div>
                        </a>
                    </p>
                    <p id="title">支付成功后大约一分钟内提示</p>
                    <p id="info"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function chenPayLoad() {
        var $alipay = 'alipays://platformapi/startapp?saId=10000007&clientVersion=3.7.0.0718&qrcode=',
            $wxpayApp = 'weixin://',
            $pay_type = 0,
            $order_id = 0,
            qrcode = new QRCode(document.getElementById("qrcode"));
        if ('{$QRcodeUrl}'.indexOf('|') > 0) {
            var $alipayUrl = '{$QRcodeUrl}'.split('|'),
                $wxpayUrl = '{$WxQRcodeUrl}'.split('|');
        } else {
            var $alipayUrl = '{$QRcodeUrl}',
                $wxpayUrl = '{$WxQRcodeUrl}';
        }
        $("#AliPayType").val($('.btn-price:first-child').attr('price'));
        $(".btn-price").click(function () {
            $pay_type = $(this).attr('type');
            $('.btn-price').removeClass('active');
            $(this).addClass('active');
            $("#AliPayType").val($(this).attr('price'));
        });
        $("#urlChangeAliPay,#urlChangeAliPay2").unbind('click').click(function () {
            var $type = $(this).attr('type');
            if ($type == 2) {
                $('.textShow').html('手机端长按二维码保存到手机<br>点击二维码进入扫一扫选择图片支付');
                if ('{$QRcodeUrl}'.indexOf('|') > 0) var pay_url = $wxpayUrl[$pay_type];
                else var pay_url = $wxpayUrl;
            } else {
                $('.textShow').html('手机端点击二维码即可转跳支付宝支付');
                if ('{$QRcodeUrl}'.indexOf('|') > 0) var pay_url = $alipayUrl[$pay_type];
                else var pay_url = $alipayUrl;
            }
            $.ajax({
                type: "GET",
                url: "/user/chenPay",
                dataType: "json",
                data: {
                    fee: $("#AliPayType").val(),
                    type: $type,
                    url: pay_url
                },
                success: function (data) {
                    if (data.ret) {
                        $order_id = data.id;
                        $("#AliPayReadyToPay").modal();
                        getCountdown();
                        $id = setInterval(function () {
                            getCountdown()
                        }, 1000);
                        setTimeout(function () {
                            checkPayTime(data.id)
                        }, 1000);
                        if (data.url) {
                            if ($type == 2)
                                $('.pay').attr('href', $wxpayApp);
                            else $('.pay').attr('href', $alipay + data.url);
                            qrcode.clear();
                            qrcode.makeCode(data.url);
                        }
                    } else {
                        $("#result").modal();
                        $("#msg").html(data.msg);
                    }
                }
            });

            function checkPayTime(id) {
                $.ajax({
                    type: "GET",
                    url: "/chenPay/status?" + Math.random(),
                    dataType: "json",
                    data: {
                        id: id
                    },
                    success: function (data) {
                        if (data.ret) {
                            // if (data.url) {
                            //     if ($type == 2)
                            //         $('.pay').attr('href', $wxpayApp).children('img').attr('src', $zxing + data.url);
                            //     else $('.pay').attr('href', $alipay + data.url).children('img').attr('src', $zxing + data.url);
                            // }
                            if (data.status == 1) {
                                close('充值成功！');
                                setTimeout(function () {
                                    location.reload()
                                }, 3000);
                            }
                        }
                    }
                });
                CheckPayTimeId = setTimeout(function () {
                    checkPayTime(id)
                }, 3000); //循环调用触发setTimeout
            }

            function AliPayDelete(id) {
                $.ajax({
                    type: "GET",
                    url: "/user/orderDelete",
                    dataType: "json",
                    data: {
                        id: id
                    },
                    success: function (data) {
                    }
                });
            }

            $('#AliPayReadyToPay').unbind('hide.bs.modal').on('hide.bs.modal', function () {
                if (CheckPayTimeId) clearTimeout(CheckPayTimeId);
                if ($id) clearInterval($id);
                AliPayDelete($order_id);
                $('.pay').attr('href', '').children('img').attr('src', '/images/loading.gif');
            });

            function close($msg) {
                if (CheckPayTimeId) clearTimeout(CheckPayTimeId);
                if ($id) clearInterval($id)
                $('.pay').attr('href', '').children('img').attr('src', '/images/loading.gif');
                $("#AliPayReadyToPay").modal('hide');
                $("#result").modal();
                $("#msg").html($msg);
            }

            var m = 2, s = 59, countdown = document.getElementById("countTime");

            function getCountdown() {
                countdown.innerHTML = "<span>" + (m >= 10 ? m : '0' + m) + "</span>:<span>" + (s >= 10 ? s : '0' + s) + "</span>";
                if (m == 0 && s == 0) {
                    close('倒计时结束了');
                } else if (m >= 0) {
                    if (s > 0) {
                        s--;
                    } else if (s == 0) {
                        m--, s = 59;
                    }
                }
            }
        });
    }

    $(function () {
        chenPayLoad();
    });
    // setTimeout("chenPayLoad()", 2000);
</script>
