<?php

declare(strict_types=1);

namespace App\Services\Bot\Telegram\Commands;

use App\Services\Bot\Telegram\Callback;
use App\Services\Bot\Telegram\Message;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use function json_encode;

/**
 * Class MenuCommand.
 */
final class MenuCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected string $name = 'menu';

    /**
     * @var string Command Description
     */
    protected string $description = '[私聊] 个人中心';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $update = $this->update;
        $message = $update->message;
        $chat_id = $message->chat->id;

        if ($message->chat->type === 'private') {
            // 私人会话
            // 发送 '输入中' 会话状态
            $this->replyWithChatAction(['action' => Actions::TYPING]);
            // 触发用户
            $send_user = [
                'id' => $message->from->id,
            ];

            $user = Message::getUser($send_user['id']);

            if ($user === null) {
                $reply = null;
            } else {
                $reply = Callback::getUserIndexKeyboard($user);
            }
            // 回送信息
            return $this->replyWithMessage(
                [
                    'text' => $reply['text'] ?? 'Hi!',
                    'parse_mode' => 'Markdown',
                    'disable_web_page_preview' => false,
                    'reply_to_message_id' => null,
                    'reply_markup' => json_encode(
                        [
                            'inline_keyboard' => $reply['keyboard'] ?? [],
                        ]
                    ),
                ]
            );
        }

        return null;
    }
}
