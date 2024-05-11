<?php

declare(strict_types=1);

namespace App\Services\IM;

use App\Models\Config;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use function str_replace;
use function strip_tags;

final class Telegram extends Base
{
    private Api $bot;

    /**
     * @throws TelegramSDKException
     */
    public function __construct()
    {
        $this->bot = new Api(Config::obtain('telegram_token'));
    }

    /**
     * 发送讯息，默认给群组发送
     *
     * @throws TelegramSDKException
     */
    public function send($to, $msg = ''): void
    {
        $msg = str_replace(
            [
                '_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!',
            ],
            [
                '\_', '\*', '\[', '\]', '\(', '\)', '\~', '\`', '\>', '\#', '\+', '\-', '\=', '\|', '\{', '\}', '\.', '\!',
            ],
            $msg
        );

        $sendMessage = [
            'chat_id' => $to,
            'text' => $msg,
            'parse_mode' => 'MarkdownV2',
            'disable_web_page_preview' => false,
            'reply_to_message_id' => null,
            'reply_markup' => null,
        ];

        $this->bot->sendMessage($sendMessage);
    }

    /**
     * 以 HTML 格式发送讯息，默认给群组发送
     *
     * @throws TelegramSDKException
     */
    public function sendHtml(int $to = 0, string $msg = ''): void
    {
        if ($to === 0) {
            $to = Config::obtain('telegram_chatid');
        }

        $sendMessage = [
            'chat_id' => $to,
            'text' => strip_tags(
                $msg,
                ['b', 'strong', 'i', 'em', 'u', 'ins', 's', 'strike','del', 'span','tg-spoiler', 'a', 'tg-emoji',
                    'code', 'pre', 'blockquote',
                ]
            ),
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => false,
            'reply_to_message_id' => null,
            'reply_markup' => null,
        ];

        $this->bot->sendMessage($sendMessage);
    }
}
