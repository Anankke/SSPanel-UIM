<?php

declare(strict_types=1);

namespace App\Services\Bot\Telegram\Commands;

use App\Models\Config;
use App\Services\Bot\Telegram\Message;
use App\Services\I18n;
use App\Services\Reward;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use function in_array;

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
    protected string $description = '[群组/私聊] 每日签到';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $update = $this->update;
        $message = $update->message;
        $chat_id = $message->chat->id;

        if (in_array($message->chat->type, ['group', 'supergroup'])) {
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
            'id' => $message->from->id,
        ];

        $user = Message::getUser($send_user['id']);

        if ($user === null) {
            // 回送信息
            $response = $this->replyWithMessage(
                [
                    'text' => I18n::trans('bot.user_not_bind', $_ENV['locale']),
                    'parse_mode' => 'Markdown',
                    'reply_to_message_id' => $message->messageId,
                ]
            );
        } else {
            if ($user->isAbleToCheckin()) {
                $traffic = Reward::issueCheckinReward($user->id);

                if (! $traffic) {
                    $msg = '签到失败';
                } else {
                    $msg = '获得了 ' . $traffic . 'MB 流量';
                }
            } else {
                $msg = '你今天已经签到过了';
            }
            // 回送信息
            $response = $this->replyWithMessage(
                [
                    'text' => $msg,
                    'parse_mode' => 'Markdown',
                    'reply_to_message_id' => $message->messageId,
                ]
            );
        }

        return $response;
    }
}
