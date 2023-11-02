<?php

declare(strict_types=1);

namespace App\Services\Bot\Telegram\Commands;

use App\Models\Config;
use App\Services\Bot\Telegram\Message;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

/**
 * Class CheckinCommand.
 */
final class CheckinCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected string $name = 'checkin';

    /**
     * @var string Command Description
     */
    protected string $description = '[群组/私聊] 每日签到.';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $update = $this->getUpdate();
        $message = $update->getMessage();
        // 消息会话 ID
        $chat_id = $message->getChat()->getId();

        if ($chat_id < 0) {
            if (Config::obtain('telegram_group_quiet')) {
                // 群组中不回应
                return null;
            }
            if ($chat_id !== Config::obtain('telegram_chatid')) {
                // 非我方群组
                return null;
            }
        }

        // 发送 '输入中' 会话状态
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        // 触发用户
        $send_user = [
            'id' => $message->getFrom()->getId(),
        ];
        $user = Message::getUser($send_user['id']);

        if ($user === null) {
            // 回送信息
            $response = $this->replyWithMessage(
                [
                    'text' => Config::obtain('user_not_bind_reply'),
                    'parse_mode' => 'Markdown',
                    'reply_to_message_id' => $message->getMessageId(),
                ]
            );
        } else {
            $checkin = $user->checkin();
            // 回送信息
            $response = $this->replyWithMessage(
                [
                    'text' => $checkin['msg'],
                    'parse_mode' => 'Markdown',
                    'reply_to_message_id' => $message->getMessageId(),
                ]
            );
        }

        return $response;
    }
}
