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
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;
use UnexpectedValueException;
use voku\helper\AntiXSS;
use function in_array;

final class Stripe extends Base
{
    public function __construct()
    {
        $this->antiXss = new AntiXSS();
    }

    public static function _name(): string
    {
        return 'stripe';
    }

    public static function _enable(): bool
    {
        return self::getActiveGateway('stripe');
    }

    public static function _readableName(): string
    {
        return 'Stripe';
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

        if ($price < Config::obtain('stripe_min_recharge') ||
            $price > Config::obtain('stripe_max_recharge')
        ) {
            return $response->withJson([
                'ret' => 0,
                'msg' => 'Price out of range',
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

        $stripe_currency = Config::obtain('stripe_currency');

        try {
            $exchange_amount = (new Exchange())->exchange((float) $price, 'CNY', $stripe_currency);
        } catch (GuzzleException|RedisException) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '汇率获取失败',
            ]);
        }
        // https://docs.stripe.com/currencies?presentment-currency=US#zero-decimal
        if (! in_array(
            $stripe_currency,
            ['BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW',
                'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF',
            ]
        )) {
            $exchange_amount *= 100;
        }

        $stripe = new StripeClient(Config::obtain('stripe_api_key'));
        $session = null;

        try {
            $session = $stripe->checkout->sessions->create([
                'customer_email' => $user->email,
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => Config::obtain('stripe_currency'),
                            'product_data' => [
                                'name' => 'Invoice #' . $invoice_id,
                            ],
                            'unit_amount' => (int) $exchange_amount,
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'payment_intent_data' => [
                    'metadata' => [
                        'trade_no' => $trade_no,
                    ],
                ],
                'success_url' => $_ENV['baseUrl'] . '/user/invoice/' . $invoice_id . '/view',
                'cancel_url' => $_ENV['baseUrl'] . '/user/invoice/' . $invoice_id . '/view',
            ]);
        } catch (ApiErrorException) {
            return $response->withJson([
                'ret' => 0,
                'msg' => 'Stripe API error',
            ]);
        }

        return $response->withHeader('HX-Redirect', $session->url);
    }

    public function notify(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        try {
            $event = Webhook::constructEvent(
                $request->getBody()->getContents(),
                $request->getHeaderLine('Stripe-Signature'),
                Config::obtain('stripe_endpoint_secret')
            );
        } catch (UnexpectedValueException) {
            return $response->withStatus(400)->withJson([
                'ret' => 0,
                'msg' => 'Unexpected Value error',
            ]);
        } catch (SignatureVerificationException) {
            return $response->withStatus(400)->withJson([
                'ret' => 0,
                'msg' => 'Signature Verification error',
            ]);
        }

        $payment_intent = $event->data->object;

        if ($event->type === 'payment_intent.succeeded' && $payment_intent->status === 'succeeded') {
            $this->postPayment($payment_intent->metadata->trade_no);

            return $response->withJson([
                'ret' => 1,
                'msg' => 'Payment success',
            ]);
        }

        return $response->withJson([
            'ret' => 0,
            'msg' => 'Payment failed',
        ]);
    }

    /**
     * @throws Exception
     */
    public static function getPurchaseHTML(): string
    {
        return View::getSmarty()->fetch('gateway/stripe.tpl');
    }
}
