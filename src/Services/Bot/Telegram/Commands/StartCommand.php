<?php

declare(strict_types=1);

namespace App\Services\Bot\Telegram\Commands;

use App\Models\Config;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

/**
 * Class StratCommand.
 */
final class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected string $name = 'start';

    /**
     * @var string Command Description
     */
    protected string $description = '[群组/私聊] Bot 初始命令.';

    public function handle(): void
    {
        $update = $this->getUpdate();
        $message = $update->getMessage();
        // 消息会话 ID
        $chat_id = $message->getChat()->getId();

        if ($chat_id > 0) {
            // 发送 '输入中' 会话状态
            $this->replyWithChatAction(['action' => Actions::TYPING]);
            // 回送信息
            $this->replyWithMessage(
                [
                    'text' => '发送 /help 获取帮助',
                    'parse_mode' => 'Markdown',
                ]
            );
        } else {
            if (! Config::obtain('telegram_group_quiet')) {
                // 发送 '输入中' 会话状态
                $this->replyWithChatAction(['action' => Actions::TYPING]);
                // 回送信息
                $this->replyWithMessage(
                    [
                        'text' => '喵喵喵.',
                        'parse_mode' => 'Markdown',
                        'reply_to_message_id' => $message->getMessageId(),
                    ]
                );
            }
        }
    }
}
