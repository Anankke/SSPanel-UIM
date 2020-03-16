<?php

namespace App\Utils\Telegram\Commands;

use App\Models\User;
use App\Utils\Telegram\TelegramTools;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

/**
 * Class CheckinCommand.
 */
class CheckinCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'checkin';

    /**
     * @var string Command Description
     */
    protected $description = '[群组/私聊] 每日签到.';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        $Update = $this->getUpdate();
        $Message = $Update->getMessage();

        // 消息会话 ID
        $ChatID = $Message->getChat()->getId();

        if ($ChatID < 0) {
            // 群组
            if ($_ENV['enable_delete_user_cmd'] === true) {
                TelegramTools::DeleteMessage([
                    'chatid'      => $ChatID,
                    'messageid'   => $Message->getMessageId(),
                ]);
            }
            if ($_ENV['telegram_group_quiet'] === true) {
                // 群组中不回应
                return;
            }
            if ($ChatID != $_ENV['telegram_chatid']) {
                // 非我方群组
                return;
            }
        }

        // 发送 '输入中' 会话状态
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        // 触发用户
        $SendUser = [
            'id'       => $Message->getFrom()->getId(),
            'name'     => $Message->getFrom()->getFirstName() . ' ' . $Message->getFrom()->getLastName(),
            'username' => $Message->getFrom()->getUsername(),
        ];

        $User = User::where('telegram_id', $SendUser['id'])->first();
        if ($User == null) {
            // 回送信息
            $response = $this->replyWithMessage(
                [
                    'text'       => $_ENV['user_not_bind_reply'],
                    'parse_mode' => 'Markdown',
                ]
            );
        } else {
            $checkin = $User->checkin();
            // 回送信息
            $response = $this->replyWithMessage(
                [
                    'text'                  => $checkin['msg'],
                    'reply_to_message_id'   => $Message->getMessageId(),
                    'parse_mode'            => 'Markdown',
                ]
            );
        }
        if ($ChatID < 0) {
            // 消息删除任务
            TelegramTools::DeleteMessage([
                'chatid'      => $ChatID,
                'messageid'   => $response->getMessageId(),
            ]);
        }
        return $response;
    }
}
