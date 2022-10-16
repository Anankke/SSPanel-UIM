<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use App\Utils\Geetest;

final class Captcha
{
    public static function generate(): array
    {
        $geetest = null;
        $turnstile = null;

        switch (Setting::obtain('captcha_provider')) {
            case 'turnstile':
                $turnstile = Setting::obtain('turnstile_sitekey');
                break;
            case 'geetest':
                $geetest = Geetest::get(\time() . random_int(1, 10000));
                break;
        }

        return [
            'geetest' => $geetest,
            'turnstile' => $turnstile,
        ];
    }

    /**
     * 获取验证结果
     */
    public static function verify($param): bool
    {
        $result = false;

        switch (Setting::obtain('captcha_provider')) {
            case 'turnstile':
                if (isset($param['turnstile'])) {
                    if ($param['turnstile'] !== '') {
                        $json = file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify?secret=' . Setting::obtain('turnstile_secret') . '&response=' . $param['turnstile']);
                        $result = \json_decode($json)->success;
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
