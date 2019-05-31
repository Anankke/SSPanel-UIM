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
        if (Config::get('enable_telegram') == 'true') {
            $bot = new BotApi(Config::get('telegram_token'));
            try {
                $bot->sendMessage(Config::get('telegram_chatid'), $messageText);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }


    public static function SendMarkdown($messageText)
    {
        if (Config::get('enable_telegram') == 'true') {
            $bot = new BotApi(Config::get('telegram_token'));
            try {
                $bot->sendMessage(Config::get('telegram_chatid'), $messageText, 'Markdown');
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }
}
