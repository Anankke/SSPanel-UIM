<?php

declare(strict_types=1);

namespace App\Services\Bot\Telegram;

use App\Controllers\SubController;
use App\Models\InviteCode;
use App\Models\LoginIp;
use App\Models\OnlineLog;
use App\Models\Payback;
use App\Models\Setting;
use App\Models\SubscribeLog;
use App\Services\Config;
use App\Utils\Tools;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\CallbackQuery;
use function in_array;
use function is_null;
use function json_encode;
use function time;

final class Callback
{
    /**
     * Bot
     */
    private Api $bot;

    /**
     * 触发用户
     */
    private $user;

    /**
     * 触发用户TG信息
     */
    private array $trigger_user;

    /**
     * 回调
     */
    private CallbackQuery $callback;

    /**
     * 回调数据内容
     */
    private $callback_data;

    /**
     * 消息会话 ID
     */
    private $chat_id;

    /**
     * 触发源信息 ID
     */
    private $message_id;

    /**
     * 源消息是否可编辑
     */
    private bool $allow_edit_message;

    /**
     * @param Api $bot
     * @param CallbackQuery $callback
     *
     * @throws InvalidDatabaseException
     * @throws TelegramSDKException
     */
    public function __construct(Api $bot, CallbackQuery $callback)
    {
        $this->bot = $bot;
        $this->trigger_user = [
            'id' => $callback->getFrom()->getId(),
            'name' => $callback->getFrom()->getFirstName() . ' Callback.php' . $callback->getFrom()->getLastName(),
            'username' => $callback->getFrom()->getUsername(),
        ];
        $this->user = Tool::getUser($this->trigger_user['id']);
        $this->chat_id = $callback->getMessage()->getChat()->getId();
        $this->callback = $callback;
        $this->message_id = $callback->getMessage()->getMessageId();
        $this->callback_data = $callback->getData();
        $this->allow_edit_message = time() < $callback->getMessage()->getDate() + 172800;

        if ($this->chat_id < 0 && Setting::obtain('telegram_group_quiet')) {
            // 群组中不回应
            return;
        }

        if (str_starts_with($this->callback_data, 'user.')) {
            // 用户相关
            $this->userCallback();
        }
    }

    /**
     * 响应回调查询 | 默认已添加 chat_id 和 message_id
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

        if ($this->allow_edit_message) {
            Tool::sendPost('editMessageText', $send_message);
        } else {
            $this->bot->sendMessage($send_message);
        }
    }

    /**
     * 响应回调查询 | 默认已添加 callback_query_id
     *
     * <code>
     * [
     *  'text'       => '',
     *  'show_alert' => false
     * ]
     * </code>
     *
     * @param array $send_message
     */
    public function answerCallbackQuery(array $send_message): void
    {
        $send_message = array_merge(
            [
                'callback_query_id' => $this->callback->getId(),
                'show_alert' => false,
            ],
            $send_message
        );
        Tool::sendPost('answerCallbackQuery', $send_message);
    }

    public static function getUserIndexKeyboard($user): array
    {
        $checkin = (! $user->isAbleToCheckin() ? '已签到' : '签到');
        $Keyboard = [
            [
                [
                    'text' => '用户中心',
                    'callback_data' => 'user.center',
                ],
                [
                    'text' => '资料编辑',
                    'callback_data' => 'user.edit',
                ],
            ],
            [
                [
                    'text' => '订阅中心',
                    'callback_data' => 'user.subscribe',
                ],
                [
                    'text' => '分享计划',
                    'callback_data' => 'user.invite',
                ],
            ],
            [
                [
                    'text' => $checkin,
                    'callback_data' => 'user.checkin.' . $user->im_value,
                ],
            ],
        ];
        $text = Message::getUserTitle($user);
        $text .= PHP_EOL . PHP_EOL;
        $text .= Message::getUserInfo($user);

        return [
            'text' => $text,
            'keyboard' => $Keyboard,
        ];
    }

