<?php


namespace App\Services\Gateway;


use App\Models\Paylist;
use App\Services\Auth;
use App\Services\Config;
use App\Services\Gateway\CoinPay\CoinPayApi;
use App\Services\Gateway\CoinPay\CoinPayConfig;
use App\Services\Gateway\CoinPay\CoinPayException;
use App\Services\Gateway\CoinPay\CoinPayUnifiedOrder;

class CoinPay extends AbstractPayment
{
    private $coinPaySecret;
    private $coinPayGatewayUrl;
    private $coinPayAppId;


    public function __construct($coinPaySecret, $coinPayAppId)
    {
        $this->coinPaySecret = $coinPaySecret;
        $this->coinPayAppId = $coinPayAppId;
        $this->coinPayGatewayUrl = "https://openapi.coinpay.la/"; // 网关地址
    }


    public function purchase($request, $response, $args)
    {
        // set timezone
        date_default_timezone_set('Asia/Hong_Kong');
        /**************************请求参数**************************/
        $amount = $request->getParam('price');
        //var_dump($request->getParam("price"));die();
        $user = Auth::getUser();
        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->total = $amount;
        $pl->tradeno = self::generateGuid();
        $pl->save();
        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = $pl->tradeno;
        //订单名称，必填
        $subject = $pl->id . 'UID:' . $user->id . ' 充值' . $amount . '元';
        //付款金额，必填
        $total_fee = (float)$amount;
        /************************************************************/
        $report_data = new CoinPayUnifiedOrder();
        $report_data->SetSubject($subject);
        $report_data->SetOut_trade_no($out_trade_no);
        $report_data->SetTotal_amount($total_fee);
        $report_data->SetTimestamp(date('Y-m-d H:i:s', time()));
        $report_data->SetReturn_url(Config::get('baseUrl') . '/user/code');
        $report_data->SetNotify_url(Config::get('baseUrl') . '/payment/coinpay/notify');
//        $report_data->SetBody(json_encode($pl));
//        $report_data->SetTransCurrency("CNY");
//        $report_data->SetAttach("");
        $config = new CoinPayConfig();
        try {
            $url = CoinPayApi::unifiedOrder($config, $report_data);
            return json_encode(['code' => 0, 'url' => $this->coinPayGatewayUrl . 'api/gateway?' . $url]);
        } catch (CoinPayException $exception) {
            print_r($exception->getMessage());
            die();
        }
    }

    private function Sign($value, $secret)
    {
        ksort($value);
        reset($value);
        $sign_param = implode('&', $value);
        $signature = hash_hmac('sha256', $sign_param, $secret, true);
        return base64_encode($signature);
    }

    /**
     * @param $data
     * @param $sign
     * @return bool
     */
    public function verify($data, $sign)
    {
        $payConfig = new CoinPayConfig();
        if ($sign === self::Sign($data, $payConfig->GetSecret())) {
            return true;
        }
        return false;
    }

    /**
     * 异步通知
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args
     */
    public function notify($request, $response, $args)
    {
        $raw = file_get_contents("php://input");
        file_put_contents(BASE_PATH . '/coinpay_purchase.log', $raw . "\r\n", FILE_APPEND);
        $data = json_decode($raw, true);
        if (empty($data)) {
            file_put_contents(BASE_PATH . '/coinpay_purchase.log', "返回数据异常\r\n", FILE_APPEND);
            echo "fail";
            die();
        }
        // 签名验证
        $sign = $data['sign'];
        unset($data['sign']);
        $resultVerify = self::verify($data, $sign);
        $isPaid = $data !== null && $data['trade_status'] !== null && $data['trade_status'] === 'TRADE_SUCCESS';
        if ($resultVerify) {
            if ($isPaid) {
                $this->postPayment($data['out_trade_no'], 'CoinPay');
                echo "success";
                file_put_contents(BASE_PATH . '/coinpay_purchase.log', "订单{$data['out_trade_no']}支付成功\r\n" . json_encode($data) . "\r\n", FILE_APPEND);
            } else {
                echo "success";
                file_put_contents(BASE_PATH . '/coinpay_purchase.log', "订单{$data['out_trade_no']}未支付自动关闭成功\r\n" . json_encode($data) . "\r\n", FILE_APPEND);
            }
        } else {
            echo "fail";
            file_put_contents(BASE_PATH . '/coinpay_purchase.log', "订单{$data['out_trade_no']}签名验证失败或者订单未支付成功\r\n" . json_encode($data) . "\r\n", FILE_APPEND);
        }
        die();
    }

    public function getReturnHTML($request, $response, $args)
    {
        // TODO: Implement getStatus() method.
    }

    public function getStatus($request, $response, $args)
    {
        // TODO: Implement getStatus() method.
    }

    public function getPurchaseHTML()
    {
        return '<div class="card-inner">
						<div class="form-group pull-left">
                            <p class="modal-title">CoinPay 支持BTC、ETH、USDT等数十种数字货币</p>
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="price">充值金额</label>
                                <input id="type" class="form-control maxwidth-edit" name="amount" />
                            </div>
                             <a class="btn btn-flat waves-attach" id="submitSpay" style="padding: 8px 24px;color: #fff;background: #1890ff;"><span class="icon">check</span>&nbsp;充&nbsp;值&nbsp;</a>
                        </div>
                    </div>
                        <script>
                        window.onload = function(){
        $("#submitSpay").click(function() {
            var price = parseFloat($("#type").val());
            if (isNaN(price)) {
                $("#result").modal();
                $("#msg").html("非法的金额!");
                return false;
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
                    success: (data) => {
                        if (data.code == 0) {
                            $("#result").modal();
                            $("#msg").html("正在跳转CoinPay支付网关...");
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
}
