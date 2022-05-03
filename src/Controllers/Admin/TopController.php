<?php
namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\Node;
use App\Models\Statistics;

class TopController extends AdminController
{
    public function user($request, $response, $args)
    {
        $date = $args['date'];
        $array = strptime($date, '%Y%m%d');
        $start_timestamp = mktime(0, 0, 0, $array['tm_mon'] + 1, $array['tm_mday'], $array['tm_year'] + 1900);
        $stop_timestamp = $start_timestamp + 86400;
        $previous_day = date('Ymd', $start_timestamp - 86400);
        $next_day = date('Ymd', $stop_timestamp);

        $logs = Statistics::where('item', 'user_traffic')
            ->where('created_at', '>', $start_timestamp + 3600) // 记录的是 n-1 天前的数据
            ->where('created_at', '<', $stop_timestamp + 3600)
            ->where('value', '!=', '0')
            ->get();

        return $response->write(
            $this->view()
                ->assign('date', $date)
                ->assign('logs', $logs)
                ->assign('next_day', $next_day)
                ->assign('previous_day', $previous_day)
                ->display('admin/top/user.tpl')
        );
    }

    public function node($request, $response, $args)
    {
        $date = $args['date'];
        $array = strptime($date, '%Y%m%d');
        $start_timestamp = mktime(0, 0, 0, $array['tm_mon'] + 1, $array['tm_mday'], $array['tm_year'] + 1900);
        $stop_timestamp = $start_timestamp + 86400;
        $previous_day = date('Ymd', $start_timestamp - 86400);
        $next_day = date('Ymd', $stop_timestamp);

        $logs = Statistics::where('item', 'node_traffic')
            ->where('created_at', '>', $start_timestamp + 3600)
            ->where('created_at', '<', $stop_timestamp + 3600)
            ->where('value', '!=', '0')
            ->get();

        $nodes = Node::all();
        $names = [];
        foreach ($nodes as $node)
        {
            $names[$node->id] = $node->name;
        }

        return $response->write(
            $this->view()
                ->assign('date', $date)
                ->assign('logs', $logs)
                ->assign('names', $names)
                ->assign('next_day', $next_day)
                ->assign('previous_day', $previous_day)
                ->display('admin/top/node.tpl')
        );
    }
}
