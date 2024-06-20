<?php

declare(strict_types=1);

namespace App\Services\Gateway\Epay;

use App\Models\Config;
use function hash;
use function strlen;

final class EpayTool
{
    public static function sign($prestr, $key): string
    {
        $prestr .= $key;

        return hash(Config::obtain('epay_sign_type'), $prestr);
    }

    public static function verify($prestr, $sign, $key): bool
    {
        $prestr .= $key;
        $correct_sign = hash(Config::obtain('epay_sign_type'), $prestr);

        return $correct_sign === $sign;
    }

    public static function createLinkstring($para): string
    {
        $arg = '';

        foreach ($para as $key => $val) {
            $arg .= $key . '=' . $val . '&';
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, strlen($arg) - 1);
        //如果存在转义字符，那么去掉转义
        return stripslashes($arg);
    }

    public static function paraFilter($para): array
    {
        $para_filter = [];

        foreach ($para as $key => $val) {
            if ($key === 'sign' || $key === 'sign_type' || $val === '') {
                continue;
            }
            $para_filter[$key] = $val;
        }

        return $para_filter;
    }

    public static function argSort($para)
    {
        ksort($para);

        return $para;
    }
}
