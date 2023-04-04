<?php

declare(strict_types=1);

namespace App\Command;

use App\Models\Ann;
use App\Models\Bought;
use App\Models\DetectBanLog;
use App\Models\DetectLog;
use App\Models\EmailQueue;
use App\Models\EmailVerify;
use App\Models\Node;
use App\Models\OnlineLog;
use App\Models\PasswordReset;
use App\Models\Setting;
use App\Models\Shop;
use App\Models\StreamMedia;
use App\Models\TelegramSession;
use App\Models\User;
use App\Models\UserHourlyUsage;
use App\Models\UserSubscribeLog;
use App\Services\Analytics;
use App\Services\DB;
use App\Services\Mail;
use App\Utils\Telegram;
use App\Utils\Tools;
use Exception;
use Psr\Http\Client\ClientExceptionInterface;
use Telegram\Bot\Exceptions\TelegramSDKException;
use function count;
use function in_array;
use function json_decode;
use function max;
use function strtotime;
use function time;

final class Job extends Command
{
    public string $description = <<<EOL
├─=: php xcat Job [选项]
│ ├─ DailyJob                - 每日任务，每天
│ ├─ CheckJob                - 检查任务，每分钟
│ ├─ UserJob                 - 用户账户相关任务，每小时
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

        // ------- 用户每日流量报告
        $users = User::all();

        $ann_latest_raw = Ann::orderBy('date', 'desc')->first();

        // 判断是否有公告
        if ($ann_latest_raw === null) {
            $ann_latest = '<br><br>';
        } else {
            $ann_latest = $ann_latest_raw->content . '<br><br>';
        }

        $lastday_total = 0;

        foreach ($users as $user) {
            // 将用户日流量加到统计中
            $lastday_total += $user->u + $user->d - $user->last_day_t;
            $user->sendDailyNotification($ann_latest);
            // 覆盖用户 last_day_t 值，为下一个周期的流量重置做准备
            $user->last_day_t = $user->u + $user->d;
            $user->save();
        }
        // ------- 用户每日流量报告

        // ------- 付费用户流量重置
        // 取消已下架的商品不支持重置的限制，因为目前没有库存限制
        $shopid = Shop::where('content->reset', '<>', 0)->where('content->reset_value', '<>', 0)->where('content->reset_exp', '<>', 0)->pluck('id')->toArray();
        // 用 UserID 分组倒序取最新一条包含周期重置商品的购买记录
        $boughts = Bought::whereIn('shopid', $shopid)->orderBy('id', 'desc')->groupBy('userid')->get();
        $bought_users = [];

        foreach ($boughts as $bought) {
            /** @var Bought $bought */
            $user = $bought->user();

            if ($user === null) {
                continue;
            }
            // 跳过使用新商店系统的用户
            if ($user->use_new_shop === 1) {
                continue;
            }

            $shop = $bought->shop();

            if ($shop === null) {
                $bought->delete();
                continue;
            }

            $bought_users[] = $bought->userid;

            if ($bought->valid() && $bought->usedDays() % $shop->reset() === 0 && $bought->usedDays() !== 0) {
                echo '流量重置-' . $user->id . "\n";
                $user->transfer_enable = Tools::toGB($shop->resetValue());
                $user->u = 0;
                $user->d = 0;
                $user->last_day_t = 0;
                $user->save();
                $user->sendMail(
                    $_ENV['appName'] . '-您的流量被重置了',
                    'news/warn.tpl',
                    [
                        'text' => '您好，根据您所订购的订单 ID:' . $bought->id . '，流量已经被重置为' . $shop->resetValue() . 'GB',
                    ],
                    [],
                    true
                );
            }
        }
        // ------- 付费用户流量重置

