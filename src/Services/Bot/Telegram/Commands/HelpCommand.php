<?php

declare(strict_types=1);

namespace App\Services\Bot\Telegram\Commands;

use App\Models\Config;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use function preg_match;
use const PHP_EOL;

/**
 * Class HelpCommand.
 */
final class HelpCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected string $name = 'help';

    /**
     * @var string Command Description
     */
    protected string $description = '[群组/私聊] 系统中可用的所有指令';

    public function handle(): void
    {
        $update = $this->getUpdate();
        $message = $update->getMessage();

        if ($message->getChat()->getId() < 0 && Config::obtain('telegram_group_quiet')) {
            return;
        }

        if (! preg_match('/^\/help\s?(@' . Config::obtain('telegram_bot') . ')?.*/i', $message->getText()) &&
            ! Config::obtain('help_any_command')) {
            return;
        }

        $this->replyWithChatAction(['action' => Actions::TYPING]);
        $commands = $this->telegram->getCommands();
        $text = '系统中可用的所有指令：';
        $text .= PHP_EOL . PHP_EOL;

        foreach ($commands as $name => $handler) {
            $text .= '/' . $name . PHP_EOL . '`    - ' . $handler->getDescription() . '`' . PHP_EOL;
        }

        $this->replyWithMessage(
            [
                'text' => $text,
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => false,
                'reply_to_message_id' => $message->getMessageId(),
                'reply_markup' => null,
            ]
        );
    }
}