    /**
     * 用户相关回调数据处理
     *
     * @throws InvalidDatabaseException
     * @throws TelegramSDKException
     */
    public function userCallback(): void
    {
        if ($this->user === null) {
            if ($this->chat_id < 0) {
                // 群组内提示
                $this->answerCallbackQuery([
                    'text' => '你好，你尚未绑定账户，无法进行操作。',
                    'show_alert' => true,
                ]);
            }
        }

        $CallbackDataExplode = explode('|', $this->callback_data);
        $Operate = explode('.', $CallbackDataExplode[0]);
        $op_1 = $Operate[1];

        switch ($op_1) {
            case 'edit':
                // 资料编辑
                $this->userEdit();
                break;
            case 'subscribe':
                // 订阅中心
                $this->userSubscribe();
                break;
            case 'invite':
                // 分享计划
                $this->userInvite();
                break;
            case 'checkin':
                // 签到
                if ((int) $Operate[2] !== $this->trigger_user['id']) {
                    $this->answerCallbackQuery([
                        'text' => '你好，你无法操作他人的账户。',
                        'show_alert' => true,
                    ]);
                }
                $this->userCheckin();
                break;
            case 'center':
                // 用户中心
                $this->userCenter();
                break;
            default:
                // 用户首页
                $temp = self::getUserIndexKeyboard($this->user);

                $this->replyWithMessage([
                    'text' => $temp['text'],
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => false,
                    'reply_to_message_id' => null,
                    'reply_markup' => json_encode(
                        [
                            'inline_keyboard' => $temp['keyboard'],
                        ]
                    ),
                ]);
        }
    }

    public function getUserCenterKeyboard(): array
    {
        $text = Message::getUserTitle($this->user);
        $text .= PHP_EOL . PHP_EOL;
        $text .= Message::getUserTrafficInfo($this->user);
        $keyboard = [
            [
                [
                    'text' => '登录记录',
                    'callback_data' => 'user.center.login_log',
                ],
                [
                    'text' => '在线 IP',
                    'callback_data' => 'user.center.usage_log',
                ],
            ],
            [
                [
                    'text' => '返利记录',
                    'callback_data' => 'user.center.rebate_log',
                ],
                [
                    'text' => '订阅记录',
                    'callback_data' => 'user.center.subscribe_log',
                ],
            ],
            [
                [
                    'text' => '回主菜单',
                    'callback_data' => 'user.index',
                ],
            ],
        ];

        return [
            'text' => $text,
            'keyboard' => $keyboard,
        ];
    }

