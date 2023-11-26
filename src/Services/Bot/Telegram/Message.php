<?php

declare(strict_types=1);

namespace App\Services\Bot\Telegram;

use App\Models\Config;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use function array_merge;
use function implode;
use function in_array;
use function json_decode;
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
    private $chat_id;

    /**
     * 触发源信息
     */
    private Collection $message;

    /**
     * 触发源信息 ID
     */
    private $message_id;

    /**
     * @throws TelegramSDKException|GuzzleException
     */
    public function __construct(Api $bot, Collection $message)
    {
        $this->bot = $bot;
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
     * @throws GuzzleException
     */
    public function newChatParticipant(): void
    {
        $new_chat_member = $this->message->getNewChatParticipant();

        $member = [
            'id' => $new_chat_member->getId(),
            'name' => $new_chat_member->getFirstName() . ' Message.php' . $new_chat_member->getLastName(),
        ];

        if ($new_chat_member->getUsername() === Config::obtain('telegram_bot')) {
            // 机器人加入新群组
            if (! Config::obtain('allow_to_join_new_groups')
                &&
                ! in_array($this->chat_id, json_decode(Config::obtain('group_id_allowed_to_join')))) {
                // 退群

                $this->replyWithMessage(
                    [
                        'text' => '不约，叔叔我们不约.',
                    ]
                );

                self::sendPost(
                    'kickChatMember',
                    [
                        'chat_id' => $this->chat_id,
                        'user_id' => $member['id'],
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
                ! $new_chat_member->isBot()
            ) {
                $this->replyWithMessage(
                    [
                        'text' => '由于 ' . $member['name'] . ' 未绑定账户，将被移除。',
                    ]
                );

                self::sendPost(
                    'kickChatMember',
                    [
                        'chat_id' => $this->chat_id,
                        'user_id' => $member['id'],
                    ]
                );

                return;
            }

            if (Config::obtain('enable_welcome_message')) {
                $text = ($new_user->class > 0 ? '欢迎 VIP' . $new_user->class .
                    ' 用户 ' . $member['name'] . '加入群组。' : '欢迎 ' . $member['name']);

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

    /**
     * Sends a POST request to Telegram Bot API.
     * 伪异步，无结果返回.
     *
     * @param $method
     * @param $params
     *
     * @throws GuzzleException
     */
    public static function sendPost($method, $params): void
    {
        $client = new Client();
        $telegram_api_url = 'https://api.telegram.org/bot' . Config::obtain('telegram_token') . '/' . $method;

        $headers = [
            'Content-Type' => 'application/json; charset=utf-8',
        ];

        $client->post(
            $telegram_api_url,
            [
                'headers' => $headers,
                'json' => $params,
                'timeout' => 1,
            ]
        );
    }

    /**
     * 搜索用户
     *
     * @param int $value  搜索值
     * @param string $method 查找列
     */
    public static function getUser(int $value, string $method = 'im_value'): null|Model|User
    {
        return (new User())->where('im_type', 4)->where($method, $value)->first();
    }
}
