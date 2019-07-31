<?php
/**
 * BitPayment - 数字货币支付接口
 *
 * Date: 2019/5/24
 * Time: 11:08 AM PST
 */
namespace App\Services;

use App\Services\Gateway\BitPay;

class BitPayment
{
    public static function getClient()
    {
        return new BitPay(Config::get('bitpay_secret'));
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
        $bitpayConfig = Config::get('bitpay_secret');
        if (self::getClient() != null && $bitpayConfig != '') {
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
