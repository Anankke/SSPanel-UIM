<?php

declare(strict_types=1);

namespace App\Utils\Telegram;

use App\Models\Setting;
use RedisException;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
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
    private array $triggerUser;

    /**
     * 消息会话 ID
     */
    private $ChatID;

    /**
     * 触发源信息
     */
    private \Telegram\Bot\Objects\Message $Message;

    /**
     * 触发源信息 ID
     */
    private $MessageID;
    private $User;

    /**
     * @throws TelegramSDKException
     * @throws RedisException
     */
    public function __construct(Api $bot, \Telegram\Bot\Objects\Message $Message)
    {
        $this->bot = $bot;
        $this->triggerUser = [
            'id' => $Message->getFrom()->getId(),
            'name' => $Message->getFrom()->getFirstName() . ' ' . $Message->getFrom()->getLastName(),
            'username' => $Message->getFrom()->getUsername(),
        ];
        $this->User = TelegramTools::getUser($this->triggerUser['id']);
        $this->ChatID = $Message->getChat()->getId();
        $this->Message = $Message;
        $this->MessageID = $Message->getMessageId();

        if ($this->Message->getNewChatParticipant() !== null) {
            $this->newChatParticipant();
        }
    }

    /**
     * 回复讯息 | 默认已添加 chat_id 和 message_id
     *
     * @param array $sendMessage
     *
     * @throws TelegramSDKException
     */
    public function replyWithMessage(array $sendMessage): void
    {
        $sendMessage = array_merge(
            [
                'chat_id' => $this->ChatID,
                'message_id' => $this->MessageID,
            ],
            $sendMessage
        );
        $this->bot->sendMessage($sendMessage);
    }

    /**
     * 入群检测
     *
     * @throws TelegramSDKException
     */
    public function newChatParticipant(): void
    {
        $NewChatMember = $this->Message->getNewChatParticipant();

        $Member = [
            'id' => $NewChatMember->getId(),
            'name' => $NewChatMember->getFirstName() . ' ' . $NewChatMember->getLastName(),
        ];

        if ($NewChatMember->getUsername() === Setting::obtain('telegram_bot')) {
            // 机器人加入新群组
            if (! Setting::obtain('allow_to_join_new_groups')
                &&
                ! in_array($this->ChatID, json_decode(Setting::obtain('group_id_allowed_to_join')))) {
                // 退群

                $this->replyWithMessage(
                    [
                        'text' => '不约，叔叔我们不约.',
                    ]
                );

                TelegramTools::sendPost(
                    'kickChatMember',
                    [
                        'chat_id' => $this->ChatID,
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
            $NewUser = TelegramTools::getUser($Member['id']);

            if (Setting::obtain('telegram_group_bound_user')
                &&
                $this->ChatID === Setting::obtain('telegram_chatid')
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

                TelegramTools::sendPost(
                    'kickChatMember',
                    [
                        'chat_id' => $this->ChatID,
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
}
