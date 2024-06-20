<?php

declare(strict_types=1);

namespace App\Services\Gateway\Epay;

use function is_null;

final class EpayNotify
{
    private array $epay_config;

    public function __construct($epay_config)
    {
        $this->epay_config = $epay_config;
    }

    public function verifyNotify(): bool
    {
        if (is_null($_GET)) {//判断POST来的数组是否为空
            return false;
        }

        if ($this->getSignVeryfy($_GET, $_GET['sign'])) {
            return true;
        }

        return false;
    }

    public function getSignVeryfy($para_temp, $sign): bool
    {
        //除去待签名参数数组中的空值和签名参数
        $para_filter = EpayTool::paraFilter($para_temp);
        //对待签名参数数组排序
        $para_sort = EpayTool::argSort($para_filter);
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = EpayTool::createLinkstring($para_sort);

        return EpayTool::verify($prestr, $sign, $this->epay_config['key']);
    }
}
