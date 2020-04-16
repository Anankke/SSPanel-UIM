<?php

/**
 * Created by PhpStorm.
 * User: tonyzou
 * Date: 2018/9/24
 * Time: 下午7:07
 */

namespace App\Services;

use App\Services\Gateway\{
    AopF2F,
    Codepay,
    PaymentWall,
    ChenPay,
    SPay,
    PAYJS,
    YftPay,
    BitPayX,
    TomatoPay,
    IDtPay
};

class Payment
{
    public static function getClient()
    {
        $method = $_ENV['payment_system'];
        switch ($method) {
            case ('codepay'):
                return new Codepay();
            case ('paymentwall'):
                return new PaymentWall();
            case ('spay'):
                return new SPay();
            case ('f2fpay'):
                return new AopF2F();
            case ('chenAlipay'):
                return new ChenPay();
            case ('payjs'):
                return new PAYJS($_ENV['payjs_key']);
            case ('yftpay'):
                return new YftPay();
            case ('bitpayx'):
                return new BitPayX($_ENV['bitpay_secret']);
            case ("tomatopay"):
                return new TomatoPay();
            case ("idtpay"):
                return new IDtPay();
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
