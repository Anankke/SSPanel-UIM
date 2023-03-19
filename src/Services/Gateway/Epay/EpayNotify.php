<?php

declare(strict_types=1);

namespace App\Services\Gateway\Epay;

use function is_null;

final class EpayNotify
{
    private $alipay_config;
    private $http_verify_url;

    public function __construct($alipay_config)
    {
        $this->alipay_config = $alipay_config;
        $this->http_verify_url = $this->alipay_config['apiurl'] . 'api.php?';
    }

    public function verifyNotify(): bool
    {
        if (is_null($_GET)) {//判断POST来的数组是否为空
            return false;
        }
        //生成签名结果
        $isSign = $this->getSignVeryfy($_GET, $_GET['sign']);
        //获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
        $responseTxt = 'true';
        //if (! empty($_POST["notify_id"])) {$responseTxt = $this->getResponse($_POST["notify_id"]);}

        //验证
        //$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
        //isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
        if (preg_match('/true$/i', $responseTxt) && $isSign) {
            return true;
        }
        return false;
    }

    public function verifyReturn(): bool
    {
        if (is_null($_GET)) {//判断POST来的数组是否为空
            return false;
        }
        //生成签名结果
        $isSign = $this->getSignVeryfy($_GET, $_GET['sign']);
        //获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
        $responseTxt = 'true';
        //if (! empty($_GET["notify_id"])) {$responseTxt = $this->getResponse($_GET["notify_id"]);}

        //验证
        //$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
        //isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
        if (preg_match('/true$/i', $responseTxt) && $isSign) {
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

        return EpayTool::md5Verify($prestr, $sign, $this->alipay_config['key']);
    }

    public function getResponse($notify_id): bool|string
    {
        $partner = trim($this->alipay_config['partner']);
        $veryfy_url = '';
        $veryfy_url = $this->http_verify_url;
        $veryfy_url .= 'partner=' . $partner . '&notify_id=' . $notify_id;
        return EpayTool::getHttpResponseGET($veryfy_url, $this->alipay_config['cacert']);
    }
}
