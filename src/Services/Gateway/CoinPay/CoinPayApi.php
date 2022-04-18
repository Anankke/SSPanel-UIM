<?php

declare(strict_types=1);

namespace App\Services\Gateway\CoinPay;

class CoinPayApi
{
    /**
     * 统一下单，CoinPayUnifiedOrder、total_amount、必填
     *
     * @param CoinPayConfigInterface $config "配置对象"
     *
     * @throws CoinPayException
     */
    public static function unifiedOrder(CoinPayConfigInterface $config, CoinPayUnifiedOrder $inputObj, int $timeOut = 6): string
    {
        if (! $inputObj->IsSubjectSet()) {
            throw new CoinPayException('缺少统一支付接口必填参数subject！');
        }
        if (! $inputObj->IsTimestampSet()) {
            throw new CoinPayException('缺少统一支付接口必填参数timestamp！');
        }
        if (! $inputObj->IsOut_trade_noSet()) {
            throw new CoinPayException('缺少统一支付接口必填参数out_trade_no！');
        }
        if (! $inputObj->IsTotal_amountSet()) {
            throw new CoinPayException('缺少统一支付接口必填参数total_amount！');
        }

        //异步通知url未设置，则使用配置文件中的url
        if (! $inputObj->IsNotify_urlSet() && $config->GetNotifyUrl() !== '') {
            $inputObj->SetNotify_url($config->GetNotifyUrl());
        }
        if (! $inputObj->IsReturn_urlSet() && $config->GetReturnUrl() !== '') {
            $inputObj->SetReturn_url($config->GetReturnUrl());
        }
        if (! $inputObj->IsAttachSet() && $config->GetAttach() !== '') {
            $inputObj->SetAttach($config->GetAttach());
        }
        if (! $inputObj->IsBodySet() && $config->GetBody() !== '') {
            $inputObj->SetBody($config->GetBody());
        }
        if (! $inputObj->IsTransCurrencySet() && $config->GetTransCurrency() !== '') {
            $inputObj->SetTransCurrency($config->GetTransCurrency());
        }

        // 设置AppID于随机字符串
        $inputObj->SetAppid($config->GetAppId());
        $inputObj->SetNonce_str(self::getNonceStr());

        //签名
        $inputObj->SetSign($config->GetSecret());
        return http_build_query($inputObj->ReturnArray());
    }

    /**
     * 返回随机字符串
     */
    public static function getNonceStr(int $length = 32): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
}
