<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Ann;
use App\Models\Config;
use App\Models\DetectLog;
use App\Models\EmailQueue;
use App\Models\HourlyUsage;
use App\Models\Invoice;
use App\Models\Node;
use App\Models\OnlineLog;
use App\Models\Order;
use App\Models\Paylist;
use App\Models\SubscribeLog;
use App\Models\User;
use App\Services\IM\Telegram;
use App\Utils\Tools;
use DateTime;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Client\ClientExceptionInterface;
use Telegram\Bot\Exceptions\TelegramSDKException;
use function array_map;
use function date;
use function in_array;
use function json_decode;
use function str_replace;
use function strtotime;
use function time;
use const PHP_EOL;

final class Cron
{
    public static function cleanDb(): void
    {
        (new SubscribeLog())->where(
            'request_time',
            '<',
            time() - 86400 * Config::obtain('subscribe_log_retention_days')
        )->delete();
        (new HourlyUsage())->where(
            'date',
            '<',
            date('Y-m-d', time() - 86400 * Config::obtain('traffic_log_retention_days'))
        )->delete();
        (new DetectLog())->where('datetime', '<', time() - 86400 * 3)->delete();
        (new EmailQueue())->where('time', '<', time() - 86400)->delete();
        (new OnlineLog())->where('last_time', '<', time() - 86400)->delete();

        echo Tools::toDateTime(time()) . ' 数据库清理完成' . PHP_EOL;
    }

    public static function detectInactiveUser(): void
    {
        $checkin_days = Config::obtain('detect_inactive_user_checkin_days');
        $login_days = Config::obtain('detect_inactive_user_login_days');
        $use_days = Config::obtain('detect_inactive_user_use_days');

        (new User())->where('is_admin', 0)
            ->where('is_inactive', 0)
            ->where('last_check_in_time', '<', time() - 86400 * $checkin_days)
            ->where('last_login_time', '<', time() - 86400 * $login_days)
            ->where('last_use_time', '<', time() - 86400 * $use_days)
            ->update(['is_inactive' => 1]);

        (new User())->where('is_admin', 0)
            ->where('is_inactive', 1)
            ->where('last_check_in_time', '>', time() - 86400 * $checkin_days)
            ->where('last_login_time', '>', time() - 86400 * $login_days)
            ->where('last_use_time', '>', time() - 86400 * $use_days)
            ->update(['is_inactive' => 0]);

        echo Tools::toDateTime(time()) .
            ' 检测到 ' . (new User())->where('is_inactive', 1)->count() . ' 个账户处于闲置状态' . PHP_EOL;
    }

    public static function detectNodeOffline(): void
    {
        $nodes = (new Node())->where('type', 1)->get();

        foreach ($nodes as $node) {
            if ($node->getNodeOnlineStatus() >= 0 && $node->online === 1) {
                continue;
            }

            if ($node->getNodeOnlineStatus() === -1 && $node->online === 1) {
                echo 'Send Node Offline Email to admin users' . PHP_EOL;

                try {
                    Notification::notifyAdmin(
                        $_ENV['appName'] . '-系统警告',
                        '管理员你好，系统发现节点 ' . $node->name . ' 掉线了，请你及时处理。'
                    );
                } catch (GuzzleException|ClientExceptionInterface|TelegramSDKException $e) {
                    echo $e->getMessage() . PHP_EOL;
                }

                if (Config::obtain('telegram_node_offline')) {
                    $notice_text = str_replace(
                        '%node_name%',
                        $node->name,
                        Config::obtain('telegram_node_offline_text')
                    );

                    try {
                        (new Telegram())->send(0, $notice_text);
                    } catch (TelegramSDKException $e) {
                        echo $e->getMessage();
                    }
                }

                $node->online = 0;
                $node->save();

                continue;
            }

            if ($node->getNodeOnlineStatus() === 1 && $node->online === 0) {
                echo 'Send Node Online Email to admin user' . PHP_EOL;

                try {
                    Notification::notifyAdmin(
                        $_ENV['appName'] . '-系统提示',
                        '管理员你好，系统发现节点 ' . $node->name . ' 恢复上线了。'
                    );
                } catch (GuzzleException|ClientExceptionInterface|TelegramSDKException $e) {
                    echo $e->getMessage() . PHP_EOL;
                }

                if (Config::obtain('telegram_node_online')) {
                    $notice_text = str_replace(
                        '%node_name%',
                        $node->name,
                        Config::obtain('telegram_node_online_text')
                    );

                    try {
                        (new Telegram())->send(0, $notice_text);
                    } catch (TelegramSDKException $e) {
                        echo $e->getMessage();
                    }
                }

                $node->online = 1;
                $node->save();
            }
        }

        echo Tools::toDateTime(time()) . ' 节点离线检测完成' . PHP_EOL;
    }

