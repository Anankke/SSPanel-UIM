<?php
namespace App\Services;

use App\Services\Gateway\AopF2F;
use App\Services\Gateway\Epay;
use App\Services\Gateway\Universal;
use App\Services\Gateway\VmqAlipay;
use App\Services\Gateway\VmqWechat;

class Payment
{
    public static function create($user_id, $method, $order_no, $amount)
    {
        switch ($method) {
            case 'alipay_f2f':
                return AopF2F::createOrder($amount, $order_no, $user_id);
            case 'universal':
                return Universal::createOrder($amount, $order_no, $user_id);
            case 'epay':
                return Epay::createOrder($amount, $order_no, $user_id);
            case 'vmq_alipay':
                return VmqAlipay::createOrder($amount, $order_no, $user_id);
            case 'vmq_wechat':
                return VmqWechat::createOrder($amount, $order_no, $user_id);
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
            case 'epay':
                return Epay::notify($request, $response, $args);
            case 'vmq_alipay':
                return VmqAlipay::notify($request, $response, $args);
            case 'vmq_wechat':
                return VmqWechat::notify($request, $response, $args);
        }
    }
}
