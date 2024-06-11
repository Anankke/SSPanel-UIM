<?php

declare(strict_types=1);

namespace App\Services\Gateway;

use App\Models\Config;
use App\Models\Invoice;
use App\Models\Paylist;
use App\Services\Auth;
use App\Services\Exchange;
use App\Services\View;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use RedisException;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Throwable;
use voku\helper\AntiXSS;

final class PayPal extends Base
{
    protected array $gateway_config;

    public function __construct()
    {
        $this->antiXss = new AntiXSS();
        $configs = Config::getClass('billing');

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

    public function purchase(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $invoice_id = $this->antiXss->xss_clean($request->getParam('invoice_id'));
        $invoice = (new Invoice())->find($invoice_id);

        if ($invoice === null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => 'Invoice not found',
            ]);
        }

        $price = $invoice->price;
        $trade_no = self::generateGuid();

        if ($price <= 0) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '非法的金额',
            ]);
        }

        try {
            $exchange_amount = (new Exchange())->exchange((float) $price, 'CNY', Config::obtain('paypal_currency'));
        } catch (GuzzleException|RedisException) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '汇率获取失败',
            ]);
        }

        $order_data = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => Config::obtain('paypal_currency'),
                        'value' => $exchange_amount,
                    ],
                    'invoice_id' => $trade_no,
                ],
            ],
        ];

        try {
            $pp = new PayPalClient($this->gateway_config);
            $pp->getAccessToken();
            $order = $pp->createOrder($order_data);
        } catch (Throwable) {
            return $response->withJson([
                'ret' => 0,
                'msg' => 'PayPal API Error',
            ]);
        }

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

    public function notify($request, $response, $args): ResponseInterface
    {
        $webhook_data = json_decode($request->getBody()->getContents(), true);

        try {
            $pp = new PayPalClient($this->gateway_config);
            $pp->getAccessToken();
            $verify_result = $pp->verifyWebHook($webhook_data);
        } catch (Throwable) {
            return $response->withJson([
                'ret' => 0,
                'msg' => 'PayPal API Error',
            ]);
        }

        if ($verify_result['verification_status'] === 'SUCCESS' &&
            $webhook_data['event_type'] === 'PAYMENT.CAPTURE.COMPLETED' &&
            $webhook_data['resource']['status'] === 'COMPLETED'
        ) {
            $this->postPayment($webhook_data['resource']['invoice_id']);

            return $response->withJson([
                'ret' => 1,
                'msg' => 'Payment Success',
            ]);
        }

        return $response->withJson([
            'ret' => 0,
            'msg' => 'Payment Failed',
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
