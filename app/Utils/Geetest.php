<?php

namespace App\Utils;

use App\Services\Config;

/**
 * 极验行为式验证安全平台，php 网站主后台包含的库文件
 *
 * @author Tanxu
 */
class Geetest
{
    public static function get($user_id = null)
    {
        $GtSdk = new GeetestLib(Config::get('geetest_id'), Config::get('geetest_key'));
        $status = $GtSdk->pre_process($user_id);
        $ret = json_decode($GtSdk->get_response_str());
        session_start();
        $_SESSION['gtserver'] = $status;
        $_SESSION['user_id'] = $user_id;
        return $ret;
    }

    public static function verify($geetest_challenge, $geetest_validate, $geetest_seccode)
    {
        session_start();
        $GtSdk = new GeetestLib(Config::get('geetest_id'), Config::get('geetest_key'));
        $user_id = $_SESSION['user_id'];
        if ($_SESSION['gtserver'] == 1) {
            $result = $GtSdk->success_validate($geetest_challenge, $geetest_validate, $geetest_seccode, $user_id);
            if ($result) {
                return true;
            } else {
                return false;
            }
        } else {
            if ($GtSdk->fail_validate($geetest_challenge, $geetest_validate, $geetest_seccode)) {
                return true;
            } else {
                return false;
            }
        }
    }
}
