<?php

declare(strict_types=1);

namespace App\Services\Gateway;

use App\Models\Config;
use App\Models\Paylist;
use App\Services\Auth;
use App\Services\Gateway\MeowPay\MeowPayment;
use App\Services\View;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use voku\helper\AntiXSS;

final class MeowPay extends Base
{
    private string $app_id;
    public function __construct()
    {
        $configs = Config::getClass('billing');
        $this->app_id = $configs['meowpay_app_id'];
    }

    public static function _name(): string
    {
        return 'meowpay';
    }

    public static function _enable(): bool
    {
        return self::getActiveGateway('meowpay');
    }

    public static function _readableName(): string
    {
        return 'MeowPay';
    }

    public function purchase(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $antiXss = new AntiXSS();
        $price = $antiXss->xss_clean($request->getParam('price'));
        $invoice_id = $antiXss->xss_clean($request->getParam('invoice_id'));
        $trade_no = uniqid();

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
        $pl->tradeno = $trade_no;
        $pl->invoice_id = $invoice_id;
        $pl->gateway = self::_readableName();
        $pl->save();
        $app_id = $this->app_id;
        $currency_type = 'CNY';
        $amount = bcmul($price, '100', 0);
        $meowpay = new MeowPayment($app_id, $trade_no, $currency_type, (int) $amount);
        $pay_link = $meowpay->getPayLink();
        return $response->withRedirect($pay_link);
    }

    public function notify($request, $response, $args): ResponseInterface
    {
        $r = (object) $request->getParsedBody();
        $params = (object) $r->{'params'};
        $app_id = $params->{'app_id'};
        if ($app_id === $this->app_id) {
            $order_id = $params->{'trade_no'};
            $this->postPayment($order_id);
            return $response->withJson(['jsonrpc' => '2.0', 'id' => $r->id, 'result' => ['status' => 'Done']]);
        }
        return $response->withJson(['jsonrpc' => '2.0', 'id' => $r->id, 'error' => 'fail']);
    }

    public static function getPurchaseHTML(): string
    {
        return View::getSmarty()->fetch('gateway/meowpay.tpl');
    }
    protected static function getUserReturnUrl(): string
    {
        return $_ENV['baseUrl'] . '/user/payment/return/' . get_called_class()::_name();
    }
}
