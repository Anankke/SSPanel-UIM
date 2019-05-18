<?php
/**
 * Created by PhpStorm.
 * User: spay
 * Date: 2019/4/3
 * Time: 22:20 PM
 */

namespace App\Services\Gateway;

use App\Services\View;
use App\Services\Auth;
use App\Services\Config;

use App\Models\Paylist;

class SPay extends AbstractPayment
{
    public function purchase($request, $response, $args)
    {
        /**************************请求参数**************************/
        $amount = $request->getParam("price");
        //var_dump($request->getParam("price"));die();
        $user = Auth::getUser();
        $pl = new Paylist();
        $pl->userid = $user->id;
        //if ($amount <= '10') {
        //$amount='10';
        //}
        $pl->total = $amount;
        $pl->tradeno = self::generateGuid();
        $pl->save();
        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = $pl->tradeno;
        //订单名称，必填
        $subject = $pl->id . "UID:" . $user->id . " 充值" . $amount . "元";
        //付款金额，必填
        $total_fee = (float)$amount;
        //商品描述，可空
        $body = $user->id;
        /************************************************************/


        $data['total_fee'] = $total_fee;    //支付金额
        $data['partner'] = Config::get('alipay_id');    //spay合作者id
        $data['notify_url'] = Config::get('baseUrl') . "/spay_back";    //不能有get参数 也就是?xxx=xxx&    xxx=xxx
        $data['return_url'] = Config::get('baseUrl') . "/spay_back";    //不能有get参数 也就是?xxx=xxx&    xxx=xxx
        $data['out_trade_no'] = $out_trade_no;    //商户唯一订单号
        $data['service'] = 'create_direct_pay_by_user';
        $i = 0;
        $jk = '';
        ksort($data);
        reset($data);
        $ii = count($data);
        foreach ($data as $as1 => $as2) {
            $i++;
            $jk .= $as1 . "=" . $as2;
            if ($ii != $i) $jk .= "&";
        }
        $data['sign'] = md5($jk . Config::get('alipay_key'));
        //header("Location: http://www.dayyun.com/pay/pay/alipay.php?".http_build_query($data));    


        return json_encode(['code' => 0, 'url' => "http://www.dayyun.com/pay/pay/alipay.php?" . http_build_query($data)]);
    }

    public function notify($request, $response, $args)
    {
        $data = $_GET;
        $ispost = 0;
        if (empty($data['out_trade_no'])) {
            $data = $_POST;
            $ispost = 1;
        } //判断是同步通知还是异步通知 并赋值校验签名
        if (empty($data['out_trade_no'])) die(time());

        $i = 0;
        $jk = '';
        ksort($data);
        reset($data);
        $sign = $data['sign'];
        unset($data['sign']);
        unset($data['sign_type']);
        $ii = count($data);
        foreach ($data as $as1 => $as2) {
            $i++;
            $jk .= $as1 . "=" . $as2;
            if ($ii != $i) $jk .= "&";
        }
        $newsign = md5($jk . Config::get('alipay_key'));
        if ($newsign != $sign) die('false');
        $this->postPayment($data['out_trade_no'], "SPay 支付");
        echo 'success';
        if ($ispost == 0) header("Location: /user/code");
    }

    public function getPurchaseHTML()
    {
        return '
                    <div class="card-inner">
						<div class="form-group pull-left">
                        <p class="modal-title" >本站支持支付宝在线充值</p>
                        <p>输入充值金额：</p>
                        <p>低于10元自动转为10元  (亲!手续费很贵滴)</p>
                        <div class="form-group form-group-label">
                        <label class="floating-label" for="price">充值金额</label>
                        <input id="type" class="form-control maxwidth-edit" name="amount" />
                        </div>
                        <a class="btn btn-flat waves-attach" id="submitSpay" ><span class="icon">check</span>&nbsp;充值</a>
                        </div>
                    </div>
                        <script>
                        window.onload = function(){
        $("#submitSpay").click(function() {
            var price = parseFloat($("#type").val());
            console.log("将要使用 SPay 方法充值" + price + "元");
            if (isNaN(price)) {
                $("#result").modal();
                $("#msg").html("非法的金额!");
            }
            $(\'#readytopay\').modal();
            $("#readytopay").on(\'shown.bs.modal\', function () {
                $.ajax({
                    \'url\': "/user/payment/purchase",
                    \'data\': {
                        \'price\': price,
                    },
                    \'dataType\': \'json\',
                    \'type\': "POST",
                    success: function (data) {
                        if (data.code == 0) {
                            $("#result").modal();
                            $("#msg").html("正在跳转到支付宝...");
                            console.log(data);
                            window.location.href = data.url;
                        } else {
                            $("#result").modal();
                            $("#msg").html(data.msg);
                            console.log(data);
                        }
                    }
                });
            });
        });
    };</script>                        
';
    }

    public function getReturnHTML($request, $response, $args)
    {
        // TODO: Implement getReturnHTML() method.
    }

    public function getStatus($request, $response, $args)
    {
        // TODO: Implement getStatus() method.
    }
}
