<?php

declare(strict_types=1);

namespace App\Controllers\Admin\Setting;

use App\Controllers\BaseController;
use App\Models\Config;
use App\Services\Payment;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Srmklive\PayPal\Services\PayPal;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\WebhookEndpoint;
use Throwable;
use function json_decode;
use function json_encode;

final class BillingController extends BaseController
{
    private static array $update_field = [
        // 支付宝当面付
        'f2f_pay_app_id',
        'f2f_pay_pid',
        'f2f_pay_public_key',
        'f2f_pay_private_key',
        'f2f_pay_notify_url',
        // Stripe
        'stripe_api_key',
        'stripe_endpoint_secret',
        'stripe_currency',
        'stripe_card',
        'stripe_alipay',
        'stripe_wechat',
        'stripe_min_recharge',
        'stripe_max_recharge',
        // EPay
        'epay_url',
        'epay_pid',
        'epay_key',
        'epay_sign_type',
        'epay_alipay',
        'epay_wechat',
        'epay_qq',
        'epay_usdt',
        // PayPal
        'paypal_mode',
        'paypal_client_id',
        'paypal_client_secret',
        'paypal_currency',
        'paypal_locale',
    ];

    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $settings = Config::getClass('billing');

        return $response->write(
            $this->view()
                ->assign('update_field', self::$update_field)
                ->assign('settings', $settings)
                ->assign('payment_gateways', self::returnGatewaysList())
                ->assign('active_payment_gateway', self::returnActiveGateways())
                ->fetch('admin/setting/billing.tpl')
        );
    }

    public function save(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $gateway_in_use = [];

        foreach (self::returnGatewaysList() as $value) {
            $payment_enable = $request->getParam($value);

            if ($payment_enable === 'true') {
                $gateway_in_use[] = $value;
            }
        }

        $gateway = (new Config())->where('item', 'payment_gateway')->first();
        $gateway->value = json_encode($gateway_in_use);

        if (! $gateway->save()) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '保存支付网关时出错',
            ]);
        }

        foreach (self::$update_field as $item) {
            if (! Config::set($item, $request->getParam($item))) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '保存 ' . $item . ' 时出错',
                ]);
            }
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '保存成功',
        ]);
    }

    public function setStripeWebhook(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $stripe_api_key = $request->getParam('stripe_api_key');

        Stripe::setApiKey($stripe_api_key);

        try {
            WebhookEndpoint::create([
                'url' => $_ENV['baseUrl'] . '/payment/notify/stripe',
                'enabled_events' => [
                    'payment_intent.succeeded',
                ],
            ]);
        } catch (ApiErrorException) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '设置 Stripe Webhook 失败',
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '设置 Stripe Webhook 成功',
        ]);
    }

    public function setPaypalWebhook(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $paypal_client_id = $request->getParam('paypal_client_id');
        $paypal_client_secret = $request->getParam('paypal_client_secret');

        $gateway_config = [
            'mode' => 'live',
            'live' => [
                'client_id' => $paypal_client_id,
                'client_secret' => $paypal_client_secret,
            ],
            'payment_action' => 'Sale',
            'currency' => 'USD',
            'notify_url' => '',
            'locale' => 'en_US',
        ];

        try {
            $pp = new PayPal($gateway_config);
            $pp->getAccessToken();
            $pp->createWebHook($_ENV['baseUrl'] . '/payment/notify/paypal', ['PAYMENT.CAPTURE.COMPLETED']);
        } catch (Throwable $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '设置 PayPal Webhook 失败',
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '设置 PayPal Webhook 成功',
        ]);
    }

    public function returnGatewaysList(): array
    {
        $result = [];

        foreach (Payment::getAllPaymentMap() as $payment) {
            $result[$payment::_name()] = $payment::_name();
        }

        return $result;
    }

    public function returnActiveGateways(): ?array
    {
        $payment_gateways = (new Config())->where('item', 'payment_gateway')->first();

        return json_decode($payment_gateways->value);
    }
}
