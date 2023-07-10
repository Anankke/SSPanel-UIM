<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\DB;
use App\Services\Mail;
use App\Utils\Hash;
use App\Utils\Telegram;
use App\Utils\Telegram\TelegramTools;
use App\Utils\Tools;
use Exception;
use Psr\Http\Client\ClientExceptionInterface;
use Ramsey\Uuid\Uuid;
use function array_merge;
use function date;
use function in_array;
use function is_null;
use function json_encode;
use function md5;
use function random_int;
use function round;
use function str_replace;
use function time;
use const PHP_EOL;

final class User extends Model
{
    /**
     * 已登录
     *
     * @var bool
     */
    public bool $isLogin;

    protected $connection = 'default';
    protected $table = 'user';

    /**
     * 强制类型转换
     *
     * @var array
     */
    protected $casts = [
        'port' => 'int',
        'node_speedlimit' => 'float',
        'daily_mail_enable' => 'int',
        'ref_by' => 'int',
    ];

    /**
     * DiceBear 头像
     */
    public function getDiceBearAttribute(): string
    {
        return 'https://api.dicebear.com/6.x/identicon/svg?seed=' . md5($this->email);
    }

    /**
     * 联系方式类型
     */
    public function imType(): string
    {
        return match ($this->im_type) {
            1 => '微信',
            2 => 'QQ',
            5 => 'Discord',
            default => 'Telegram',
        };
    }

    /**
     * 联系方式
     */
    public function imValue(): string
    {
        return match ($this->im_type) {
            1, 2, 5 => $this->im_value,
            default => '<a href="https://telegram.me/' . $this->im_value . '">' . $this->im_value . '</a>',
        };
    }

    /**
     * 最后使用时间
     */
    public function lastUseTime(): string
    {
        return $this->last_use_time === 0 ? '从未使用' : Tools::toDateTime($this->last_use_time);
    }

    /**
     * 最后签到时间
     */
    public function lastCheckInTime(): string
    {
        return $this->last_check_in_time === 0 ? '从未签到' : Tools::toDateTime($this->last_check_in_time);
    }

    /**
     * 更新密码
     */
    public function updatePassword(string $pwd): bool
    {
        $this->pass = Hash::passwordHash($pwd);
        return $this->save();
    }

    public function getForbiddenIp(): array|string
    {
        return str_replace(',', PHP_EOL, $this->forbidden_ip);
    }

    public function getForbiddenPort(): array|string
    {
        return str_replace(',', PHP_EOL, $this->forbidden_port);
    }

    /**
     * 生成邀请码
     */
    public function addInviteCode(): string
    {
        while (true) {
            $temp_code = Tools::genRandomChar(10);
            if (InviteCode::where('code', $temp_code)->first() === null) {
                if (InviteCode::where('user_id', $this->id)->count() === 0) {
                    $code = new InviteCode();
                    $code->code = $temp_code;
                    $code->user_id = $this->id;
                    $code->save();
                    return $temp_code;
                }
                return InviteCode::where('user_id', $this->id)->first()->code;
            }
        }
    }

    /**
     * 添加邀请次数
     */
    public function addInviteNum(int $num): bool
    {
        $this->invite_num += $num;
        return $this->save();
    }

    /**
     * 生成新的 UUID
     */
    public function generateUUID(): bool
    {
        for ($i = 0; $i < 10; $i++) {
            $uuid = Uuid::uuid4();
            $is_uuid_used = User::where('uuid', $uuid)->first();

            if ($is_uuid_used === null) {
                $this->uuid = Uuid::uuid4();
                return $this->save();
            }
        }

        return false;
    }

    /**
     * 生成新的 API Token
     */
    public function generateApiToken(): bool
    {
        for ($i = 0; $i < 10; $i++) {
            $api_token = Uuid::uuid4();
            $is_api_token_used = User::where('api_token', $api_token)->first();

            if ($is_api_token_used === null) {
                $this->api_token = Uuid::uuid4();
                return $this->save();
            }
        }

        return false;
    }

    /*
     * 总流量[自动单位]
     */
    public function enableTraffic(): string
    {
        return Tools::autoBytes($this->transfer_enable);
    }

    /*
     * 总流量[GB]，不含单位
     */
    public function enableTrafficInGB(): float
    {
        return Tools::flowToGB($this->transfer_enable);
    }

    /*
     * 当期用量[自动单位]
     */
    public function usedTraffic(): string
    {
        return Tools::autoBytes($this->u + $this->d);
    }

    /*
     * 累计用量[自动单位]
     */
    public function totalTraffic(): string
    {
        return Tools::autoBytes($this->transfer_total);
    }

    /*
     * 已用流量占总流量的百分比
     */
    public function trafficUsagePercent(): int
    {
        if ($this->transfer_enable === 0) {
            return 0;
        }
        $percent = ($this->u + $this->d) / $this->transfer_enable;
        $percent = round($percent, 2);
        return (int) $percent * 100;
    }

    /*
     * 剩余流量[自动单位]
     */
    public function unusedTraffic(): string
    {
        return Tools::autoBytes($this->transfer_enable - ($this->u + $this->d));
    }

