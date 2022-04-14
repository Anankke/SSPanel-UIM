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
    public function handle()
    {
        $Update  = $this->getUpdate();
        $Message = $Update->getMessage();

        // 消息会话 ID
        $ChatID = $Message->getChat()->getId();

        // 消息 ID
        $MessageID = $Message->getMessageId();

        if ($ChatID < 0) {
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
                    $response = $this->replyWithMessage(
                        [
                            'text'                  => '您无权限',
                            'parse_mode'            => 'HTML',
                            'reply_to_message_id'   => $MessageID,
                        ]
                    );
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
                            'text'                  => '无此用户',
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
        }
    }
}
