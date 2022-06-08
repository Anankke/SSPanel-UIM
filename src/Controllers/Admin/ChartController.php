<?php
namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\Node;
use App\Models\ProductOrder;
use App\Models\Statistics;

class ChartController extends AdminController
{
    public function encode($item, $offset = false)
    {
        $items = Statistics::where('item', $item)
            ->orderBy('created_at', 'desc')
            ->limit($_ENV['statistics_range'][$item])
            ->get();

        $chart_x = [];
        $chart_y = [];

        foreach ($items as $record) {
            $timestamp = ($offset) ? $record->created_at - 86400 : $record->created_at;
            $chart_x[] = "'" . date('m-d', $timestamp) . "'";
            $chart_y[] = $record->value;
        }

        $result = [
            'x' => array_reverse($chart_x),
            'y' => array_reverse($chart_y),
        ];

        return $result;
    }

    public function index($request, $response, $args)
    {
        $traffic = self::encode('traffic', true);
        $check_in = self::encode('checkin', false);
        $register = self::encode('register', true);
        $deal_amount = self::encode('deal_amount', true);
        $order_amount = self::encode('order_amount', true);

        $charts = [
            'checkin' => [
                'element_id' => 'check-in',
                'series_name' => '人数',
                'x' => $check_in['x'],
                'y' => $check_in['y'],
            ],
            'traffic' => [
                'element_id' => 'total-traffic',
                'series_name' => '流量',
                'x' => $traffic['x'],
                'y' => $traffic['y'],
            ],
            'regitser' => [
                'element_id' => 'register',
                'series_name' => '人数',
                'x' => $register['x'],
                'y' => $register['y'],
            ],
        ];

        return $response->write(
            $this->view()
                ->assign('charts', $charts)
                ->assign('deal_amount', $deal_amount)
                ->assign('order_amount', $order_amount)
                ->assign('range', $_ENV['statistics_range'])
                ->display('admin/chart/index.tpl')
        );
    }

    public function user($request, $response, $args)
    {
        $date = $args['date'];
        $day = strptime($date, '%Y%m%d');
        $day_start = mktime(0, 0, 0, $day['tm_mon'] + 1, $day['tm_mday'], $day['tm_year'] + 1900); // timestamp

        $logs = Statistics::where('item', 'user_traffic')
            ->where('created_at', '>', $day_start + 86400)
            ->where('created_at', '<', $day_start + 86400 + 86400)
            ->where('value', '!=', '0')
            ->get();

        return $response->write(
            $this->view()
                ->assign('date', $date)
                ->assign('logs', $logs)
                ->assign('next_day', date('Ymd', $day_start + 86400))
                ->assign('previous_day', date('Ymd', $day_start - 86400))
                ->display('admin/chart/user.tpl')
        );
    }

    public function node($request, $response, $args)
    {
        // 如果 $date 是 20220501, 则 $day_start 是 1651334400 (2022-05-01 00:00:00)
        // 要查的范围就是 2022-05-02 00:00:00 < created_at < 2022-05-03 00:00:00
        $date = $args['date'];
        $day = strptime($date, '%Y%m%d');
        $day_start = mktime(0, 0, 0, $day['tm_mon'] + 1, $day['tm_mday'], $day['tm_year'] + 1900); // timestamp

        $logs = Statistics::where('item', 'node_traffic')
            ->where('created_at', '>', $day_start + 86400)
            ->where('created_at', '<', $day_start + 86400 + 86400)
            ->where('value', '!=', '0')
            ->orderBy('value', 'desc')
            ->get();

        $nodes = Node::all();
        $names = [];
        foreach ($nodes as $node) {
            $names[$node->id] = $node->name;
        }

        return $response->write(
            $this->view()
                ->assign('date', $date)
                ->assign('logs', $logs)
                ->assign('names', $names)
                ->assign('next_day', date('Ymd', $day_start + 86400))
                ->assign('previous_day', date('Ymd', $day_start - 86400))
                ->display('admin/chart/node.tpl')
        );
    }

    public function finance($request, $response, $args)
    {
        $begin_time = strtotime(date('Y-m-01 00:00:00', strtotime('-1 month')));
        $end_time = strtotime(date('Y-m-d 23:59:59', strtotime(-date('d') . 'day')));

        $result = [];
        $total_fee = 0;
        $total_net_income = 0;
        $total_deal_amount = 0;
        $total_customer_price = 0;
        $total_deal_order_count = 0;
        $total_balance_payment_amount = 0;
        $active_payments = $_ENV['active_payments'];
        foreach ($active_payments as $payment) {
            $key = $payment['name'];
            $payment_fee = empty($payment['rate']) ? '0' : $payment['rate'];
            $condition = [
                ['created_at', '>', $begin_time],
                ['created_at', '<', $end_time],
                ['order_payment', '=', $key],
                ['order_status', '=', 'paid'],
            ];
            $deal_order = ProductOrder::where($condition)->get();
            $balance_payment_amount = $deal_order->sum('balance_payment');
            $deal_amount = $deal_order->sum('order_price');
            $deal_order_count = $deal_order->count();
            $fee = ($deal_amount - $balance_payment_amount) * $payment_fee;
            $net_income = $deal_amount - $balance_payment_amount - $fee;

            if ($deal_order_count == 0) {
                $customer_price = 0;
            } else {
                $customer_price = round(($net_income / 100) / $deal_order_count, 2);
            }

            $result[$key] = [
                'fee' => round($fee / 100, 2),                             // 手续费
                'net_income' => round($net_income / 100, 2),               // 净收入
                'deal_amount' => $deal_amount / 100,                       // 成交额
                'customer_price' => $customer_price,                       // 客单价
                'deal_order_count' => $deal_order_count,                   // 成交数
                'balance_payment_amount' => $balance_payment_amount / 100, // 余额抵扣
            ];

            // 累加统计数据
            $total_fee += $result[$key]['fee'];
            $total_net_income += $result[$key]['net_income'];
            $total_deal_amount += $result[$key]['deal_amount'];
            $total_customer_price += $result[$key]['customer_price'];
            $total_deal_order_count += $result[$key]['deal_order_count'];
            $total_balance_payment_amount += $result[$key]['balance_payment_amount'];
        }

        $result['Total'] = [
            'fee' => $total_fee,
            'net_income' => $total_net_income,
            'deal_amount' => $total_deal_amount,
            'customer_price' => round($total_customer_price / count($active_payments), 2),
            'deal_order_count' => $total_deal_order_count,
            'balance_payment_amount' => $total_balance_payment_amount,
        ];

        return $response->write(
            $this->view()
                ->assign('result', $result)
                ->display('admin/chart/finance.tpl')
        );
    }
}
