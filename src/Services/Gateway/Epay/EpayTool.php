<?php

declare(strict_types=1);

namespace App\Services\Gateway\Epay;

use function function_exists;
use function strlen;
use function time;

final class EpayTool
{
    public static function md5Sign($prestr, $key): string
    {
        $prestr .= $key;
        return md5($prestr);
    }

    public static function md5Verify($prestr, $sign, $key): bool
    {
        $prestr .= $key;
        $mysgin = md5($prestr);

        if ($mysgin === $sign) {
            return true;
        }
        return false;
    }

    public static function createLinkstring($para): string
    {
        $arg = '';
        foreach ($para as $key => $val) {
            $arg .= $key . '=' . $val . '&';
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, strlen($arg) - 1);

        //如果存在转义字符，那么去掉转义
        return stripslashes($arg);
    }

    public static function createLinkstringUrlencode($para): string
    {
        $arg = '';

        foreach ($para as $key => $val) {
            $arg .= $key.'='.urlencode($val).'&';
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, strlen($arg) - 1);

        //如果存在转义字符，那么去掉转义

        return stripslashes($arg);
    }

    public static function paraFilter($para): array
    {
        $para_filter = [];
        foreach ($para as $key => $val) {
            if ($key === 'sign' || $key === 'sign_type' || $val === '') {
                continue;
            }
            $para_filter[$key] = $val;
        }

        return $para_filter;
    }

    public static function argSort($para)
    {
        ksort($para);
        return $para;
    }

    public static function logResult($word = ''): void
    {
        $fp = fopen('/storage/epaylog.txt', 'a');
        flock($fp, LOCK_EX);
        fwrite($fp, '执行日期：'.date('Y-m-d H:i:s', time())."\n".$word."\n");
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    public static function getHttpResponsePOST($url, $cacert_url, $para, $input_charset = ''): bool|string
    {
        if (trim($input_charset) !== '') {
            $url .= '_input_charset='.$input_charset;
        }
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
        curl_setopt($curl, CURLOPT_CAINFO, $cacert_url);//证书地址
        curl_setopt($curl, CURLOPT_HEADER, 0); // 过滤HTTP头
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        curl_setopt($curl, CURLOPT_POST, true); // post传输数据
        curl_setopt($curl, CURLOPT_POSTFIELDS, $para);// post传输数据
        $responseText = curl_exec($curl);
        //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
        curl_close($curl);

        return $responseText;
    }

    public static function getHttpResponseGET($url, $cacert_url): bool|string
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 0); // 过滤HTTP头
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
        curl_setopt($curl, CURLOPT_CAINFO, $cacert_url);//证书地址
        $responseText = curl_exec($curl);
        //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
        curl_close($curl);

        return $responseText;
    }

    public static function charsetEncode($input, $_output_charset, $_input_charset)
    {
        $output = '';
        if (! isset($_output_charset)) {
            $_output_charset = $_input_charset;
        }
        if ($_input_charset === $_output_charset || $input === null) {
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

    public static function charsetDecode($input, $_input_charset, $_output_charset)
    {
        $output = '';
        if (! isset($_input_charset)) {
            $_input_charset = $_output_charset;
        }
        if ($_input_charset === $_output_charset || $input === null) {
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
}
