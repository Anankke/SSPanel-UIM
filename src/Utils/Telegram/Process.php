<?php

declare(strict_types=1);

namespace App\Utils\Telegram;

use Exception;
use Telegram\Bot\Api;

final class Process
{
    public static function index(): void
    {
        try {
            $bot = new Api($_ENV['telegram_token']);
            $bot->addCommands(
                [
                    Commands\MyCommand::class,
                    Commands\HelpCommand::class,
                    Commands\InfoCommand::class,
                    Commands\MenuCommand::class,
                    Commands\PingCommand::class,
                    Commands\StartCommand::class,
                    Commands\UnbindCommand::class,
                    Commands\CheckinCommand::class,
                    Commands\SetuserCommand::class,
                ]
            );
            $update = $bot->commandsHandler(true);
            if ($update->getCallbackQuery() !== null) {
                new Callbacks\Callback($bot, $update->getCallbackQuery());
            }
            if ($update->getMessage() !== null) {
                new Message($bot, $update->getMessage());
            }
        } catch (Exception $e) {
            $e->getMessage();
        }
    }
}
