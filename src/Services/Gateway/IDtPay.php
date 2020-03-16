<?php

namespace App\Services\Gateway;

use App\Services\Auth;
use App\Models\Paylist;
use App\Services\View;

require_once("IDt/epay_submit.class.php");
require_once("IDt/epay_notify.class.php");

class IDtPay extends AbstractPayment
{
    public function purchase($request, $response, $args)
    {
        $type = $request->getParsedBodyParam('type');
        $price = $request->getParsedBodyParam('price');
        if ($price <= 0) {
            return $response->write(json_encode(['errcode' => -1, 'errmsg' => "非法的金额."]));
        }
        $user = Auth::getUser();
        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->total = $price;
        $pl->tradeno = self::generateGuid();
        $pl->save();

        $settings = $_ENV['idtpay'];
        $alipay_config = array(
            'partner' => $settings['partner'],
            'key' => $settings['key'],
            'sign_type' => $settings['sign_type'],
            'input_charset' => $settings['input_charset'],
            'transport' => $settings['transport'],
            'apiurl' => $settings['apiurl']
        );


        /**************************请求参数**************************/
        $notify_url = $_ENV['baseUrl'] . "/payment/notify";
        //需http://格式的完整路径，不能加?id=123这类自定义参数

        //页面跳转同步通知页面路径
        $return_url = $_ENV['baseUrl'] . "/user/payment/return";
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

        //商户订单号
        $out_trade_no = $pl->tradeno;
        //商户网站订单系统中唯一订单号，必填


        //商品名称
        $name = $settings["subjects"][array_rand($settings["subjects"])];
        //付款金额
        $money = $price;
        //站点名称
        $sitename = $settings['appname'];
        //必填

        //订单描述

        /************************************************************/
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "pid" => trim($alipay_config['partner']),
            "type" => $type,
            "notify_url"    => $notify_url,
            "return_url"    => $return_url,
            "out_trade_no"    => $out_trade_no,
            "name"    => $name,
            "money"    => $money,
            "sitename"    => $sitename
        );

        //建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter);
        return $response->write(json_encode(['code' => $html_text, 'errcode' => 0, 'pid' => $pl->id]));
    }

    public function notify($request, $response, $args)
    {
        $pid = $_GET['out_trade_no'];
        $p = Paylist::where('tradeno', '=', $pid)->first();
        if ($p->status == 1) {
            $success = 1;
        } else {
            $settings = $_ENV['idtpay'];
            $alipay_config = array(
                'partner' => $settings['partner'],
                'key' => $settings['key'],
                'sign_type' => $settings['sign_type'],
                'input_charset' => $settings['input_charset'],
                'transport' => $settings['transport'],
                'apiurl' => $settings['apiurl']
            );

            //计算得出通知验证结果
            $alipayNotify = new AlipayNotify($alipay_config);
            $verify_result = $alipayNotify->verifyNotify();

            if ($verify_result) {

                if ($_GET['trade_status'] == 'TRADE_SUCCESS') {
                    $this->postPayment($_GET['out_trade_no'], "IDtPay");
                    $success = 1;
                } else {
                    $success = 0;
                }
            } else {
                $success = 0;
            }
        }
        if ($success == 1) {
            echo "success";
        } else {
            echo "fail";
        }
    }
    public function getReturnHTML($request, $response, $args)
    {

        $pid = $_GET['out_trade_no'];
        $p = Paylist::where('tradeno', '=', $pid)->first();
        $money = $p->total;
        if ($p->status == 1) {
            $success = 1;
        } else {
            $settings = $_ENV['idtpay'];
            $alipay_config = array(
                'partner' => $settings['partner'],
                'key' => $settings['key'],
                'sign_type' => $settings['sign_type'],
                'input_charset' => $settings['input_charset'],
                'transport' => $settings['transport'],
                'apiurl' => $settings['apiurl']
            );

            //计算得出通知验证结果
            $alipayNotify = new AlipayNotify($alipay_config);
            $verify_result = $alipayNotify->verifyNotify();

            if ($verify_result) {

                if ($_GET['trade_status'] == 'TRADE_SUCCESS') {
                    $this->postPayment($_GET['out_trade_no'], "IDtPay");
                    $success = 1;
                } else {
                    $success = 0;
                }
            } else {
                $success = 0;
            }
        }
        return View::getSmarty()->assign('money', $money)->assign('success', $success)->fetch('user/pay_success.tpl');
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
                                            <button class="btn btn-flat waves-attach" id="idt_pay" ><span class="icon">check</span>&nbsp;充值</NOtton>
                                        </div>
                                    </div>
                        <script>
        var type = "wxpay";
            var type = "alipay";
    var pid = 0;
    $(".type").click(function(){
        type = $(this).data("pay");
    });
    $("#idt_pay").click(function(){
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
    });
</script>
';
    }
    public function getStatus($request, $response, $args)
    {
        $return = [];
        $p = Paylist::where('tradeno', $_POST['pid'])->first();
        $return['ret'] = 1;
        $return['result'] = $p->status;
        // file_put_contents(BASE_PATH.'/bitpay_status_success.log', json_encode($return)."\r\n", FILE_APPEND);
        return json_encode($return);
    }
}
