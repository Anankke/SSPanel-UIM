<?php

declare(strict_types=1);

namespace App\Services;

use App\Utils\ClassHelper;

final class Payment
{
    public static function getAllPaymentMap(): array
    {
        $payments = [];

        $helper = new ClassHelper();
        $class_list = $helper->getClassesByNamespace('\\App\\Services\\Gateway\\');

        foreach ($class_list as $clazz) {
            if (get_parent_class($clazz) === 'App\\Services\\Gateway\\AbstractPayment') {
                $payments[] = $clazz;
            }
        }

        return $payments;
    }

    public static function getPaymentsEnabled(): array
    {
        return array_values(array_filter(Payment::getAllPaymentMap(), static function ($payment) {
            return $payment::_enable();
        }));
    }

    public static function getPaymentMap(): array
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

    public static function notify($request, $response, $args)
    {
        $payment = self::getPaymentByName($args['type']);

        if ($payment !== null) {
            $instance = new $payment();
            return $instance->notify($request, $response, $args);
        }

        return $response->withStatus(404);
    }

    public static function returnHTML($request, $response, $args)
    {
        $payment = self::getPaymentByName($args['type']);

        if ($payment !== null) {
            $instance = new $payment();
            return $instance->getReturnHTML($request, $response, $args);
        }

        return '';
    }

    public static function getStatus($request, $response, $args)
    {
        $payment = self::getPaymentByName($args['type']);

        if ($payment !== null) {
            $instance = new $payment();
            return $instance->getStatus($request, $response, $args);
        }

        return $response->withStatus(404);
    }

    public static function purchase($request, $response, $args)
    {
        $payment = self::getPaymentByName($args['type']);

        if ($payment !== null) {
            $instance = new $payment();
            return $instance->purchase($request, $response, $args);
        }

        return $response->withStatus(404);
    }
}
