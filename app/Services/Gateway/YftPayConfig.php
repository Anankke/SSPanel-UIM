<?php

namespace App\Services\Gateway;

class YftPayConfig{

    public $pay_config;

    public function init()
    {
        $this->pay_config = [
            "notify_url" => "/yft/notify",
            "return_url" => "/user/code",
            "type" => "aliPay"
        ];
    }
}