<?php

/**
 * Created by PhpStorm.
 * User: xudong.ding
 * Date: 16/6/27
 * Time: 下午3:31
 */
class ContentBuilder
{
    //第三方应用授权令牌
    private $appAuthToken;

    //异步通知地址(仅扫码支付使用)
    private $notifyUrl;

    public function setAppAuthToken($appAuthToken)
    {
        $this->appAuthToken = $appAuthToken;
    }

    public function setNotifyUrl($notifyUrl)
    {
        $this->notifyUrl = $notifyUrl;
    }

    public function getAppAuthToken()
    {
        return $this->appAuthToken;
    }

    public function getNotifyUrl()
    {
        return $this->notifyUrl;
    }
}