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
use App\Utils\ClassHelper;

class Payment
{
    // public static function getClient()
    // {
    //     $method = $_ENV['payment_system'];
    //     switch ($method) {
    //         case ('vmqpay'):
    //             return new Vmqpay();
    //         case ('paymentwall'):
    //             return new PaymentWall();
    //         case ('f2fpay'):
    //             return new AopF2F();
    //         case ('payjs'):
    //             return new PAYJS();
    //         case ('theadpay'):
    //             return new THeadPay();
    //         case ('coinpay'):
    //             return new CoinPay();
    //         default:
    //             return null;
    //     }
    // }

    static function getPaymentsEnabled() {
        $payments = array();

        $helper = new ClassHelper();
        $class_list = $helper->getClassesByNamespace("\\App\\Services\\Gateway\\");

        foreach ($class_list as $clazz) {
            if (get_parent_class($clazz) == "App\\Services\\Gateway\\AbstractPayment") {
                if ($clazz::_enable()) {
                    $payments[] = $clazz;
                }
            }
        }

        return $payments;
    }

    static function getPaymentMap() {
        $result = array();

        foreach (self::getPaymentsEnabled() as $payment) {
            $result[$payment::_name()] = $payment;
        }

        return $result;
    }

    static function getPaymentByName($name) {
        $all = self::getPaymentMap();

        return $all[$name];
    }

    public static function notify($request, $response, $args)
    {
        $payment = self::getPaymentByName($args['type']);

        if ($payment != null) {
            $instance = new $payment();
            return $instance->notify($request, $response, $args);
        }

        return $response->withStatus(404);
    }

    public static function returnHTML($request, $response, $args)
    {
        $payment = self::getPaymentByName($args['type']);

        if ($payment != null) {
            $instance = new $payment();
            return $instance->getReturnHTML($request, $response, $args);
        }

        return '';
    }

    public static function getStatus($request, $response, $args)
    {
        $payment = self::getPaymentByName($args['type']);

        if ($payment != null) {
            $instance = new $payment();
            return $instance->getStatus($request, $response, $args);
        }

        return $response->withStatus(404);
    }

    public static function purchase($request, $response, $args)
    {
        $payment = self::getPaymentByName($args['type']);

        if ($payment != null) {
            $instance = new $payment();
            return $instance->purchase($request, $response, $args);
        }

        return $response->withStatus(404);
    }
}
