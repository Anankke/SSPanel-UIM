<?php

namespace App\Services;

use App\Services\Gateway\{
    AopF2F,
    Vmqpay,
    PaymentWall,
    PAYJS,
    THeadPay,
    CoinPay
};

class Payment
{
    public static function getClient()
    {
        $method = $_ENV['payment_system'];
        switch ($method) {
            case ('vmqpay'):
                return new Vmqpay();
            case ('paymentwall'):
                return new PaymentWall();
            case ('f2fpay'):
                return new AopF2F();
            case ('payjs'):
                return new PAYJS($_ENV['payjs_key']);
            case ('theadpay'):
                return new THeadPay();
            case ('coinpay'):
                return new CoinPay(Config::get('coinpay_secret'), Config::get('coinpay_appid'));
            default:
                return null;
        }
    }

    public static function notify($request, $response, $args)
    {
        return self::getClient()->notify($request, $response, $args);
    }

    public static function returnHTML($request, $response, $args)
    {
        return self::getClient()->getReturnHTML($request, $response, $args);
    }

    public static function purchaseHTML()
    {
        if (self::getClient() != null) {
            return self::getClient()->getPurchaseHTML();
        }

        return '';
    }

    public static function getStatus($request, $response, $args)
    {
        return self::getClient()->getStatus($request, $response, $args);
    }

    public static function purchase($request, $response, $args)
    {
        return self::getClient()->purchase($request, $response, $args);
    }
}
