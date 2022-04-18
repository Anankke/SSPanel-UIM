<?php

declare(strict_types=1);

namespace App\Services\Gateway\CoinPay;

abstract class CoinPayConfigInterface
{
    // 系统加密key
    abstract public function GetSecret(): void;

    // 支付回调url
    abstract public function GetNotifyUrl(): void;

    // 同步返回url
    abstract public function GetReturnUrl(): void;

    // 设置应用AppID
    abstract public function GetAppId(): void;

    //附加数据,按原样返回,不填写默认为空.
    abstract public function GetAttach(): void;

    // body数据.不填写默认为空
    abstract public function GetBody(): void;

    // 货币代号,不填写默认为CNY  可选值为CNY USD
    abstract public function GetTransCurrency(): void;

    // 表单提交字符集编码
    abstract public function GetPostCharset(): void;
}
