<?php
/**
 * Copyright (c) 2019.
 * Author:Alone88
 * Github:https://github.com/anhao
 */

namespace App\Services\Gateway;
use Exception;
use App\Services\Gateway\Epay\Epay_notify;
use App\Services\Gateway\Epay\Epay_submit;
use App\Services\View;
use App\Services\Auth;
use App\Models\Paylist;
use App\Models\Setting;

class Epay extends AbstractPayment
{
	public static function _name() 
    {
        return 'epay';
    }

    public static function _enable() 
    {
        return self::getActiveGateway('epay');
    }

    public static function _readableName() {
        return "epay在线充值";
    }


    protected $epay = array();

    public function __construct()
    {
        $this->epay['apiurl'] = Config::get('epay_url');//易支付API地址
        $this->epay['partner'] = Config::get('epay_pid');//易支付商户pid
        $this->epay['key'] = Config::get('epay_key');//易支付商户Key
        $this->epay['sign_type'] = strtoupper('MD5'); //签名方式
        $this->epay['input_charset'] = strtolower('utf-8');//字符编码
        $this->epay['transport'] = 'https';//协议 http 或者https
    }

    public function purchase($request, $response, $args)
    {
        $type = $request->getParam('type');
        $price = $request->getParam('price');
        if ($price <= 0) {
            return json_encode(['errcode' => -1, 'errmsg' => "非法的金额."]);
        }
        $user = Auth::getUser();
        $pl = new Paylist();

        $pl->userid = $user->id;
        $pl->total = $price;
        //订单号
        $pl->tradeno = self::generateGuid();
        $pl->save();

        //请求参数
        $data = array(
            "pid" => trim($this->epay['partner']),
            "type" => $type,
            "out_trade_no" => $pl->tradeno,
            "notify_url" => Config::get('baseUrl') . "/epay/notify",
            "return_url" => Config::get('baseUrl') . "/epay/return",
            "name" => $pl->tradeno, 
            #"name" =>  $user->mobile . "" . $price . "",
            "money" => $price,
            "sitename" => Config::get('appName')
        );
        $alipaySubmit = new Epay_submit($this->epay);
        $html_text = $alipaySubmit->buildRequestForm($data);
        echo $html_text;

    }

    public function notify($request, $response, $args)
    {
        $alipayNotify = new Epay_notify($this->epay);
        $verify_result = $alipayNotify->verifyNotify();
        if ($verify_result) {
            $out_trade_no = $_GET['out_trade_no'];
            $type = $_GET['type'];
            switch ($type) {
                case 'alipay':
                    $type = "Alipay";
                    break;
                case 'qqpay':
                    $type = "QQ";
                    break;
                case 'wxpay':
                    $type = "Wechat";
				case 'epusdt':
                    $type = "Epusdt";
                    break;
            }
            $trade_status = $_GET['trade_status'];
            if ($trade_status == 'TRADE_SUCCESS') {
                $this->postPayment($out_trade_no, $type);
                return json_encode(['state' => 'success', 'msg' => '支付成功']);
            }else{
                return json_encode(['state' => 'fail', 'msg' => '支付失败']);
            }
        } else {
            return '非法請求';
        }
    }

    public function getStatus($request, $response, $args)
    {
        // TODO: Implement getStatus() method.
    }

    public static function getPurchaseHTML()
    {
        return View::getSmarty()->fetch('user/epay.tpl');
    }

    public function getReturnHTML($request, $response, $args)
    {
        $money = $_GET['money'];
        echo "您已成功充值 $money 元,正在跳转..";
        echo <<<HTML
<script>
    setTimeout(function() {
      location.href="/user/code";
    },500)
</script>
HTML;
        return;
    }

    public function postPayment($pid, $method)
    {
        return parent::postPayment($pid, $method);
    }

    public static function generateGuid()
    {
        return parent::generateGuid();
    }
} 