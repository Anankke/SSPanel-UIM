<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use function hash_hmac;
use function json_decode;

final class Captcha
{
    public static function generate(): array
    {
        return match (Config::obtain('captcha_provider')) {
            'turnstile' => [
                'turnstile_sitekey' => Config::obtain('turnstile_sitekey'),
            ],
            'geetest' => [
                'geetest_id' => Config::obtain('geetest_id'),
            ],
            'hcaptcha' => [
                'hcaptcha_sitekey' => Config::obtain('hcaptcha_sitekey'),
            ],
            'recaptcha_enterprise' => [
                'recaptcha_enterprise_key_id' => Config::obtain('recaptcha_enterprise_key_id'),
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

        switch (Config::obtain('captcha_provider')) {
            case 'turnstile':
                if (isset($param['turnstile'])) {
                    $turnstile_url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

                    $turnstile_headers = [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ];

                    $turnstile_body = [
                        'secret' => Config::obtain('turnstile_secret'),
                        'response' => $param['turnstile'],
                    ];

                    try {
                        $result = json_decode($client->post($turnstile_url, [
                            'headers' => $turnstile_headers,
                            'form_params' => $turnstile_body,
                            'timeout' => 3,
                        ])->getBody()->getContents())->success;
                    } catch (GuzzleException) {
                        break;
                    }
                }

                break;
            case 'geetest':
                if (isset($param['geetest'])) {
                    $geetest = $param['geetest'];
                    $captcha_id = Config::obtain('geetest_id');
                    $captcha_key = Config::obtain('geetest_key');
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
                    } catch (GuzzleException) {
                        $json = null;
                    }

                    if ($json?->result === 'success') {
                        $result = true;
                    }
                }

                break;
            case 'hcaptcha':
                if (isset($param['hcaptcha'])) {
                    $hcaptcha_url = 'https://hcaptcha.com/siteverify';

                    $hcaptcha_headers = [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ];

                    $hcaptcha_body = [
                        'secret' => Config::obtain('hcaptcha_secret'),
                        'response' => $param['hcaptcha'],
                    ];

                    try {
                        $result = json_decode($client->post($hcaptcha_url, [
                            'headers' => $hcaptcha_headers,
                            'form_params' => $hcaptcha_body,
                            'timeout' => 3,
                        ])->getBody()->getContents())->success;
                    } catch (GuzzleException) {
                        break;
                    }
                }

                break;
            case 'recaptcha_enterprise':
                if (isset($param['recaptcha_enterprise'])) {
                    $recaptcha_enterprise_url = 'https://recaptchaenterprise.googleapis.com/v1/projects/' .
                        Config::obtain('recaptcha_enterprise_project_id') . '/assessments?key=' .
                        Config::obtain('recaptcha_enterprise_api_key');

                    $recaptcha_enterprise_headers = [
                        'Content-Type' => 'application/json',
                    ];

                    $recaptcha_enterprise_body = [
                        'event' => [
                            'token' => $param['recaptcha_enterprise'],
                            'siteKey' => Config::obtain('recaptcha_enterprise_key_id'),
                        ],
                    ];

                    try {
                        $result = json_decode($client->post($recaptcha_enterprise_url, [
                            'headers' => $recaptcha_enterprise_headers,
                            'json' => $recaptcha_enterprise_body,
                            'timeout' => 3,
                        ])->getBody()->getContents())->tokenProperties->valid;
                    } catch (GuzzleException) {
                        break;
                    }
                }

                break;
            default:
                return false;
        }

        return $result;
    }
}
