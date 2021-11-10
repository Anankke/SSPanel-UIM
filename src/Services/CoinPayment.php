<?php

namespace App\Services;

use App\Models\Setting;
use App\Services\Gateway\CoinPay;

class CoinPayment
{
    public static function getClient()
    {
        $configs = Setting::getClass('coinpay');
        return new CoinPay($configs['coinpay_secret'], $configs['coinpay_appid']);
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
        $coinpay_secret = Setting::obtain('coinpay_secret');
        if (self::getClient() != null && $coinpay_secret != '') {
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
