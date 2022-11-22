<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;

final class Captcha
{
    public static function generate(): array
    {
        switch (Setting::obtain('captcha_provider')) {
            case 'turnstile':
                return [
                    'turnstile_sitekey' => Setting::obtain('turnstile_sitekey'),
                ];
                break;
            case 'geetest':
                return [
                    'geetest_id' => Setting::obtain('geetest_id'),
                ];
                break;
        }

        return [];
    }

    /**
     * 获取验证结果
     */
    public static function verify($param): bool
    {
        $result = false;

        switch (Setting::obtain('captcha_provider')) {
            case 'turnstile':
                if ($param['turnstile'] !== '') {
                    $postdata = http_build_query(
                        [
                            'secret' => Setting::obtain('turnstile_secret'),
                            'response' => $param['turnstile'],
                        ]
                    );
                    $opts = ['http' => [
                        'method' => 'POST',
                        'header' => 'Content-Type: application/x-www-form-urlencoded',
                        'content' => $postdata,
                    ],
                    ];
                    $json = @file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, stream_context_create($opts));
                    $result = \json_decode($json)->success;
                }
                break;
            case 'geetest':
                // Todo https://github.com/GeeTeam/gt4-php-demo/blob/master/LoginController.php
                break;
        }

        return $result;
    }
}
