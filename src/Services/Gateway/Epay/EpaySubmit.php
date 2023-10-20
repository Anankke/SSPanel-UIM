<?php

declare(strict_types=1);

namespace App\Services\Gateway\Epay;

final class EpaySubmit
{
    private array $epay_config;
    private string $epay_gateway;

    public function __construct($epay_config)
    {
        $this->epay_config = $epay_config;
        $this->epay_gateway = $this->epay_config['apiurl'] . 'submit.php?';
    }

    public function buildRequestMysign($para_sort): string
    {
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = EpayTool::createLinkstring($para_sort);

        return EpayTool::sign($prestr, $this->epay_config['key']);
    }

    public function buildRequestPara($para_temp)
    {
        //除去待签名参数数组中的空值和签名参数
        $para_filter = EpayTool::paraFilter($para_temp);
        //对待签名参数数组排序
        $para_sort = EpayTool::argSort($para_filter);
        //生成签名结果
        $mysign = $this->buildRequestMysign($para_sort);
        //签名结果与签名方式加入请求提交参数组中
        $para_sort['sign'] = $mysign;
        $para_sort['sign_type'] = strtoupper(trim($this->epay_config['sign_type']));

        return $para_sort;
    }

    public function buildRequestForm($para_temp, $method = 'POST', $button_name = '正在跳转'): string
    {
        //待请求参数数组
        $para = $this->buildRequestPara($para_temp);
        $html = "<form id='alipaysubmit' name='alipaysubmit' action='".
            $this->epay_gateway . "' method='" . $method . "'>";

        foreach ($para as $key => $val) {
            $html .= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }
        //submit按钮控件请不要含有name属性
        $html .= "<input type='submit' value='".$button_name."'></form>";
        $html .= "<script>document.forms['alipaysubmit'].submit();</script>";

        return $html;
    }
}
