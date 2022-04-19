<?php

declare(strict_types=1);

namespace App\Utils;

use App\Models\Setting;

/**
 * 极验行为式验证安全平台，php 网站主后台包含的库文件
 *
 * @author Tanxu
 */
final class Geetest
{
    public static function get($user_id = null)
    {
        $configs = Setting::getClass('geetest');
        $GtSdk = new GeetestLib($configs['geetest_id'], $configs['geetest_key']);
        $status = $GtSdk->preProcess($user_id);
        $ret = json_decode($GtSdk->getResponseStr());
        session_start();
        $_SESSION['gtserver'] = $status;
        $_SESSION['user_id'] = $user_id;
        return $ret;
    }

    public static function verify($geetest_challenge, $geetest_validate, $geetest_seccode)
    {
        session_start();
        $configs = Setting::getClass('geetest');
        $GtSdk = new GeetestLib($configs['geetest_id'], $configs['geetest_key']);
        $user_id = $_SESSION['user_id'];
        if ($_SESSION['gtserver'] === 1) {
            $result = $GtSdk->successValidate($geetest_challenge, $geetest_validate, $geetest_seccode, $user_id);
            if ($result) {
                return true;
            }

            return false;
        }

        if ($GtSdk->failValidate($geetest_challenge, $geetest_validate, $geetest_seccode)) {
            return true;
        }

        return false;
    }
}
