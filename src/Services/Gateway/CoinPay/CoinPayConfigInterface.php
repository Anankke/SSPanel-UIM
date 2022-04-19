<?php

declare(strict_types=1);

namespace App\Services\Gateway\CoinPay;

abstract class CoinPayConfigInterface
{
    // 系统加密key
    abstract public function getSecret(): string;

    // 支付回调url
    abstract public function getNotifyUrl(): string;

    // 同步返回url
    abstract public function getReturnUrl(): string;

    // 设置应用AppID
    abstract public function getAppId(): string;

    //附加数据,按原样返回,不填写默认为空.
    abstract public function getAttach(): string;

    // body数据.不填写默认为空
    abstract public function getBody(): string;

    // 货币代号,不填写默认为CNY  可选值为CNY USD
    abstract public function getTransCurrency(): string;

    // 表单提交字符集编码
    abstract public function getPostCharset(): string;
}
