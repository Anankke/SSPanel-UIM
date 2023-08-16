<?php

declare(strict_types=1);

namespace App\Services\Bot\Telegram\Commands;

use App\Models\Setting;
use RedisException;
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

    /**
     * @throws RedisException
     */
    public function handle(): void
    {
        $Update = $this->getUpdate();
        $Message = $Update->getMessage();

        // 消息会话 ID
        $ChatID = $Message->getChat()->getId();

        if ($ChatID > 0) {
            // 发送 '输入中' 会话状态
            $this->replyWithChatAction(['action' => Actions::TYPING]);
            // 触发用户
            $SendUser = [
                'id' => $Message->getFrom()->getId(),
                'name' => $Message->getFrom()->getFirstName() . ' StartCommand.php' . $Message->getFrom()->getLastName(),
                'username' => $Message->getFrom()->getUsername(),
            ];
            // 消息内容
            $MessageText = explode(' ', trim($Message->getText()));
            $MessageKey = array_splice($MessageText, -1)[0];
            // 回送信息
            $this->replyWithMessage(
                [
                    'text' => '发送 /help 获取帮助',
                    'parse_mode' => 'Markdown',
                ]
            );
        } else {
            if (! Setting::obtain('telegram_group_quiet')) {
                // 发送 '输入中' 会话状态
                $this->replyWithChatAction(['action' => Actions::TYPING]);
                // 回送信息
                $this->replyWithMessage(
                    [
                        'text' => '喵喵喵.',
                        'parse_mode' => 'Markdown',
                        'reply_to_message_id' => $Message->getMessageId(),
                    ]
                );
            }
        }
    }
}
