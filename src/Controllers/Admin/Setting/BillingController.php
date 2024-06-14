<?php

declare(strict_types=1);

namespace App\Controllers\Admin\Setting;

use App\Controllers\BaseController;
use App\Models\Config;
use App\Services\Payment;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Smarty\Exception;
use Srmklive\PayPal\Services\PayPal;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\WebhookEndpoint;
use Throwable;
use function json_decode;
use function json_encode;

final class BillingController extends BaseController
{
    private array $update_field;
    private array $settings;

    public function __construct()
    {
        parent::__construct();
        $this->update_field = Config::getItemListByClass('billing');
        $this->settings = Config::getClass('billing');
    }

    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $settings = Config::getClass('billing');

        return $response->write(
            $this->view()
                ->assign('update_field', $this->update_field)
                ->assign('settings', $this->settings)
                ->assign('payment_gateways', $this->returnGatewaysList())
                ->assign('active_payment_gateway', $this->returnActiveGateways())
                ->fetch('admin/setting/billing.tpl')
        );
    }

    public function save(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $gateway_in_use = [];

        foreach ($this->returnGatewaysList() as $value) {
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

        foreach ($this->update_field as $item) {
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
