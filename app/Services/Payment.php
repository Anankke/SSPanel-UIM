<?php
/**
 * Created by PhpStorm.
 * User: tonyzou
 * Date: 2018/9/24
 * Time: 下午7:07
 */

namespace App\Services;

use App\Services\Config;
use App\Services\Gateway\{
    AopF2F, Codepay, DoiAMPay, PaymentWall, ChenPay, SPay, TrimePay, YftPay
};

class Payment
{
    public static function getClient(){
        $method = Config::get("payment_system");
        switch($method){
            case("codepay"):
                return new Codepay();
            case("doiampay"):
                return new DoiAMPay();
            case("paymentwall"):
                return new PaymentWall();
            case("spay"):
                return new SPay();
            case("f2fpay"):
                return new AopF2F();
            case("yftpay"):
                return new YftPay();
            case("chenAlipay"):
                return new ChenPay();
            case("trimepay"):
                return new TrimePay(Config::get('trimepay_secret'));
            default:
                return NULL;
        }
    }

    public static function notify($request, $response, $args){
        return self::getClient()->notify($request, $response, $args);
    }

    public static function returnHTML($request, $response, $args){
        return self::getClient()->getReturnHTML($request, $response, $args);
    }

	public static function purchaseHTML(){
		if (self::getClient() != NULL) {
			return self::getClient()->getPurchaseHTML();
		} else {
			return '';
		}
    }

    public static function getStatus($request, $response, $args){
        return self::getClient()->getStatus($request, $response, $args);
    }

    public static function purchase($request, $response, $args){
        return self::getClient()->purchase($request, $response, $args);
    }
}