<?php

namespace App\Models;

/**
 * Node Model
 */

use App\Utils\Tools;

class Node extends Model
{
    protected $connection = 'default';
    protected $table = 'ss_node';

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
            return '暂无数据';
        }
        return Tools::secondsToTime((int)$log->uptime);
    }


    public function getNodeUpRate()
    {
        $id = $this->attributes['id'];
        $log = NodeOnlineLog::where('node_id', $id)->where('log_time', '>=', time() - 86400)->count();

        return $log / 1440;
    }

    public function getNodeLoad()
    {
        $id = $this->attributes['id'];
        $log = NodeInfoLog::where('node_id', $id)->orderBy(
            'id',
            'desc'
        )->whereRaw('`log_time`%1800<60')->limit(48)->get();
        return $log;
    }

    public function getNodeAlive()
    {
        $id = $this->attributes['id'];
        $log = NodeOnlineLog::where('node_id', $id)->orderBy(
            'id',
            'desc'
        )->whereRaw('`log_time`%1800<60')->limit(48)->get();
        return $log;
    }

    public function getOnlineUserCount()
    {
        $id = $this->attributes['id'];
        $log = NodeOnlineLog::where('node_id', $id)->where('log_time', '>', time() - 300)->orderBy(
            'id',
            'desc'
        )->first();
        if ($log == null) {
            return 0;
        }
        return $log->online_user;
    }

    public function getSpeedtest()
    {
        $id = $this->attributes['id'];
        $log = Speedtest::where('nodeid', $id)->orderBy('datetime', 'desc')->first();
        if ($log == null) {
            return '暂无数据';
        }


        return '电信延迟：' . $log->telecomping . ' 下载：' . $log->telecomeupload . ' 上传：' . $log->telecomedownload . '<br>
		联通延迟：' . $log->unicomping . ' 下载：' . $log->unicomupload . ' 上传：' . $log->unicomdownload . '<br>
		移动延迟：' . $log->cmccping . ' 下载：' . $log->cmccupload . ' 上传：' . $log->cmccdownload . '<br>定时测试，仅供参考';
    }

    public function getSpeedtestResult()
    {
        $id = $this->attributes['id'];
        $log = Speedtest::where('nodeid', $id)->orderBy('id', 'desc')->limit(48)->get();
        if ($log == null) {
            return '暂无数据';
        }


        return $log;
    }

    public function getTrafficFromLogs()
    {
        $id = $this->attributes['id'];

        $traffic = TrafficLog::where('node_id', $id)->sum('u') + TrafficLog::where('node_id', $id)->sum('d');

        if ($traffic == 0) {
            return '暂无数据';
        }

        return Tools::flowAutoShow($traffic);
    }

    public function isNodeOnline()
    {
        $delay = 300;
        if ($this->attributes['node_heartbeat'] === 0) {
            return false;
        }

        if (!in_array($this->attributes['sort'], [0, 7, 8, 10, 11, 12, 13])) {
            return null;
        }

        if ($this->attributes['node_heartbeat'] > time() - $delay) {
            return true;
        }
        return false;
    }

    public function isNodeTrafficOut()
    {
        $node_bandwidth = $this->attributes['node_bandwidth'];
        $node_bandwidth_limit = $this->attributes['node_bandwidth_limit'];

        return !($node_bandwidth_limit == 0 || $node_bandwidth < $node_bandwidth_limit);
    }

    public function isNodeAccessable()
    {
        return $this->isNodeTrafficOut() == false && $this->isNodeOnline() == true;
    }

    public function changeNodeIp($server_name)
    {
        $ip = gethostbyname($server_name);
        $node_id = $this->attributes['id'];

        if ($ip == '') {
            return false;
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
