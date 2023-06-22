<?php

declare(strict_types=1);

namespace App\Utils;

use App\Models\TelegramSession;
use Exception;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use function strip_tags;
use function time;

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
        for ($i = 0; $i < 10; $i++) {
            $token = Tools::genRandomChar(16);
            $session = TelegramSession::where('session_content', '=', $token)->first();

            if ($session === null) {
                return $token;
            }
        }

        return "couldn't alloc token";
    }

    public static function verifyBindSession($token): int
    {
        $session = TelegramSession::where('type', '=', 0)->where('session_content', $token)
            ->where('datetime', '>', time() - 600)->orderBy('datetime', 'desc')->first();

        if ($session !== null) {
            $uid = $session->user_id;
            $session->delete();
            return $uid;
        }

        return 0;
    }

    public static function addBindSession($user): string
    {
        $session = TelegramSession::where('type', '=', 0)->where('user_id', '=', $user->id)->first();

        if ($session === null) {
            $session = new TelegramSession();
            $session->type = 0;
            $session->user_id = $user->id;
        }

        $session->datetime = time();
        $session->session_content = self::generateRandomLink();
        $session->save();
        return $session->session_content;
    }
}
