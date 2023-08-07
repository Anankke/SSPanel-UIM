<?php

declare(strict_types=1);

namespace App\Utils\Telegram;

use App\Models\Setting;
use App\Models\User;
use App\Services\Cache;
use App\Utils\Tools;
use RedisException;
use voku\helper\AntiXSS;
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

final class TelegramTools
{
    /**
     * 搜索用户
     *
     * @param int $value  搜索值
     * @param string $method 查找列
     */
    public static function getUser(int $value, string $method = 'telegram_id')
    {
        return User::where($method, $value)->first();
    }

    /**
     * Sends a POST request to Telegram Bot API.
     * 伪异步，无结果返回.
     *
     * @param $Method
     * @param $Params
     */
    public static function sendPost($Method, $Params): void
    {
        $URL = 'https://api.telegram.org/bot' . Setting::obtain('telegram_token') . '/' . $Method;
        $POSTData = json_encode($Params);
        $C = curl_init();
        curl_setopt($C, CURLOPT_URL, $URL);
        curl_setopt($C, CURLOPT_POST, 1);
        curl_setopt($C, CURLOPT_HTTPHEADER, ['Content-Type:application/json; charset=utf-8']);
        curl_setopt($C, CURLOPT_POSTFIELDS, $POSTData);
        curl_setopt($C, CURLOPT_TIMEOUT, 1);
        curl_exec($C);
        curl_close($C);
    }

    /**
     * @param $token
     *
     * @return int
     *
     * @throws RedisException
     */
    public static function verifyBindSession($token): int
    {
        $antiXss = new AntiXSS();
        $redis = Cache::initRedis();
        $uid = $redis->get('telegram_bind:' . $antiXss->xss_clean($token));

        if (! $uid) {
            return 0;
        }

        $redis->del('telegram_bind:' . $token);

        return (int) $uid;
    }

    /**
     * @param $user
     *
     * @return string
     *
     * @throws RedisException
     */
    public static function addBindSession($user): string
    {
        $redis = Cache::initRedis();
        $token = Tools::genRandomChar(16);

        $redis->setex(
            'telegram_bind:' . $token,
            600,
            $user->id
        );

        return $token;
    }
}
