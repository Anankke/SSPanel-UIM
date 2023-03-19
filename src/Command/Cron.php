<?php

declare(strict_types=1);

namespace App\Command;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use App\Utils\Tools;
use DateTime;
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
                $order->status = 'activated';
                $order->update_time = time();
                $order->save();
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
    }
}
