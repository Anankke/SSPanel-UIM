<?php

namespace App\Utils;

class Spay_submit
{
    protected $alipay_gateway_new = 'http://www.dayyun.com/spaymq.php/Napi/api/?';

    public function __construct($alipay_config)
    {
        $this->alipay_config = $alipay_config;
    }
    public static function Spay_submit($alipay_config)
    {
        $this->__construct($alipay_config);
    }
    
    /**
     * 生成签名结果
     * @param $para_sort 已排序要签名的数组
     * return 签名结果字符串
     */
    public function buildRequestMysign($para_sort)
    {
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = Spay_tool::createLinkstring($para_sort);
        return Spay_tool::md5Sign($prestr, $this->alipay_config['key']);
    }

    /**
     * 生成要请求给支付宝的参数数组
     * @param $para_temp 请求前的参数数组
     * @return 要请求的参数数组
     */
    public function buildRequestPara($para_temp)
    {
        //除去待签名参数数组中的空值和签名参数
        $para_filter = Spay_tool::paraFilter($para_temp);

        //对待签名参数数组排序
        $para_sort = Spay_tool::argSort($para_filter);

        //生成签名结果
        $mysign = $this->buildRequestMysign($para_sort);
        
        //签名结果与签名方式加入请求提交参数组中
        $para_sort['sign'] = $mysign;
        $para_sort['sign_type'] = "MD5";
        
        return $para_sort;
    }

    /**
     * 生成要请求给支付宝的参数数组
     * @param $para_temp 请求前的参数数组
     * @return 要请求的参数数组字符串
     */
    public function buildRequestParaToString($para_temp)
    {
        //待请求参数数组
        $para = $this->buildRequestPara($para_temp);
        
        //把参数组中所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
        $request_data = Spay_tool::createLinkstringUrlencode($para);
        
        return $request_data;
    }
    
    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @param $method 提交方式。两个值可选：post、get
     * @param $button_name 确认按钮显示文字
     * @return 提交表单HTML文本
     */
    public function buildRequestForm($para_temp)
    {
        $para = $this->buildRequestPara($para_temp);
        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$this->alipay_gateway_new."_input_charset=UTF-8' method='get'>";
        while (list($key, $val) = each($para)) {
            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }
        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml."<input type='submit' value='正在跳转支付中'></form>";
        $sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";
        return $sHtml;
    }
}
