<?php

namespace App\Services\Gateway\CoinPay;

abstract class CoinPayConfigInterface
{
    // 系统加密key
    public abstract function GetSecret();

    // 支付回调url
    public abstract function GetNotifyUrl();

    // 同步返回url
    public abstract function GetReturnUrl();

    // 设置应用AppID
    public abstract function GetAppId();

    //附加数据,按原样返回,不填写默认为空.
    public abstract function GetAttach();

    // body数据.不填写默认为空
    public abstract function GetBody();

    // 货币代号,不填写默认为CNY  可选值为CNY USD
    public abstract function GetTransCurrency();

    // 表单提交字符集编码
    public abstract function GetPostCharset();
}