        // ------- 免费用户流量重置
        foreach ($users as $user) {
            if (in_array($user->id, $bought_users)) {
                continue;
            }
            // 跳过使用新商店系统的用户
            if ($user->use_new_shop === 1) {
                continue;
            }

            if (date('d') === $user->auto_reset_day) {
                $user->u = 0;
                $user->d = 0;
                $user->last_day_t = 0;
                $user->transfer_enable = $user->auto_reset_bandwidth * 1024 * 1024 * 1024;
                $user->save();
                $user->sendMail(
                    $_ENV['appName'] . '-您的免费流量被重置了',
                    'news/warn.tpl',
                    [
                        'text' => '您好，您的免费流量已经被重置为' . $user->auto_reset_bandwidth . 'GB',
                    ],
                    [],
                    true
                );
            }
        }
        // ------- 免费用户流量重置

        // ------- 发送系统运行状况通知
        $sts = new Analytics();
        if (Setting::obtain('telegram_diary')) {
            try {
                Telegram::send(
                    str_replace(
                        [
                            '%getTodayCheckinUser%',
                            '%lastday_total%',
                        ],
                        [
                            $sts->getTodayCheckinUser(),
                            Tools::flowAutoShow($lastday_total),
                        ],
                        Setting::obtain('telegram_diary_text')
                    )
                );
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        // ------- 发送系统运行状况通知

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
     * 检查任务，每分钟
     *
     * @throws TelegramSDKException
     * @throws ClientExceptionInterface
     */
    public function CheckJob(): void
    {
        //记录当前时间戳
        $timestatmp = time();
        //邮件队列处理
        while (true) {
            if (time() - $timestatmp > 59) {
                echo '邮件队列处理超时，已跳过' . PHP_EOL;
                break;
            }
            DB::beginTransaction();
            $email_queues_raw = DB::select('SELECT * FROM email_queue LIMIT 1 FOR UPDATE SKIP LOCKED');
            if (count($email_queues_raw) === 0) {
                DB::commit();
                break;
            }
            $email_queues = array_map(static function ($value) {
                return (array) $value;
            }, $email_queues_raw);
            $email_queue = $email_queues[0];
            echo '发送邮件至 ' . $email_queue['to_email'] . PHP_EOL;
            DB::delete('DELETE FROM email_queue WHERE id = ?', [$email_queue['id']]);
            if (Tools::isEmail($email_queue['to_email'])) {
                try {
                    Mail::send($email_queue['to_email'], $email_queue['subject'], $email_queue['template'], json_decode($email_queue['array']));
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            } else {
                echo $email_queue['to_email'] . ' 邮箱格式错误，已跳过' . PHP_EOL;
            }
            DB::commit();
        }
        //节点掉线检测
        if ($_ENV['enable_detect_offline']) {
            echo '节点掉线检测开始' . PHP_EOL;
            $adminUser = User::where('is_admin', '=', '1')->get();
            $nodes = Node::all();

            foreach ($nodes as $node) {
                $notice_text = '';
                if ($node->isNodeOnline() === false && $node->online === true) {
                    if ($_ENV['useScFtqq'] === true) {
                        $ScFtqq_SCKEY = $_ENV['ScFtqq_SCKEY'];
                        $text = '管理员您好，系统发现节点 ' . $node->name . ' 掉线了，请您及时处理。';
                        $postdata = http_build_query(
                            [
                                'title' => $_ENV['appName'] . '-节点掉线了',
                                'desp' => $text,
                            ]
                        );
                        $opts = [
                            'http' => [
                                'method' => 'POST',
                                'header' => 'Content-type: application/x-www-form-urlencoded',
                                'content' => $postdata,
                            ],
                        ];
                        $context = stream_context_create($opts);
                        file_get_contents('https://sctapi.ftqq.com/' . $ScFtqq_SCKEY . '.send', false, $context);
                    }

                    foreach ($adminUser as $user) {
                        echo 'Send Email to admin user: ' . $user->id . PHP_EOL;
                        $user->sendMail(
                            $_ENV['appName'] . '-系统警告',
                            'news/warn.tpl',
                            [
                                'text' => '管理员您好，系统发现节点 ' . $node->name . ' 掉线了，请您及时处理。',
                            ],
                            [],
                            false
                        );
                        $notice_text = str_replace(
                            '%node_name%',
                            $node->name,
                            Setting::obtain('telegram_node_offline_text')
                        );
                    }

                    if (Setting::obtain('telegram_node_offline')) {
                        try {
                            Telegram::send($notice_text);
                        } catch (Exception $e) {
                            echo $e->getMessage() . PHP_EOL;
                        }
                    }

                    $node->online = false;
                    $node->save();
                } elseif ($node->isNodeOnline() === true && $node->online === false) {
                    if ($_ENV['useScFtqq'] === true) {
                        $ScFtqq_SCKEY = $_ENV['ScFtqq_SCKEY'];
                        $text = '管理员您好，系统发现节点 ' . $node->name . ' 恢复上线了。';
                        $postdata = http_build_query(
                            [
                                'title' => $_ENV['appName'] . '-节点恢复上线了',
                                'desp' => $text,
                            ]
                        );

                        $opts = [
                            'http' => [
                                'method' => 'POST',
                                'header' => 'Content-type: application/x-www-form-urlencoded',
                                'content' => $postdata,
                            ],
                        ];
                        $context = stream_context_create($opts);
                        file_get_contents('https://sctapi.ftqq.com/' . $ScFtqq_SCKEY . '.send', false, $context);
                    }

                    foreach ($adminUser as $user) {
                        echo 'Send Email to admin user: ' . $user->id . PHP_EOL;
                        $user->sendMail(
                            $_ENV['appName'] . '-系统提示',
                            'news/warn.tpl',
                            [
                                'text' => '管理员您好，系统发现节点 ' . $node->name . ' 恢复上线了。',
                            ],
                            [],
                            false
                        );
                        $notice_text = str_replace(
                            '%node_name%',
                            $node->name,
                            Setting::obtain('telegram_node_online_text')
                        );
                    }

                    if (Setting::obtain('telegram_node_online')) {
                        try {
                            Telegram::send($notice_text);
                        } catch (Exception $e) {
                            echo $e->getMessage() . PHP_EOL;
                        }
                    }

                    $node->online = true;
                    $node->save();
                }
            }
            echo '节点掉线检测结束' . PHP_EOL;
        }
        //更新节点 IP，每分钟
        $nodes = Node::get();
        foreach ($nodes as $node) {
            /** @var Node $node */
            $server = $node->server;
            if (! Tools::isIPv4($server) && ! Tools::isIPv6($server)) {
                $node->changeNodeIp($server);
                $node->save();
            }
        }
    }

    /**
     * 用户账户相关任务，每小时
     */
    public function UserJob(): void
    {
        $users = User::all();
        foreach ($users as $user) {
            //流量记录
            if ($_ENV['trafficLog'] === true) {
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

            if (strtotime($user->expire_in) < time() && $user->expire_notified === false) {
                $user->transfer_enable = 0;
                $user->u = 0;
                $user->d = 0;
                $user->last_day_t = 0;

                $user->sendMail(
                    $_ENV['appName'] . '-您的用户账户已经过期了',
                    'news/warn.tpl',
                    [
                        'text' => '您好，系统发现您的账号已经过期了。',
                    ],
                    [],
                    true
                );

                $user->expire_notified = true;
                $user->save();
            } elseif (strtotime($user->expire_in) > time() && $user->expire_notified === true) {
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

                if ($under_limit === true && $user->traffic_notified === false) {
                    $result = $user->sendMail(
                        $_ENV['appName'] . '-您的剩余流量过低',
                        'news/warn.tpl',
                        [
                            'text' => '您好，系统发现您剩余流量已经低于 ' . $_ENV['notify_limit_value'] . $unit_text . ' 。',
                        ],
                        [],
                        true
                    );
                    if ($result) {
                        $user->traffic_notified = true;
                        $user->save();
                    }
                } elseif ($under_limit === false && $user->traffic_notified === true) {
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
                    $_ENV['appName'] . '-您的用户账户已经被删除了',
                    'news/warn.tpl',
                    [
                        'text' => '您好，系统发现您的账户已经过期 ' . $_ENV['account_expire_delete_days'] . ' 天了，帐号已经被删除。',
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
                    $_ENV['appName'] . '-您的用户账户已经被删除了',
                    'news/warn.tpl',
                    [
                        'text' => '您好，系统发现您的账号已经 ' . $_ENV['auto_clean_uncheck_days'] . ' 天没签到了，帐号已经被删除。',
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
                    $_ENV['appName'] . '-您的用户账户已经被删除了',
                    'news/warn.tpl',
                    [
                        'text' => '您好，系统发现您的账号已经 ' . $_ENV['auto_clean_unused_days'] . ' 天没使用了，帐号已经被删除。',
                    ],
                    [],
                    true
                );
                $user->killUser();
                continue;
            }

            if (
                $user->class !== 0 &&
                strtotime($user->class_expire) < time() &&
                strtotime($user->class_expire) > 1420041600
            ) {
                $text = '您好，系统发现您的账号等级已经过期了。';
                $reset_traffic = $_ENV['class_expire_reset_traffic'];
                if ($reset_traffic >= 0) {
                    $user->transfer_enable = Tools::toGB($reset_traffic);
                    $user->u = 0;
                    $user->d = 0;
                    $user->last_day_t = 0;
                    $text .= '流量已经被重置为' . $reset_traffic . 'GB';
                }
                $user->sendMail(
                    $_ENV['appName'] . '-您的账户等级已经过期了',
                    'news/warn.tpl',
                    [
                        'text' => $text,
                    ],
                    [],
                    true
                );
                $user->class = 0;
            }
            // 审计封禁解封
            if ($user->is_banned === 1) {
                $logs = DetectBanLog::where('user_id', $user->id)->orderBy('id', 'desc')->first();
                if ($logs !== null && ($logs->end_time + $logs->ban_time * 60) <= time()) {
                    $user->is_banned = 0;
                }
            }

            $user->save();
        }
        //自动续费
        $boughts = Bought::where('renew', '<', time() + 60)->where('renew', '<>', 0)->get();
        foreach ($boughts as $bought) {
            /** @var Bought $bought */
            $user = $bought->user();

            if ($user === null) {
                continue;
            }
            // 跳过使用新商店系统的用户
            if ($user->use_new_shop === 1) {
                continue;
            }

            $shop = $bought->shop();

            if ($shop === null) {
                $bought->delete();
                $user->sendMail(
                    $_ENV['appName'] . '-续费失败',
                    'news/warn.tpl',
                    [
                        'text' => '您好，系统为您自动续费商品时，发现该商品已被下架，为能继续正常使用，建议您登录用户面板购买新的商品。',
                    ],
                    [],
                    true
                );
                $bought->is_notified = true;
                $bought->save();
                continue;
            }

            if ($user->money >= $shop->price) {
                $user->money -= $shop->price;
                $user->save();
                $shop->buy($user, 1);
                $bought->renew = 0;
                $bought->save();

                $bought_new = new Bought();
                $bought_new->userid = $user->id;
                $bought_new->shopid = $shop->id;
                $bought_new->datetime = time();
                $bought_new->renew = time() + $shop->auto_renew * 86400;
                $bought_new->price = $shop->price;
                $bought_new->coupon = '';
                $bought_new->save();

                $user->sendMail(
                    $_ENV['appName'] . '-续费成功',
                    'news/warn.tpl',
                    [
                        'text' => '您好，系统已经为您自动续费，商品名：' . $shop->name . ',金额:' . $shop->price . ' 元。',
                    ],
                    [],
                    true
                );

                $bought->is_notified = true;
                $bought->save();
            } elseif ($bought->is_notified === false) {
                $user->sendMail(
                    $_ENV['appName'] . '-续费失败',
                    'news/warn.tpl',
                    [
                        'text' => '您好，系统为您自动续费商品名：' . $shop->name . ',金额:' . $shop->price . ' 元 时，发现您余额不足，请及时充值。充值后请稍等系统便会自动为您续费。',
                    ],
                    [],
                    true
                );
                $bought->is_notified = true;
                $bought->save();
            }
        }
    }
}
