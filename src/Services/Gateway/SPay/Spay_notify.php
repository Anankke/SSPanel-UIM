<?php

namespace App\Services\Gateway\SPay;

class Spay_notify
{

    public function __construct($alipay_config)
    {
        $this->alipay_config = $alipay_config;
    }

    public static function Spay_notify($alipay_config)
    {
        (new Spay_notify())->__construct($alipay_config);
    }

    /**
     * 针对notify_url验证消息是否是支付宝发出的合法消息
     * @return 验证结果|bool
     */
    public function verifyNotify()
    {
        if (empty($_POST)) {//判断POST来的数组是否为空
            return false;
        }

//生成签名结果
        $isSign = $this->getSignVeryfy($_POST, $_POST['sign']);


        //写日志记录
        //if ($isSign) {
        //  $isSignStr = 'true';
        //}
        //else {
        //  $isSignStr = 'false';
        //}
        //$log_text = "responseTxt=".$responseTxt."\n notify_url_log:isSign=".$isSignStr.",";
        //$log_text = $log_text.Spay_tool::createLinkstring($_POST);
        //Spay_tool::logResult($log_text);

        //验证
        //$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
        //isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
        if ($isSign) {
            return true;
        }

        return false;
    }

    /**
     * 针对return_url验证消息是否是支付宝发出的合法消息
     * @return 验证结果|bool
     */
    public function verifyReturn()
    {
        if (empty($_GET)) {//判断POST来的数组是否为空
            return false;
        }

//生成签名结果
        $isSign = $this->getSignVeryfy($_GET, $_GET['sign']);
        //获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）

        //写日志记录
        //if ($isSign) {
        //  $isSignStr = 'true';
        //}
        //else {
        //  $isSignStr = 'false';
        //}
        //$log_text = "responseTxt=".$responseTxt."\n return_url_log:isSign=".$isSignStr.",";
        //$log_text = $log_text.Spay_tool::createLinkstring($_GET);
        //Spay_tool::logResult($log_text);

        //验证
        //$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
        //isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
        if ($isSign) {
            return true;
        }

        return false;
    }

    /**
     * 获取返回时的签名验证结果
     * @param $para_temp 通知返回来的参数数组
     * @param $sign 返回的签名结果
     * @return 签名验证结果|bool
     */
    public function getSignVeryfy($para_temp, $sign)
    {
        //除去待签名参数数组中的空值和签名参数
        $para_filter = Spay_tool::paraFilter($para_temp);

        //对待签名参数数组排序
        $para_sort = Spay_tool::argSort($para_filter);

        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = Spay_tool::createLinkstring($para_sort);

        $isSgin = false;

        return Spay_tool::md5Verify($prestr, $sign, $this->alipay_config['key']);
    }

    /**
     * 获取远程服务器ATN结果,验证返回URL
     * @param $notify_id 通知校验ID
     * @return 服务器ATN结果
     * 验证结果集：
     * true 返回正确信息
     */
    public function getResponse($notify_id)
    {
        $veryfy_url = $this->http_verify_url;
        $veryfy_url = $veryfy_url . 'notify_id=' . $notify_id;
        return Spay_tool::getHttpResponseGET($veryfy_url);
    }
}
