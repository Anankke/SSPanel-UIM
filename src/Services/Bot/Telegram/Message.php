<?php

declare(strict_types=1);

namespace App\Services\Bot\Telegram;

use App\Models\Setting;
use App\Models\User;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message as TelegramMessage;
use function in_array;
use function json_decode;

final class Message
{
    /**
     * Bot
     */
    private Api $bot;

    /**
     * 触发用户TG信息
     */
    private array $trigger_user;

    /**
     * 消息会话 ID
     */
    private $chat_id;

    /**
     * 触发源信息
     */
    private TelegramMessage $message;

    /**
     * 触发源信息 ID
     */
    private $message_id;
    private $user;

    /**
     * @throws TelegramSDKException
     */
    public function __construct(Api $bot, TelegramMessage $message)
    {
        $this->bot = $bot;
        $this->trigger_user = [
            'id' => $message->getFrom()->getId(),
            'name' => $message->getFrom()->getFirstName() . ' Message.php' . $message->getFrom()->getLastName(),
            'username' => $message->getFrom()->getUsername(),
        ];
        $this->user = Tool::getUser($this->trigger_user['id']);
        $this->chat_id = $message->getChat()->getId();
        $this->message = $message;
        $this->message_id = $message->getMessageId();

        if ($this->message->getNewChatParticipant() !== null) {
            $this->newChatParticipant();
        }
    }

    /**
     * 回复讯息 | 默认已添加 chat_id 和 message_id
     *
     * @param array $send_message
     *
     * @throws TelegramSDKException
     */
    public function replyWithMessage(array $send_message): void
    {
        $send_message = array_merge(
            [
                'chat_id' => $this->chat_id,
                'message_id' => $this->message_id,
            ],
            $send_message
        );

        $this->bot->sendMessage($send_message);
    }

    /**
     * 入群检测
     *
     * @throws TelegramSDKException
     */
    public function newChatParticipant(): void
    {
        $NewChatMember = $this->message->getNewChatParticipant();

        $Member = [
            'id' => $NewChatMember->getId(),
            'name' => $NewChatMember->getFirstName() . ' Message.php' . $NewChatMember->getLastName(),
        ];

        if ($NewChatMember->getUsername() === Setting::obtain('telegram_bot')) {
            // 机器人加入新群组
            if (! Setting::obtain('allow_to_join_new_groups')
                &&
                ! in_array($this->chat_id, json_decode(Setting::obtain('group_id_allowed_to_join')))) {
                // 退群

                $this->replyWithMessage(
                    [
                        'text' => '不约，叔叔我们不约.',
                    ]
                );

                Tool::sendPost(
                    'kickChatMember',
                    [
                        'chat_id' => $this->chat_id,
                        'user_id' => $Member['id'],
                    ]
                );
            } else {
                $this->replyWithMessage(
                    [
                        'text' => '雷猴啊。',
                    ]
                );
            }
        } else {
            // 新成员加入群组
            $NewUser = Tool::getUser($Member['id']);

            if (Setting::obtain('telegram_group_bound_user')
                &&
                $this->chat_id === Setting::obtain('telegram_chatid')
                &&
                $NewUser === null
                &&
                ! $NewChatMember->isBot()
            ) {
                $this->replyWithMessage(
                    [
                        'text' => '由于 ' . $Member['name'] . ' 未绑定账户，将被移除。',
                    ]
                );

                Tool::sendPost(
                    'kickChatMember',
                    [
                        'chat_id' => $this->chat_id,
                        'user_id' => $Member['id'],
                    ]
                );
                return;
            }

            if (Setting::obtain('enable_welcome_message')) {
                $text = ($NewUser->class > 0 ? '欢迎 VIP' . $NewUser->class .
                    ' 用户 ' . $Member['name'] . '加入群组。' : '欢迎 ' . $Member['name']);

                $this->replyWithMessage(
                    [
                        'text' => $text,
                    ]
                );
            }
        }
    }

    /**
     * 用户的流量使用讯息
     */
    public static function getUserTrafficInfo(User $user): string
    {
        $text = [
            '你当前的流量状况：',
            '',
            '今日已使用 ' . $user->todayUsedTrafficPercent() . '% ：' . $user->todayUsedTraffic(),
            '之前已使用 ' . $user->lastUsedTrafficPercent() . '% ：' . $user->lastUsedTraffic(),
            '流量约剩余 ' . $user->unusedTrafficPercent() . '% ：' . $user->unusedTraffic(),
        ];

        return implode(PHP_EOL, $text);
    }

    /**
     * 用户基本讯息
     */
    public static function getUserInfo(User $user): string
    {
        $text = [
            '当前余额：' . $user->money,
            '在线 IP 数：' . ($user->node_iplimit !== 0 ? $user->onlineIpCount() .
                ' / ' . $user->node_iplimit : $user->onlineIpCount() . ' / 不限制'),
            '端口速率：' . ($user->node_speedlimit > 0 ? $user->node_speedlimit . 'Mbps' : '不限制'),
            '上次使用：' . $user->lastUseTime(),
            '过期时间：' . $user->class_expire,
        ];

        return implode(PHP_EOL, $text);
    }

    /**
     * 获取用户或管理的尊称
     */
    public static function getUserTitle(User $user): string
    {
        if ($user->class > 0) {
            $text = '尊敬的 VIP ' . $user->class . ' 你好：';
        } else {
            $text = '尊敬的用户你好：';
        }

        return $text;
    }
}
