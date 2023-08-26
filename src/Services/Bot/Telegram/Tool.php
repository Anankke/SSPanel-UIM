<?php

declare(strict_types=1);

namespace App\Services\Bot\Telegram;

use App\Models\Setting;
use App\Models\User;
use function curl_close;
use function curl_exec;
use function curl_init;
use function curl_setopt;
use function json_encode;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_POST;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_TIMEOUT;
use const CURLOPT_URL;

final class Tool
{
    /**
     * 搜索用户
     *
     * @param int $value  搜索值
     * @param string $method 查找列
     */
    public static function getUser(int $value, string $method = 'im_value')
    {
        return User::where('im_type', 4)->where($method, $value)->first();
    }

    /**
     * Sends a POST request to Telegram Bot API.
     * 伪异步，无结果返回.
     *
     * @param $method
     * @param $params
     */
    public static function sendPost($method, $params): void
    {
        $URL = 'https://api.telegram.org/bot' . Setting::obtain('telegram_token') . '/' . $method;
        $POSTData = json_encode($params);
        $C = curl_init();
        curl_setopt($C, CURLOPT_URL, $URL);
        curl_setopt($C, CURLOPT_POST, 1);
        curl_setopt($C, CURLOPT_HTTPHEADER, ['Content-Type:application/json; charset=utf-8']);
        curl_setopt($C, CURLOPT_POSTFIELDS, $POSTData);
        curl_setopt($C, CURLOPT_TIMEOUT, 1);
        curl_exec($C);
        curl_close($C);
    }
}
