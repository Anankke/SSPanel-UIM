<?php

declare(strict_types=1);

namespace App\Services\Bot\Telegram\Commands;

use App\Models\Config;
use App\Models\User;
use App\Services\Bot\Telegram\Message;
use App\Services\I18n;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use function in_array;
use function json_encode;

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
    protected string $description = '[群组/私聊] 我的个人信息';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $update = $this->update;
        $message = $update->message;
        $message_id = $message->messageId;
        $chat_id = $message->chat->id;

        if (in_array($message->chat->type, ['group', 'supergroup']) &&
            (Config::obtain('telegram_group_quiet') || $chat_id !== Config::obtain('telegram_chatid'))) {
            return null;
        }
        // 发送 '输入中' 会话状态
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        // 触发用户
        $send_user = [
            'id' => $message->from->id,
            'name' => $message->from->firstName . ' ' . $message->from->lastName,
            'username' => $message->from->username,
        ];

        $user = (new User())->where('im_type', 4)->where('im_value', $send_user['id'])->first();

        if ($user === null) {
            // 回送信息
            $response = $this->replyWithMessage(
                [
                    'text' => I18n::trans('bot.user_not_bind', $_ENV['locale']),
                    'reply_to_message_id' => $message_id,
                    'parse_mode' => 'Markdown',
                ]
            );
        } else {
            if ($message->chat->type === 'private') {
                // 私人
                $response = $this->triggerCommand('menu');
            } else {
                // 群组
                $response = $this->group($user, $send_user, $chat_id, $message, $message_id);
            }
        }

        return $response;
    }

    private function group($user, $send_user, $chat_id, $message, $message_id)
    {
        $text = Message::getUserTrafficInfo($user);
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
