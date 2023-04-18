<?php

declare(strict_types=1);

namespace App\Utils\Telegram\Commands;

use App\Models\Setting;
use App\Models\User;
use App\Utils\Telegram\Reply;
use App\Utils\Telegram\TelegramTools;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use function in_array;
use function json_decode;

/**
 * Class InfoCommand.
 */
final class InfoCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected string $name = 'info';

    /**
     * @var string Command Description
     */
    protected string $description = '[群组]     获取被回复消息的用户信息，管理员命令.';

    public function handle(): void
    {
        $Update = $this->getUpdate();
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
                'id' => $Message->getFrom()->getId(),
            ];
            if (! in_array($SendUser['id'], json_decode(Setting::obtain('telegram_admins')))) {
                $AdminUser = User::where('is_admin', 1)->where('telegram_id', $SendUser['id'])->first();
                if ($AdminUser === null) {
                    // 非管理员回复消息
                    if (Setting::obtain('enable_not_admin_reply') && Setting::obtain('not_admin_reply_msg') !== '') {
                        $this->replyWithMessage(
                            [
                                'text' => Setting::obtain('not_admin_reply_msg'),
                                'parse_mode' => 'HTML',
                                'reply_to_message_id' => $MessageID,
                            ]
                        );
                    }
                    return;
                }
            }
            if ($Message->getReplyToMessage() !== null) {
                // 回复源消息用户
                $FindUser = [
                    'id' => $Message->getReplyToMessage()->getFrom()->getId(),
                ];
                $User = TelegramTools::getUser($FindUser['id']);
                if ($User === null) {
                    $this->replyWithMessage(
                        [
                            'text' => Setting::obtain('no_user_found'),
                            'reply_to_message_id' => $MessageID,
                        ]
                    );
                } else {
                    $this->replyWithMessage(
                        [
                            'text' => Reply::getUserInfoFromAdmin($User, $ChatID),
                            'reply_to_message_id' => $MessageID,
                        ]
                    );
                }
            } else {
                $this->replyWithMessage(
                    [
                        'text' => '请回复消息使用.',
                        'reply_to_message_id' => $MessageID,
                    ]
                );
            }
        }
    }
}
