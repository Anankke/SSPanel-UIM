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

use App\Services\Config;
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
        if ($this->node_heartbeat === 0) {
            return false;
        }

        $nodeSort = [1, 2, 5, 9, 999];
        if (in_array($this->sort, $nodeSort)) {
            return null;
        }

        return ($this->node_heartbeat > time() - $delay);
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

    /**
     * 获取出口地址
     */
    public function getOutServer(): string
    {
        return explode(';', $this->server)[0];
    }

    /**
     * 获取入口地址
     */
    public function getServer(): string
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

    /**
     * 获取偏移后的端口
     *
     * @param mixed $port
     */
    public function getOffsetPort($port)
    {
        return Tools::OutPort($this->attributes['server'], $this->attributes['name'], $port)['port'];
    }

    /**
     * 获取 SS/SSR 节点
     *
     * @param User $user
     * @param int  $mu_port
     * @param int  $relay_rule_id
     * @param int  $is_ss
     * @param bool $emoji
     */
    public function getItem(User $user, int $mu_port = 0, int $relay_rule_id = 0, int $is_ss = 0, bool $emoji = false):? array
    {
        $relay_rule = Relay::where('id', $relay_rule_id)
            ->where(
                static function ($query) use ($user) {
                    $query->Where('user_id', '=', $user->id)
                        ->orWhere('user_id', '=', 0);
                }
            )
            ->orderBy('priority', 'DESC')
            ->orderBy('id')
            ->first();
        $node_name = $this->name;
        if ($relay_rule != null) {
            $node_name .= ' - ' . $relay_rule->dist_node()->name;
        }
        if ($mu_port != 0) {
            $mu_user = User::where('port', '=', $mu_port)->where('is_multi_user', '<>', 0)->first();
            if ($mu_user == null) {
                return null;
            }
            $mu_user->obfs_param = $user->getMuMd5();
            $mu_user->protocol_param = $user->id . ':' . $user->passwd;
            $user = $mu_user;
            $node_name .= ($_ENV['disable_sub_mu_port'] ? '' : ' - ' . $mu_port . ' 单端口');
        }
        if ($is_ss) {
            if (!URL::SSCanConnect($user)) {
                return null;
            }
            $user = URL::getSSConnectInfo($user);
            $return_array['type'] = 'ss';
        } else {
            if (!URL::SSRCanConnect($user)) {
                return null;
            }
            $user = URL::getSSRConnectInfo($user);
            $return_array['type'] = 'ssr';
        }
        $return_array['address']        = $this->getServer();
        $return_array['port']           = $user->port;
        $return_array['protocol']       = $user->protocol;
        $return_array['protocol_param'] = $user->protocol_param;
        $return_array['obfs']           = $user->obfs;
        $return_array['obfs_param']     = $user->obfs_param;
        if ($mu_port != 0 && strpos($this->server, ';') !== false) {
            $node_tmp             = Tools::OutPort($this->server, $this->name, $mu_port);
            $return_array['port'] = $node_tmp['port'];
            $node_name            = $node_tmp['name'];
        }
        $return_array['passwd'] = $user->passwd;
        $return_array['method'] = $user->method;
        $return_array['remark'] = ($emoji ? Tools::addEmoji($node_name) : $node_name);
        $return_array['class']  = $this->node_class;
        $return_array['group']  = $_ENV['appName'];
        $return_array['ratio']  = ($relay_rule != null ? $this->traffic_rate + $relay_rule->dist_node()->traffic_rate : $this->traffic_rate);

        return $return_array;
    }

    /**
     * 获取 V2Ray 节点
     *
     * @param User $user
     * @param int  $mu_port
     * @param int  $relay_rule_id
     * @param int  $is_ss
     * @param bool $emoji
     */
    public function getV2RayItem(User $user, int $mu_port = 0, int $relay_rule_id = 0, int $is_ss = 0, bool $emoji = false): array
    {
        $item           = Tools::v2Array($this->server);
        $item['type']   = 'vmess';
        $item['remark'] = ($emoji ? Tools::addEmoji($this->name) : $this->name);
        $item['id']     = $user->getUuid();
        $item['class']  = $this->node_class;
        return $item;
    }

    /**
     * 获取 V2RayPlugin | obfs 节点
     *
     * @param User $user 用户
     * @param int  $mu_port
     * @param int  $relay_rule_id
     * @param int  $is_ss
     * @param bool $emoji
     *
     * @return array|null
     */
    public function getV2RayPluginItem(User $user, int $mu_port = 0, int $relay_rule_id = 0, int $is_ss = 0, bool $emoji = false)
    {
        $return_array = Tools::ssv2Array($this->server);
        // 非 AEAD 加密无法使用
        if ($return_array['net'] != 'obfs' && !in_array($user->method, Config::getSupportParam('ss_aead_method'))) {
            return null;
        }
        $return_array['remark']         = ($emoji ? Tools::addEmoji($this->name) : $this->name);
        $return_array['address']        = $return_array['add'];
        $return_array['method']         = $user->method;
        $return_array['passwd']         = $user->passwd;
        $return_array['protocol']       = 'origin';
        $return_array['protocol_param'] = '';
        if ($return_array['net'] == 'obfs') {
            $return_array['obfs_param'] = $user->getMuMd5();
        } else {
            $return_array['obfs'] = 'v2ray';
            if ($return_array['tls'] == 'tls' && $return_array['net'] == 'ws') {
                $return_array['obfs_param'] = ('mode=ws;security=tls;path=' . $return_array['path'] .
                    ';host=' . $return_array['host']);
            } else {
                $return_array['obfs_param'] = ('mode=ws;security=none;path=' . $return_array['path'] .
                    ';host=' . $return_array['host']);
            }
            $return_array['path'] = ($return_array['path'] . '?redirect=' . $user->getMuMd5());
        }
        $return_array['class'] = $this->node_class;
        $return_array['group'] = $_ENV['appName'];
        $return_array['type'] = 'ss';
        $return_array['ratio'] = $this->traffic_rate;

        return $return_array;
    }

    /**
     * Trojan 节点
     *
     * @param User $user 用户
     * @param int  $mu_port
     * @param int  $relay_rule_id
     * @param int  $is_ss
     * @param bool $emoji
     */
    public function getTrojanItem(User $user, int $mu_port = 0, int $relay_rule_id = 0, int $is_ss = 0, bool $emoji = false): array
    {
        $server = explode(';', $this->server);
        $opt    = [];
        if (isset($server[1])) {
            $opt = URL::parse_args($server[1]);
        }
        $item['remark']   = ($emoji ? Tools::addEmoji($this->name) : $this->name);
        $item['type']     = 'trojan';
        $item['address']  = $server[0];
        $item['port']     = (isset($opt['port']) ? (int) $opt['port'] : 443);
        $item['passwd']   = $user->getUuid();
        $item['host']     = $item['address'];
        if (isset($opt['host'])) {
            $item['host'] = $opt['host'];
        }
        return $item;
    }
}
