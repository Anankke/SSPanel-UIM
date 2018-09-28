<?php
/**
 * Created by PhpStorm.
 * User: tonyzou
 * Date: 2018/9/28
 * Time: 9:08 PM
 */

namespace App\Services\Gateway;
use App\Services\View;
use App\Services\Auth;
use App\Services\Config;
use App\Models\Paylist;

class TrimePay extends AbstractPayment
{

    private $appSecret;
    /**
     * 签名初始化
     * @param merKey	签名密钥
     */

    public function __construct($appSecret) {
        $this->appSecret = $appSecret;
        $this->gatewayUri = 'https://api.Trimepay.com/gateway/pay/go';
    }

    /**
     * @name	准备签名/验签字符串
     */
    public function prepareSign($data) {
        ksort($data);
        return http_build_query($data);
    }

    /**
     * @name	生成签名
     * @param	sourceData
     * @return	签名数据
     */
    public function sign($data) {
        $signature = strtolower(md5(md5($data).$this->appSecret));
        return $signature;
    }

    /*
     * @name	验证签名
     * @param	signData 签名数据
     * @param	sourceData 原数据
     * @return
     */
    public function verify($data, $signature) {
        $mySign = $this->sign($data);
        if ($mySign === $signature) {
            return true;
        } else {
            return false;
        }
    }

    public function post($data){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->gatewayUri);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }


    public function init()
    {
        // TODO: Implement init() method.
    }

    public function setMethod($method)
    {
        // TODO: Implement setMethod() method.
    }

    public function setNotifyUrl()
    {
        // TODO: Implement setNotifyUrl() method.
    }

    public function setReturnUrl()
    {
        // TODO: Implement setReturnUrl() method.
    }

    public function purchase($request, $response, $args)
    {
        $price = $request->getParam('price');


        if($price <= 0){
            return json_encode(['code'=>-1,'errmsg'=>"非法的金额."]);
        }
        $user = Auth::getUser();
        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->total = $price;
        $pl->save();

        $data['appId'] = Config::get('trimepay_appid');
        $data['payType'] = 'ALIPAY_WEB';
        $data['merchantTradeNo'] = $pl->id;
        $data['totalFee'] = (int)$price * 100;
        $data['notifyUrl'] = Config::get("baseUrl")."/user/payment/notify";
        $data['returnUrl'] = Config::get("baseUrl")."/user/payment/return";
        $params = self::prepareSign($data);
        $data['sign'] = self::sign($params);
        return self::post($data);
    }

    public function notify($request, $response, $args)
    {
        $data = array();
        $data['payStatus']=$_REQUEST['payStatus'];
        $data['payFee']=$_REQUEST['payFee'];
        $data['callbackTradeNo']=$_REQUEST['callbackTradeNo'];
        $data['payType']=$_REQUEST['payType'];
        $data['merchantTradeNo']=$_REQUEST['merchantTradeNo'];

        //file_put_contents('./trimepay_notify.log', json_encode($data)."\r\n", FILE_APPEND);
        // 准备待签名数据
        $str_to_sign = self::prepareSign($data);
        // 验证签名
        $resultVerify = self::verify($str_to_sign, $_REQUEST['sign']);
        if ($resultVerify) {
            //file_put_contents('./trimepay_notify_success.log', json_encode($data)."\r\n", FILE_APPEND);
            self::postPayment($data['merchantTradeNo'], "TrimePay 支付宝");
            echo 'SUCCESS';
        }else{
            echo 'FAIL';
        }
    }


    public function getPurchaseHTML()
    {
        return View::getSmarty()->fetch("user/trimepay.tpl");
    }

    public function getReturnHTML($request, $response, $args)
    {
        $pid = $_GET['merchantTradeNo'];
        $p = Paylist::find($pid);
        $money = $p->total;
        if ($p->status == 1){
            $success = 1;
        } else {
            $success = 0;
        }
        return View::getSmarty()->assign('money', $money)->assign('success', $success)->fetch('user/pay_success.tpl');
    }

    public function getStatus($request, $response, $args)
    {
        // TODO: Implement getStatus() method.
    }
}