    /**
     * 用户中心
     *
     * @throws TelegramSDKException
     * @throws InvalidDatabaseException
     */
    public function userCenter(): void
    {
        $back = [
            [
                [
                    'text' => '回主菜单',
                    'callback_data' => 'user.index',
                ],
                [
                    'text' => '回上一页',
                    'callback_data' => 'user.center',
                ],
            ],
        ];
        $CallbackDataExplode = explode('|', $this->callback_data);
        $Operate = explode('.', $CallbackDataExplode[0]);
        $OpEnd = end($Operate);

        switch ($OpEnd) {
            case 'login_log':
                // 登录记录
                $total = LoginIp::where('userid', $this->user->id)
                    ->where('type', '=', 0)
                    ->orderBy('datetime', 'desc')
                    ->take(10)
                    ->get();
                $text = '<strong>以下是你最近 10 次的登录 IP 和地理位置：</strong>' . PHP_EOL;
                $text .= PHP_EOL;

                foreach ($total as $single) {
                    $location = Tools::getIpLocation($single->ip);
                    $text .= $single->ip . ' - ' . $location . PHP_EOL;
                }

                $text .= PHP_EOL . '<strong>注意：地理位置根据 IP 数据库预估，可能与实际位置不符，仅供参考使用</strong>' . PHP_EOL;

                $sendMessage = [
                    'text' => $text,
                    'disable_web_page_preview' => false,
                    'parse_mode' => 'HTML',
                    'reply_to_message_id' => null,
                    'reply_markup' => json_encode(
                        [
                            'inline_keyboard' => $back,
                        ]
                    ),
                ];
                break;
            case 'usage_log':
                // 使用记录
                $logs = OnlineLog::where('user_id', $this->user->id)
                    ->where('last_time', '>', time() - 90)->orderByDesc('last_time')->get('ip');
                $text = '<strong>以下是你账户在线 IP 和地理位置：</strong>' . PHP_EOL;
                $text .= PHP_EOL;

                foreach ($logs as $log) {
                    $ip = $log->ip();
                    $location = Tools::getIpLocation($ip);
                    $text .= "{$ip} - {$location}\n";
                }

                $text .= PHP_EOL . '<strong>注意：地理位置根据 IP 数据库预估，可能与实际位置不符，仅供参考使用</strong>' . PHP_EOL;

                $sendMessage = [
                    'text' => $text,
                    'disable_web_page_preview' => false,
                    'parse_mode' => 'HTML',
                    'reply_to_message_id' => null,
                    'reply_markup' => json_encode(
                        [
                            'inline_keyboard' => $back,
                        ]
                    ),
                ];
                break;
            case 'rebate_log':
                // 返利记录
                $paybacks = Payback::where('ref_by', $this->user->id)->orderBy('datetime', 'desc')->take(10)->get();
                $temp = [];
                foreach ($paybacks as $payback) {
                    $temp[] = '<code>#' . $payback->id .
                        '：' . ($payback->user() !== null ? $payback->user()->user_name : '已注销') . '：' .
                        $payback->ref_get . ' 元</code>';
                }
                $text = '<strong>以下是你最近 10 次返利记录：</strong>';
                $text .= PHP_EOL . PHP_EOL;
                $text .= implode(PHP_EOL, $temp);
                $sendMessage = [
                    'text' => $text,
                    'disable_web_page_preview' => false,
                    'parse_mode' => 'HTML',
                    'reply_to_message_id' => null,
                    'reply_markup' => json_encode(
                        [
                            'inline_keyboard' => $back,
                        ]
                    ),
                ];
                break;
            case 'subscribe_log':
                // 订阅记录
                $logs = SubscribeLog::orderBy('id', 'desc')->where('user_id', $this->user->id)->take(10)->get();
                $temp = [];
                foreach ($logs as $log) {
                    $location = Tools::getIpLocation($log->request_ip);
                    $temp[] = '<code>' . Tools::toDateTime($log->request_time) .
                        ' 在 [' . $log->request_ip . '] ' . $location .
                        ' 访问了 ' . $log->type . ' 订阅</code>';
                }
                $text = '<strong>以下是你最近 10 次订阅记录：</strong>';
                $text .= PHP_EOL . PHP_EOL;
                $text .= implode(PHP_EOL . PHP_EOL, $temp);
                $sendMessage = [
                    'text' => $text,
                    'disable_web_page_preview' => false,
                    'parse_mode' => 'HTML',
                    'reply_to_message_id' => null,
                    'reply_markup' => json_encode(
                        [
                            'inline_keyboard' => $back,
                        ]
                    ),
                ];
                break;
            default:
                $temp = $this->getUserCenterKeyboard();
                $sendMessage = [
                    'text' => $temp['text'],
                    'disable_web_page_preview' => false,
                    'reply_to_message_id' => null,
                    'reply_markup' => json_encode(
                        [
                            'inline_keyboard' => $temp['keyboard'],
                        ]
                    ),
                ];
                break;
        }

        $this->replyWithMessage($sendMessage);
    }

    public function getUserEditKeyboard(): array
    {
        $text = Message::getUserTitle($this->user);
        $keyboard = [
            [
                [
                    'text' => '重置订阅链接',
                    'callback_data' => 'user.edit.update_link',
                ],
                [
                    'text' => '重置链接密码',
                    'callback_data' => 'user.edit.update_passwd',
                ],
            ],
            [
                [
                    'text' => '更改加密方式',
                    'callback_data' => 'user.edit.encrypt',
                ],
                [
                    'text' => '账户解绑',
                    'callback_data' => 'user.edit.unbind',
                ],
            ],
            [
                [
                    'text' => '群组解封',
                    'callback_data' => 'user.edit.unban',
                ],
            ],
            [
                [
                    'text' => '回主菜单',
                    'callback_data' => 'user.index',
                ],
            ],
        ];

        return [
            'text' => $text,
            'keyboard' => $keyboard,
        ];
    }

