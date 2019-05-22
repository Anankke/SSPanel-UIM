<?php
/**
 * BitPay - 更匿名、更安全、无需支付宝姓名和账户等个人私密信息
 *
 * Date: 2019/5/17
 * Time: 1:08 AM PST
 */

namespace App\Services\Gateway;

use App\Services\View;
use App\Services\Auth;
use App\Services\Config;
use App\Models\Paylist;

class TomatoBitPay extends AbstractPayment
{


    private $bitpayAppSecret;
    private $bitpayGatewayUri;
    /**
     * 签名初始化
     * @param merKey    签名密钥
     */

    public function __construct() {
        $this->bitpayAppSecret = Config::get("bitpay_secret");
        $this->bitpayGatewayUri = 'https://api.mugglepay.com/v1/';
    }

    /**
     * @name    准备签名/验签字符串
     */
    public function prepareSignId($tradeno) {
        $data_sign = array();
        $data_sign['merchant_order_id'] = $tradeno;
        $data_sign['secret'] = $this->bitpayAppSecret;
        ksort($data_sign);
        return http_build_query($data_sign);
    }

    /**
     * @name    生成签名
     * @param   sourceData
     * @return  签名数据
     */
    public function sign($data) {
        $signature = strtolower(md5(md5($data).$this->bitpayAppSecret));
        return $signature;
    }

    /*
     * @name    验证签名
     * @param   signData 签名数据
     * @param   sourceData 原数据
     * @return
     */
    public function verify($data, $signature) {
        $mySign = $this->sign($data);
        if ($mySign == $signature) {
            return true;
        } else {
            return false;
        }
    }

