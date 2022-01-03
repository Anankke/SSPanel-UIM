<?php
namespace App\Utils;

class Check
{
    public static function isEmailLegal($email)
    {
        $res['ret'] = 0;
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $res['msg'] = '邮箱不规范';
            return $res;
        }

        $mail_suffix = explode('@', $email)[1];
        $mail_filter_list = $_ENV['mail_filter_list'];
        $res['msg'] = '我们无法将邮件投递至域 ' . $mail_suffix . ' ，请更换邮件地址';
        
        switch ($_ENV['mail_filter']) {
            case 0:
                // 关闭
                $res['ret'] = 1;
                return $res;
            case 1:
                // 白名单
                if (in_array($mail_suffix, $mail_filter_list)) {
                    $res['ret'] = 1;
                }
                return $res;
            case 2:
                // 黑名单
                if (!in_array($mail_suffix, $mail_filter_list)) {
                    $res['ret'] = 1;
                }
                return $res;
            default:
                // 更新后未设置该选项
                $res['ret'] = 1;
                return $res;
        }
    }
}
