<?php


namespace App\Utils;

class Check
{
    //
    public static function isEmailLegal($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($email) <= 32;
    }
    
    // 禁止 Gmail 邮箱带 + 号的小号注册
    public static function isGmailSmall($email)
    {
        $email_exp = explode('@', $email);
        if ( $email_exp[1] === 'gmail.com' && strstr($email_exp[0], '+') ) {
            return false;
        }
        return true;
    }
}
