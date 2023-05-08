<?php

declare(strict_types=1);

namespace App\Command;

use App\Models\Ann;
use App\Models\DetectLog;
use App\Models\EmailQueue;
use App\Models\EmailVerify;
use App\Models\Node;
use App\Models\OnlineLog;
use App\Models\PasswordReset;
use App\Models\Setting;
use App\Models\StreamMedia;
use App\Models\TelegramSession;
use App\Models\User;
use App\Models\UserHourlyUsage;
use App\Models\UserSubscribeLog;
use App\Services\Analytics;
use App\Utils\Telegram;
use App\Utils\Tools;
use Exception;
use function count;
use function max;
use function str_replace;
use function strtotime;
use function time;

final class Job extends Command
{
    public string $description = <<<EOL
├─=: php xcat Job [选项]
│ ├─ DailyJob                - 每日任务，每天
│ ├─ UserJob                 - 账户相关任务，每小时
EOL;

    public function boot(): void
    {
        if (count($this->argv) === 2) {
            echo $this->description;
        } else {
            $methodName = $this->argv[2];
            if (method_exists($this, $methodName)) {
                $this->$methodName();
            } else {
                echo '方法不存在.' . PHP_EOL;
            }
        }
    }

    /**
     * 每日任务
     */
    public function DailyJob(): void
    {
        ini_set('memory_limit', '-1');

        // ------- 重置节点流量
        Node::where('bandwidthlimit_resetday', date('d'))->update(['node_bandwidth' => 0]);
        // ------- 重置节点流量

        // ------- 清理各表记录
        UserSubscribeLog::where('request_time', '<', date('Y-m-d H:i:s', time() - 86400 * (int) $_ENV['subscribeLog_keep_days']))->delete();
        UserHourlyUsage::where('datetime', '<', time() - 86400 * (int) $_ENV['trafficLog_keep_days'])->delete();
        DetectLog::where('datetime', '<', time() - 86400 * 3)->delete();
        EmailVerify::where('expire_in', '<', time() - 86400)->delete();
        EmailQueue::where('time', '<', time() - 86400)->delete();
        PasswordReset::where('expire_time', '<', time() - 86400)->delete();
        OnlineLog::where('last_time', '<', time() - 86400)->delete();
        StreamMedia::where('created_at', '<', time() - 86400)->delete();
        TelegramSession::where('datetime', '<', time() - 900)->delete();
        // ------- 清理各表记录

        // ------- 发送系统运行状况通知
        if (Setting::obtain('telegram_diary')) {
            $sts = new Analytics();
            try {
                Telegram::send(
                    str_replace(
                        [
                            '%getTodayCheckinUser%',
                            '%lastday_total%',
                        ],
                        [
                            $sts->getTodayCheckinUser(),
                            $sts->getTodayTrafficUsage(),
                        ],
                        Setting::obtain('telegram_diary_text')
                    )
                );
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        // ------- 发送系统运行状况通知

        // ------- 用户每日流量报告
        $users = User::all();

        // 判断是否有公告
        $ann_latest_raw = Ann::orderBy('date', 'desc')->first();

        if ($ann_latest_raw === null) {
            $ann_latest = '<br><br>';
        } else {
            $ann_latest = $ann_latest_raw->content . '<br><br>';
        }

        foreach ($users as $user) {
            // ------- 用户每日流量报告
            $user->sendDailyNotification($ann_latest);
            // ------- 用户每日流量报告

            // ------- 免费用户流量重置
            if ($user->class === 0 && date('d') === $user->auto_reset_day) {
                $user->u = 0;
                $user->d = 0;
                $user->transfer_enable = $user->auto_reset_bandwidth * 1024 * 1024 * 1024;
                $user->save();

                try {
                    $user->sendMail(
                        $_ENV['appName'] . '-你的免费流量被重置了',
                        'warn.tpl',
                        [
                            'text' => '你好，你的免费流量已经被重置为' . $user->auto_reset_bandwidth . 'GB',
                        ],
                        [],
                        true
                    );
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
            // ------- 免费用户流量重置
        }

        // 清空用户的当日使用流量
        User::query()->update(['transfer_today' => 0]);

        // ------- 发送每日任务运行报告
        if (Setting::obtain('telegram_daily_job')) {
            try {
                Telegram::send(Setting::obtain('telegram_daily_job_text'));
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        // ------- 发送每日系统运行报告
    }

    /**
     * 账户相关任务，每小时
     */
    public function UserJob(): void
    {
        $users = User::all();
        foreach ($users as $user) {
            //流量记录
            if ($_ENV['trafficLog']) {
                $transfer_total = $user->transfer_total;
                $transfer_total_last = UserHourlyUsage::where('user_id', $user->id)->orderBy('id', 'desc')->first();

                if ($transfer_total_last === null) {
                    $transfer_total_last = 0;
                } else {
                    $transfer_total_last = $transfer_total_last->traffic;
                }

                $trafficlog = new UserHourlyUsage();
                $trafficlog->user_id = $user->id;
                $trafficlog->traffic = $transfer_total;
                $trafficlog->hourly_usage = $transfer_total - $transfer_total_last;
                $trafficlog->datetime = time();
                $trafficlog->save();
            }

            if (strtotime($user->expire_in) < time() && ! $user->expire_notified) {
                $user->transfer_enable = 0;
                $user->u = 0;
                $user->d = 0;
                $user->transfer_today = 0;

                $user->sendMail(
                    $_ENV['appName'] . '-你的账户已经过期了',
                    'warn.tpl',
                    [
                        'text' => '你好，系统发现你的账号已经过期了。',
                    ],
                    [],
                    true
                );

                $user->expire_notified = true;
                $user->save();
            } elseif (strtotime($user->expire_in) > time() && $user->expire_notified) {
                $user->expire_notified = false;
                $user->save();
            }
            //余量不足检测
            if ($_ENV['notify_limit_mode'] !== false) {
                $user_traffic_left = $user->transfer_enable - $user->u - $user->d;
                $under_limit = false;
                $unit_text = '';

                if ($user->transfer_enable !== 0 && $user->class !== 0) {
                    if (
                        $_ENV['notify_limit_mode'] === 'per' &&
                        $user_traffic_left / $user->transfer_enable * 100 < $_ENV['notify_limit_value']
                    ) {
                        $under_limit = true;
                        $unit_text = '%';
                    } elseif (
                        $_ENV['notify_limit_mode'] === 'mb' &&
                        Tools::flowToMB($user_traffic_left) < $_ENV['notify_limit_value']
                    ) {
                        $under_limit = true;
                        $unit_text = 'MB';
                    }
                }

                if ($under_limit && ! $user->traffic_notified) {
                    $result = $user->sendMail(
                        $_ENV['appName'] . '-你的剩余流量过低',
                        'warn.tpl',
                        [
                            'text' => '你好，系统发现你剩余流量已经低于 ' . $_ENV['notify_limit_value'] . $unit_text . ' 。',
                        ],
                        [],
                        true
                    );
                    if ($result) {
                        $user->traffic_notified = true;
                        $user->save();
                    }
                } elseif (! $under_limit && $user->traffic_notified) {
                    $user->traffic_notified = false;
                    $user->save();
                }
            }

            if (
                $_ENV['account_expire_delete_days'] >= 0 &&
                strtotime($user->expire_in) + $_ENV['account_expire_delete_days'] * 86400 < time() &&
                $user->money <= $_ENV['auto_clean_min_money']
            ) {
                $user->sendMail(
                    $_ENV['appName'] . '-你的账户因为过期被删除了',
                    'warn.tpl',
                    [
                        'text' => '你好，系统发现你的账户已经过期 ' . $_ENV['account_expire_delete_days'] . ' 天了，帐号已经被删除。',
                    ],
                    [],
                    true
                );
                $user->killUser();
                continue;
            }

            if (
                $_ENV['auto_clean_uncheck_days'] > 0 &&
                max(
                    $user->last_check_in_time,
                    strtotime($user->reg_date)
                ) + ($_ENV['auto_clean_uncheck_days'] * 86400) < time() &&
                $user->class === 0 &&
                $user->money <= $_ENV['auto_clean_min_money']
            ) {
                $user->sendMail(
                    $_ENV['appName'] . '-你的账户因为未签到被删除了',
                    'warn.tpl',
                    [
                        'text' => '你好，系统发现你的账号已经 ' . $_ENV['auto_clean_uncheck_days'] . ' 天没签到了，帐号已经被删除。',
                    ],
                    [],
                    true
                );
                $user->killUser();
                continue;
            }

            if (
                $_ENV['auto_clean_unused_days'] > 0 &&
                max($user->t, strtotime($user->reg_date)) + ($_ENV['auto_clean_unused_days'] * 86400) < time() &&
                $user->class === 0 &&
                $user->money <= $_ENV['auto_clean_min_money']
            ) {
                $user->sendMail(
                    $_ENV['appName'] . '-你的账户因为闲置被删除了',
                    'warn.tpl',
                    [
                        'text' => '你好，系统发现你的账号已经 ' . $_ENV['auto_clean_unused_days'] . ' 天没使用了，帐号已经被删除。',
                    ],
                    [],
                    true
                );
                $user->killUser();
                continue;
            }

            if (
                $user->class !== 0 &&
                strtotime($user->class_expire) < time()
            ) {
                $text = '你好，系统发现你的账号等级已经过期了。';
                $reset_traffic = $_ENV['class_expire_reset_traffic'];
                if ($reset_traffic >= 0) {
                    $user->transfer_enable = Tools::toGB($reset_traffic);
                    $user->u = 0;
                    $user->d = 0;
                    $user->transfer_today = 0;
                    $text .= '流量已经被重置为' . $reset_traffic . 'GB';
                }
                $user->sendMail(
                    $_ENV['appName'] . '-你的账户等级已经过期了',
                    'warn.tpl',
                    [
                        'text' => $text,
                    ],
                    [],
                    true
                );
                $user->class = 0;
            }

            $user->save();
        }
    }
}
