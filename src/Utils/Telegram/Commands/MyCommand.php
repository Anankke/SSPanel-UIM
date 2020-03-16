<?php

namespace App\Utils\Telegram\Commands;

use App\Models\User;
use App\Services\Config;
use App\Utils\Telegram\Reply;
use App\Utils\Telegram\TelegramTools;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

/**
 * Class MyCommand.
 */
class MyCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'my';

    /**
     * @var string Command Description
     */
    protected $description = '[群组/私聊] 我的个人信息.';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        $Update = $this->getUpdate();
        $Message = $Update->getMessage();

        // 消息 ID
        $MessageID = $Message->getMessageId();

        // 消息会话 ID
        $ChatID = $Message->getChat()->getId();

        if ($ChatID < 0) {
            // 群组
            if ($_ENV['enable_delete_user_cmd'] === true) {
                TelegramTools::DeleteMessage([
                    'chatid'      => $ChatID,
                    'messageid'   => $MessageID,
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
                    'text'                  => $_ENV['user_not_bind_reply'],
                    'reply_to_message_id'   => $MessageID,
                    'parse_mode'            => 'Markdown',
                ]
            );
        } else {
            if ($ChatID > 0) {
                // 私人
                $response = $this->triggerCommand('menu');
            } else {
                // 群组
                $response = self::Group($User, $SendUser, $ChatID, $Message, $MessageID);
                // 消息删除任务
                TelegramTools::DeleteMessage([
                    'chatid'      => $ChatID,
                    'messageid'   => $response->getMessageId(),
                ]);
            }
        }

        return $response;
    }

    public function Group($User, $SendUser, $ChatID, $Message, $MessageID)
    {
        $text = Reply::getUserTitle($User);
        $text .= PHP_EOL . PHP_EOL;
        $text .= Reply::getUserTrafficInfo($User);
        $text .= PHP_EOL;
        $text .= '流量重置时间：' . $User->valid_use_loop();
        // 回送信息
        return $this->replyWithMessage(
            [
                'text'                  => $text,
                'parse_mode'            => 'Markdown',
                'reply_to_message_id'   => $MessageID,
                'reply_markup'          => json_encode(
                    [
                        'inline_keyboard' => [
                            [
                                [
                                    'text'          => (!$User->isAbleToCheckin() ? '已签到' : '签到'),
                                    'callback_data' => 'user.checkin.' . $SendUser['id']
                                ]
                            ],
                        ]
                    ]
                ),
            ]
        );
    }
}
