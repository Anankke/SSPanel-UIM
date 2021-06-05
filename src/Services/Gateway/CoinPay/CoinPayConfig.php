<?php

namespace App\Services\Gateway\CoinPay;

use App\Services\Config;

class CoinPayConfig extends CoinPayConfigInterface
{
    /**
     * 系统secret加密字符串
     * 【注意:请勿泄露该key,如果不慎泄露请登录后台进行重置该key】
     * @return string
     */
    public function GetSecret()
    {
        return Config::get('coinpay_secret');
    }

    /**
     * 设置当前应用AppID
     * @return string
     */
    public function GetAppId()
    {
        return Config::get('coinpay_appid');
    }


    /**
     * 设置默认POST回调url
     * @return string
     */
    public function GetNotifyUrl()
    {
        return Config::get('coinpay_notify');
    }

    /**
     * 设置同步返回url
     * @return string
     */
    public function GetReturnUrl()
    {
        return Config::get('coinpay_return');
    }

    /**
     * 设置当前提交参数字符编码 默认UTF-8
     * @return string
     */
    public function GetPostCharset()
    {
        return "UTF-8";
    }

    public function GetAttach()
    {
        // TODO: Implement GetAttach() method.
    }

    public function GetTransCurrency()
    {
        // TODO: Implement GetTransCurrency() method.
    }

    public function GetBody()
    {
        // TODO: Implement GetBody() method.
    }
}
