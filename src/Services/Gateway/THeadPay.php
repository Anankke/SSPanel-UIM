<?php

declare(strict_types=1);

namespace App\Services\Gateway;

use App\Models\Paylist;
use App\Models\Setting;
use App\Services\Auth;
use App\Services\View;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class THeadPay extends AbstractPayment
{
    protected $sdk;

    public function __construct()
    {
        $configs = Setting::getClass('theadpay');

        $this->sdk = new THeadPaySDK([
            'theadpay_url' => $configs['theadpay_url'],
            'theadpay_mchid' => $configs['theadpay_mchid'],
            'theadpay_key' => $configs['theadpay_key'],
        ]);
    }
    public static function _name(): string
    {
        return 'theadpay';
    }

    public static function _enable(): bool
    {
        return self::getActiveGateway('theadpay');
    }

    public static function _readableName(): string
    {
        return 'THeadPay 平头哥支付';
    }

    public function purchase(Request $request, Response $response, array $args): ResponseInterface
    {
        $amount = (int) $request->getParam('amount');
        $user = Auth::getUser();
        if ($amount <= 0) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '订单金额错误：' . $amount,
            ]);
        }

        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->tradeno = self::generateGuid();
        $pl->total = $amount;
        $pl->save();

        try {
            $res = $this->sdk->pay([
                'trade_no' => $pl->tradeno,
                'total_fee' => $pl->total * 100,
                'notify_url' => self::getCallbackUrl(),
                'return_url' => self::getUserReturnUrl(),
            ]);

            return $response->withJson([
                'ret' => 1,
                'qrcode' => $res['code_url'],
                'amount' => $pl->total,
                'pid' => $pl->tradeno,
            ]);
        } catch (Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '创建支付订单错误：' . $e->getMessage(),
            ]);
        }
    }

    public function notify($request, $response, $args): ResponseInterface
    {
        $params = $request->getParsedBody();
        if ($this->sdk->verify($params)) {
            $pid = $params['out_trade_no'];
            $this->postPayment($pid, 'THeadPay 平头哥支付 ' . $pid);
            die('success'); //The response should be 'success' only
        }

        die('fail');
    }

    public static function getPurchaseHTML(): string
    {
        return View::getSmarty()->fetch('user/theadpay.tpl');
    }
}
