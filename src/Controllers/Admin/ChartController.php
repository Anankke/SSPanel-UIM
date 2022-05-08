<?php
namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\Node;
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
}
