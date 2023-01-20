<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Mail;
use App\Utils\GA;
use App\Utils\Hash;
use App\Utils\Telegram;
use App\Utils\Tools;
use Exception;
use Ramsey\Uuid\Uuid;

/**
 * User Model
 *
 * @property-read   int     $id         ID
 *
 * @todo More property
 *
 * @property        bool    $is_admin           是否管理员
 * @property        bool    $expire_notified    If user is notified for expire
 * @property        bool    $traffic_notified   If user is noticed for low traffic
 */
final class User extends Model
{
    /**
     * 已登录
     *
     * @var bool
     */
    public $isLogin;
    protected $connection = 'default';

    protected $table = 'user';

    /**
     * 强制类型转换
     *
     * @var array
     */
    protected $casts = [
        'port' => 'int',
        'is_admin' => 'boolean',
        'node_speedlimit' => 'float',
        'sendDailyMail' => 'int',
        'ref_by' => 'int',
    ];

    /**
     * Gravatar 头像地址
     */
    public function getGravatarAttribute(): string
    {
        $hash = md5(strtolower(trim($this->email)));
        return 'https://www.gravatar.com/avatar/' . $hash . '?&d=identicon';
    }

    /**
     * 联系方式类型
     */
    public function imType(): string
    {
        switch ($this->im_type) {
            case 1:
                return '微信';
            case 2:
                return 'QQ';
            case 5:
                return 'Discord';
            default:
                return 'Telegram';
        }
    }

    /**
     * 联系方式
     */
    public function imValue(): string
    {
        switch ($this->im_type) {
            case 1:
            case 2:
            case 5:
                return $this->im_value;
            default:
                return '<a href="https://telegram.me/' . $this->im_value . '">' . $this->im_value . '</a>';
        }
    }

