<?php
namespace App\Services;

use App\Services\Gateway\AopF2F;
use App\Services\Gateway\Universal;

class Payment
{
    public static function create($method, $order_no, $amount)
    {
        switch ($method) {
            case 'alipay_f2f':
                return AopF2F::createOrder($amount, $order_no);
            case 'universal':
                return Universal::createOrder($amount, $order_no);
        }
    }

    public static function notify($request, $response, $args)
    {
        $method = $args['type'];
        switch ($method) {
            case 'alipay_f2f':
                return AopF2F::notify($request, $response, $args);
            case 'universal':
                return Universal::notify($request, $response, $args);
        }
    }
}