    /*
     * 剩余流量占总流量的百分比
     */
    public function unusedTrafficPercent(): float
    {
        if ($this->transfer_enable === 0) {
            return 0;
        }
        $unused = $this->transfer_enable - ($this->u + $this->d);
        $percent = $unused / $this->transfer_enable;
        $percent = round($percent, 4);
        return $percent * 100;
    }

    /*
     * 今天使用的流量[自动单位]
     */
    public function todayUsedTraffic(): string
    {
        return Tools::autoBytes($this->transfer_today);
    }

    /*
     * 今天使用的流量占总流量的百分比
     */
    public function todayUsedTrafficPercent(): float
    {
        if ($this->transfer_enable === 0 || $this->transfer_enable === null) {
            return 0;
        }
        $percent = $this->transfer_today / $this->transfer_enable;
        $percent = round($percent, 4);
        return $percent * 100;
    }

    /*
     * 今天之前已使用的流量[自动单位]
     */
    public function lastUsedTraffic(): string
    {
        return Tools::autoBytes($this->u + $this->d - $this->transfer_today);
    }

    /*
     * 今天之前已使用的流量占总流量的百分比
     */
    public function lastUsedTrafficPercent(): float
    {
        if ($this->transfer_enable === 0 || $this->transfer_enable === null) {
            return 0;
        }
        $percent = ($this->u + $this->d - $this->transfer_today) / $this->transfer_enable;
        $percent = round($percent, 4);
        return $percent * 100;
    }

    /*
     * 是否可以签到
     */
    public function isAbleToCheckin(): bool
    {
        return date('Ymd') !== date('Ymd', $this->last_check_in_time);
    }

    /**
     * 获取用户的邀请码
     */
    public function getInviteCodes(): ?InviteCode
    {
        return InviteCode::where('user_id', $this->id)->first();
    }

    /**
     * 用户的邀请人
     */
    public function refByUser(): ?User
    {
        return self::find($this->ref_by);
    }

    /**
     * 用户邀请人的用户名
     */
    public function refByUserName(): string
    {
        if ($this->ref_by === 0) {
            return '系统邀请';
        }

        $refUser = $this->refByUser();

        if ($refUser === null) {
            return '邀请人已经被删除';
        }
        return $refUser->user_name;
    }

    /**
     * 删除用户的订阅链接
     */
    public function cleanLink(): void
    {
        Link::where('userid', $this->id)->delete();
    }

    /**
     * 删除用户的邀请码
     */
    public function clearInviteCodes(): void
    {
        InviteCode::where('user_id', $this->id)->delete();
    }

    /**
     * 累计充值金额
     */
    public function getTopUp(): float
    {
        $number = Paylist::where('userid', $this->id)->sum('number');
        return is_null($number) ? 0.00 : round((float) $number, 2);
    }

    /**
     * 在线 IP 个数
     */
    public function onlineIpCount(): int
    {
        return DB::select(
            '
            SELECT
                COUNT(*) AS count
            FROM
                online_log
            WHERE
                user_id = ?
                AND last_time >= UNIX_TIMESTAMP() - 90',
            [$this->attributes['id']]
        )[0]->count;
    }

    /**
     * 销户
     */
    public function killUser(): bool
    {
        $uid = $this->id;
        $email = $this->email;

        DetectBanLog::where('user_id', '=', $uid)->delete();
        DetectLog::where('user_id', '=', $uid)->delete();
        InviteCode::where('user_id', '=', $uid)->delete();
        OnlineLog::where('user_id', '=', $uid)->delete();
        Link::where('userid', '=', $uid)->delete();
        LoginIp::where('userid', '=', $uid)->delete();
        UserSubscribeLog::where('user_id', '=', $uid)->delete();

        $this->delete();

        return true;
    }

    /**
     * 最后一次被封禁的时间
     */
    public function lastDetectBanTime(): string
    {
        return $this->last_detect_ban_time === '1989-06-04 00:05:00' ? '未被封禁过' : $this->last_detect_ban_time;
    }

    /**
     * 当前解封时间
     */
    public function relieveTime(): string
    {
        $logs = DetectBanLog::where('user_id', $this->id)->orderBy('id', 'desc')->first();
        if ($this->enable === 0 && $logs !== null) {
            $time = $logs->end_time + $logs->ban_time * 60;
            return date('Y-m-d H:i:s', $time);
        }
        return '当前未被封禁';
    }

    /**
     * 累计被封禁的次数
     */
    public function detectBanNumber(): int
    {
        return DetectBanLog::where('user_id', $this->id)->count();
    }

    /**
     * 最后一次封禁的违规次数
     */
    public function userDetectBanNumber(): int
    {
        $logs = DetectBanLog::where('user_id', $this->id)->orderBy('id', 'desc')->first();
        return $logs->detect_number;
    }

