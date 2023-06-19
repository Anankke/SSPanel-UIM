<?php

declare(strict_types=1);

namespace App\Services\Gateway;

use App\Models\Paylist;
use App\Models\Setting;
use App\Services\Auth;
use App\Services\View;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Throwable;
use voku\helper\AntiXSS;
use function round;

final class PayPal extends AbstractPayment
{
    private array $gateway_config;

    public function __construct()
    {
        $configs = Setting::getClass('paypal');

        $this->gateway_config = [
            'mode' => $configs['paypal_mode'],
            'sandbox' => [
                'client_id' => $configs['paypal_client_id'],
                'client_secret' => $configs['paypal_client_secret'],
                'app_id' => '',
            ],
            'live' => [
                'client_id' => $configs['paypal_client_id'],
                'client_secret' => $configs['paypal_client_secret'],
                'app_id' => '',
            ],
            'payment_action' => 'Sale',
            'currency' => $configs['paypal_currency'],
            'notify_url' => '',
            'locale' => $configs['paypal_locale'],
            'validate_ssl' => true,
        ];
    }

    public static function _name(): string
    {
        return 'paypal';
    }

    public static function _enable(): bool
    {
        return self::getActiveGateway('paypal');
    }

    public static function _readableName(): string
    {
        return 'PayPal';
    }

    /**
     * @throws Throwable
     */
    public function purchase(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $antiXss = new AntiXSS();

        $price = $antiXss->xss_clean($request->getParam('price'));
        $invoice_id = $antiXss->xss_clean($request->getParam('invoice_id'));
        $trade_no = self::generateGuid();

        if ($price <= 0) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '非法的金额',
            ]);
        }

        $exchange_amount = round($price / self::exchange(Setting::obtain('paypal_currency')), 2);

        $order_data = [
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => Setting::obtain('paypal_currency'),
                        "value" => $exchange_amount,
                    ],
                    "reference_id" => $trade_no,
                ],
            ],
        ];

        $pp = new PayPalClient($this->gateway_config);
        $pp->getAccessToken();

        $order = $pp->createOrder($order_data);

        $user = Auth::getUser();
        $pl = new Paylist();

        $pl->userid = $user->id;
        $pl->total = $price;
        $pl->invoice_id = $invoice_id;
        $pl->tradeno = $trade_no;
        $pl->gateway = self::_readableName();

        $pl->save();

        return $response->withJson($order);
    }

    /**
     * @throws Throwable
     */
    public function notify($request, $response, $args): ResponseInterface
    {
        $antiXss = new AntiXSS();

        $order_id = $antiXss->xss_clean($request->getParam('order_id'));

        $pp = new PayPalClient($this->gateway_config);
        $pp->getAccessToken();

        $result = $pp->capturePaymentOrder($order_id);

        if (isset($result['status']) && $result['status'] === 'COMPLETED') {
            $trade_no = $result['purchase_units'][0]['reference_id'];
            $this->postPayment($trade_no);

            return $response->withJson([
                'ret' => 1,
                'msg' => '支付成功',
            ]);
        }

        return $response->withJson([
            'ret' => 0,
            'msg' => '支付失败',
        ]);
    }

    /**
     * @throws Exception
     */
    public static function getPurchaseHTML(): string
    {
        return View::getSmarty()->fetch('gateway/paypal.tpl');
    }
}
