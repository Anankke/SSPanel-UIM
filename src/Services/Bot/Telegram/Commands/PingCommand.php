<?php

declare(strict_types=1);

namespace App\Services\Bot\Telegram\Commands;

use App\Models\Config;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use function implode;
use function in_array;
use const PHP_EOL;

/**
 * Class PingCommand.
 */
final class PingCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected string $name = 'ping';

    /**
     * @var string Command Description
     */
    protected string $description = '[群组/私聊] 获取我或者群组的唯一 ID';

    public function handle(): void
    {
        $update = $this->update;
        $message = $update->message;
        $chat_id = $message->chat->id;

        if ($message->chat->type === 'private') {
            // 发送 '输入中' 会话状态
            $this->replyWithChatAction(['action' => Actions::TYPING]);

            $text = [
                'Pong！',
                'User ID is ' . $chat_id,
            ];
            // 回送信息
            $this->replyWithMessage(
                [
                    'text' => implode(PHP_EOL, $text),
                    'parse_mode' => 'Markdown',
                ]
            );
        } elseif (in_array($message->chat->type, ['group', 'supergroup']) &&
            ! Config::obtain('telegram_group_quiet')) {
            // 发送 '输入中' 会话状态
            $this->replyWithChatAction(['action' => Actions::TYPING]);

            $text = [
                'Pong！',
                'User ID is ' . $message->from->id,
                'Group ID is ' . $chat_id,
            ];
            // 回送信息
            $this->replyWithMessage(
                [
                    'text' => implode(PHP_EOL, $text),
                ]
            );
        }
    }
}
