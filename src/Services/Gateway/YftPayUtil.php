<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/10
 * Time: 17:21
 */

namespace App\Services\Gateway;


class YftPayUtil
{

    public function md5Verify($p1, $p2, $p3, $p4, $sign)
    {
        $preStr = $p1 . $p2 . $p3 . $p4 . "yft";
        $mySign = md5($preStr);
        if ($mySign == $sign) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     *
     */
    public function buildRequestForm($para_temp, $ss_order_no, $pay_config)
    {
        //待请求参数数组
        $para = YftPayUtil::buildRequestPara($para_temp);
        $sHtml = "<form id='paysubmit' name='paysubmit' action='https://payment.pi.do/pay/subOrder/zfb' accept-charset='utf-8' method='POST'>";
        foreach ($para as $key => $val) {
            $sHtml .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
        }
        if ($pay_config->pay_config["type"] == "aliPay") {
            $sHtml .= "<input type='hidden' name='subject' value='余额充值'/>";
        } else {
            $sHtml .= "<input type='hidden' name='trade_no' value='" . $ss_order_no . "'/>";
        }
        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml . "</form>";
        $sHtml = $sHtml . "<script>document.forms['paysubmit'].submit();</script>";
        return $sHtml;
    }

    /**
     * 生成要请求给易付的参数数组
     * @param $para_temp 请求前的参数数组
     * @return 要请求的参数数组
     */
    static function buildRequestPara($para_temp)
    {
        //除去待签名参数数组中的空值和签名参数
        $para_filter = YftPayUtil::paraFilter($para_temp);
        //生成签名结果
        $mysign = YftPayUtil::buildRequestMysign($para_filter);
        //签名结果与签名方式加入请求提交参数组中
        $para_filter['sign'] = $mysign;
        return $para_filter;
    }

    /**
     * 除去数组中的空值和签名参数
     * @param $para 签名参数组
     * return 去掉空值与签名参数后的新签名参数组
     */
    static function paraFilter($para)
    {
        $para_filter = array();
        foreach ($para as $key => $val) {
            if ($key == "sign" || $val == "") continue;
            else    $para_filter[$key] = $para[$key];
        }
        return $para_filter;
    }

    /**
     * 生成签名结果
     * @param $para_filter 要签名的数组
     * return 签名结果字符串
     */
    static function buildRequestMysign($para_filter)
    {
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = YftPayUtil::createLinkstring($para_filter);
        $mysign = MD5($prestr);
        return $mysign;
    }

    static function md5Sign($prestr)
    {
        return md5($prestr);
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    static function createLinkstring($para)
    {
        $arg = "";
        foreach ($para as $key => $val) {
            $arg .= $key . "=" . $val . "&";
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, strlen($arg) - 1);
        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }
        return $arg;
    }
}
