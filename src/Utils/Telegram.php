<?php

namespace App\Utils;

use Exception;
use TelegramBot\Api\BotApi;
use Telegram\Bot\Api;

class Telegram
{
    /**
     * 向 $_ENV['telegram_chatid'] 中配置的群组发送讯息
     *
     * @param string $messageText
     */
    public static function Send($messageText): void
    {
        if ($_ENV['enable_telegram'] == true) {
            if ($_ENV['use_new_telegram_bot'] === true) {
                $bot = new Api($_ENV['telegram_token']);
                $sendMessage = [
                    'chat_id'                   => $_ENV['telegram_chatid'],
                    'text'                      => $messageText,
                    'parse_mode'                => '',
                    'disable_web_page_preview'  => false,
                    'reply_to_message_id'       => null,
                    'reply_markup'              => null
                ];
                try {
                    $bot->sendMessage($sendMessage);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            } else {
                $bot = new BotApi($_ENV['telegram_token']);
                try {
                    $bot->sendMessage($_ENV['telegram_chatid'], $messageText);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
        }
    }

    /**
     * 向 $_ENV['telegram_chatid'] 中配置的群组以 Markdown 格式发送讯息
     *
     * @param string $messageText
     */
    public static function SendMarkdown($messageText): void
    {
        if ($_ENV['enable_telegram'] == true) {
            if ($_ENV['use_new_telegram_bot'] === true) {
                $bot = new Api($_ENV['telegram_token']);
                $sendMessage = [
                    'chat_id'                   => $_ENV['telegram_chatid'],
                    'text'                      => $messageText,
                    'parse_mode'                => 'Markdown',
                    'disable_web_page_preview'  => false,
                    'reply_to_message_id'       => null,
                    'reply_markup'              => null
                ];
                try {
                    $bot->sendMessage($sendMessage);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            } else {
                $bot = new BotApi($_ENV['telegram_token']);
                try {
                    $bot->sendMessage($_ENV['telegram_chatid'], $messageText, 'Markdown');
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
        }
    }
}
