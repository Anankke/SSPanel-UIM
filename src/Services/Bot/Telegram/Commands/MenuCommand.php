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
    protected string $description = '[私聊]     用户主菜单、个人中心.';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $update = $this->getUpdate();
        $message = $update->getMessage();

        // 消息会话 ID
        $chat_id = $message->getChat()->getId();

        if ($chat_id > 0) {
            // 私人会话

            // 发送 '输入中' 会话状态
            $this->replyWithChatAction(['action' => Actions::TYPING]);

            // 触发用户
            $send_user = [
                'id' => $message->getFrom()->getId(),
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
