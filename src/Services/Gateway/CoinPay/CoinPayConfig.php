<?php

declare(strict_types=1);

namespace App\Services\Gateway\CoinPay;

use App\Models\Setting;

final class CoinPayConfig extends CoinPayConfigInterface
{
    /**
     * 系统secret加密字符串
     * 【注意:请勿泄露该key,如果不慎泄露请登录后台进行重置该key】
     */
    public function getSecret(): string
    {
        return Setting::obtain('coinpay_secret');
    }

    /**
     * 设置当前应用AppID
     */
    public function getAppId(): string
    {
        return Setting::obtain('coinpay_appid');
    }

    /**
     * 设置默认POST回调url
     */
    public function getNotifyUrl(): string
    {
        return Setting::obtain('coinpay_notify');
    }

    /**
     * 设置同步返回url
     */
    public function getReturnUrl(): string
    {
        return Setting::obtain('coinpay_return');
    }

    /**
     * 设置当前提交参数字符编码 默认UTF-8
     */
    public function getPostCharset(): string
    {
        return 'UTF-8';
    }

    public function getAttach(): string
    {
        return '';
    }

    public function getTransCurrency(): string
    {
        return '';
    }

    public function getBody(): string
    {
        return '';
    }
}
