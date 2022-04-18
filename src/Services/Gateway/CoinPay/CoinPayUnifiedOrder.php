<?php

declare(strict_types=1);

namespace App\Services\Gateway\CoinPay;

/**
 * 统一下单输入对象
 * Class CoinPayUnifiedOrder
 *
 * @package sdk
 */
class CoinPayUnifiedOrder extends CoinPayDataBase
{
    /**
     * 设置
     */
    public function SetSubject(string $value): void
    {
        $this->values['subject'] = $value;
    }

    /**
     * 获取
     *
     * @return string 值
     */
    public function GetSubject(): string
    {
        return $this->values['subject'];
    }

    /**
     * 判断
     *
     * @return true 或 false
     */
    public function IsSubjectSet(): bool
    {
        return array_key_exists('subject', $this->values);
    }

    /**
     * 设置
     */
    public function SetBody(string $value): void
    {
        $this->values['body'] = $value;
    }

    /**
     * 获取
     *
     * @return string 值
     */
    public function GetBody(): string
    {
        return $this->values['body'];
    }

    /**
     * 判断
     *
     * @return true 或 false
     */
    public function IsBodySet(): bool
    {
        return array_key_exists('body', $this->values);
    }

    /**
     * 设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
     */
    public function SetOut_trade_no(string $value): void
    {
        $this->values['out_trade_no'] = $value;
    }

    /**
     * 获取商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号的值
     *
     * @return string 值
     */
    public function GetOut_trade_no(): string
    {
        return $this->values['out_trade_no'];
    }

    /**
     * 判断商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号是否存在
     *
     * @return true 或 false
     */
    public function IsOut_trade_noSet(): bool
    {
        return array_key_exists('out_trade_no', $this->values);
    }

    /**
     * 设置订单总金额，只能为整数，详见支付金额
     */
    public function SetTotal_amount(string $value): void
    {
        $this->values['total_amount'] = $value;
    }

    /**
     * 获取订单总金额，只能为整数，详见支付金额的值
     *
     * @return string 值
     */
    public function GetTotal_amount(): string
    {
        return $this->values['total_amount'];
    }

    /**
     * 判断订单总金额，只能为整数，详见支付金额是否存在
     *
     * @return true 或 false
     */
    public function IsTotal_amountSet(): bool
    {
        return array_key_exists('total_amount', $this->values);
    }

    /**
     * 设置
     */
    public function SetTimestamp(string $value): void
    {
        $this->values['timestamp'] = $value;
    }

    /**
     * 获取
     *
     * @return string 值
     */
    public function GetTimestamp(): string
    {
        return $this->values['timestamp'];
    }

    /**
     * 判断
     *
     * @return true 或 false
     */
    public function IsTimestampSet(): bool
    {
        return array_key_exists('timestamp', $this->values);
    }

    /**
     * 设置
     */
    public function SetNonce_str(string $value): void
    {
        $this->values['nonce_str'] = $value;
    }

    /**
     * 获取
     *
     * @return string 值
     */
    public function GetNonce_str(): string
    {
        return $this->values['nonce_str'];
    }

    /**
     * 判断
     *
     * @return true 或 false
     */
    public function IsNonce_strSet(): bool
    {
        return array_key_exists('nonce_str', $this->values);
    }

    /**
     * 设置接收coinpay支付异步通知回调地址
     */
    public function SetNotify_url(string $value): void
    {
        $this->values['notify_url'] = $value;
    }

    /**
     * 获取接收coinpay支付异步通知回调地址的值
     *
     * @return string 值
     */
    public function GetNotify_url(): string
    {
        return $this->values['notify_url'];
    }

    /**
     * 判断接收coinpay支付异步通知回调地址是否存在
     *
     * @return true 或 false
     */
    public function IsNotify_urlSet(): bool
    {
        return array_key_exists('notify_url', $this->values);
    }

    /**
     * 设置
     */
    public function SetReturn_url(string $value): void
    {
        $this->values['return_url'] = $value;
    }

    /**
     * 获取
     *
     * @return string 值
     */
    public function GetReturn_url(): string
    {
        return $this->values['return_url'];
    }

    /**
     * 判断
     *
     * @return true 或 false
     */
    public function IsReturn_urlSet(): bool
    {
        return array_key_exists('return_url', $this->values);
    }

    /**
     * 设置
     */
    public function SetAttach(string $value): void
    {
        $this->values['attach'] = $value;
    }

    /**
     * 获取
     *
     * @return string 值
     */
    public function GetAttach(): string
    {
        return $this->values['attach'];
    }

    /**
     * 判断
     *
     * @return true 或 false
     */
    public function IsAttachSet(): bool
    {
        return array_key_exists('attach', $this->values);
    }

    /**
     * 设置
     */
    public function SetTransCurrency(string $value): void
    {
        $this->values['trans_currency'] = $value;
    }

    /**
     * 获取
     *
     * @return string 值
     */
    public function GetTransCurrency(): string
    {
        return $this->values['trans_currency'];
    }

    /**
     * 判断
     *
     * @return true 或 false
     */
    public function IsTransCurrencySet(): bool
    {
        return array_key_exists('trans_currency', $this->values);
    }
}
