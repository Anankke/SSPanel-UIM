<?php

namespace App\Utils;

use App\Services\Config;

class Telegram
{

    /**
     * ·¢ËÍÏûÏ¢
     */
    public static function Send($messageText)
    {
        if (Config::get('enable_telegram') == 'true') {
            $bot = new \TelegramBot\Api\BotApi(Config::get('telegram_token'));

            $bot->sendMessage(Config::get('telegram_chatid'), $messageText);
        }
    }
    
    public static function SendManage($messageText)
    {
        if (Config::get('enable_telegram_manage') == 'true') {
            $bot = new \TelegramBot\Api\BotApi(Config::get('telegram_token'));

            $bot->sendMessage(Config::get('telegram_manage_chatid'), $messageText);
        }
    }
    
    public static function SendMarkdown($messageText)
    {
        if (Config::get('enable_telegram') == 'true') {
            $bot = new \TelegramBot\Api\BotApi(Config::get('telegram_token'));

            $bot->sendMessage(Config::get('telegram_chatid'), $messageText, "Markdown");
        }
    }
}
