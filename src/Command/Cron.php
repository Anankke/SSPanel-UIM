<?php

declare(strict_types=1);

namespace App\Command;

use App\Models\Invoice;
use App\Models\Node;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Services\DB;
use App\Services\Mail;
use App\Utils\Telegram;
use App\Utils\Tools;
use DateTime;
use Exception;
use Psr\Http\Client\ClientExceptionInterface;
use function count;
use function in_array;
use function json_decode;
use function time;

final class Cron extends Command
{
    public string $description = <<<EOL
├─=: php xcat Cron - 站点定时任务，每五分钟
EOL;

    public function boot(): void
    {
        ini_set('memory_limit', '-1');
        // 新商店系统相关
        // 获取等待支付的订单，检查账单支付状态
        $pending_payment_orders = Order::where('status', 'pending_payment')->get();

        foreach ($pending_payment_orders as $order) {
            // 检查账单支付状态
            $invoice = Invoice::where('order_id', $order->id)->first();

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
        // 获取使用新商店系统的用户，仅更新这部分用户避免与旧系统冲突
        $users_new_shop = User::where('use_new_shop', 1)->get();

        foreach ($users_new_shop as $user) {
            $user_id = $user->id;
            // 获取用户账户等待激活的订单
            $pending_activation_orders = Order::where('user_id', $user_id)->where('status', 'pending_activation')->orderBy('id', 'asc')->get();
            // 获取用户账户已激活的订单，一个用户同时只能有一个已激活的订单
            $activated_order = Order::where('user_id', $user_id)->where('status', 'activated')->orderBy('id', 'asc')->first();
            // 如果用户账户中没有已激活的订单，且有等待激活的订单，则激活最早的等待激活订单
            if ($activated_order === null && count($pending_activation_orders) > 0) {
                $order = $pending_activation_orders[0];
                // 获取订单内容准备激活
                $content = json_decode($order->product_content);
                // 激活商品
                $user->u = 0;
                $user->d = 0;
                $user->last_day_t = 0;
                $user->transfer_enable = Tools::toGB($content->bandwidth);
                $user->class = $content->class;
                $old_expire_in = new DateTime();
                $old_class_expire = new DateTime();
                $user->expire_in = $old_expire_in->modify('+' . $content->time . ' days')->format('Y-m-d H:i:s');
                $user->class_expire = $old_class_expire->modify('+' . $content->class_time . ' days')->format('Y-m-d H:i:s');
                $user->node_group = $content->node_group;
                $user->node_speedlimit = $content->speed_limit;
                $user->node_iplimit = $content->ip_limit;
                $user->save();
                $order->status = 'activated';
                $order->update_time = time();
                $order->save();
                echo "订单 #{$order->id} 已激活。\n";
                continue;
            }
            // 如果用户账户中有已激活的订单，则判断是否过期
            if ($activated_order !== null) {
                $content = json_decode($activated_order->product_content);
                if ($activated_order->update_time + $content->time * 86400 < time()) {
                    $activated_order->status = 'expired';
                    $activated_order->update_time = time();
                    $activated_order->save();
                    echo "订单 #{$activated_order->id} 已过期。\n";
                }
            }
        }
        //记录当前时间戳
        $timestamp = time();
        //邮件队列处理
        while (true) {
            if (time() - $timestamp > 299) {
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
                } catch (Exception|ClientExceptionInterface $e) {
                    echo $e->getMessage();
                }
            } else {
                echo $email_queue['to_email'] . ' 邮箱格式错误，已跳过' . PHP_EOL;
            }
            DB::commit();
        }
        //取出所有节点
        $nodes = Node::all();
        //节点掉线检测
        if ($_ENV['enable_detect_offline']) {
            echo '节点掉线检测开始' . PHP_EOL;
            $adminUser = User::where('is_admin', '=', '1')->get();

            foreach ($nodes as $node) {
                $notice_text = '';
                if ($node->getNodeOnlineStatus() === -1 && $node->online === 1) {
                    foreach ($adminUser as $user) {
                        echo 'Send Email to admin user: ' . $user->id . PHP_EOL;
                        $user->sendMail(
                            $_ENV['appName'] . '-系统警告',
                            'warn.tpl',
                            [
                                'text' => '管理员你好，系统发现节点 ' . $node->name . ' 掉线了，请你及时处理。',
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
                } elseif ($node->getNodeOnlineStatus() === 1 && $node->online === 0) {
                    foreach ($adminUser as $user) {
                        echo 'Send Email to admin user: ' . $user->id . PHP_EOL;
                        $user->sendMail(
                            $_ENV['appName'] . '-系统提示',
                            'warn.tpl',
                            [
                                'text' => '管理员你好，系统发现节点 ' . $node->name . ' 恢复上线了。',
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
        //更新节点 IP
        foreach ($nodes as $node) {
            $server = $node->server;
            if (! Tools::isIPv4($server) && ! Tools::isIPv6($server)) {
                $node->changeNodeIp($server);
                $node->save();
            }
        }
    }
}
