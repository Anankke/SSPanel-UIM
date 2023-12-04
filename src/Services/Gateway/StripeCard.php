<?php

declare(strict_types=1);

namespace App\Services\Gateway;

use App\Models\Config;
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
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\StripeClient;
use voku\helper\AntiXSS;

final class StripeCard extends Base
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

    /**
     * @throws GuzzleException
     * @throws RedisException
     */
    public function purchase(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $price = $this->antiXss->xss_clean($request->getParam('price'));
        $invoice_id = $this->antiXss->xss_clean($request->getParam('invoice_id'));
        $trade_no = self::generateGuid();

        if ($price < Config::obtain('stripe_min_recharge') ||
            $price > Config::obtain('stripe_max_recharge')
        ) {
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
        $pl->tradeno = $trade_no;
        $pl->save();

        $exchange_amount = (new Exchange())->exchange($price, 'CNY', Config::obtain('stripe_currency'));

        Stripe::setApiKey(Config::obtain('stripe_sk'));
        $session = null;

        try {
            $session = Session::create([
                'customer_email' => $user->email,
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => Config::obtain('stripe_currency'),
                            'product_data' => [
                                'name' => 'Account Recharge',
                            ],
                            'unit_amount' => (int) $exchange_amount,
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'client_reference_id' => $trade_no,
                'success_url' => self::getUserReturnUrl() . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $_ENV['baseUrl'] . '/user/invoice',
            ]);
        } catch (ApiErrorException $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => 'Stripe API error',
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
        $session_id = $this->antiXss->xss_clean($request->getParam('session_id'));

        $stripe = new StripeClient(Config::obtain('stripe_sk'));
        $session = null;

        try {
            $session = $stripe->checkout->sessions->retrieve($session_id, []);
        } catch (ApiErrorException $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => 'Stripe API error',
            ]);
        }

        if ($session !== null && $session->payment_status === 'paid') {
            $this->postPayment($session->client_reference_id);
        }

        return $response->withRedirect($_ENV['baseUrl'] . '/user/invoice');
    }
}
