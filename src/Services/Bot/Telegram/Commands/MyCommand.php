<?php

declare(strict_types=1);

namespace App\Services\Bot\Telegram\Commands;

use App\Models\Config;
use App\Models\User;
use App\Services\Bot\Telegram\Message;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use function json_encode;
use const PHP_EOL;

/**
 * Class MyCommand.
 */
final class MyCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected string $name = 'my';

    /**
     * @var string Command Description
     */
    protected string $description = '[群组/私聊] 我的个人信息.';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $update = $this->getUpdate();
        $message = $update->getMessage();
        // 消息 ID
        $message_id = $message->getMessageId();
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
            'name' => $message->getFrom()->getFirstName() . ' MyCommand.php' . $message->getFrom()->getLastName(),
            'username' => $message->getFrom()->getUsername(),
        ];

        $user = (new User())->where('im_type', 4)->where('im_value', $send_user['id'])->first();

        if ($user === null) {
            // 回送信息
            $response = $this->replyWithMessage(
                [
                    'text' => Config::obtain('user_not_bind_reply'),
                    'reply_to_message_id' => $message_id,
                    'parse_mode' => 'Markdown',
                ]
            );
        } else {
            if ($chat_id > 0) {
                // 私人
                $response = $this->triggerCommand('menu');
            } else {
                // 群组
                $response = $this->group($user, $send_user, $chat_id, $message, $message_id);
            }
        }

        return $response;
    }

    public function group($user, $send_user, $chat_id, $message, $message_id)
    {
        $text = Message::getUserTitle($user);
        $text .= PHP_EOL . PHP_EOL;
        $text .= Message::getUserTrafficInfo($user);
        // 回送信息
        return $this->replyWithMessage(
            [
                'text' => $text,
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => $message_id,
                'reply_markup' => json_encode(
                    [
                        'inline_keyboard' => [
                            [
                                [
                                    'text' => (! $user->isAbleToCheckin() ? '已签到' : '签到'),
                                    'callback_data' => 'user.checkin.' . $send_user['id'],
                                ],
                            ],
                        ],
                    ]
                ),
            ]
        );
    }
}
