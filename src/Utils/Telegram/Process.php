<?php

declare(strict_types=1);

namespace App\Utils\Telegram;

use MaxMind\Db\Reader\InvalidDatabaseException;
use Psr\Http\Message\RequestInterface;
use RedisException;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

final class Process
{
    /**
     * @throws TelegramSDKException
     * @throws InvalidDatabaseException
     * @throws RedisException
     */
    public static function index(RequestInterface $request): void
    {
        $bot = new Api($_ENV['telegram_token']);

        $bot->addCommands([
            new Commands\MyCommand(),
            new Commands\HelpCommand(),
            new Commands\InfoCommand(),
            new Commands\MenuCommand(),
            new Commands\PingCommand(),
            new Commands\StartCommand(),
            new Commands\UnbindCommand(),
            new Commands\CheckinCommand(),
            new Commands\SetuserCommand(),
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