    /**
     * 用户编辑
     *
     * @throws TelegramSDKException
     */
    public function userEdit(): void
    {
        if ($this->chat_id < 0) {
            $this->answerCallbackQuery([
                'text' => '无法在群组中进行该操作。',
                'show_alert' => true,
            ]);
        }

        $back = [
            [
                [
                    'text' => '回主菜单',
                    'callback_data' => 'user.index',
                ],
                [
                    'text' => '回上一页',
                    'callback_data' => 'user.edit',
                ],
            ],
        ];

        $sendMessage = [];
        $CallbackDataExplode = explode('|', $this->callback_data);
        $Operate = explode('.', $CallbackDataExplode[0]);
        $OpEnd = end($Operate);

        switch ($OpEnd) {
            case 'update_link':
                // 重置订阅链接
                $this->user->cleanLink();
                $this->answerCallbackQuery([
                    'text' => '订阅链接重置成功，请在下方重新更新订阅。',
                    'show_alert' => true,
                ]);
                $temp = $this->getUserSubscribeKeyboard();
                $sendMessage = [
                    'text' => $temp['text'],
                    'disable_web_page_preview' => false,
                    'reply_to_message_id' => null,
                    'reply_markup' => json_encode(
                        [
                            'inline_keyboard' => $temp['keyboard'],
                        ]
                    ),
                ];
                break;
            case 'update_passwd':
                // 重置链接密码
                $this->user->passwd = Tools::genRandomChar();
                if ($this->user->save()) {
                    $answerCallbackQuery = '连接密码更新成功，请在下方重新更新订阅。';
                    $temp = $this->getUserSubscribeKeyboard();
                } else {
                    $answerCallbackQuery = '出现错误，连接密码更新失败，请联系管理员。';
                    $temp = $this->getUserEditKeyboard();
                }
                $this->answerCallbackQuery([
                    'text' => $answerCallbackQuery,
                    'show_alert' => true,
                ]);
                $sendMessage = [
                    'text' => $temp['text'],
                    'disable_web_page_preview' => false,
                    'reply_to_message_id' => null,
                    'reply_markup' => json_encode(
                        [
                            'inline_keyboard' => $temp['keyboard'],
                        ]
                    ),
                ];
                break;
            case 'encrypt':
                // 加密方式更改
                $keyboard = $back;
                if (isset($CallbackDataExplode[1])) {
                    if (in_array($CallbackDataExplode[1], Config::getSupportParam('method'))) {
                        $temp = $this->user->setMethod($CallbackDataExplode[1]);
                        if ($temp['ok']) {
                            $text = '你当前的加密方式为：' . $this->user->method . PHP_EOL . PHP_EOL . $temp['msg'];
                        } else {
                            $text = '发生错误，请重新选择。' . PHP_EOL . PHP_EOL . $temp['msg'];
                        }
                    } else {
                        $text = '发生错误，请重新选择。';
                    }
                } else {
                    $Encrypts = [];
                    foreach (Config::getSupportParam('method') as $value) {
                        $Encrypts[] = [
                            'text' => $value,
                            'callback_data' => 'user.edit.encrypt|' . $value,
                        ];
                    }
                    $Encrypts = array_chunk($Encrypts, 2);
                    $keyboard = [];
                    foreach ($Encrypts as $Encrypt) {
                        $keyboard[] = $Encrypt;
                    }
                    $keyboard[] = $back[0];
                    $text = '你当前的加密方式为：' . $this->user->method;
                }
                $sendMessage = [
                    'text' => $text,
                    'disable_web_page_preview' => false,
                    'reply_to_message_id' => null,
                    'reply_markup' => json_encode(
                        [
                            'inline_keyboard' => $keyboard,
                        ]
                    ),
                ];
                break;
            case 'unbind':
                // Telegram 账户解绑
                $this->allow_edit_message = false;
                $text = '发送 **/unbind 账户邮箱** 进行解绑。';
                if (Setting::obtain('telegram_unbind_kick_member')) {
                    $text .= PHP_EOL . PHP_EOL . '根据管理员的设定，你解绑账户将会被自动移出用户群。';
                }
                $sendMessage = [
                    'text' => $text,
                    'disable_web_page_preview' => false,
                    'reply_to_message_id' => null,
                    'parse_mode' => 'Markdown',
                    'reply_markup' => null,
                ];
                break;
            case 'unban':
                // 群组解封
                $sendMessage = [
                    'text' => '如果你已经身处用户群，请勿随意点击解封，否则会导致你被移除出群组。',
                    'disable_web_page_preview' => false,
                    'reply_to_message_id' => null,
                    'reply_markup' => json_encode(
                        [
                            'inline_keyboard' => [
                                [
                                    [
                                        'text' => '点击提交解封',
                                        'callback_data' => 'user.edit.unban_update',
                                    ],
                                ],
                                $back[0],
                            ],
                        ]
                    ),
                ];
                break;
            case 'unban_update':
                // 提交群组解封
                Tool::sendPost(
                    'unbanChatMember',
                    [
                        'chat_id' => Setting::obtain('telegram_chatid'),
                        'user_id' => $this->trigger_user['id'],
                    ]
                );
                $this->answerCallbackQuery([
                    'text' => '已提交解封，如你仍无法加入群组，请联系管理员。',
                    'show_alert' => true,
                ]);
                break;
            default:
                $temp = $this->getUserEditKeyboard();
                $text = '你可在此编辑你的资料或连接信息：' . PHP_EOL . PHP_EOL;
                $text .= '端口：' . $this->user->port . PHP_EOL;
                $text .= '密码：' . $this->user->passwd . PHP_EOL;
                $text .= '加密：' . $this->user->method;
                $sendMessage = [
                    'text' => $text,
                    'disable_web_page_preview' => false,
                    'reply_to_message_id' => null,
                    'reply_markup' => json_encode(
                        [
                            'inline_keyboard' => $temp['keyboard'],
                        ]
                    ),
                ];
                break;
        }

        if (! isset($sendMessage['parse_mode'])) {
            $sendMessage['parse_mode'] = 'HTML';
        }

        $this->replyWithMessage($sendMessage);
    }

