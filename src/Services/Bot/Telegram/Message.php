<?php

declare(strict_types=1);

namespace App\Services\Bot\Telegram;

use App\Models\Config;
use App\Models\User;
use App\Services\I18n;
use GuzzleHttp\Exception\GuzzleException;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use function array_merge;
use function implode;
use function in_array;
use function json_decode;
use function str_replace;
use const PHP_EOL;

final class Message
{
    /**
     * Bot
     */
    private Api $bot;

    /**
     * 消息会话 ID
     */
    private int $chat_id;

    /**
     * 触发源信息
     */
    private \Telegram\Bot\Objects\Message $message;

    /**
     * 触发源信息 ID
     */
    private int $message_id;

    /**
     * @throws TelegramSDKException|GuzzleException
     */
    public function __construct(Api $bot, \Telegram\Bot\Objects\Message $message)
    {
        $this->bot = $bot;
        $this->chat_id = $message->chat->id;
        $this->message = $message;
        $this->message_id = $message->messageId;

        if ($this->message->newChatMembers !== null) {
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
     * @throws TelegramSDKException|GuzzleException
     */
    public function newChatParticipant(): void
    {
        $new_chat_member = $this->message->newChatMembers[0];

        $member = [
            'id' => $new_chat_member->id,
            'name' => $new_chat_member->firstName . ' ' . $new_chat_member->lastName,
        ];

        if ($new_chat_member->username === Config::obtain('telegram_bot')) {
            // 机器人加入新群组
            if (! Config::obtain('allow_to_join_new_groups')
                &&
                ! in_array($this->chat_id, json_decode(Config::obtain('group_id_allowed_to_join')))) {
                // 退群
                $this->bot->leaveChat(
                    [
                        'chat_id' => $this->chat_id,
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
            $new_user = self::getUser($member['id']);

            if (Config::obtain('telegram_group_bound_user')
                &&
                $this->chat_id === Config::obtain('telegram_chatid')
                &&
                $new_user === null
                &&
                ! $new_chat_member->isBot
            ) {
                $this->replyWithMessage(
                    [
                        'text' => $member['name'] . ' 未绑定 Telegram 账户，将被移除。',
                    ]
                );

                $this->bot->banChatMember(
                    [
                        'chat_id' => $this->chat_id,
                        'user_id' => $member['id'],
                    ]
                );

                return;
            }

            if (Config::obtain('enable_welcome_message')) {
                $text = ($new_user->class > 0 ?
                    I18n::trans('user_join_welcome_paid', $_ENV['locale']) :
                    I18n::trans('user_join_welcome_free', $_ENV['locale']));

                $this->replyWithMessage(
                    [
                        'text' => str_replace(
                            [
                                '%user_name%',
                                '%user_class%',
                            ],
                            [
                                $member['name'],
                                $new_user->class,
                            ],
                            $text
                        ),
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
            $text = '付费用户你好：';
        } else {
            $text = '免费用户你好：';
        }

        return $text;
    }

    /**
     * 搜索用户
     *
     * @param int $value  搜索值
     */
    public static function getUser(int $value): null|User
    {
        return (new User())->where('im_type', 4)->where('im_value', $value)->first();
    }
}
