<?php

declare(strict_types=1);

namespace App\Utils;

use App\Services\Cache;
use Exception;
use RedisException;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use voku\helper\AntiXSS;
use function strip_tags;

final class Telegram
{
    /**
     * 发送讯息，默认给群组发送
     *
     * @throws TelegramSDKException
     */
    public static function send(string $messageText, int $chat_id = 0): void
    {
        $bot = null;

        if ($chat_id === 0) {
            $chat_id = $_ENV['telegram_chatid'];
        }

        if ($_ENV['enable_telegram']) {
            // 发送给非群组时使用异步
            $async = ($chat_id !== $_ENV['telegram_chatid']);
            $bot = new Api($_ENV['telegram_token'], $async);
            $sendMessage = [
                'chat_id' => $chat_id,
                'text' => $messageText,
                'parse_mode' => '',
                'disable_web_page_preview' => false,
                'reply_to_message_id' => null,
                'reply_markup' => null,
            ];

            $bot->sendMessage($sendMessage);
        }
    }

    /**
     * 以 HTML 格式发送讯息，默认给群组发送
     *
     * @throws TelegramSDKException
     */
    public static function sendHtml(string $messageText, int $chat_id = 0): void
    {
        $bot = null;

        if ($chat_id === 0) {
            $chat_id = $_ENV['telegram_chatid'];
        }

        if ($_ENV['enable_telegram']) {
            // 发送给非群组时使用异步
            $async = ($chat_id !== $_ENV['telegram_chatid']);
            $bot = new Api($_ENV['telegram_token'], $async);
            $sendMessage = [
                'chat_id' => $chat_id,
                'text' => strip_tags(
                    $messageText,
                    ['b', 'strong', 'i', 'em', 'u', 'ins', 's', 'strike','del', 'span','tg-spoiler', 'a', 'tg-emoji',
                        'code', 'pre',
                    ]
                ),
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => false,
                'reply_to_message_id' => null,
                'reply_markup' => null,
            ];

            try {
                $bot->sendMessage($sendMessage);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    /**
     * 以 Markdown 格式发送讯息，默认给群组发送
     *
     * @throws TelegramSDKException
     */
    public static function sendMarkdown(string $messageText, int $chat_id = 0): void
    {
        $bot = null;

        if ($chat_id === 0) {
            $chat_id = $_ENV['telegram_chatid'];
        }

        if ($_ENV['enable_telegram']) {
            // 发送给非群组时使用异步
            $async = ($chat_id !== $_ENV['telegram_chatid']);
            $bot = new Api($_ENV['telegram_token'], $async);
            $sendMessage = [
                'chat_id' => $chat_id,
                'text' => $messageText,
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => false,
                'reply_to_message_id' => null,
                'reply_markup' => null,
            ];

            try {
                $bot->sendMessage($sendMessage);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    /**
     * 以 MarkdownV2 格式发送讯息，默认给群组发送
     *
     * @throws TelegramSDKException
     */
    public static function sendMarkdownV2(string $messageText, int $chat_id = 0): void
    {
        $bot = null;

        if ($chat_id === 0) {
            $chat_id = $_ENV['telegram_chatid'];
        }

        if ($_ENV['enable_telegram']) {
            // 发送给非群组时使用异步
            $async = ($chat_id !== $_ENV['telegram_chatid']);
            $bot = new Api($_ENV['telegram_token'], $async);
            $sendMessage = [
                'chat_id' => $chat_id,
                'text' => $messageText,
                'parse_mode' => 'MarkdownV2',
                'disable_web_page_preview' => false,
                'reply_to_message_id' => null,
                'reply_markup' => null,
            ];

            try {
                $bot->sendMessage($sendMessage);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    public static function generateRandomLink(): string
    {
        return Tools::genRandomChar(16);
    }

    /**
     * @throws RedisException
     */
    public static function verifyBindSession($token): int
    {
        $antiXss = new AntiXSS();
        $redis = Cache::initRedis();
        $uid = $redis->get($antiXss->xss_clean($token));

        if (! $uid) {
            return 0;
        }

        $redis->del($token);

        return (int) $uid;
    }

    /**
     * @throws RedisException
     */
    public static function addBindSession($user): string
    {
        $redis = Cache::initRedis();
        $token = self::generateRandomLink();

        $redis->setex(
            $token,
            600,
            $user->id
        );

        return $token;
    }
}