    /**
     * 签到
     */
    public function checkin(): array
    {
        $return = [
            'ok' => true,
        ];
        if (! $this->isAbleToCheckin()) {
            $return['ok'] = false;
            $return['msg'] = '你似乎已经签到过了...';
        } else {
            try {
                $traffic = random_int((int) $_ENV['checkinMin'], (int) $_ENV['checkinMax']);
            } catch (Exception $e) {
                $traffic = 0;
            }
            $this->transfer_enable += Tools::toMB($traffic);
            $this->last_check_in_time = time();
            $this->save();
            $return['msg'] = '获得了 ' . $traffic . 'MB 流量.';
        }

        return $return;
    }

    /**
     * 解绑 Telegram
     */
    public function telegramReset(): array
    {
        $return = [
            'ok' => true,
            'msg' => '解绑成功.',
        ];
        $telegram_id = $this->telegram_id;
        $this->telegram_id = 0;
        if ($this->save()) {
            if (
                $_ENV['enable_telegram']
                &&
                Setting::obtain('telegram_group_bound_user')
                &&
                Setting::obtain('telegram_unbind_kick_member')
                &&
                ! $this->is_admin
            ) {
                TelegramTools::SendPost(
                    'kickChatMember',
                    [
                        'chat_id' => $_ENV['telegram_chatid'],
                        'user_id' => $telegram_id,
                    ]
                );
            }
        } else {
            $return = [
                'ok' => false,
                'msg' => '解绑失败.',
            ];
        }

        return $return;
    }

    /**
     * 更新端口
     */
    public function setPort(int $Port): array
    {
        $PortOccupied = User::pluck('port')->toArray();
        if (in_array($Port, $PortOccupied)) {
            return [
                'ok' => false,
                'msg' => '端口已被占用',
            ];
        }
        $this->port = $Port;
        $this->save();
        return [
            'ok' => true,
            'msg' => $this->port,
        ];
    }

    /**
     * 发送邮件
     */
    public function sendMail(
        string $subject,
        string $template,
        array $array = [],
        array $files = [],
        $is_queue = false
    ): bool {
        if ($is_queue) {
            $emailqueue = new EmailQueue();
            $emailqueue->to_email = $this->email;
            $emailqueue->subject = $subject;
            $emailqueue->template = $template;
            $emailqueue->time = time();
            $array = array_merge(['user' => $this], $array);
            $emailqueue->array = json_encode($array);
            $emailqueue->save();
            return true;
        }
        // 验证邮箱地址是否正确
        if (Tools::isEmail($this->email)) {
            // 发送邮件
            try {
                Mail::send(
                    $this->email,
                    $subject,
                    $template,
                    array_merge(
                        [
                            'user' => $this,
                        ],
                        $array
                    ),
                    $files
                );
                return true;
            } catch (Exception | ClientExceptionInterface $e) {
                echo $e->getMessage();
            }
        }

        return false;
    }

    /**
     * 发送 Telegram 讯息
     */
    public function sendTelegram(string $text): bool
    {
        $result = false;
        try {
            if ($this->telegram_id > 0) {
                Telegram::send(
                    $text,
                    $this->telegram_id
                );
                $result = true;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $result;
    }

    /**
     * 发送每日流量报告
     *
     * @param string $ann 公告
     */
    public function sendDailyNotification(string $ann = ''): void
    {
        $lastday_traffic = $this->todayUsedTraffic();
        $enable_traffic = $this->enableTraffic();
        $used_traffic = $this->usedTraffic();
        $unused_traffic = $this->unusedTraffic();
        switch ($this->daily_mail_enable) {
            case 1:
                echo 'Send daily mail to user: ' . $this->id;
                $this->sendMail(
                    $_ENV['appName'] . '-每日流量报告以及公告',
                    'traffic_report.tpl',
                    [
                        'user' => $this,
                        'text' => '下面是系统中目前的最新公告:<br><br>' . $ann . '<br><br>晚安！',
                        'lastday_traffic' => $lastday_traffic,
                        'enable_traffic' => $enable_traffic,
                        'used_traffic' => $used_traffic,
                        'unused_traffic' => $unused_traffic,
                    ],
                    [],
                    true
                );
                break;
            case 2:
                echo 'Send daily Telegram message to user: ' . $this->id;
                $text = date('Y-m-d') . ' 流量使用报告' . PHP_EOL . PHP_EOL;
                $text .= '流量总计：' . $enable_traffic . PHP_EOL;
                $text .= '已用流量：' . $used_traffic . PHP_EOL;
                $text .= '剩余流量：' . $unused_traffic . PHP_EOL;
                $text .= '今日使用：' . $lastday_traffic;
                $this->sendTelegram(
                    $text
                );
                break;
            case 0:
            default:
        }
    }

    /**
     * 记录登录 IP
     *
     * @param int    $type 登录失败为 1
     */
    public function collectLoginIP(string $ip, int $type = 0): bool
    {
        $loginip = new LoginIp();
        $loginip->ip = $ip;
        $loginip->userid = $this->id;
        $loginip->datetime = time();
        $loginip->type = $type;

        if ($type === 0) {
            $this->last_login_time = time();
            $this->save();
        }

        return $loginip->save();
    }
}
