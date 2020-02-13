<?php

namespace App\Utils;

use App\Services\Config;
use Exception;
use TelegramBot\Api\BotApi;

class Telegram
{

    /**
     * ������Ϣ
     */
    public static function Send($messageText)
    {
        if ($_ENV['enable_telegram'] == true) {
            $bot = new BotApi($_ENV['telegram_token']);
            try {
                $bot->sendMessage($_ENV['telegram_chatid'], $messageText);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }


    public static function SendMarkdown($messageText)
    {
        if ($_ENV['enable_telegram'] == true) {
            $bot = new BotApi($_ENV['telegram_token']);
            try {
                $bot->sendMessage($_ENV['telegram_chatid'], $messageText, 'Markdown');
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }
}
