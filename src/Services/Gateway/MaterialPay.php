<?php
namespace App\Services\Gateway;

use App\Services\View;
use App\Services\Auth;
use App\Services\Config;
use App\Models\Paylist;

class MaterialPay extends AbstractPayment
{

    private $appSecret;
    private $gatewayUri;


    public function __construct($appSecret)
    {
        $this->appSecret = $appSecret;
        $this->gatewayUri = 'https://www.materialpay.com/api/payment/';
    }

    public function prepareSign($data)
    {
        ksort($data);
        return http_build_query($data);
    }

    public function sign($data)
    {
        return strtolower(md5(md5($data) . $this->appSecret));
    }

    public function verify($data, $signature)
    {
        $mySign = $this->sign($data);
        return $mySign === $signature;
    }

    public function post($data, $type = 'create')
    {
        if ($type == 'create') {
            $this->gatewayUri .= 'create';
        } else {
            $this->gatewayUri .= 'query';
        }

        $headerArray = array("Content-type:application/json;charset='utf-8'","Accept:application/json");

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->gatewayUri);
        curl_setopt($curl, CURLOPT_HTTPHEADER,$headerArray);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }

    public function purchase($request, $response, $args)
    {
        $price = $request->getParam('price');
        $type = $request->getParam('type');

        if ($price <= 0) {
            return json_encode(['code' => -1, 'errmsg' => '非法的金额.']);
        }
        $user = Auth::getUser();
        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->total = $price;
        $pl->tradeno = self::generateGuid();
        $pl->save();

        $data['appId'] = Config::get('materialpay_appid');
        $data['outTradeNo'] = $pl->tradeno;
        $data['payAmount'] = (float)$price;
        $data['payType'] = $type;
        $params = $this->prepareSign($data);
        $data['sign'] = $this->sign($params);
        $data['returnUrl'] = Config::get('baseUrl') . '/user/code';
        switch ($type) {
            case ('WXPAY'):
                $result = json_decode($this->post($data), true);
                break;
            default:
                $result = json_decode($this->post($data), true);
        }
        $result['pid'] = $pl->tradeno;
        return json_encode($result);
    }

    public function query($tradeNo)
    {
        $data['appId'] = Config::get('materialpay_appid');
        $data['outTradeNo'] = $tradeNo;
        $params = $this->prepareSign($data);
        $data['sign'] = $this->sign($params);
        return json_decode($this->post($data, $type = 'query'), true);
    }

    public function notify($request, $response, $args)
    {
        $data = array();
        $data['tradeStatus'] = $request->getParam('tradeStatus');
        $data['payAmount'] = number_format($request->getParam('payAmount'),2);
        $data['tradeNo'] = $request->getParam('tradeNo');
        $data['payType'] = $request->getParam('payType');
        $data['outTradeNo'] = $request->getParam('outTradeNo');

        // 准备待签名数据
        $str_to_sign = $this->prepareSign($data);
        // 验证签名
        $resultVerify = $this->verify($str_to_sign, $request->getParam('sign'));
        if ($resultVerify) {
            $this->postPayment($data['outTradeNo'], 'MaterialPay');
            echo 'success';
        } else {
            echo 'fail';
        }
    }

    public function getPurchaseHTML()
    {
        return View::getSmarty()->fetch('user/materialpay.tpl');
    }

    public function getReturnHTML($request, $response, $args)
    {
        return 0;
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
