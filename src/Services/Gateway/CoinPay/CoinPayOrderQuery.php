<?php

declare(strict_types=1);

namespace App\Services\Gateway\CoinPay;

final class CoinPayOrderQuery extends CoinPayDataBase
{
    /**
     * 设置票据ID
     */
    public function setInvoiceId(string $value): void
    {
        $this->values['invoice_id'] = $value;
    }
    /**
     * 获取票据ID
     */
    public function getInvoiceId(): 值
    {
        return $this->values['invoice_id'];
    }
    /**
     * 判断票据ID是否存在
     *
     * @return true 或 false
     */
    public function isInvoiceIdSet(): bool
    {
        return array_key_exists('invoice_id', $this->values);
    }

    /**
     * 设置商户系统内部的订单号，当没提供transaction_id时需要传这个。
     */
    public function setOutTradeNo(string $value): void
    {
        $this->values['out_trade_no'] = $value;
    }
    /**
     * 获取商户系统内部的订单号，当没提供transaction_id时需要传这个。的值
     */
    public function getOutTradeNo(): 值
    {
        return $this->values['out_trade_no'];
    }
    /**
     * 判断商户系统内部的订单号，当没提供transaction_id时需要传这个。是否存在
     *
     * @return true 或 false
     */
    public function isOutTradeNoSet(): bool
    {
        return array_key_exists('out_trade_no', $this->values);
    }

    /**
     * 设置随机字符串，不长于32位。推荐随机数生成算法
     */
    public function setNonceStr(string $value): void
    {
        $this->values['nonce_str'] = $value;
    }
    /**
     * 获取随机字符串，不长于32位。推荐随机数生成算法的值
     */
    public function getNonceStr(): 值
    {
        return $this->values['nonce_str'];
    }
    /**
     * 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
     *
     * @return true 或 false
     */
    public function isNonceStrSet(): bool
    {
        return array_key_exists('nonce_str', $this->values);
    }
}
