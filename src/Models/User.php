<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\IM;
use App\Services\IM\Telegram;
use App\Utils\Tools;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Query\Builder;
use Telegram\Bot\Exceptions\TelegramSDKException;
use function date;
use function hash;
use function round;
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
 * @property float  $money 账户余额
 * @property int    $ref_by 邀请人ID
 * @property string $method Shadowsocks加密方式
 * @property string $reg_ip 注册IP
 * @property int    $node_speedlimit 用户限速
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
        'daily_mail_enable' => 'int',
        'ref_by' => 'int',
    ];

    /**
     * DiceBear 头像
     */
    public function getDiceBearAttribute(): string
    {
        return 'https://api.dicebear.com/8.x/identicon/svg?seed=' . hash('sha3-256', $this->email);
    }

    /**
     * User identifier
     */
    public function getIdentifierAttribute(): string
    {
        return hash('sha3-256', $this->id . ':' . $this->email);
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
        return date('Ymd') !== date('Ymd', $this->last_check_in_time) && ! $this->is_shadow_banned;
    }

    /**
     * 删除用户的订阅链接
     */
    public function removeLink(): void
    {
        (new Link())->where('userid', $this->id)->delete();
    }

    /**
     * 删除用户的邀请码
     */
    public function removeInvite(): void
    {
        (new InviteCode())->where('user_id', $this->id)->delete();
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

    public function unbindIM(): bool
    {
        $this->im_type = 0;
        $this->im_value = '';

        if ($this->im_type === 4 && Config::obtain('telegram_unbind_kick_member')) {
            try {
                (new Telegram())->banGroupMember((int) $this->im_value);
            } catch (TelegramSDKException) {
                return false;
            }
        }

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
            echo 'Sending daily mail to user: ' . $this->id . PHP_EOL;

            (new EmailQueue())->add(
                $this->email,
                $_ENV['appName'] . '-每日流量报告以及公告',
                'traffic_report.tpl',
                [
                    'user' => $this,
                    'text' => '站点公告:<br><br>' . $ann . '<br><br>晚安！',
                    'lastday_traffic' => $lastday_traffic,
                    'enable_traffic' => $enable_traffic,
                    'used_traffic' => $used_traffic,
                    'unused_traffic' => $unused_traffic,
                ]
            );
        } elseif ($this->daily_mail_enable === 2 && $this->im_value !== '') {
            echo 'Sending daily IM message to user: ' . $this->id . PHP_EOL;

            $text = date('Y-m-d') . ' 流量使用报告' . PHP_EOL . PHP_EOL;
            $text .= '流量总计：' . $enable_traffic . PHP_EOL;
            $text .= '已用流量：' . $used_traffic . PHP_EOL;
            $text .= '剩余流量：' . $unused_traffic . PHP_EOL;
            $text .= '今日使用：' . $lastday_traffic;

            try {
                IM::send((int) $this->im_value, $text, $this->im_type);
            } catch (GuzzleException|TelegramSDKException $e) {
                echo $e->getMessage() . PHP_EOL;
            }
        }
    }
}
