<?php
namespace App\Utils;

class Check
{
    public static function isEmailLegal($email)
    {
        if ($_ENV['mail_filter'] != '0') {
            $mail_suffix = explode('@', $email)[1];
            if ($_ENV['mail_filter'] != '1') {
                // 白名单模式
                if (!in_array($mail_suffix, $_ENV['mail_filter_list'])) {
                    return false;
                }
            } else {
                // 黑名单模式
                if (in_array($mail_suffix, $_ENV['mail_filter_list'])) {
                    return false;
                }
            }
        }

        return true;
    }
}
