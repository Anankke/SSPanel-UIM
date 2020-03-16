<?php

namespace App\Models;

/**
 * Node Model
 *
 * @property-read   int     $id         id
 * @property        string  $name       Display name
 * @property        int     $type       If node display @todo Correct column name and type
 * @property        string  $server     Domain
 * @property        string  $method     Crypt method @deprecated
 * @property        string  $info       Infomation
 * @property        string  $status     Status description
 * @property        int     $sort       Node type @todo Correct column name to `type`
 * @property        int     $custom_method  Customs node crypt @deprecated
 * @property        float   $traffic_rate   Node traffic rate
 * @todo More property
 * @property        bool    $online     If node is online
 * @property        bool    $gfw_block  If node is blocked by GFW
 */

use App\Utils\{Tools, URL};

class Node extends Model
{
    protected $connection = 'default';

    protected $table = 'ss_node';

    protected $casts = [
        'node_speedlimit' => 'float',
        'traffic_rate'    => 'float',
        'mu_only'         => 'int',
        'sort'            => 'int',
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
        return Tools::secondsToTime((int) $log->uptime);
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


    public function getServer()
    {
        $out = '';
        $explode = explode(';', $this->attributes['server']);
        if (in_array($this->attributes['sort'], [0, 10])) {
            if (isset($explode[1]) && stripos($explode[1], 'server=') !== false) {
                $out = URL::parse_args($explode[1])['server'];
            }
        }
        return ($out != '' ? $out : $explode[0]);
    }

    public function getOffsetPort($port)
    {
        return Tools::OutPort($this->attributes['server'], $this->attributes['name'], $port)['port'];
    }
}
