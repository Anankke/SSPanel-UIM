<?php

namespace App\Models;

/**
 * Node Model
 */

use App\Utils\Tools;

class Node extends Model
{
    protected $connection = "default";
    protected $table = "ss_node";

    protected $casts = [
        'node_speedlimit' => 'float',
        'traffic_rate' => 'float',
        'mu_only' => 'int',
        'sort' => 'int',
    ];

    public function getLastNodeInfoLog()
    {
        $id = $this->attributes['id'];
        $log = NodeInfoLog::where('node_id', $id)->orderBy('id', 'desc')->first();
        if ($log == null) {
            return null;
        }
        return $log;
    }

    public function getNodeUptime()
    {
        $log = $this->getLastNodeInfoLog();
        if ($log == null) {
            return "暂无数据";
        }
        return Tools::secondsToTime((int)$log->uptime);
    }


    public function getNodeUpRate()
    {
        $id = $this->attributes['id'];
        $log = NodeOnlineLog::where('node_id', $id)->where('log_time', '>=', time()-86400)->count();

        return $log/1440;
    }

    public function getNodeLoad()
    {
        $id = $this->attributes['id'];
        $log = NodeInfoLog::where('node_id', $id)->orderBy('id', 'desc')->whereRaw('`log_time`%1800<60')->limit(48)->get();
        return $log;
    }

    public function getNodeAlive()
    {
        $id = $this->attributes['id'];
        $log = NodeOnlineLog::where('node_id', $id)->orderBy('id', 'desc')->whereRaw('`log_time`%1800<60')->limit(48)->get();
        return $log;
    }

    public function getOnlineUserCount()
    {
        $id = $this->attributes['id'];
        $log = NodeOnlineLog::where('node_id', $id)->orderBy('id', 'desc')->first();
        if ($log == null) {
            return "暂无数据";
        }
        return $log->online_user;
    }

    public function getSpeedtest()
    {
        $id = $this->attributes['id'];
        $log = Speedtest::where('nodeid', $id)->orderBy('datetime', 'desc')->first();
        if ($log == null) {
            return "暂无数据";
        }


        return "电信延迟：".$log->telecomping." 下载：".$log->telecomeupload." 上传：".$log->telecomedownload."<br>
		联通延迟：".$log->unicomping." 下载：".$log->unicomupload." 上传：".$log->unicomdownload."<br>
		移动延迟：".$log->cmccping." 下载：".$log->cmccupload." 上传：".$log->cmccdownload."<br>定时测试，仅供参考";
    }

    public function getSpeedtestResult()
    {
        $id = $this->attributes['id'];
        $log = Speedtest::where('nodeid', $id)->orderBy('id', 'desc')->limit(48)->get();
        if ($log == null) {
            return "暂无数据";
        }


        return $log;
    }

    public function getTrafficFromLogs()
    {
        $id = $this->attributes['id'];

        $traffic = TrafficLog::where('node_id', $id)->sum('u') + TrafficLog::where('node_id', $id)->sum('d');

        if ($traffic == 0) {
            return "暂无数据";
        }

        return Tools::flowAutoShow($traffic);
    }

    public function isNodeOnline()
    {
        $node_heartbeat = $this->attributes['node_heartbeat'];
        $sort = $this->attributes['sort'];

        if (!($sort == 0 || $sort == 7 || $sort == 8 || $sort==10)) {
            return null;
        }

        if ($node_heartbeat == 0) {
            return null;
        }

        if (time() - $node_heartbeat > 300) {
            return false;
        } else {
            return true;
        }
    }

    public function isNodeTrafficOut()
    {
        $node_bandwidth = $this->attributes['node_bandwidth'];
        $node_bandwidth_limit = $this->attributes['node_bandwidth_limit'];

        if ($node_bandwidth_limit == 0 || $node_bandwidth < $node_bandwidth_limit) {
            return false;
        } else {
            return true;
        }
    }

    public function isNodeAccessable()
    {
        if ($this->isNodeTrafficOut() == false && $this->isNodeOnline() == true) {
            return true;
        } else {
            return false;
        }
    }

    public function changeNodeIp($server_name)
    {
        $ip = gethostbyname($server_name);
        $node_id = $this->attributes['id'];

        if ($ip == "") {
            return false;
        }

        $relay_rules = Relay::where('dist_node_id', $node_id)->get();
        foreach ($relay_rules as $relay_rule) {
            $relay_rule->dist_ip = $ip;
            $relay_rule->save();
        }

        $this->attributes['node_ip'] = $ip;
        return true;
    }

    public function getNodeIp()
    {
        $node_ip_str = $this->attributes['node_ip'];
        $node_ip_array = explode(',', $node_ip_str);
        return $node_ip_array[0];
    }
}
