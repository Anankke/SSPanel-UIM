<?php
/**
 * Created by 傲慢与偏见.
 * OSUser: D-L
 * Date: 2017/10/12
 * Time: 21:08
 */

namespace App\Utils;

class YftOrderNumUtil
{
    public static function generate_yftOrder($length = 8)
    {
		// 密码字符集，可任意添加你需要的字符
        $date = time();
        $date = "yft".date("YmdHis",$date);
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = "";
        for ($i = 0; $i < $length; $i++) {
            // 这里提供两种字符获取方式
            // 第一种是使用 substr 截取$chars中的任意一位字符；
            // 第二种是取字符数组 $chars 的任意元素
            $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $date.$password;
    }
}