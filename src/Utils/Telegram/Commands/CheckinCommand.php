<?php

declare(strict_types=1);

namespace App\Utils\Telegram\Commands;

use App\Models\Setting;
use App\Utils\Telegram\TelegramTools;
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
        $Update = $this->getUpdate();
        $Message = $Update->getMessage();

        // 消息会话 ID
        $ChatID = $Message->getChat()->getId();

        if ($ChatID < 0) {
            if (Setting::obtain('telegram_group_quiet')) {
                // 群组中不回应
                return null;
            }
            if ($ChatID !== $_ENV['telegram_chatid']) {
                // 非我方群组
                return null;
            }
        }

        // 发送 '输入中' 会话状态
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        // 触发用户
        $SendUser = [
            'id' => $Message->getFrom()->getId(),
        ];
        $User = TelegramTools::getUser($SendUser['id']);
        if ($User === null) {
            // 回送信息
            $response = $this->replyWithMessage(
                [
                    'text' => Setting::obtain('user_not_bind_reply'),
                    'parse_mode' => 'Markdown',
                    'reply_to_message_id' => $Message->getMessageId(),
                ]
            );
        } else {
            $checkin = $User->checkin();
            // 回送信息
            $response = $this->replyWithMessage(
                [
                    'text' => $checkin['msg'],
                    'parse_mode' => 'Markdown',
                    'reply_to_message_id' => $Message->getMessageId(),
                ]
            );
        }
        return $response;
    }
}
