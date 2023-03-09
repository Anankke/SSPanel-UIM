<?php

declare(strict_types=1);

namespace App\Services\Gateway\Epay;

final class EpaySubmit
{
    private $alipay_config;
    private string $alipay_gateway_new;

    public function __construct($alipay_config)
    {
        $this->alipay_config = $alipay_config;
        $this->alipay_gateway_new = $this->alipay_config['apiurl'] . 'submit.php?';
    }

    public function buildRequestMysign($para_sort): string
    {
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = EpayTool::createLinkstring($para_sort);

        return EpayTool::md5Sign($prestr, $this->alipay_config['key']);
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
        $para_sort['sign_type'] = strtoupper(trim($this->alipay_config['sign_type']));

        return $para_sort;
    }

    public function buildRequestParaToString($para_temp): string
    {
        //待请求参数数组
        $para = $this->buildRequestPara($para_temp);

        //把参数组中所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
        return EpayTool::createLinkstringUrlencode($para);
    }

    public function buildRequestForm($para_temp, $method = 'POST', $button_name = '正在跳转'): string
    {
        //待请求参数数组
        $para = $this->buildRequestPara($para_temp);

        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$this->alipay_gateway_new."' method='".$method."'>";
        foreach ($para as $key => $val) {
            $sHtml .= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }

        //submit按钮控件请不要含有name属性
        $sHtml .= "<input type='submit' value='".$button_name."'></form>";

        $sHtml .= "<script>document.forms['alipaysubmit'].submit();</script>";

        return $sHtml;
    }
}
