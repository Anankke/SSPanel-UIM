<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/10
 * Time: 17:05
 */

namespace App\Services\Gateway;

class YftPayConfig
{
    public $pay_config;
    public function init()
    {
        $this->pay_config = [
            "return_url" => "/user/code",
            "type" => "aliPay"
        ];
    }
}
