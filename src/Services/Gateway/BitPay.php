<?php
/**
 * BitPay - 更匿名、更安全、无需支付宝姓名和账户等个人私密信息
 *
 * Date: 2019/5/17
 * Time: 1:08 AM PST
 */

namespace App\Services\Gateway;

use App\Models\Paylist;
use App\Services\Auth;
use App\Services\View;

class BitPay extends AbstractPayment
{
    private $bitpayAppSecret;
    private $bitpayGatewayUri;

    /*****************************************************************
     * BitPay Helper Function
     ******************************************************************/
    public function __construct($bitpayAppSecret)
    {
        $this->bitpayAppSecret = $bitpayAppSecret;
        $this->bitpayGatewayUri = 'https://api.mugglepay.com/v1/';
    }

    public function prepareSignId($tradeno)
    {
        $data_sign = array();
        $data_sign['merchant_order_id'] = $tradeno;
        $data_sign['secret'] = $this->bitpayAppSecret;
        ksort($data_sign);
        return http_build_query($data_sign);
    }

    public function sign($data)
    {
        return strtolower(md5(md5($data) . $this->bitpayAppSecret));
    }

    public function verify($data, $signature)
    {
        $mySign = $this->sign($data);

        return $mySign === $signature;
    }

    public function mprequest($data, $type = 'pay')
    {
        $headers = array('content-type: application/json', 'token: ' . $this->bitpayAppSecret);
        $curl = curl_init();
        if ($type === 'pay') {
            $this->bitpayGatewayUri .= 'orders';
            curl_setopt($curl, CURLOPT_URL, $this->bitpayGatewayUri);
            curl_setopt($curl, CURLOPT_POST, 1);
            $data_string = json_encode($data);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        } elseif ($type === 'query') {
            $this->bitpayGatewayUri .= 'orders/merchant_order_id/status?id=';
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

    public function query($tradeNo)
    {
        $data['merchant_order_id'] = $tradeNo;
        return json_decode($this->mprequest($data, 'query'), true);
    }

    /*****************************************************************
     * Abstract Payment Implentation
     ******************************************************************/
    public function purchase($request, $response, $args)
    {
        $price = $request->getParam('price');
        $type = $request->getParam('type');
        // file_put_contents(BASE_PATH.'/bitpay_purchase.log', $price . "  " . $type . "\r\n", FILE_APPEND);
        if ($price <= 0) {
            return json_encode(['errcode' => -1, 'errmsg' => '非法的金额.']);
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
        $data['title'] = '支付单号' . $pl->tradeno;
        $data['description'] = '充值：' . $price;
        $data['description'] .= '  元';
        $data['callback_url'] = $_ENV['baseUrl'] . '/payment/bitpay/notify';
        $data['success_url'] = $_ENV['baseUrl'] . '/user/payment/bitpay/return?merchantTradeNo=';
        $data['success_url'] .= $pl->tradeno;
        $data['cancel_url'] = $data['success_url'];
        if ($type === 'Alipay') {
            $data['checkout'] = 'QBIT';
        }
        $str_to_sign = $this->prepareSignId($pl->tradeno);
        $data['token'] = $this->sign($str_to_sign);
        $result = json_decode($this->mprequest($data), true);
        $result['pid'] = $pl->tradeno;
        // file_put_contents(BASE_PATH.'/bitpay_purchase.log', json_encode($data)."\r\n", FILE_APPEND);
        // file_put_contents(BASE_PATH.'/bitpay_purchase.log', json_encode($result)."\r\n", FILE_APPEND);
        if ($result['status'] == 200 || $result['status'] == 201) {
            $result['payment_url'] .= '&lang=zh';
            return json_encode(array('url' => $result['payment_url'], 'errcode' => 0, 'pid' => $pl->id));
        }

        return json_encode(['errcode' => -1, 'errmsg' => $result . error]);
    }

    public function notify($request, $response, $args)
    {
        if (!$this->bitpayAppSecret || $this->bitpayAppSecret === '') {
            $return = [];
            $return['status'] = 400;
            echo json_encode($return);
            return;
        }
        $inputString = file_get_contents('php://input', 'r');
        $inputStripped = str_replace(array("\r", "\n", "\t", "\v"), '', $inputString);
        $inputJSON = json_decode($inputStripped, true); //convert JSON into array
        $data = array();
        if ($inputJSON !== null) {
            $data['status'] = $inputJSON['status'];
            $data['order_id'] = $inputJSON['order_id'];
            $data['merchant_order_id'] = $inputJSON['merchant_order_id'];
            $data['price_amount'] = $inputJSON['price_amount'];
            $data['price_currency'] = $inputJSON['price_currency'];
            $data['created_at_t'] = $inputJSON['created_at_t'];
        }
        // file_put_contents(BASE_PATH.'/bitpay_notify.log', json_encode($data)."\r\n", FILE_APPEND);
        // 准备待签名数据
        $str_to_sign = $this->prepareSignId($inputJSON['merchant_order_id']);
        $resultVerify = $this->verify($str_to_sign, $inputJSON['token']);
        $isPaid = $data !== null && $data['status'] !== null && $data['status'] === 'PAID';
        // file_put_contents(BASE_PATH.'/bitpay_notify.log', $resultVerify."\r\n".$isPaid."\r\n", FILE_APPEND);
        if ($resultVerify && $isPaid) {
            $this->postPayment($data['merchant_order_id'], 'BitPay');
            // echo 'SUCCESS';
            $return = [];
            $return['status'] = 200;
            echo json_encode($return);
        } else {
            // echo 'FAIL';
            $return = [];
            $return['status'] = 400;
            echo json_encode($return);
        }
    }

    public function getPurchaseHTML()
    {
        return View::getSmarty()->fetch('user/bitpay.tpl');
    }

    public function getReturnHTML($request, $response, $args)
    {
        $pid = $_GET['merchantTradeNo'];
        $p = Paylist::where('tradeno', '=', $pid)->first();
        $money = $p->total;
        if ($p->status == 1) {
            $success = 1;
        } else {
            $data = $this->query($pid);
            $isPaid = $data !== null && $data['order'] !== null && $data['order']['status'] !== null && $data['order']['status'] === 'PAID';
            // file_put_contents(BASE_PATH.'/bitpay_return.log', $pid . " " . $data ."\r\n". $isPaid . "\r\n", FILE_APPEND);
            if ($isPaid) {
                $this->postPayment($pid, 'BitPay');
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
        $p = Paylist::where('tradeno', $_POST['pid'])->first();
        $return['ret'] = 1;
        $return['result'] = $p->status;
        // file_put_contents(BASE_PATH.'/bitpay_status_success.log', json_encode($return)."\r\n", FILE_APPEND);
        return json_encode($return);
    }
}