    public function getUserSubscribeKeyboard(): array
    {
        $text = '选择你想要使用的订阅链接类型：';
        $keyboard = [
            [
                [
                    'text' => 'Clash',
                    'callback_data' => 'user.subscribe|clash',
                ],
                [
                    'text' => 'Json',
                    'callback_data' => 'user.subscribe|json',
                ],
                [
                    'text' => 'Shadowsocks SIP008',
                    'callback_data' => 'user.subscribe|sip008',
                ],
            ],
            [
                [
                    'text' => 'Shadowsocks',
                    'callback_data' => 'user.subscribe|ss',
                ],
                [
                    'text' => 'Shadowsocks SIP002',
                    'callback_data' => 'user.subscribe|sip002',
                ],
            ],
            [
                [
                    'text' => 'V2Ray',
                    'callback_data' => 'user.subscribe|v2',
                ],
                [
                    'text' => 'Trojan',
                    'callback_data' => 'user.subscribe|trojan',
                ],
            ],
            [
                [
                    'text' => '回主菜单',
                    'callback_data' => 'user.index',
                ],
            ],
        ];

        return [
            'text' => $text,
            'keyboard' => $keyboard,
        ];
    }

    /**
     * 用户订阅
     *
     * @throws TelegramSDKException
     */
    public function userSubscribe(): void
    {
        $CallbackDataExplode = explode('|', $this->callback_data);
        // 订阅中心
        if (isset($CallbackDataExplode[1])) {
            $temp = [];

            $temp['keyboard'] = [
                [
                    [
                        'text' => '回主菜单',
                        'callback_data' => 'user.index',
                    ],
                    [
                        'text' => '回上一页',
                        'callback_data' => 'user.subscribe',
                    ],
                ],
            ];

            $sendMessage = [];

            $UniversalSub_Url = SubController::getUniversalSubLink($this->user);
            $TraditionalSub_Url = SubController::getTraditionalSubLink($this->user);
            $text = match ($CallbackDataExplode[1]) {
                'clash' => 'Clash 通用订阅地址：' . PHP_EOL . PHP_EOL .
                    '<code>' . $UniversalSub_Url . '/clash</code>' . PHP_EOL . PHP_EOL,
                'json' => 'Json 通用订阅地址：' . PHP_EOL . PHP_EOL .
                    '<code>' . $UniversalSub_Url . '/json</code>' . PHP_EOL . PHP_EOL,
                'sip008' => 'Shadowsocks SIP008 通用订阅地址：' . PHP_EOL . PHP_EOL .
                    '<code>' . $UniversalSub_Url . '/sip008</code>' . PHP_EOL . PHP_EOL,
                'ss' => 'Shadowsocks 传统订阅地址：' . PHP_EOL . PHP_EOL .
                    '<code>' . $TraditionalSub_Url . '?ss=1</code>' . PHP_EOL . PHP_EOL,
                'sip002' => 'Shadowsocks SIP002 传统订阅地址：' . PHP_EOL . PHP_EOL .
                    '<code>' . $TraditionalSub_Url . '?sip002=1</code>' . PHP_EOL . PHP_EOL,
                'v2' => 'V2Ray 传统订阅地址：' . PHP_EOL . PHP_EOL .
                    '<code>' . $TraditionalSub_Url . '?v2ray=1</code>' . PHP_EOL . PHP_EOL,
                'trojan' => 'Trojan 传统订阅地址：' . PHP_EOL . PHP_EOL .
                    '<code>' . $TraditionalSub_Url . '?trojan=1</code>' . PHP_EOL . PHP_EOL,
                default => '未知参数' . PHP_EOL . PHP_EOL,
            };
            $sendMessage = [
                'text' => $text,
                'disable_web_page_preview' => true,
                'reply_to_message_id' => null,
                'reply_markup' => json_encode(
                    [
                        'inline_keyboard' => $temp['keyboard'],
                    ]
                ),
            ];
        } else {
            $temp = $this->getUserSubscribeKeyboard();
            $sendMessage = [
                'text' => $temp['text'],
                'disable_web_page_preview' => false,
                'reply_to_message_id' => null,
                'reply_markup' => json_encode(
                    [
                        'inline_keyboard' => $temp['keyboard'],
                    ]
                ),
            ];
        }

        $this->replyWithMessage(
            array_merge(
                [
                    'parse_mode' => 'HTML',
                ],
                $sendMessage
            )
        );
    }

