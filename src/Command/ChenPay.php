<?php

namespace App\Command;

use App\Services\Gateway\ChenPay as GatewayChenPay;

class ChenPay extends Command
{
    public $description = ''
        . '├─=: php xcat ChenPay [选项]' . PHP_EOL
        . '│ ├─ wxpay                   - Wechat Pay' . PHP_EOL
        . '│ ├─ alipay                  - AliPay' . PHP_EOL;

    public function boot()
    {
        if (!isset($this->argv[2])) {
            echo $this->description;
        } else {
            switch ($this->argv[2]) {
                case ('alipay'):
                    return (new GatewayChenPay())->AliPayListen();
                case ('wxpay'):
                    return (new GatewayChenPay())->WxPayListen();
                default:
                    echo '方法不存在.' . PHP_EOL;
                    break;
            }
        }
    }
}
