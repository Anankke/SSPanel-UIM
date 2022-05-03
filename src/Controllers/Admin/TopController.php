<?php
namespace App\Controllers\Admin;

use App\Models\Node;
use App\Models\Statistics;
use App\Controllers\AdminController;

class TopController extends AdminController
{
    public function user($request, $response, $args)
    {
        $date = $args['date'];
        $day = strptime($date, '%Y%m%d');
        $day_start = mktime(0, 0, 0, $day['tm_mon'] + 1, $day['tm_mday'], $day['tm_year'] + 1900); // timestamp

        $logs = Statistics::where('item', 'user_traffic')
            ->where('created_at', '>', $day_start + 86400)
            ->where('created_at', '<', $day_start + 86400 + 86400)
            ->where('value', '!=', '0')
            ->orderBy('value', 'desc')
            ->limit(100)
            ->get();

        return $response->write(
            $this->view()
                ->assign('date', $date)
                ->assign('logs', $logs)
                ->assign('next_day', date('Ymd', $day_start + 86400))
                ->assign('previous_day', date('Ymd', $day_start - 86400))
                ->display('admin/top/user.tpl')
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
                ->display('admin/top/node.tpl')
        );
    }
}
