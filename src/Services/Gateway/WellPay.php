<?php

namespace App\Services\Gateway;

use App\Services\View;
use App\Services\Auth;
use App\Services\Config;
use App\Models\Paylist;
use App\Controllers\UserController;

class WellPay
{

    private $appid;
    private $appkey;
    private $gatewayUri;

    /**
     * 签名初始化
     * @param merKey    签名密钥
     */

    public static function _name()
    {
        return 'wellpay';
    }

    public static function _enable()
    {
        if (empty($_ENV['active_payments']['wellpay']) || $_ENV['active_payments']['wellpay']['enable'] == false) {
            return false;
        }

        return true;
    }
    public static function _readableName() {
        return "wellpay";
    }
    public function __construct()
    {
        $this->appid = $_ENV['active_payments']['wellpay']['wellpay_app_id'];
        $this->appSecret = $_ENV['active_payments']['wellpay']['wellpay_app_secret'];
        $this->serverID = $_ENV['active_payments']['wellpay']['server_id'];
        $this->gatewayUri = 'https://api.crossz.pro/v1/service/payment';
    }

    public function createOrder($amount, $order_no, $user_id)
    {
        $price = $amount;
        if ($price <1) {
            return json_encode(['code' => -1, 'msg' => '付款金额至少要1.00元.']);
        }
        $data = array(
            'appid'=>$this->appid,//商户编号
            'orderno' => $order_no,//商户订单号，需保证在商户平台唯一
            'totalfee'=> $price,//单位元
            'server_id'=> $this->serverID,//
            'panel'=> 'ssp-newfeat',//
            'ver'=> '101',//
            'request_time'=> time(),//
            'remark'=> '',//
            'notifyurl' =>'/payments/notify/wellpay',//	异步通知地址
            'returnurl' => '/user/order/' . $order_no
        );
        $data['sign'] = $this->sign($data);
        $resdata = $this->post($data);
        $result = json_decode($resdata,true);
        if (!isset($result['resultCode'])!=0) {
            return ['errcode' => -1, 'errmsg' => '处理支付网关失败。'];
        }
        return json_encode([
            'ret' => 1,
            'type' => 'link',
            'link' => $result['data']['payUrl'],
        ]);

    }
    public function notify($request, $response, $args)
    {
        $params = json_decode(@file_get_contents("php://input"),true);
        if (!$this->verify($params)) {
            abort(500,'签名错误');
        }
        UserController::execute($params['orderno']);
        die('success');
    }
    /**
     * @name    准备签名/验签字符串
     */
    public function prepareSign($data)
    {
        ksort($data);
        return htmlspecialchars(http_build_query($data));
    }

    public function verify(array $data)
    {
        $sign = $data['sign'] ?? null;
        return $sign === $this->sign($data);
    }

    private function sign(array $data)
    {
        $str = urldecode(http_build_query($this->argSorts($this->paraFilters($data))));
        return md5($str . "&app_secret=" . $this->appSecret);
    }
    private function paraFilters(array $para)
    {
        return array_filter($para, function ($item, $key) {
            if ($key != "sign" && !empty($item)) return true;
        }, ARRAY_FILTER_USE_BOTH);
    }
    private function argSorts(array $para)
    {
        ksort($para);
        reset($para);
        return $para;
    }

    public function post($data)
    {
        if (is_array($data))
        {
            $data = http_build_query($data);
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->gatewayUri);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER,  array(
            "Content-Type: application/x-www-form-urlencoded") );
        $data = curl_exec($curl);
        // $data = curl_getinfo($curl,CURLINFO_EFFECTIVE_URL);
        curl_close($curl);
        return $data;
    }
    public static function getPurchaseHTML()
    {
        return '<div class="card-inner">
						<div class="form-group pull-left">
                            <p class="modal-title">wellpay支持多种充值</p>
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="amount-coinpay">充值金额</label>
                                <input id="amount-coinpay" class="form-control maxwidth-edit" name="amount-coinpay" />
                            </div>
                            <div class="form-group form-group-label">
                               <label><input name="paytype" type="radio" value="alipay" checked="checked"/>支付宝 </label>
                                <label><input name="paytype" type="radio" value="wxpay" />微信 </label>
                            </div>
                             <a class="btn btn-flat waves-attach" id="submitCoinPay" style="padding: 8px 24px;color: #fff;background: #1890ff;"><span class="icon">check</span>&nbsp;充&nbsp;值&nbsp;</a>
                        </div>
                    </div>
                        <script>
                        window.onload = function(){
        $("#submitCoinPay").click(function() {
            var price = parseFloat($("#amount-coinpay").val());
            var paytype = $("input[name=\'paytype\']:checked").val();;
            if (isNaN(price)) {
                $("#result").modal();
                $("#msg").html("非法的金额!");
                return false;
            }
            $(\'#readytopay\').modal();
            $("#readytopay").on(\'shown.bs.modal\', function () {
                $.ajax({
                    \'url\': "/user/payment/purchase/wellpay",
                    \'data\': {
                        \'price\': price,
                        \'paytype\':paytype,
                    },
                    \'dataType\': \'json\',
                    \'type\': "POST",
                    success: (data) => {
                        if (data.code == 0) {
                            $("#result").modal();
                            $("#msg").html("正在跳转WellPay支付网关...");
                            window.location.href = data.url;
                        } else {
                            $("#result").modal();
                            $$.getElementById(\'msg\').innerHTML = data.msg;
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
        header('Location:/user/code');
    }

    public function getStatus($request, $response, $args)
    {
        $return = [];
        $p = Paylist::where('tradeno', $_POST['pid'])->first();
        $return['ret'] = 1;
        $return['result'] = $p->status;
        return json_encode($return);
    }

}
