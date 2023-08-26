<?php

declare(strict_types=1);

namespace App\Services\Bot\Telegram\Commands;

use App\Models\Setting;
use App\Services\Bot\Telegram\Tool;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

/**
 * Class UnbindCommand.
 */
final class UnbindCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected string $name = 'unbind';

    /**
     * @var string Command Description
     */
    protected string $description = '[私聊]     解除账户绑定.';

    public function handle(): void
    {
        $update = $this->getUpdate();
        $message = $update->getMessage();
        // 消息会话 ID
        $chat_id = $message->getChat()->getId();
        // 触发用户
        $send_user = [
            'id' => $message->getFrom()->getId(),
        ];
        $user = Tool::getUser($send_user['id']);

        if ($chat_id > 0) {
            // 发送 '输入中' 会话状态
            $this->replyWithChatAction(['action' => Actions::TYPING]);

            if ($user === null) {
                // 回送信息
                $this->replyWithMessage(
                    [
                        'text' => Setting::obtain('user_not_bind_reply'),
                        'parse_mode' => 'Markdown',
                    ]
                );
                return;
            }

            // 消息内容
            $message_text = explode(' ', trim($message->getText()));
            $message_key = array_splice($message_text, -1)[0];
            $text = '';

            if ($message_key === $user->email) {
                $temp = $user->unbindIM();
                $text = $temp['msg'];
                // 回送信息
                $this->replyWithMessage(
                    [
                        'text' => $text,
                        'parse_mode' => 'Markdown',
                    ]
                );
                return;
            }

            if ($message_key !== '') {
                $text = '键入的 Email 地址与你的账户不匹配.';
            }

            if ($message_key === '/unbind') {
                $text = $this->sendText();
            }

            // 回送信息
            $this->replyWithMessage(
                [
                    'text' => $text,
                    'parse_mode' => 'Markdown',
                ]
            );
        }
    }

    public function sendText(): string
    {
        $text = '以 `/unbind example@qq.com` 的形式发送进行解绑.';

        if (Setting::obtain('telegram_unbind_kick_member')) {
            $text .= PHP_EOL . PHP_EOL . '根据管理员的设定，你解绑账户将会被自动移出用户群.';
        }

        return $text;
    }
}