    public function mprequest($data, $type = "pay"){
        $headers = array(
            "content-type: application/json",
            "token: ".$this->bitpayAppSecret,
        ); 
        $curl = curl_init();
        if ($type == "pay"){
            $this->bitpayGatewayUri .= "orders";
            curl_setopt($curl, CURLOPT_URL, $this->bitpayGatewayUri);
            curl_setopt($curl, CURLOPT_POST, 1);

            $data_string = json_encode($data);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        } else if ($type == "query") {
            $this->bitpayGatewayUri .= "orders/merchant_order_id/status?id=";
            $this->bitpayGatewayUri .= $data['merchant_order_id'];
            
            curl_setopt($curl, CURLOPT_URL, $this->bitpayGatewayUri);
            curl_setopt($curl, CURLOPT_HTTPGET, 1);         
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }

    public function query($tradeNo){
        $data['merchant_order_id'] = $tradeNo;
        $result = json_decode(self::mprequest($data, "query"), TRUE);
        return $result;
    }

    public function purchaseBitPay($request, $response, $args)
    {
        $price = $request->getParam('price');
        $type = $request->getParam('type');

        if($price <= 0){
            return json_encode(['errcode' => -1,'errmsg'=>"非法的金额."]);
        }
        $user = Auth::getUser();
        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->total = $price;
        $pl->tradeno = self::generateGuid();
        $pl->save();

        $data['merchant_order_id'] = $pl->tradeno;
        $data['price_amount'] = (float)$price;
        $data['price_currency'] = 'CNY';
        $data['title'] = "支付单号".$pl->tradeno;
        $data['description'] = "充值：".$price;
        $data['description'] .= "  元";

        $data['callback_url'] = Config::get("baseUrl")."/payment/notify";
        $data['success_url'] = Config::get("baseUrl")."/user/payment/return?merchantTradeNo=";
        $data['success_url'] .= $pl->tradeno;
        $data['cancel_url'] = $data['success_url'];

        if ($type == 'Alipay') {
            $data['checkout'] = 'QBIT';
        }

        $str_to_sign = self::prepareSignId($pl->tradeno);
        $data['token'] = self::sign($str_to_sign);
        $result = json_decode(self::mprequest($data, "pay"), TRUE);
        $result['pid'] = $pl->tradeno;

        if ($result['status'] == 200 || $result['status'] == 201) {
            $result['payment_url'] .= '&lang=zh';
            $code = "<script language='javascript' type='text/javascript'>window.location.href='" . $result['payment_url'] . "';</script>";
            return json_encode(array('code' => $code, 'url' => $result['payment_url'], 'errcode' => 0, 'pid' => $pl->id));
        } else {
            return json_encode(['errcode' => -1, 'errmsg' => $result.error]);
        }
    }

    public function purchase($request, $response, $args)
    {
        $type = $request->getParam('type');

        if ($type == 'bitpay') {
            return self::purchaseBitPay($request, $response, $args);
        }
        // file_put_contents(BASE_PATH.'/bitpay_notify_purchase.log', $type."\r\n", FILE_APPEND);
        if ($type != 'wxpay' && $type != 'alipay') {
            return json_encode(['errcode' => -1, 'errmsg' => "请选择支付方式."]);
        }

        $price = $request->getParam('price');
        if ($price <= 0) {
            return json_encode(['errcode' => -1, 'errmsg' => "非法的金额."]);
        }
        $user = Auth::getUser();
        $settings = Config::get("tomatopay")[$type];
        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->total = $price;
        $pl->tradeno = self::generateGuid();
        $pl->save();
        $fqaccount = $settings['account'];
        $fqkey = $settings['token'];
        $fqmchid = $settings['mchid'];
        $fqtype = 1;
        $fqtrade = $pl->tradeno;
        $fqcny = $price;
        $data = [
            'account' => $settings['account'],
            'mchid' => $settings['mchid'],
            'type' => 1,
            'trade' => $pl->tradeno,
            'cny' => $price,
        ];
        $signs = md5("mchid=" . $fqmchid . "&account=" . $fqaccount . "&cny=" . $fqcny . "&type=1&trade=" . $fqtrade . $fqkey);
        $url = "https://b.fanqieui.com/gateways/" . $type . ".php?account=" . $fqaccount . "&mchid=" . $fqmchid . "&type=" . $fqtype . "&trade=" . $fqtrade . "&cny=" . $fqcny . "&signs=" . $signs;
        $result = "<script language='javascript' type='text/javascript'>window.location.href='" . $url . "';</script>";
        $result = json_encode(array('code' => $result, 'errcode' => 0, 'pid' => $pl->id));
        return $result;
    }

    public function notifyBitPay($request, $response, $args)
    {
        // file_put_contents(BASE_PATH.'/bitpay_notify.log', "Bitpay \r\n", FILE_APPEND);
        $inputString = file_get_contents('php://input', 'r');
        $inputStripped = str_replace(array("\r", "\n", "\t", "\v"), '', $inputString);
        $inputJSON = json_decode($inputStripped, TRUE); //convert JSON into array
        // file_put_contents(BASE_PATH.'/bitpay_notify.log', $inputString . "\r\n", FILE_APPEND);

        $data = array();
        if (!is_null($inputJSON)) {
            $data['status'] = $inputJSON['status'];
            $data['order_id'] = $inputJSON['order_id'];
            $data['merchant_order_id'] = $inputJSON['merchant_order_id'];
            $data['price_amount'] = $inputJSON['price_amount'];
            $data['price_currency'] = $inputJSON['price_currency'];
            $data['created_at_t'] = $inputJSON['created_at_t'];
        }
        file_put_contents(BASE_PATH.'/bitpay_notify.log', json_encode($data)."\r\n", FILE_APPEND);

        // 准备待签名数据
        $str_to_sign = self::prepareSignId($inputJSON['merchant_order_id']);
        $resultVerify = self::verify($str_to_sign, $inputJSON['token']);

        $isPaid = !is_null($data) && !is_null($data['status']) && $data['status'] == 'PAID';
        // file_put_contents(BASE_PATH.'/bitpay_notify.log', $resultVerify."\r\n".$isPaid."\r\n", FILE_APPEND);

        if ($resultVerify && $isPaid) {
            self::postPayment($data['merchant_order_id'], "BitPay");
            # echo 'SUCCESS';
            $return = [];
            $return['status'] = 200;
            echo json_encode($return);
        }else{
            // echo 'FAIL';
            $return = [];
            $return['status'] = 400;
            echo json_encode($return);
        }
    }

    public function notify($request, $response, $args)
    {
        $type = $args['type'];

        // file_put_contents(BASE_PATH.'/bitpay_notify.log', $type."\r\n", FILE_APPEND);
        if ($type != 'wxpay' && $type != 'alipay') {
            return self::notifyBitPay($request, $response, $args);
        }
        // file_put_contents(BASE_PATH.'/bitpay_notify.log', "Wxpay or Alipay \r\n", FILE_APPEND);

        $settings = Config::get("tomatopay")[$type];
        $order_data = $_REQUEST;
        $transid = $order_data['trade_no'];       //转账交易号
        $invoiceid = $order_data['out_trade_no'];     //订单号
        $amount = $order_data['total_fee'];          //获取递过来的总价格
        $status = $order_data['trade_status'];         //获取传递过来的交易状态
        $signs = $order_data['sign'];

        $security = array();
        $security['out_trade_no'] = $invoiceid;
        $security['total_fee'] = $amount;
        $security['trade_no'] = $transid;
        $security['trade_status'] = $status;
        foreach ($security as $k => $v) {
            $o .= "$k=" . urlencode($v) . "&";
        }
        $sign = md5(substr($o, 0, -1) . $settings['token']);


        if ($sign == $signs) {
            $this->postPayment($order_data['out_trade_no'], "在线支付");
            echo 'success';
            if ($ispost == 0) header("Location: /user/code");
        } else {
            echo '验证失败';
        }
    }

    public function getPurchaseHTML()
    {
        return View::getSmarty()->fetch("user/tomatobitpay.tpl");
    }

    public function getReturnHTML($request, $response, $args)
    {
        $pid = $_GET['merchantTradeNo'];
        $p = Paylist::where('tradeno','=',$pid)->first();
        $money = $p->total;
        if ($p->status == 1){
            $success = 1;
        } else {
            $data = self::query($pid);
            // file_put_contents(BASE_PATH.'/bitpay_return.log', $pid . " " . json_encode($data)."\r\n", FILE_APPEND);

            $isPaid = !is_null($data) && !is_null($data['order']) && !is_null($data['order']['status']) && $data['order']['status'] == 'PAID';
            file_put_contents(BASE_PATH.'/bitpay_return.log', $pid . " " . $data ."\r\n". $isPaid . "\r\n", FILE_APPEND);

            // file_put_contents(BASE_PATH.'/bitpay_return.log', $pid . " " . $p . " " .$isPaid."\r\n", FILE_APPEND);

            if ($isPaid) {
                self::postPayment($pid, "BitPay");
                $success = 1;
            } else {
                $success = 0;
            }
        }
        return View::getSmarty()->assign('money', $money)->assign('success', $success)->fetch('user/pay_success.tpl');
    }

    public function getStatus($request, $response, $args)
    {
        $return = [];
        $p = Paylist::where("tradeno", $_POST['pid'])->first();
        $return['ret'] = 1;
        $return['result'] = $p->status;
        // file_put_contents(BASE_PATH.'/bitpay_status_success.log', json_encode($return)."\r\n", FILE_APPEND);
        return json_encode($return);
    }
}
