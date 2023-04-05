<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use function hash_hmac;
use function json_decode;

final class Captcha
{
    public static function generate(): array
    {
        return match (Setting::obtain('captcha_provider')) {
            'turnstile' => [
                'turnstile_sitekey' => Setting::obtain('turnstile_sitekey'),
            ],
            'geetest' => [
                'geetest_id' => Setting::obtain('geetest_id'),
            ],
            default => [],
        };
    }

    /**
     * 获取验证结果
     */
    public static function verify($param): bool
    {
        $result = false;

        switch (Setting::obtain('captcha_provider')) {
            case 'turnstile':
                $turnstile = $param['turnstile'] ?? '';
                if ($turnstile !== '') {
                    $postdata = http_build_query(
                        [
                            'secret' => Setting::obtain('turnstile_secret'),
                            'response' => $turnstile,
                        ]
                    );
                    $opts = ['http' => [
                        'method' => 'POST',
                        'header' => 'Content-Type: application/x-www-form-urlencoded',
                        'content' => $postdata,
                    ],
                    ];
                    $json = file_get_contents(
                        'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                        false,
                        stream_context_create($opts)
                    );
                    $result = json_decode($json)->success;
                }
                break;
            case 'geetest':
                $geetest = $param['geetest'] ?? [];
                if ($geetest !== []) {
                    $captcha_id = Setting::obtain('geetest_id');
                    $captcha_key = Setting::obtain('geetest_key');
                    $lot_number = $geetest['lot_number'];
                    $captcha_output = $geetest['captcha_output'];
                    $pass_token = $geetest['pass_token'];
                    $gen_time = $geetest['gen_time'];
                    $sign_token = hash_hmac('sha256', $lot_number, $captcha_key);
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
                        'https://gcaptcha4.geetest.com/validate?captcha_id=' . $captcha_id,
                        false,
                        stream_context_create($opts)
                    );
                    if (json_decode($json)->result === 'success') {
                        $result = true;
                    }
                }
                break;
            default:
                return false;
        }

        return $result;
    }
}
