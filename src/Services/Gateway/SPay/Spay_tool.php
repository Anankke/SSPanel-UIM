<?php

namespace App\Services\Gateway\SPay;

use App\Services\Config;

class Spay_tool
{
    /* *
     * 说明：
     * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
     * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
     */
    /**
     * 签名字符串
     * @param $prestr 需要签名的字符串
     * @param $key 私钥
     * return 签名结果
     */
    public static function md5Sign($prestr, $key)
    {
        $prestr .= $key;
        return md5($prestr);
    }

    /**
     * 验证签名
     * @param $prestr 需要签名的字符串
     * @param $sign 签名结果
     * @param $key 私钥
     * return 签名结果
     */
    public static function md5Verify($prestr, $sign, $key)
    {
        $prestr .= $key;
        $mysgin = md5($prestr);

        return $mysgin == $sign;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    public static function createLinkstring($para)
    {
        $arg = '';
        foreach ($para as $key => $val) {
            $arg .= $key . '=' . $val . '&';
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, -1);

        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }

        return $arg;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    public static function createLinkstringUrlencode($para)
    {
        $arg = '';
        foreach ($para as $key => $val) {
            $arg .= $key . '=' . urlencode($val) . '&';
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, count($arg) - 2);

        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }

        return $arg;
    }

    /**
     * 除去数组中的空值和签名参数
     * @param $para 签名参数组
     * return 去掉空值与签名参数后的新签名参数组
     */
    public static function paraFilter($para)
    {
        $para_filter = array();
        while (list($key, $val) = each($para)) {
            if ($key == 'sign' || $key == 'sign_type' || $val == '') {
                continue;
            }

            $para_filter[$key] = $para[$key];
        }
        return $para_filter;
    }

    /**
     * 对数组排序
     * @param $para 排序前的数组
     * return 排序后的数组
     */
    public static function argSort($para)
    {
        ksort($para);
        reset($para);
        return $para;
    }

    /**
     * 写日志，方便测试（看网站需求，也可以改成把记录存入数据库）
     * 注意：服务器需要开通fopen配置
     * @param $word 要写入日志里的文本内容 默认值：空值
     */
    public static function logResult($word = '')
    {
        $fp = fopen(BASE_PATH . '/storage/log.txt', 'ab');
        flock($fp, LOCK_EX);
        fwrite($fp, '执行日期：' . strftime('%Y%m%d%H%M%S', time()) . "\n" . $word . "\n");
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    /**
     * 远程获取数据，GET模式
     * 注意：
     * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
     * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
     * @param $url 指定URL完整路径地址
     * @param $cacert_url 指定当前工作目录绝对路径
     * return 远程输出的数据
     */
    public static function getHttpResponseGET($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 0); // 过滤HTTP头
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
        $responseText = curl_exec($curl);
        //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
        curl_close($curl);

        return $responseText;
    }

    /**
     * 实现多种字符编码方式
     * @param $input 需要编码的字符串
     * @param $_output_charset 输出的编码格式
     * @param $_input_charset 输入的编码格式
     * return 编码后的字符串
     */
    public static function charsetEncode($input, $_output_charset, $_input_charset)
    {
        $output = '';
        if (!isset($_output_charset)) {
            $_output_charset = $_input_charset;
        }
        if ($_input_charset == $_output_charset || $input == null) {
            $output = $input;
        } elseif (function_exists('mb_convert_encoding')) {
            $output = mb_convert_encoding($input, $_output_charset, $_input_charset);
        } elseif (function_exists('iconv')) {
            $output = iconv($_input_charset, $_output_charset, $input);
        } else {
            die('sorry, you have no libs support for charset change.');
        }
        return $output;
    }

    /**
     * 实现多种字符解码方式
     * @param $input 需要解码的字符串
     * @param $_output_charset 输出的解码格式
     * @param $_input_charset 输入的解码格式
     * return 解码后的字符串
     */
    public static function charsetDecode($input, $_input_charset, $_output_charset)
    {
        $output = '';
        if (!isset($_input_charset)) {
            $_input_charset = $_input_charset;
        }
        if ($_input_charset == $_output_charset || $input == null) {
            $output = $input;
        } elseif (function_exists('mb_convert_encoding')) {
            $output = mb_convert_encoding($input, $_output_charset, $_input_charset);
        } elseif (function_exists('iconv')) {
            $output = iconv($_input_charset, $_output_charset, $input);
        } else {
            die('sorry, you have no libs support for charset changes.');
        }
        return $output;
    }

    public static function getConfig()
    {
        $alipay_config = array();

        $alipay_config['partner'] = $_ENV['alipay_id'];

        //收款支付宝账号，以2088开头由16位纯数字组成的字符串，一般情况下收款账号就是签约账号
        $alipay_config['seller_id'] = $alipay_config['partner'];

        // MD5密钥，安全检验码，由数字和字母组成的32位字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm
        $alipay_config['key'] = $_ENV['alipay_key'];

        // 服务器异步通知页面路径  需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
        $alipay_config['notify_url'] = $_ENV['baseUrl'] . '/purchase/notify';

        // 页面跳转同步通知页面路径 需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问

        //此处请填 访问地址+"/user/code"
        $alipay_config['return_url'] = $_ENV['baseUrl'] . '/user/code';

        //签名方式
        $alipay_config['sign_type'] = strtoupper('MD5');

        //字符编码格式 目前支持 gbk 或 utf-8
        $alipay_config['input_charset'] = strtolower('utf-8');

        //ca证书路径地址，用于curl中ssl校验
        //请保证cacert.pem文件在当前文件夹目录中
        $alipay_config['cacert'] = '';

        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $alipay_config['transport'] = 'http';

        // 支付类型 ，无需修改
        $alipay_config['payment_type'] = '1';

        // 产品类型，无需修改
        $alipay_config['service'] = 'create_direct_pay_by_user';

        //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑


        //↓↓↓↓↓↓↓↓↓↓ 请在这里配置防钓鱼信息，如果没开通防钓鱼功能，为空即可 ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓

        // 防钓鱼时间戳  若要使用请调用类文件submit中的query_timestamp函数
        $alipay_config['anti_phishing_key'] = '';

        // 客户端的IP地址 非局域网的外网IP地址，如：221.0.0.1
        $alipay_config['exter_invoke_ip'] = '';

        //↑↑↑↑↑↑↑↑↑↑请在这里配置防钓鱼信息，如果没开通防钓鱼功能，为空即可 ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑

        return $alipay_config;
    }
}
