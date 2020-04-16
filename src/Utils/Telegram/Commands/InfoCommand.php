<?php

namespace App\Utils\Telegram\Commands;

use App\Services\Config;
use App\Models\User;
use App\Utils\Telegram\{Reply, TelegramTools};
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

/**
 * Class InfoCommand.
 */
class InfoCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'info';

    /**
     * @var string Command Description
     */
    protected $description = '[群组]     获取被回复消息的用户信息，管理员命令.';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        $Update  = $this->getUpdate();
        $Message = $Update->getMessage();

        // 消息会话 ID
        $ChatID = $Message->getChat()->getId();

        // 消息 ID
        $MessageID = $Message->getMessageId();

        if ($ChatID < 0) {
            // 群组
            if ($_ENV['enable_delete_user_cmd'] === true) {
                TelegramTools::DeleteMessage([
                    'chatid'      => $ChatID,
                    'messageid'   => $MessageID,
                ]);
            }
            // 发送 '输入中' 会话状态
            $this->replyWithChatAction(['action' => Actions::TYPING]);
            // 触发用户
            $SendUser = [
                'id'       => $Message->getFrom()->getId(),
                'name'     => $Message->getFrom()->getFirstName() . ' ' . $Message->getFrom()->getLastName(),
                'username' => $Message->getFrom()->getUsername(),
            ];
            if (!in_array($SendUser['id'], $_ENV['telegram_admins'])) {
                $AdminUser = User::where('is_admin', 1)->where('telegram_id', $SendUser['id'])->first();
                if ($AdminUser == null) {
                    // 非管理员回复消息
                    if ($_ENV['enable_not_admin_reply'] === true && $_ENV['not_admin_reply_msg'] != '') {
                        $response = $this->replyWithMessage(
                            [
                                'text'                  => $_ENV['not_admin_reply_msg'],
                                'parse_mode'            => 'HTML',
                                'reply_to_message_id'   => $MessageID,
                            ]
                        );
                        // 消息删除任务
                        TelegramTools::DeleteMessage([
                            'chatid'      => $ChatID,
                            'messageid'   => $response->getMessageId(),
                        ]);
                    }
                    return;
                }
            }
            if ($Message->getReplyToMessage() != null) {
                // 回复源消息用户
                $FindUser = [
                    'id'       => $Message->getReplyToMessage()->getFrom()->getId(),
                    'name'     => $Message->getReplyToMessage()->getFrom()->getFirstName() . ' ' . $Message->getReplyToMessage()->getFrom()->getLastName(),
                    'username' => $Message->getReplyToMessage()->getFrom()->getUsername(),
                ];
                $User = TelegramTools::getUser($FindUser['id']);
                if ($User == null) {
                    $response = $this->replyWithMessage(
                        [
                            'text'                  => $_ENV['no_user_found'],
                            'reply_to_message_id'   => $MessageID,
                        ]
                    );
                } else {
                    $response = $this->replyWithMessage(
                        [
                            'text'                  => Reply::getUserInfoFromAdmin($User, $ChatID),
                            'reply_to_message_id'   => $MessageID,
                        ]
                    );
                }
            } else {
                $response = $this->replyWithMessage(
                    [
                        'text'                  => '请回复消息使用.',
                        'reply_to_message_id'   => $MessageID,
                    ]
                );
            }
            // 消息删除任务
            TelegramTools::DeleteMessage([
                'chatid'      => $ChatID,
                'messageid'   => $response->getMessageId(),
                'executetime' => $_ENV['delete_admin_message_time']
            ]);
        }
    }
}
