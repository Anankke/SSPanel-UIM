<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\DB;
use App\Services\IM;
use App\Utils\Hash;
use App\Utils\Tools;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Ramsey\Uuid\Uuid;
use Telegram\Bot\Exceptions\TelegramSDKException;
use function date;
use function is_null;
use function md5;
use function random_int;
use function round;
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
            1 => 'Slack',
            2 => 'Discord',
            default => 'Telegram',
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

    /**
     * 生成邀请码
     */
    public function addInviteCode(): string
    {
        $code = new InviteCode();
        $code->code = Tools::genRandomChar(10);
        $code->user_id = $this->id;
        $code->save();

        return $code->code;
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
        $this->uuid = Uuid::uuid4();

        return $this->save();
    }

    /**
     * 生成新的 API Token
     */
    public function generateApiToken(): bool
    {
        $this->api_token = Uuid::uuid4();

        return $this->save();
    }

    /*
     * 总流量[自动单位]
     */
    public function enableTraffic(): string
    {
        return Tools::autoBytes($this->transfer_enable);
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
    public function trafficUsagePercent(): float
    {
        return $this->transfer_enable === 0 ?
            0
            :
            round(($this->u + $this->d) / $this->transfer_enable, 2) * 100;
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
        return $this->transfer_enable === 0 ?
            0
            :
            round(($this->transfer_enable - ($this->u + $this->d)) / $this->transfer_enable, 2) * 100;
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
        return $this->transfer_enable === 0 ?
            0
            :
            round($this->transfer_today / $this->transfer_enable, 2) * 100;
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
        return $this->transfer_enable === 0 ?
            0
            :
            round(($this->u + $this->d - $this->transfer_today) / $this->transfer_enable, 2) * 100;
    }

    /*
     * 是否可以签到
     */
    public function isAbleToCheckin(): bool
    {
        return date('Ymd') !== date('Ymd', $this->last_check_in_time);
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
    public function kill(): bool
    {
        $uid = $this->id;

        DetectBanLog::where('user_id', $uid)->delete();
        DetectLog::where('user_id', $uid)->delete();
        InviteCode::where('user_id', $uid)->delete();
        OnlineLog::where('user_id', $uid)->delete();
        Link::where('userid', $uid)->delete();
        LoginIp::where('userid', $uid)->delete();
        SubscribeLog::where('user_id', $uid)->delete();

        return $this->delete();
    }

    /**
     * 签到
     */
    public function checkin(): array
    {
        $return = [
            'ok' => true,
        ];

        if (! $this->isAbleToCheckin() || $this->is_shadow_banned) {
            $return['ok'] = false;
            $return['msg'] = '签到失败，请稍后再试';
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

    public function unbindIM(): bool
    {
        $this->im_type = 0;
        $this->im_value = '';
        return $this->save();
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

        if ($this->daily_mail_enable === 1) {
            echo 'Send daily mail to user: ' . $this->id . PHP_EOL;

            (new EmailQueue())->add(
                $this->email,
                $_ENV['appName'] . '-每日流量报告以及公告',
                'traffic_report.tpl',
                [
                    'user' => $this,
                    'text' => '下面是系统中目前的最新公告:<br><br>' . $ann . '<br><br>晚安！',
                    'lastday_traffic' => $lastday_traffic,
                    'enable_traffic' => $enable_traffic,
                    'used_traffic' => $used_traffic,
                    'unused_traffic' => $unused_traffic,
                ]
            );
        } else {
            echo 'Send daily IM message to user: ' . $this->id . PHP_EOL;
            $text = date('Y-m-d') . ' 流量使用报告' . PHP_EOL . PHP_EOL;
            $text .= '流量总计：' . $enable_traffic . PHP_EOL;
            $text .= '已用流量：' . $used_traffic . PHP_EOL;
            $text .= '剩余流量：' . $unused_traffic . PHP_EOL;
            $text .= '今日使用：' . $lastday_traffic;

            try {
                IM::send($this->im_value, $text, $this->im_type);
            } catch (GuzzleException|TelegramSDKException $e) {
                echo $e->getMessage() . PHP_EOL;
            }
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
