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
use App\Services\Gateway\Epay\EpayNotify;
use App\Services\Gateway\Epay\EpaySubmit;
use App\Services\View;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use voku\helper\AntiXSS;

final class Epay extends AbstractPayment
{
    protected array $epay = [];

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
        return 'EPay';
    }

    public function purchase(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $antiXss = new AntiXSS();

        $price = $antiXss->xss_clean($request->getParam('price'));
        $invoice_id = $antiXss->xss_clean($request->getParam('invoice_id'));
        // EPay 特定参数
        $type = $antiXss->xss_clean($request->getParam('type'));

        if ($price <= 0) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '非法的金额',
            ]);
        }

        $user = Auth::getUser();
        $pl = new Paylist();

        $pl->userid = $user->id;
        $pl->total = $price;
        $pl->invoice_id = $invoice_id;
        $pl->tradeno = self::generateGuid();

        $type_text = match ($type) {
            'qqpay' => 'QQ',
            'wxpay' => 'WeChat',
            'epusdt' => 'USDT',
            default => 'Alipay',
        };

        $pl->gateway = self::_readableName() . ' ' . $type_text;

        $pl->save();
        //请求参数
        $data = [
            'pid' => trim($this->epay['partner']),
            'type' => $type,
            'out_trade_no' => $pl->tradeno,
            'notify_url' => $_ENV['baseUrl'] . '/payment/notify/epay',
            'return_url' => $_ENV['baseUrl'] . '/user/payment/return/epay',
            'name' => $pl->tradeno,
            #"name" =>  $user->mobile . "" . $price . "",
            'money' => $price,
            'sitename' => $_ENV['appName'],
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
            $type = match ($type) {
                'qqpay' => 'QQ',
                'wxpay' => 'WeChat',
                'epusdt' => 'USDT',
                default => 'Alipay',
            };
            $trade_status = $_GET['trade_status'];

            if ($trade_status === 'TRADE_SUCCESS') {
                $this->postPayment($out_trade_no);
                return $response->withJson(['state' => 'success', 'msg' => '支付成功']);
            }

            return $response->withJson(['state' => 'fail', 'msg' => '支付失败']);
        }

        return $response->write('非法请求');
    }

    /**
     * @throws Exception
     */
    public static function getPurchaseHTML(): string
    {
        return View::getSmarty()->fetch('gateway/epay.tpl');
    }

    public function getReturnHTML($request, $response, $args): ResponseInterface
    {
        $user = Auth::getUser();

        $money = $_GET['money'];

        if ($user->use_new_shop) {
            $html = <<<HTML
            你已成功充值 {$money} 元，正在跳转..
            <script>
                setTimeout(function() {
                    location.href="/user/invoice";
                },500)
            </script>
            HTML;
        } else {
            $html = <<<HTML
            你已成功充值 {$money} 元，正在跳转..
            <script>
                setTimeout(function() {
                    location.href="/user/code";
                },500)
            </script>
            HTML;
        }

        return $response->write($html);
    }
}
