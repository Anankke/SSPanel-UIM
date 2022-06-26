<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019.
 * Author:Alone88
 * Github:https://github.com/anhao
 */

namespace App\Services\Gateway;

use App\Models\Paylist;
use App\Models\Setting;
use App\Services\Auth;
use App\Services\Config;
use App\Services\Gateway\Epay\EpayNotify;
use App\Services\Gateway\Epay\EpaySubmit;
use App\Services\View;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class Epay extends AbstractPayment
{
    protected $epay = [];

    public function __construct()
    {
        $this->epay['apiurl'] = Setting::obtain('epay_url');//易支付API地址
        $this->epay['partner'] = Setting::obtain('epay_pid');//易支付商户pid
        $this->epay['key'] = Setting::obtain('epay_key');//易支付商户Key
        $this->epay['sign_type'] = strtoupper('MD5'); //签名方式
        $this->epay['input_charset'] = strtolower('utf-8');//字符编码
        $this->epay['transport'] = 'https';//协议 http 或者https
    }

    public static function _name(): string
    {
        return 'epay';
    }

    public static function _enable(): bool
    {
        return self::getActiveGateway('epay');
    }

    public static function _readableName(): string
    {
        return 'epay在线充值';
    }

    public function purchase(Request $request, Response $response, array $args): ResponseInterface
    {
        $type = $request->getParam('type');
        $price = $request->getParam('price');
        if ($price <= 0) {
            return json_encode(['errcode' => -1, 'errmsg' => '非法的金额.']);
        }
        $user = Auth::getUser();
        $pl = new Paylist();

        $pl->userid = $user->id;
        $pl->total = $price;
        //订单号
        $pl->tradeno = self::generateGuid();
        $pl->save();

        //请求参数
        $data = [
            'pid' => trim($this->epay['partner']),
            'type' => $type,
            'out_trade_no' => $pl->tradeno,
            'notify_url' => Config::get('baseUrl') . '/payment/notify/epay',
            'return_url' => Config::get('baseUrl') . '/user/payment/return/epay',
            'name' => $pl->tradeno,
            #"name" =>  $user->mobile . "" . $price . "",
            'money' => $price,
            'sitename' => Config::get('appName'),
        ];
        $alipaySubmit = new EpaySubmit($this->epay);
        $html_text = $alipaySubmit->buildRequestForm($data);
        return $response->write($html_text);
    }

    public function notify($request, $response, $args): ResponseInterface
    {
        $alipayNotify = new EpayNotify($this->epay);
        $verify_result = $alipayNotify->verifyNotify();
        if ($verify_result) {
            $out_trade_no = $_GET['out_trade_no'];
            $type = $_GET['type'];
            switch ($type) {
                case 'alipay':
                    $type = 'Alipay';
                    break;
                case 'qqpay':
                    $type = 'QQ';
                    break;
                case 'wxpay':
                    $type = 'Wechat';
                    // no break
                case 'epusdt':
                    $type = 'Epusdt';
                    break;
            }
            $trade_status = $_GET['trade_status'];
            if ($trade_status === 'TRADE_SUCCESS') {
                $this->postPayment($out_trade_no, $type);
                return $response->withJson(['state' => 'success', 'msg' => '支付成功']);
            }
            return $response->withJson(['state' => 'fail', 'msg' => '支付失败']);
        }
        return $response->write('非法请求');
    }
    public static function getPurchaseHTML(): string
    {
        return View::getSmarty()->fetch('user/epay.tpl');
    }

    public function getReturnHTML($request, $response, $args): ResponseInterface
    {
        $money = $_GET['money'];
        $html = <<<HTML
        您已成功充值 ${money} 元，正在跳转..
        <script>
            setTimeout(function() {
                location.href="/user/code";
            },500)
        </script>
        HTML;
        return $response->write($html);
    }
}