    /**
     * 最后使用时间
     */
    public function lastSsTime(): string
    {
        return $this->t === 0 || $this->t === null ? '从未使用喵' : Tools::toDateTime($this->t);
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

    public function getForbiddenIp()
    {
        return str_replace(',', PHP_EOL, $this->forbidden_ip);
    }

    public function getForbiddenPort()
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
    public function generateUUID($s): bool
    {
        $this->uuid = Uuid::uuid3(
            Uuid::NAMESPACE_DNS,
            $this->email . '|' . $s
        );
        return $this->save();
    }

    /**
     * 生成新的 API Token
     */
    public function generateApiToken($s): bool
    {
        $this->api_token = Uuid::uuid3(
            Uuid::NAMESPACE_DNS,
            $this->pass . '|' . $s
        );
        return $this->save();
    }

    /*
     * 总流量[自动单位]
     */
    public function enableTraffic(): string
    {
        return Tools::flowAutoShow($this->transfer_enable);
    }

    /*
     * 总流量[GB]，不含单位
     */
    public function enableTrafficInGB(): float
    {
        return Tools::flowToGB($this->transfer_enable);
    }

    /*
     * 已用流量[自动单位]
     */
    public function usedTraffic(): string
    {
        return Tools::flowAutoShow($this->u + $this->d);
    }

    /*
     * 已用流量占总流量的百分比
     */
    public function trafficUsagePercent(): int
    {
        if ($this->transfer_enable == 0) {
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
        return Tools::flowAutoShow($this->transfer_enable - ($this->u + $this->d));
    }

    /*
     * 剩余流量占总流量的百分比
     */
    public function unusedTrafficPercent(): float
    {
        if ($this->transfer_enable == 0) {
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
        return Tools::flowAutoShow($this->u + $this->d - $this->last_day_t);
    }

    /*
     * 今天使用的流量占总流量的百分比
     */
    public function todayUsedTrafficPercent(): float
    {
        if ($this->transfer_enable == 0 || $this->transfer_enable === '0' || $this->transfer_enable === null) {
            return 0;
        }
        $Todayused = $this->u + $this->d - $this->last_day_t;
        $percent = $Todayused / $this->transfer_enable;
        $percent = round($percent, 4);
        return $percent * 100;
    }

    /*
     * 今天之前已使用的流量[自动单位]
     */
    public function lastUsedTraffic(): string
    {
        return Tools::flowAutoShow($this->last_day_t);
    }

    /*
     * 今天之前已使用的流量占总流量的百分比
     */
    public function lastUsedTrafficPercent(): float
    {
        if ($this->transfer_enable == 0 || $this->transfer_enable === '0' || $this->transfer_enable === null) {
            return 0;
        }
        $Lastused = $this->last_day_t;
        $percent = $Lastused / $this->transfer_enable;
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

    public function getGAurl()
    {
        $ga = new GA();
        return $ga->getUrl(
            urlencode($_ENV['appName'] . '-' . $this->user_name . '-两步验证码'),
            $this->ga_token
        );
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
     * 获取用户的订阅链接
     */
    public function getSublink()
    {
        return Tools::generateSSRSubCode($this->id);
    }

    /**
     * 删除用户的邀请码
     */
    public function clearInviteCodes(): void
    {
        InviteCode::where('user_id', $this->id)->delete();
    }

    /**
     * 在线 IP 个数
     */
    public function onlineIpCount(): int
    {
        // 根据 IP 分组去重
        $total = Ip::where('datetime', '>=', \time() - 90)->where('userid', $this->id)->orderBy('userid', 'desc')->groupBy('ip')->get();
        $ip_list = [];
        foreach ($total as $single_record) {
            $ip = Tools::getRealIp($single_record->ip);
            if (Node::where('node_ip', $ip)->first() !== null) {
                continue;
            }
            $ip_list[] = $ip;
        }
        return count($ip_list);
    }

    /**
     * 销户
     */
    public function killUser(): bool
    {
        $uid = $this->id;
        $email = $this->email;

        Bought::where('userid', '=', $uid)->delete();
        Code::where('userid', '=', $uid)->delete();
        DetectBanLog::where('user_id', '=', $uid)->delete();
        DetectLog::where('user_id', '=', $uid)->delete();
        EmailVerify::where('email', $email)->delete();
        InviteCode::where('user_id', '=', $uid)->delete();
        Ip::where('userid', '=', $uid)->delete();
        Link::where('userid', '=', $uid)->delete();
        LoginIp::where('userid', '=', $uid)->delete();
        PasswordReset::where('email', '=', $email)->delete();
        TelegramSession::where('user_id', '=', $uid)->delete();
        Token::where('user_id', '=', $uid)->delete();
        UserSubscribeLog::where('user_id', '=', $uid)->delete();

        $this->delete();

        return true;
    }

    /**
     * 累计充值金额
     */
    public function getTopUp(): float
    {
        $number = Code::where('userid', $this->id)->sum('number');
        return is_null($number) ? 0.00 : round((float) $number, 2);
    }

    /**
     * 获取累计收入
     */
    public function calIncome(string $req): float
    {
        switch ($req) {
            case 'yesterday':
                $number = Code::whereDate('usedatetime', '=', date('Y-m-d', strtotime('-1 days')))->sum('number');
                break;
            case 'today':
                $number = Code::whereDate('usedatetime', '=', date('Y-m-d'))->sum('number');
                break;
            case 'this month':
                $number = Code::whereYear('usedatetime', '=', date('Y'))->whereMonth('usedatetime', '=', date('m'))->sum('number');
                break;
            case 'last month':
                $number = Code::whereYear('usedatetime', '=', date('Y'))->whereMonth('usedatetime', '=', date('m', strtotime('last month')))->sum('number');
                break;
            default:
                $number = Code::sum('number');
                break;
        }
        return is_null($number) ? 0.00 : round(floatval($number), 2);
    }

    /**
     * 获取付费用户总数
     */
    public function paidUserCount(): int
    {
        return self::where('class', '!=', '0')->count();
    }

    /**
     * 获取用户被封禁的理由
     */
    public function disableReason(): string
    {
        $reason_id = DetectLog::where('user_id', $this->id)->orderBy('id', 'DESC')->first();
        $reason = DetectRule::find($reason_id->list_id);
        if (is_null($reason)) {
            return '特殊原因被禁用，了解详情请联系管理员';
        }
        return $reason->text;
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
            'msg' => '',
        ];
        if (! $this->isAbleToCheckin()) {
            $return['ok'] = false;
            $return['msg'] = '您似乎已经签到过了...';
        } else {
            $traffic = random_int((int) $_ENV['checkinMin'], (int) $_ENV['checkinMax']);
            $this->transfer_enable += Tools::toMB($traffic);
            $this->last_check_in_time = \time();
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
                $_ENV['enable_telegram'] === true
                &&
                Setting::obtain('telegram_group_bound_user') === true
                &&
                Setting::obtain('telegram_unbind_kick_member') === true
                &&
                ! $this->is_admin
            ) {
                \App\Utils\Telegram\TelegramTools::SendPost(
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
        if (\in_array($Port, $PortOccupied) === true) {
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
     * 用户下次流量重置时间
     */
    public function validUseLoop(): string
    {
        $boughts = Bought::where('userid', $this->id)->orderBy('id', 'desc')->get();
        $data = [];
        foreach ($boughts as $bought) {
            $shop = $bought->shop();
            if ($shop !== null && $bought->valid()) {
                $data[] = $bought->resetTime();
            }
        }
        if (count($data) === 0) {
            return '未购买套餐.';
        }
        if (count($data) === 1) {
            return $data[0];
        }
        return '多个有效套餐无法显示.';
    }

    /**
     * 手动修改用户余额时增加充值记录，受限于 Config
     *
     * @param mixed $total 金额
     */
    public function addMoneyLog($total): void
    {
        if ($_ENV['money_from_admin'] && $total !== 0.00) {
            $codeq = new Code();
            $codeq->code = ($total > 0 ? '管理员赏赐' : '管理员惩戒');
            $codeq->isused = 1;
            $codeq->type = -1;
            $codeq->number = $total;
            $codeq->usedatetime = date('Y-m-d H:i:s');
            $codeq->userid = $this->id;
            $codeq->save();
        }
    }

    /**
     * 发送邮件
     *
     * @param array  $array
     * @param array  $files
     */
    public function sendMail(string $subject, string $template, array $array = [], array $files = [], $is_queue = false): bool
    {
        if ($is_queue) {
            $emailqueue = new EmailQueue();
            $emailqueue->to_email = $this->email;
            $emailqueue->subject = $subject;
            $emailqueue->template = $template;
            $emailqueue->time = \time();
            $array = array_merge(['user' => $this], $array);
            $emailqueue->array = \json_encode($array);
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
            } catch (Exception $e) {
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
        switch ($this->sendDailyMail) {
            case 0:
                return;
            case 1:
                echo 'Send daily mail to user: ' . $this->id;
                $this->sendMail(
                    $_ENV['appName'] . '-每日流量报告以及公告',
                    'news/daily-traffic-report.tpl',
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
        $loginip->datetime = \time();
        $loginip->type = $type;

        return $loginip->save();
    }
}
