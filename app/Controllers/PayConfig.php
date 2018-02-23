<?php

namespace App\Controllers;

class PayConfig{

    public $pay_config;

    public function init()
    {
        $this->pay_config = [
            "secret" => "9A30EEBF727DE9A98B6AF916C2BCCA75",
            "accesskey" => "C390D630A2B48094854048740A3900D9",
            "notify_url" => "/yft/notify",
            "return_url" => "/yft/notify",
            "type" => "aliPay"
        ];
    }
}