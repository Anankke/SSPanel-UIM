<?php

namespace App\Services\Gateway\CoinPay;

class CoinPayDataBase
{
    protected $values = array();

    /**
     * 设置分配的APPID
     * @param string $value
     **/
    public function SetAppid($value)
    {
        $this->values['app_id'] = $value;
    }

    /**
     * 获取APPID
     * @return 值
     **/
    public function GetAppid()
    {
        return $this->values['app_id'];
    }

    /**
     * 判断应用ID是否存在
     * @return true 或 false
     **/
    public function IsAppidSet()
    {
        return array_key_exists('app_id', $this->values);
    }

    /**
     * 设置签名，详见签名生成算法
     * @param $secret
     */
    public function SetSign($secret)
    {
        ksort($this->values);
        reset($this->values);
        $sign_param = implode('&', $this->values);
        $signature = hash_hmac('sha256', $sign_param, $secret, true);
        $this->values['sign'] = base64_encode($signature);
    }

    /**
     * 获取签名，详见签名生成算法的值
     * @return 值
     **/
    public function GetSign()
    {
        return $this->values['sign'];
    }

    /**
     * 判断签名，详见签名生成算法是否存在
     * @return true 或 false
     **/
    public function IsSignSet()
    {
        return array_key_exists('sign', $this->values);
    }

    /**
     * @return array
     */
    public function ReturnArray()
    {
        return $this->values;
    }
}
