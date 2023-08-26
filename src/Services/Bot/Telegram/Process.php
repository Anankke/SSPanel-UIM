<?php

declare(strict_types=1);

namespace App\Services\Bot\Telegram;

use App\Models\Setting;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Psr\Http\Message\RequestInterface;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

final class Process
{
    /**
     * @throws InvalidDatabaseException
     * @throws TelegramSDKException
     */
    public static function index(RequestInterface $request): void
    {
        $bot = new Api(Setting::obtain('telegram_token'));

        $bot->addCommands([
            new Commands\MyCommand(),
            new Commands\HelpCommand(),
            new Commands\MenuCommand(),
            new Commands\PingCommand(),
            new Commands\StartCommand(),
            new Commands\UnbindCommand(),
            new Commands\CheckinCommand(),
        ]);

        $bot->commandsHandler(true, $request);
        $update = $bot->getWebhookUpdate();

        if ($update->has('callback_query')) {
            new Callback($bot, $update->getCallbackQuery());
        } elseif ($update->has('message')) {
            new Message($bot, $update->getMessage());
        }
    }
}
