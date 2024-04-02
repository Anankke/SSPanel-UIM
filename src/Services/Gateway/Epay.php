<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019.
 * Author:Alone88
 * Github:https://github.com/anhao
 */

namespace App\Services\Gateway;

use App\Models\Config;
use App\Models\Paylist;
use App\Services\Auth;
use App\Services\Gateway\Epay\EpayNotify;
use App\Services\Gateway\Epay\EpaySubmit;
use App\Services\Gateway\Epay\EpayTool;
use App\Services\View;
use Exception;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use voku\helper\AntiXSS;

final class Epay extends Base
{
    protected array $epay = [];
    private static string $err_msg = '请求支付失败';

    public function __construct()
    {
        $this->antiXss = new AntiXSS();
        $this->epay['apiurl'] = Config::obtain('epay_url');//易支付API地址
        $this->epay['partner'] = Config::obtain('epay_pid');//易支付商户pid
        $this->epay['key'] = Config::obtain('epay_key');//易支付商户Key
        $this->epay['sign_type'] = strtoupper(Config::obtain('epay_sign_type')); //签名方式
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
        $price = $this->antiXss->xss_clean($request->getParam('price'));
        $invoice_id = $this->antiXss->xss_clean($request->getParam('invoice_id'));
        // EPay 特定参数
        $type = $this->antiXss->xss_clean($request->getParam('type'));

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
            'return_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/user/payment/return/epay',
            'name' => $pl->tradeno,
            'money' => $price,
            'sitename' => $_ENV['appName'],
            'clientip' => $_SERVER['REMOTE_ADDR'],
        ];

        $epaySubmit = new EpaySubmit($this->epay);
        $data['sign'] = $epaySubmit->buildRequestMysign(EpayTool::argSort($data));
        $data['sign_type'] = $this->epay['sign_type'];
        $client = new Client();
        try {
            $res = $client->request('POST', $this->epay['apiurl'] . 'mapi.php', ['form_params' => $data]);
            if ($res->getStatusCode() !== 200) {
                throw new Exception(self::$err_msg);
            }
            $resData = json_decode((string) $res->getBody(), true);
            if ($resData['code'] !== 1 || ! isset($resData['payurl'])) {
                throw new Exception(self::$err_msg);
            }
            return $response->withJson([
                'ret' => 1,
                'url' => $resData['payurl'],
                'msg' => '订单发起成功，正在跳转到支付页面...',
            ]);
        } catch (Exception) {
            return $response->withJson([
                'ret' => 0,
                'msg' => self::$err_msg,
            ]);
        }
    }

    public function notify($request, $response, $args): ResponseInterface
    {
        $epayNotify = new EpayNotify($this->epay);
        $verify_result = $epayNotify->verifyNotify();

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
        $money = $_GET['money'];

        $html = <<<HTML
            你已成功充值 {$money} 元，正在跳转..
            <script>
                setTimeout(function() {
                    location.href="/user/invoice";
                },500)
            </script>
            HTML;

        return $response->write($html);
    }
}
