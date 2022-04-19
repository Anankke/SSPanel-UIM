<?php

declare(strict_types=1);

namespace App\Services\Gateway\CoinPay;

final class CoinPayApi
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
        if (! $inputObj->isSubjectSet()) {
            throw new CoinPayException('缺少统一支付接口必填参数subject！');
        }
        if (! $inputObj->isTimestampSet()) {
            throw new CoinPayException('缺少统一支付接口必填参数timestamp！');
        }
        if (! $inputObj->isOutTradeNoSet()) {
            throw new CoinPayException('缺少统一支付接口必填参数out_trade_no！');
        }
        if (! $inputObj->isTotalAmountSet()) {
            throw new CoinPayException('缺少统一支付接口必填参数total_amount！');
        }

        //异步通知url未设置，则使用配置文件中的url
        if (! $inputObj->isNotifyUrlSet() && $config->getNotifyUrl() !== '') {
            $inputObj->setNotifyUrl($config->getNotifyUrl());
        }
        if (! $inputObj->isReturnUrlSet() && $config->getReturnUrl() !== '') {
            $inputObj->setReturnUrl($config->getReturnUrl());
        }
        if (! $inputObj->isAttachSet() && $config->getAttach() !== '') {
            $inputObj->setAttach($config->getAttach());
        }
        if (! $inputObj->isBodySet() && $config->getBody() !== '') {
            $inputObj->setBody($config->getBody());
        }
        if (! $inputObj->isTransCurrencySet() && $config->getTransCurrency() !== '') {
            $inputObj->setTransCurrency($config->getTransCurrency());
        }

        // 设置AppID于随机字符串
        $inputObj->setAppid($config->getAppId());
        $inputObj->setNonce_str(self::getNonceStr());

        //签名
        $inputObj->setSign($config->getSecret());
        return http_build_query($inputObj->returnArray());
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
