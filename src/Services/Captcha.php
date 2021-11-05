<?php

namespace App\Services;

use App\Utils\Geetest;
use App\Models\Setting;

class Captcha
{
    public static function generate(): array
    {
        $geetest   = null;
        $recaptcha = null;

        switch (Setting::obtain('captcha_provider'))
        {
            case 'recaptcha':
                $recaptcha = Setting::obtain('recaptcha_sitekey');
                break;
            case 'geetest':
                $geetest = Geetest::get(time() . random_int(1, 10000));
                break;
        }

        return [
            'geetest'   => $geetest,
            'recaptcha' => $recaptcha
        ];
    }

    /**
     * 获取验证结果
     */
    public static function verify($param): bool
    {
        $result = false;

        switch (Setting::obtain('captcha_provider'))
        {
            case 'recaptcha':
                if (isset($param['recaptcha'])) {
                    if ($param['recaptcha'] != '') {
                        $json   = file_get_contents('https://recaptcha.net/recaptcha/api/siteverify?secret=' . Setting::obtain('recaptcha_secret') . '&response=' . $param['recaptcha']);
                        $result = json_decode($json)->success;
                    }
                }
                break;
            case 'geetest':
                if (isset($param['geetest_challenge']) && isset($param['geetest_validate']) && isset($param['geetest_seccode'])) {
                    $result = Geetest::verify($param['geetest_challenge'], $param['geetest_validate'], $param['geetest_seccode']);
                }
                break;
        }

        return $result;
    }
}
