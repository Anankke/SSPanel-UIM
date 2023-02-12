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
            case 'geetest':
                return [
                    'geetest_id' => Setting::obtain('geetest_id'),
                ];
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
                    $json = file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, stream_context_create($opts));
                    $result = \json_decode($json)->success;
                }
                break;
            case 'geetest':
                if ($param['geetest'] !== '') {
                    $captcha_id = Setting::obtain('geetest_id');
                    $captcha_key = Setting::obtain('geetest_key');
                    $lot_number = $param['geetest']['lot_number'];
                    $captcha_output = $param['geetest']['captcha_output'];
                    $pass_token = $param['geetest']['pass_token'];
                    $gen_time = $param['geetest']['gen_time'];
                    $sign_token = \hash_hmac('sha256', $lot_number, $captcha_key);
                    $postdata = http_build_query(
                        [
                            'lot_number' => $lot_number,
                            'captcha_output' => $captcha_output,
                            'pass_token' => $pass_token,
                            'gen_time' => $gen_time,
                            'sign_token' => $sign_token,
                        ]
                    );
                    $opts = [
                        'http' => [
                            'method' => 'POST',
                            'header' => 'Content-type: application/x-www-form-urlencoded',
                            'content' => $postdata,
                            'timeout' => 5,
                        ],
                    ];
                    $json = file_get_contents(
                        'http://gcaptcha4.geetest.com/validate?captcha_id=' . $captcha_id,
                        false,
                        stream_context_create($opts)
                    );
                    if (\json_decode($json)->result === 'success') {
                        $result = true;
                    } else {
                        $result = false;
                    }
                }
                break;
        }

        return $result;
    }
}
