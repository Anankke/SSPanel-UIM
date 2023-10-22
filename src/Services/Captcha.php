<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
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
        $client = new Client();

        switch (Setting::obtain('captcha_provider')) {
            case 'turnstile':
                if (isset($param['turnstile'])) {
                    $turnstile_url = "https://challenges.cloudflare.com/turnstile/v0/siteverify";

                    $turnstile_headers = [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ];

                    $turnstile_body = [
                        'secret' => Setting::obtain('turnstile_secret'),
                        'response' => $param['turnstile'],
                    ];

                    try {
                        $result = json_decode($client->post($turnstile_url, [
                            'headers' => $turnstile_headers,
                            'form_params' => $turnstile_body,
                            'timeout' => 3,
                        ])->getBody()->getContents())->success;
                    } catch (GuzzleException $e) {
                        echo $e->getMessage();
                    }
                }
                break;
            case 'geetest':
                if (isset($param['geetest'])) {
                    $geetest = $param['geetest'];
                    $captcha_id = Setting::obtain('geetest_id');
                    $captcha_key = Setting::obtain('geetest_key');
                    $lot_number = $geetest['lot_number'];
                    $captcha_output = $geetest['captcha_output'];
                    $pass_token = $geetest['pass_token'];
                    $gen_time = $geetest['gen_time'];
                    $sign_token = hash_hmac('sha256', $lot_number, $captcha_key);

                    $geetest_headers = [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ];

                    $geetest_body = [
                        'lot_number' => $lot_number,
                        'captcha_output' => $captcha_output,
                        'pass_token' => $pass_token,
                        'gen_time' => $gen_time,
                        'sign_token' => $sign_token,
                    ];

                    $geetest_url = 'https://gcaptcha4.geetest.com/validate?captcha_id=' . $captcha_id;

                    try {
                        $json = json_decode($client->post($geetest_url, [
                            'headers' => $geetest_headers,
                            'form_params' => $geetest_body,
                            'timeout' => 3,
                        ])->getBody()->getContents());
                    } catch (GuzzleException $e) {
                        $json = null;
                        echo $e->getMessage();
                    }

                    if ($json?->result === 'success') {
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
