<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\IM;
use App\Utils\Hash;
use App\Utils\Tools;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Ramsey\Uuid\Uuid;
use Telegram\Bot\Exceptions\TelegramSDKException;
use function date;
use function is_null;
use function md5;
use function random_int;
use function round;
use function time;
use const PHP_EOL;

/**
 * @property int    $id 用户ID
 * @property string $user_name 用户名
 * @property string $email E-Mail
 * @property string $pass 登录密码
 * @property string $passwd 节点密码
 * @property string $uuid UUID
 * @property int    $u 账户当前上传流量
 * @property int    $d 账户当前下载流量
 * @property int    $transfer_today 账户今日所用流量
 * @property int    $transfer_total 账户累计使用流量
 * @property int    $transfer_enable 账户当前可用流量
 * @property int    $port 端口
 * @property string $last_detect_ban_time 最后一次被封禁的时间
 * @property int    $all_detect_number 累计违规次数
 * @property int    $last_use_time 最后使用时间
 * @property int    $last_check_in_time 最后签到时间
 * @property int    $last_login_time 最后登录时间
 * @property string $reg_date 注册时间
 * @property int    $invite_num 可用邀请次数
 * @property float  $money 账户余额
 * @property int    $ref_by 邀请人ID
 * @property string $method Shadowsocks加密方式
 * @property string $reg_ip 注册IP
 * @property float  $node_speedlimit 用户限速
 * @property int    $node_iplimit 同时可连接IP数
 * @property int    $is_admin 是否管理员
 * @property int    $im_type 联系方式类型
 * @property string $im_value 联系方式
 * @property int    $contact_method 偏好的联系方式
 * @property int    $daily_mail_enable 每日报告开关
 * @property int    $class 等级
 * @property string $class_expire 等级过期时间
 * @property string $theme 网站主题
 * @property string $ga_token GA密钥
 * @property int    $ga_enable GA开关
 * @property string $remark 备注
 * @property int    $node_group 节点分组
 * @property int    $is_banned 是否封禁
 * @property string $banned_reason 封禁理由
 * @property int    $is_shadow_banned 是否处于账户异常状态
 * @property int    $expire_notified 过期提醒
 * @property int    $traffic_notified 流量提醒
 * @property string $forbidden_ip 禁止访问IP
 * @property string $forbidden_port 禁止访问端口
 * @property int    $auto_reset_day 自动重置流量日
 * @property float  $auto_reset_bandwidth 自动重置流量
 * @property string $api_token API 密钥
 * @property int    $is_dark_mode 是否启用暗黑模式
 * @property int    $is_inactive 是否处于闲置状态
 * @property string $locale 显示语言
 *
 * @mixin Builder
 */
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
        'money' => 'float',
        'port' => 'int',
        'node_speedlimit' => 'float',
        'daily_mail_enable' => 'int',
        'ref_by' => 'int',
    ];

    /**
     * @param $len
     *
     * @return string
     */
    public function getSs2022Pk($len): string
    {
        return Tools::genSs2022UserPk($this->passwd, $len);
    }

    public function getUserFrontEndNodes(): Collection
    {
        $query = Node::query();
        $query->where('type', 1);

        if (! $this->is_admin) {
            $group = ($this->node_group !== 0 ? [0, $this->node_group] : [0]);
            $query->whereIn('node_group', $group);
        }

        return $query->where(static function ($query): void {
            $query->where('node_bandwidth_limit', '=', 0)->orWhereRaw('node_bandwidth < node_bandwidth_limit');
        })->orderBy('node_class')
            ->orderBy('name')
            ->get();
    }

    /**
     * DiceBear 头像
     */
    public function getDiceBearAttribute(): string
    {
        return 'https://api.dicebear.com/7.x/identicon/svg?seed=' . md5($this->email);
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
        (new Link())->where('userid', $this->id)->delete();
    }

    /**
     * 删除用户的邀请码
     */
    public function clearInviteCodes(): void
    {
        (new InviteCode())->where('user_id', $this->id)->delete();
    }

    /**
     * 累计充值金额
     */
    public function getTopUp(): float
    {
        $number = (new Paylist())->where('userid', $this->id)->sum('number');
        return is_null($number) ? 0.00 : round((float) $number, 2);
    }

    /**
     * 在线 IP 个数
     */
    public function onlineIpCount(): int
    {
        return (new OnlineLog())->where('user_id', $this->id)
            ->where('last_time', '>', time() - 90)
            ->count();
    }

    /**
     * 销户
     */
    public function kill(): bool
    {
        $uid = $this->id;

        (new DetectBanLog())->where('user_id', $uid)->delete();
        (new DetectLog())->where('user_id', $uid)->delete();
        (new InviteCode())->where('user_id', $uid)->delete();
        (new OnlineLog())->where('user_id', $uid)->delete();
        (new Link())->where('userid', $uid)->delete();
        (new LoginIp())->where('userid', $uid)->delete();
        (new SubscribeLog())->where('user_id', $uid)->delete();

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
}
