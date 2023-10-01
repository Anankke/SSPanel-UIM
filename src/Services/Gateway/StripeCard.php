<?php

declare(strict_types=1);

namespace App\Services\Gateway;

use App\Models\Paylist;
use App\Models\Setting;
use App\Services\Auth;
use App\Services\Exchange;
use App\Services\View;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use RedisException;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\StripeClient;

final class StripeCard extends AbstractPayment
{
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

    /**
     * @throws GuzzleException
     * @throws RedisException
     */
    public function purchase(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user = Auth::getUser();
        $configs = Setting::getClass('billing');
        $price = $request->getParam('price');
        $trade_no = self::generateGuid();

        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->total = $price;
        $pl->tradeno = $trade_no;
        $pl->save();

        $params = [
            'trade_no' => $trade_no,
            'sign' => hash('sha256', $trade_no . ':' . $configs['stripe_webhook_key']),
        ];

        $exchange_amount = Exchange::exchange($price, 'CNY', $configs['stripe_currency']);

        Stripe::setApiKey($configs['stripe_sk']);
        $session = null;

        try {
            $session = Session::create([
                'customer_email' => $user->email,
                'line_items' => [[
                    'price_data' => [
                        'currency' => $configs['stripe_currency'],
                        'product_data' => [
                            'name' => 'Account Recharge',
                        ],
                        'unit_amount' => (int) $exchange_amount,
                    ],
                    'quantity' => 1,
                ],
                ],
                'mode' => 'payment',
                'success_url' => self::getUserReturnUrl() .
                    '?session_id={CHECKOUT_SESSION_ID}&' . http_build_query($params),
                'cancel_url' => $_ENV['baseUrl'] . '/user/invoice',
            ]);
        } catch (ApiErrorException $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage(),
            ]);
        }

        return $response->withRedirect($session->url);
    }

    public function notify($request, $response, $args): ResponseInterface
    {
        return $response->write('ok');
    }

    /**
     * @throws Exception
     */
    public static function getPurchaseHTML(): string
    {
        return View::getSmarty()->fetch('gateway/stripe.tpl');
    }

    public function getReturnHTML($request, $response, $args): ResponseInterface
    {
        $sign = $request->getParam('sign');
        $trade_no = $request->getParam('trade_no');
        $session_id = $request->getParam('session_id');

        $correct_sign = hash('sha256', $trade_no . ':' . Setting::obtain('stripe_webhook_key'));

        if ($correct_sign !== $sign) {
            return $response->withJson([
                'ret' => 0,
                'msg' => 'Sign error',
            ]);
        }

        $stripe = new StripeClient(Setting::obtain('stripe_sk'));
        $session = null;

        try {
            $session = $stripe->checkout->sessions->retrieve($session_id, []);
        } catch (ApiErrorException $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage(),
            ]);
        }

        if ($session->payment_status === 'paid') {
            $this->postPayment($trade_no);
        }

        return $response->withRedirect($_ENV['baseUrl'] . '/user/invoice');
    }
}
