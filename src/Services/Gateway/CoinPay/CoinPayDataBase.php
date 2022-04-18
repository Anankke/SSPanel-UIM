<?php

declare(strict_types=1);

namespace App\Services\Gateway\CoinPay;

class CoinPayDataBase
{
    protected $values = [];

    /**
     * 设置分配的APPID
     */
    public function SetAppid(string $value): void
    {
        $this->values['app_id'] = $value;
    }

    /**
     * 获取APPID
     */
    public function GetAppid(): 值
    {
        return $this->values['app_id'];
    }

    /**
     * 判断应用ID是否存在
     *
     * @return true 或 false
     */
    public function IsAppidSet(): bool
    {
        return array_key_exists('app_id', $this->values);
    }

    /**
     * 设置签名，详见签名生成算法
     *
     * @param $secret
     */
    public function SetSign($secret): void
    {
        ksort($this->values);
        reset($this->values);
        $sign_param = implode('&', $this->values);
        $signature = hash_hmac('sha256', $sign_param, $secret, true);
        $this->values['sign'] = base64_encode($signature);
    }

    /**
     * 获取签名，详见签名生成算法的值
     */
    public function GetSign(): 值
    {
        return $this->values['sign'];
    }

    /**
     * 判断签名，详见签名生成算法是否存在
     *
     * @return true 或 false
     */
    public function IsSignSet(): bool
    {
        return array_key_exists('sign', $this->values);
    }

    /**
     * @return array
     */
    public function ReturnArray(): array
    {
        return $this->values;
    }
}
