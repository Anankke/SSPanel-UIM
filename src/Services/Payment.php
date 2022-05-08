<?php
namespace App\Services;

use App\Utils\ClassHelper;

class Payment
{
    public static function getAllPaymentMap(): array
    {
        $payments = [];
        $helper = new ClassHelper();
        $class_list = $helper->getClassesByNamespace('\\App\\Services\\Gateway\\');

        foreach ($class_list as $clazz) {
            $payments[] = $clazz;
        }

        return $payments;
    }

    public static function getPaymentsEnabled()
    {
        return array_values(array_filter(Payment::getAllPaymentMap(), static function ($payment) {
            return $payment::_enable();
        }));
    }

    public static function getPaymentMap()
    {
        $result = [];
        foreach (self::getPaymentsEnabled() as $payment) {
            $result[$payment::_name()] = $payment;
        }
        return $result;
    }

    public static function getPaymentByName($name)
    {
        $all = self::getPaymentMap();
        return $all[$name];
    }

    public static function create($user_id, $method, $order_no, $amount)
    {
        $payment = self::getPaymentByName($method);

        if ($payment !== null) {
            $instance = new $payment();
            return $instance->createOrder($amount, $order_no, $user_id);
        }
    }

    public static function notify($request, $response, $args)
    {
        $payment = self::getPaymentByName($args['type']);

        if ($payment !== null) {
            $instance = new $payment();
            return $instance->notify($request, $response, $args);
        }
    }
}