    public static function expirePaidUserAccount(): void
    {
        $paidUsers = (new User())->where('class', '>', 0)->get();

        foreach ($paidUsers as $user) {
            if (strtotime($user->class_expire) < time()) {
                $text = '你好，系统发现你的账号等级已经过期了。';
                $reset_traffic = $_ENV['class_expire_reset_traffic'];

                if ($reset_traffic >= 0) {
                    $user->transfer_enable = Tools::toGB($reset_traffic);
                    $text .= '流量已经被重置为' . $reset_traffic . 'GB。';
                }

                try {
                    Notification::notifyUser($user, $_ENV['appName'] . '-你的账号等级已经过期了', $text);
                } catch (GuzzleException|ClientExceptionInterface|TelegramSDKException $e) {
                    echo $e->getMessage() . PHP_EOL;
                }

                $user->u = 0;
                $user->d = 0;
                $user->transfer_today = 0;
                $user->class = 0;
                $user->save();
            }
        }

        echo Tools::toDateTime(time()) . ' 付费用户过期检测完成' . PHP_EOL;
    }

    public static function processEmailQueue(): void
    {
        if ((new EmailQueue())->count() === 0) {
            echo Tools::toDateTime(time()) . ' 邮件队列为空' . PHP_EOL;
        } else {
            //记录当前时间戳
            $timestamp = time();
            //邮件队列处理
            while (true) {
                if (time() - $timestamp > 299) {
                    echo Tools::toDateTime(time()) . '邮件队列处理超时，已跳过' . PHP_EOL;
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
                        Mail::send(
                            $email_queue['to_email'],
                            $email_queue['subject'],
                            $email_queue['template'],
                            json_decode($email_queue['array'])
                        );
                    } catch (Exception|ClientExceptionInterface $e) {
                        echo $e->getMessage();
                    }
                } else {
                    echo $email_queue['to_email'] . ' 邮箱格式错误，已跳过' . PHP_EOL;
                }

                DB::commit();
            }

            echo Tools::toDateTime(time()) . ' 邮件队列处理完成' . PHP_EOL;
        }
    }

    public static function processTabpOrderActivation(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $user_id = $user->id;
            // 获取用户账户等待激活的TABP订单
            $pending_activation_orders = (new Order())->where('user_id', $user_id)
                ->where('status', 'pending_activation')
                ->where('product_type', 'tabp')
                ->orderBy('id')
                ->get();
            // 获取用户账户已激活的TABP订单，一个用户同时只能有一个已激活的TABP订单
            $activated_order = (new Order())->where('user_id', $user_id)
                ->where('status', 'activated')
                ->where('product_type', 'tabp')
                ->orderBy('id')
                ->first();
            // 如果用户账户中没有已激活的TABP订单，且有等待激活的TABP订单，则激活最早的等待激活TABP订单
            if ($activated_order === null && count($pending_activation_orders) > 0) {
                $order = $pending_activation_orders[0];
                // 获取TABP订单内容准备激活
                $content = json_decode($order->product_content);
                // 激活TABP
                $user->u = 0;
                $user->d = 0;
                $user->transfer_today = 0;
                $user->transfer_enable = Tools::toGB($content->bandwidth);
                $user->class = $content->class;
                $old_class_expire = new DateTime();
                $user->class_expire = $old_class_expire
                    ->modify('+' . $content->class_time . ' days')->format('Y-m-d H:i:s');
                $user->node_group = $content->node_group;
                $user->node_speedlimit = $content->speed_limit;
                $user->node_iplimit = $content->ip_limit;
                $user->save();
                $order->status = 'activated';
                $order->update_time = time();
                $order->save();
                echo "TABP订单 #{$order->id} 已激活。\n";
                continue;
            }
            // 如果用户账户中有已激活的TABP订单，则判断是否过期
            if ($activated_order !== null) {
                $content = json_decode($activated_order->product_content);

                if ($activated_order->update_time + $content->time * 86400 < time()) {
                    $activated_order->status = 'expired';
                    $activated_order->update_time = time();
                    $activated_order->save();
                    echo "TABP订单 #{$activated_order->id} 已过期。\n";
                }
            }
        }

        echo Tools::toDateTime(time()) . ' TABP订单激活处理完成' . PHP_EOL;
    }

    public static function processBandwidthOrderActivation(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $user_id = $user->id;
            // 获取用户账户等待激活的流量包订单
            $order = (new Order())->where('user_id', $user_id)
                ->where('status', 'pending_activation')
                ->where('product_type', 'bandwidth')
                ->orderBy('id')
                ->first();

            if ($order !== null) {
                // 获取流量包订单内容准备激活
                $content = json_decode($order->product_content);
                // 激活流量包
                $user->transfer_enable += Tools::toGB($content->bandwidth);
                $user->save();
                $order->status = 'activated';
                $order->update_time = time();
                $order->save();
                echo "流量包订单 #{$order->id} 已激活。\n";
            }
        }

        echo Tools::toDateTime(time()) . ' 流量包订单激活处理完成' . PHP_EOL;
    }

    /**
     * @throws Exception
     */
    public static function processTimeOrderActivation(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $user_id = $user->id;
            // 获取用户账户等待激活的时间包订单
            $order = (new Order())->where('user_id', $user_id)
                ->where('status', 'pending_activation')
                ->where('product_type', 'time')
                ->orderBy('id')
                ->first();

            if ($order !== null) {
                $content = json_decode($order->product_content);
                // 跳过当前账户等级不等于时间包等级的非免费用户订单
                if ($user->class !== (int) $content->class && $user->class > 0) {
                    continue;
                }
                // 激活时间包
                $user->class = $content->class;
                $old_class_expire = new DateTime($user->class_expire);
                $user->class_expire = $old_class_expire
                    ->modify('+' . $content->class_time . ' days')->format('Y-m-d H:i:s');
                $user->node_group = $content->node_group;
                $user->node_speedlimit = $content->speed_limit;
                $user->node_iplimit = $content->ip_limit;
                $user->save();
                $order->status = 'activated';
                $order->update_time = time();
                $order->save();
                echo "时间包订单 #{$order->id} 已激活。\n";
            }
        }

        echo Tools::toDateTime(time()) . ' 时间包订单激活处理完成' . PHP_EOL;
    }

    public static function processPendingOrder(): void
    {
        $pending_payment_orders = (new Order())->where('status', 'pending_payment')->get();

        foreach ($pending_payment_orders as $order) {
            // 检查账单支付状态
            $invoice = (new Invoice())->where('order_id', $order->id)->first();

            if ($invoice === null) {
                continue;
            }
            // 标记订单为等待激活
            if (in_array($invoice->status, ['paid_gateway', 'paid_balance', 'paid_admin'])) {
                $order->status = 'pending_activation';
                $order->update_time = time();
                $order->save();
                echo "已标记订单 #{$order->id} 为等待激活。\n";
                continue;
            }
            // 取消超时未支付的订单和关联账单
            if ($order->create_time + 86400 < time()) {
                $order->status = 'cancelled';
                $order->update_time = time();
                $order->save();
                echo "已取消超时订单 #{$order->id}。\n";
                $invoice->status = 'cancelled';
                $invoice->update_time = time();
                $invoice->save();
                echo "已取消超时账单 #{$invoice->id}。\n";
            }
        }

        echo Tools::toDateTime(time()) . ' 等待中订单处理完成' . PHP_EOL;
    }

    public static function removeInactiveUserLinkAndInvite(): void
    {
        $inactive_users = (new User())->where('is_inactive', 1)->get();

        foreach ($inactive_users as $user) {
            $user->removeLink();
            $user->removeInvite();
        }

        echo Tools::toDateTime(time()) . ' Successfully removed inactive user\'s Link and Invite' . PHP_EOL;
    }

    public static function resetNodeBandwidth(): void
    {
        (new Node())->where('bandwidthlimit_resetday', date('d'))->update(['node_bandwidth' => 0]);

        echo Tools::toDateTime(time()) . ' 重设节点流量完成' . PHP_EOL;
    }

    public static function resetTodayBandwidth(): void
    {
        (new User())->query()->update(['transfer_today' => 0]);

        echo Tools::toDateTime(time()) . ' 重设用户每日流量完成' . PHP_EOL;
    }

    public static function resetFreeUserBandwidth(): void
    {
        $freeUsers = (new User())->where('class', 0)
            ->where('auto_reset_day', date('d'))->get();

        foreach ($freeUsers as $user) {
            try {
                Notification::notifyUser(
                    $user,
                    $_ENV['appName'] . '-免费流量重置通知',
                    '你好，你的免费流量已经被重置为' . $user->auto_reset_bandwidth . 'GB。'
                );
            } catch (GuzzleException|ClientExceptionInterface|TelegramSDKException $e) {
                echo $e->getMessage() . PHP_EOL;
            }

            $user->u = 0;
            $user->d = 0;
            $user->transfer_enable = $user->auto_reset_bandwidth * 1024 * 1024 * 1024;
            $user->save();
        }

        echo Tools::toDateTime(time()) . ' 免费用户流量重置完成' . PHP_EOL;
    }

    public static function sendDailyFinanceMail(): void
    {
        $today = strtotime('00:00:00');
        $paylists = (new Paylist())->where('status', 1)
            ->whereBetween('datetime', [strtotime('-1 day', $today), $today])->get();
        $text_html = '<table border=1><tr><td>金额</td><td>用户ID</td><td>用户名</td><td>充值时间</td>';

        foreach ($paylists as $paylist) {
            $text_html .= '<tr>';
            $text_html .= '<td>' . $paylist->total . '</td>';
            $text_html .= '<td>' . $paylist->userid . '</td>';
            $text_html .= '<td>' . (new User())->find($paylist->userid)->user_name . '</td>';
            $text_html .= '<td>' . Tools::toDateTime((int) $paylist->datetime) . '</td>';
            $text_html .= '</tr>';
        }

        $text_html .= '</table>';
        $text_html .= '<br>昨日总收入笔数：' . count($paylists) . '<br>昨日总收入金额：' . $paylists->sum('total');
        echo 'Sending daily finance email to admin user' . PHP_EOL;

        try {
            Notification::notifyAdmin(
                '财务日报',
                $text_html,
                'finance.tpl'
            );
        } catch (GuzzleException|ClientExceptionInterface|TelegramSDKException $e) {
            echo $e->getMessage() . PHP_EOL;
        }

        echo Tools::toDateTime(time()) . ' 成功发送财务日报' . PHP_EOL;
    }

    public static function sendWeeklyFinanceMail(): void
    {
        $today = strtotime('00:00:00');
        $paylists = (new Paylist())->where('status', 1)
            ->whereBetween('datetime', [strtotime('-1 week', $today), $today])
            ->get();

        $text_html = '<br>上周总收入笔数：' . count($paylists) . '<br>上周总收入金额：' . $paylists->sum('total');
        echo 'Sending weekly finance email to admin user' . PHP_EOL;

        try {
            Notification::notifyAdmin(
                '财务周报',
                $text_html,
                'finance.tpl'
            );
        } catch (GuzzleException|ClientExceptionInterface|TelegramSDKException $e) {
            echo $e->getMessage() . PHP_EOL;
        }

        echo Tools::toDateTime(time()) . ' 成功发送财务周报' . PHP_EOL;
    }

    public static function sendMonthlyFinanceMail(): void
    {
        $today = strtotime('00:00:00');
        $paylists = (new Paylist())->where('status', 1)
            ->whereBetween('datetime', [strtotime('-1 month', $today), $today])
            ->get();

        $text_html = '<br>上月总收入笔数：' . count($paylists) . '<br>上月总收入金额：' . $paylists->sum('total');
        echo 'Sending monthly finance email to admin user' . PHP_EOL;

        try {
            Notification::notifyAdmin(
                '财务月报',
                $text_html,
                'finance.tpl'
            );
        } catch (GuzzleException|ClientExceptionInterface|TelegramSDKException $e) {
            echo $e->getMessage() . PHP_EOL;
        }

        echo Tools::toDateTime(time()) . ' 成功发送财务月报' . PHP_EOL;
    }

    public static function sendPaidUserUsageLimitNotification(): void
    {
        $paidUsers = (new User())->where('class', '>', 0)->get();

        foreach ($paidUsers as $user) {
            $user_traffic_left = $user->transfer_enable - $user->u - $user->d;
            $under_limit = false;
            $unit_text = '';

            if ($_ENV['notify_limit_mode'] === 'per' &&
                $user_traffic_left / $user->transfer_enable * 100 < $_ENV['notify_limit_value']
            ) {
                $under_limit = true;
                $unit_text = '%';
            } elseif ($_ENV['notify_limit_mode'] === 'mb' &&
                Tools::flowToMB($user_traffic_left) < $_ENV['notify_limit_value']
            ) {
                $under_limit = true;
                $unit_text = 'MB';
            }

            if ($under_limit && ! $user->traffic_notified) {
                try {
                    Notification::notifyUser(
                        $user,
                        $_ENV['appName'] . '-你的剩余流量过低',
                        '你好，系统发现你剩余流量已经低于 ' . $_ENV['notify_limit_value'] . $unit_text . ' 。',
                    );

                    $user->traffic_notified = true;
                } catch (GuzzleException|ClientExceptionInterface|TelegramSDKException $e) {
                    $user->traffic_notified = false;
                    echo $e->getMessage() . PHP_EOL;
                }

                $user->save();
            } elseif (! $under_limit && $user->traffic_notified) {
                $user->traffic_notified = false;
                $user->save();
            }
        }

        echo Tools::toDateTime(time()) . ' 付费用户用量限制提醒完成' . PHP_EOL;
    }

    public static function sendDailyTrafficReport(): void
    {
        $users = (new User())->whereIn('daily_mail_enable', [1, 2])->get();
        $ann_latest_raw = (new Ann())->orderBy('date', 'desc')->first();

        if ($ann_latest_raw === null) {
            $ann_latest = '<br><br>';
        } else {
            $ann_latest = $ann_latest_raw->content . '<br><br>';
        }

        foreach ($users as $user) {
            $user->sendDailyNotification($ann_latest);
        }

        echo Tools::toDateTime(time()) . ' 成功发送每日流量报告' . PHP_EOL;
    }

    /**
     * @throws TelegramSDKException
     */
    public static function sendTelegramDailyJob(): void
    {
        (new Telegram())->send(0, Config::obtain('telegram_daily_job_text'));

        echo Tools::toDateTime(time()) . ' 成功发送 Telegram 每日任务提示' . PHP_EOL;
    }

    /**
     * @throws TelegramSDKException
     */
    public static function sendTelegramDiary(): void
    {
        (new Telegram())->send(
            0,
            str_replace(
                [
                    '%getTodayCheckinUser%',
                    '%lastday_total%',
                ],
                [
                    Analytics::getTodayCheckinUser(),
                    Analytics::getTodayTrafficUsage(),
                ],
                Config::obtain('telegram_diary_text')
            )
        );

        echo Tools::toDateTime(time()) . ' 成功发送 Telegram 系统运行日志' . PHP_EOL;
    }

    public static function updateNodeIp(): void
    {
        $nodes = (new Node())->where('type', 1)->get();

        foreach ($nodes as $node) {
            $node->updateNodeIp();
            $node->save();
        }

        echo Tools::toDateTime(time()) . ' 更新节点 IP 完成' . PHP_EOL;
    }
}