    public function getUserInviteKeyboard(): array
    {
        $paybacks_sum = Payback::where('ref_by', $this->user->id)->sum('ref_get');

        if (is_null($paybacks_sum)) {
            $paybacks_sum = 0;
        }

        $invite = Setting::getClass('invite');
        $text = [
            '<strong>你每邀请 1 位用户注册：</strong>',
            '',
            '- 你会获得 <strong>' . $invite['invitation_to_register_traffic_reward'] . 'G</strong> 流量奖励。',
            '- 对方将获得 <strong>' . $invite['invitation_to_register_balance_reward'] . ' 元</strong> 初始账户余额。',
            '- 对方充值时你还会获得对方充值金额的 <strong>' . $invite['rebate_ratio'] * 100 . '%</strong> 的返利。',
            '',
            '已获得返利：' . $paybacks_sum . ' 元。',
        ];
        $keyboard = [
            [
                [
                    'text' => '获取我的邀请链接',
                    'callback_data' => 'user.invite.get',
                ],
            ],
            [
                [
                    'text' => '回主菜单',
                    'callback_data' => 'user.index',
                ],
            ],
        ];

        return [
            'text' => implode(PHP_EOL, $text),
            'keyboard' => $keyboard,
        ];
    }

    /**
     * 分享计划
     *
     * @throws TelegramSDKException
     */
    public function userInvite(): void
    {
        $CallbackDataExplode = explode('|', $this->callback_data);
        $Operate = explode('.', $CallbackDataExplode[0]);
        $OpEnd = end($Operate);

        if ($OpEnd === 'get') {
            $this->allow_edit_message = false;
            $code = InviteCode::where('user_id', $this->user->id)->first();

            if ($code === null) {
                $this->user->addInviteCode();
                $code = InviteCode::where('user_id', $this->user->id)->first();
            }

            $inviteUrl = $_ENV['baseUrl'] . '/auth/register?code=' . $code->code;
            $text = '<a href="' . $inviteUrl . '">' . $inviteUrl . '</a>';

            $sendMessage = [
                'text' => $text,
                'disable_web_page_preview' => false,
                'reply_to_message_id' => null,
                'reply_markup' => null,
            ];
        } else {
            $temp = $this->getUserInviteKeyboard();
            $sendMessage = [
                'text' => $temp['text'],
                'disable_web_page_preview' => false,
                'reply_to_message_id' => null,
                'reply_markup' => json_encode(
                    [
                        'inline_keyboard' => $temp['keyboard'],
                    ]
                ),
            ];
        }

        $this->replyWithMessage(
            array_merge(
                [
                    'parse_mode' => 'HTML',
                ],
                $sendMessage
            )
        );
    }

    /**
     * 每日签到
     *
     * @throws TelegramSDKException
     */
    public function userCheckin(): void
    {
        $checkin = $this->user->checkin();
        $this->answerCallbackQuery([
            'text' => $checkin['msg'],
            'show_alert' => true,
        ]);
        // 回送信息
        if ($this->chat_id > 0) {
            $temp = self::getUserIndexKeyboard($this->user);
        } else {
            $temp['text'] = Message::getUserTitle($this->user);
            $temp['text'] .= PHP_EOL . PHP_EOL;
            $temp['text'] .= Message::getUserTrafficInfo($this->user);
            $temp['keyboard'] = [
                [
                    [
                        'text' => (! $this->user->isAbleToCheckin() ? '已签到' : '签到'),
                        'callback_data' => 'user.checkin.' . $this->trigger_user['id'],
                    ],
                ],
            ];
        }

        $this->replyWithMessage([
            'text' => $temp['text'] . PHP_EOL . PHP_EOL . $checkin['msg'],
            'reply_to_message_id' => $this->message_id,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode(
                [
                    'inline_keyboard' => $temp['keyboard'],
                ]
            ),
        ]);
    }
}
