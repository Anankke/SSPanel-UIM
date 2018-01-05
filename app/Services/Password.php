<?php


namespace App\Services;

use App\Models\PasswordReset;
use App\Utils\Tools;

/***
 * Class Password
 * @package App\Services
 */
class Password
{
    /**
     * @param $email string
     * @return bool
     */
    public static function sendResetEmail($email)
    {
        $pwdRst = new PasswordReset();
        $pwdRst->email = $email;
        $pwdRst->init_time = time();
        $pwdRst->expire_time = time() + 3600 * 24; // @todo
        $pwdRst->token = Tools::genRandomChar(64);
        if (!$pwdRst->save()) {
            return false;
        }
        $subject = Config::get('appName') . "重置密码";
        $resetUrl = Config::get('baseUrl') . "/password/token/" . $pwdRst->token;
        try {
            Mail::send($email, $subject, 'password/reset.tpl', [
                "resetUrl" => $resetUrl
            ], [
                //BASE_PATH.'/public/assets/email/styles.css'
            ]);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    public static function resetBy($token, $password)
    {
    }
}
