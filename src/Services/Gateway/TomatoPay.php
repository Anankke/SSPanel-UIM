<?php

namespace App\Services\Gateway;

use App\Services\Auth;
use App\Models\Paylist;

class TomatoPay extends AbstractPayment
{
    public function purchase($request, $response, $args)
    {
        $type = $request->getParsedBodyParam('type');
        $price = $request->getParsedBodyParam('price');
        if ($price <= 0) {
            return $response->write(json_encode(['errcode' => -1, 'errmsg' => "非法的金额."]));
        }
        $user = Auth::getUser();
        $settings = $_ENV['tomatopay'][$type];
        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->total = $price;
        $pl->tradeno = self::generateGuid();
        $pl->save();
        $fqaccount = $settings['account'];
        $fqkey = $settings['token'];
        $fqmchid = $settings['mchid'];
        $fqtrade = $pl->tradeno;
        $fqcny = $price;

        $signs = md5(
            'mchid=' . $fqmchid . '&account=' . $fqaccount . '&cny=' . $fqcny . '&type=1&trade=' . $fqtrade . $fqkey
        );
        $url = 'https://b.fanqieui.com/gateways/' . $type . '.php?account='
            . $fqaccount . '&mchid=' . $fqmchid . '&type=1'
            . '&trade=' . $fqtrade . '&cny=' . $fqcny . '&signs=' . $signs;
        $result = "<script language='javascript' type='text/javascript'>window.location.href='" . $url . "';</script>";
        return $response->write(json_encode(['code' => $result, 'errcode' => 0, 'pid' => $pl->id]));
    }

    public function notify($request, $response, $args)
    {
        $type = $args['type'];
        $settings = $_ENV['tomatopay'][$type];
        $order_data = $request->getParsedBody();
        $signs = $order_data['sign'];
        unset($order_data['sign']);
        $security = $order_data;
        $o = '';
        foreach ($security as $k => $v) {
            $o .= "$k=" . urlencode($v) . "&";
        }
        $sign = md5(substr($o, 0, -1) . $settings['token']);
        if ($sign == $signs) {
            $this->postPayment($order_data['out_trade_no'], "在线支付");
            return $response->write('success');
        } else {
            return $response->write('failed');
        }
    }
    public function getPurchaseHTML()
    {
        return '
                    <div class="card-inner">
                                        <p class="card-heading">充值</p>
                                        <h5>支付方式:</h5>
                                        <h5 style="color:red">推荐使用支付宝支付，更快到账！</h5>
                                        <br/>
                                        <nav class="tab-nav margin-top-no">
                                            <ul class="nav nav-list">
                                            
                                
                                                    <li>
                                                        <a class="waves-attach waves-effect type active" data-toggle="tab" data-pay="alipay"><img src="/images/alipay.jpg" height="50px"></img></a>
                                                    </li>
                                            
                                                    <li>
                                                        <a class="waves-attach waves-effect type" data-toggle="tab" data-pay="wxpay"><img src="/images/weixin.jpg" height="50px"></img></a>
                                                    </li>
                                            
                
                                            </ul>
                                            <div class="tab-nav-indicator"></div>
                                        </nav>
                                        <div class="form-group form-group-label">
                                            <label class="floating-label" for="amount">金额</label>
                                            <input class="form-control" id="amount" type="text">
                                        </div>
                                    </div>
                                    <div class="card-action">
                                        <div class="card-action-btn pull-left">
                                            <button class="btn btn-flat waves-attach" id="tomato_pay" ><span class="icon">check</span>&nbsp;充值</NOtton>
                                        </div>
                                    </div>
                        <script>
        var type = "wxpay";
            var type = "alipay";
    var pid = 0;
    $(".type").click(function(){
        type = $(this).data("pay");
    });
    $("#tomato_pay").click(function(){
        var price = parseFloat($("#amount").val());
        console.log("将要使用"+type+"方法充值"+price+"元")
        if(isNaN(price)){
            $("#result").modal();
            $("#msg").html("非法的金额!");
        }
        $.ajax({
            \'url\':"/user/payment/purchase",
            \'data\':{
                \'price\':price,
                \'type\':type,
            },
            \'dataType\':\'json\',
            \'type\':"POST",
            success:function(data){
                console.log(data);
                if(data.errcode==-1){
                    $("#result").modal();
                    $("#msg").html(data.errmsg);
                }
                if(data.errcode==0){
                    pid = data.pid;
                    if(type=="wxpay"){
                        $("#result").modal();
                        $("#msg").html("正在跳转到微信..."+data.code);
                    }else if(type=="alipay"){
                        $("#result").modal();
                        $("#msg").html("正在跳转到支付宝..."+data.code);
                    }
                }
            }
        });
        setTimeout(f, 1000);
    });
</script>
';
    }
}
