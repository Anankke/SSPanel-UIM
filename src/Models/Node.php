<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Node Model
 *
 * @property-read   int     $id         id
 *
 * @property        string  $name       Display name
 * @property        bool    $type       If node display @todo Correct column name and type
 * @property        string  $server     Domain
 * @property        string  $method     Crypt method @deprecated
 * @property        string  $info       Infomation
 * @property        string  $status     Status description
 * @property        int     $sort       Node type @todo Correct column name to `type`
 * @property        int     $custom_method  Customs node crypt @deprecated
 * @property        float   $traffic_rate   Node traffic rate
 *
 * @todo More property
 *
 * @property        bool    $online     If node is online
 * @property        bool    $gfw_block  If node is blocked by GFW
 */

use App\Services\Config;
use App\Utils\Tools;
use App\Utils\URL;

final class Node extends Model
{
    protected $connection = 'default';

    protected $table = 'node';

    protected $casts = [
        'node_speedlimit' => 'float',
        'traffic_rate' => 'float',
        'mu_only' => 'int',
        'sort' => 'int',
        'type' => 'bool',
        'node_heartbeat' => 'int',
    ];

    /**
     * 节点是否显示和隐藏
     */
    public function type(): string
    {
        return $this->type ? '显示' : '隐藏';
    }

    /**
     * 节点类型
     */
    public function sort(): string
    {
        switch ($this->sort) {
            case 0:
                $sort = 'Shadowsocks';
                break;
            case 9:
                $sort = 'Shadowsocks - 单端口多用户';
                break;
            case 11:
                $sort = 'V2Ray 节点';
                break;
            case 13:
                $sort = 'Shadowsocks - V2Ray-Plugin&Obfs';
                break;
            case 14:
                $sort = 'Trojan';
                break;
            default:
                $sort = '系统保留';
        }
        return $sort;
    }

    /**
     * 单端口多用户启用类型
     */
    public function muOnly(): string
    {
        switch ($this->mu_only) {
            case -1:
                $mu_only = '只启用普通端口';
                break;
            case 0:
                $mu_only = '单端口多用户与普通端口并存';
                break;
            case 1:
                $mu_only = '只启用单端口多用户';
                break;
            default:
                $mu_only = '错误类型';
        }
        return $mu_only;
    }

    /**
     * 节点对应的国旗
     *
     * @return string [国家].png OR unknown.png
     */
    public function getNodeFlag(): string
    {
        $regex = $_ENV['flag_regex'];
        $matches = [];
        preg_match($regex, $this->name, $matches);
        return isset($matches[0]) ? $matches[0] . '.png' : 'unknown.png';
    }

    /**
     * 节点最后活跃时间
     */
    public function nodeHeartbeat(): string
    {
        return date('Y-m-d H:i:s', $this->node_heartbeat);
    }

    public function getLastNodeInfoLog()
    {
        $log = NodeInfoLog::where('node_id', $this->id)->orderBy('id', 'desc')->first();
        if ($log === null) {
            return null;
        }
        return $log;
    }

    public function getNodeUptime()
    {
        $log = $this->getLastNodeInfoLog();
        if ($log === null) {
            return '暂无数据';
        }
        return Tools::secondsToTime((int) $log->uptime);
    }

    public function getNodeUpRate()
    {
        $log = NodeOnlineLog::where('node_id', $this->id)->where('log_time', '>=', time() - 86400)->count();
        return $log / 1440;
    }

    public function getNodeLoad()
    {
        return NodeInfoLog::where('node_id', $this->id)->orderBy('id', 'desc')->whereRaw('`log_time`%1800<60')->limit(48)->get();
    }

    public function getNodeAlive()
    {
        return NodeOnlineLog::where('node_id', $this->id)->orderBy('id', 'desc')->whereRaw('`log_time`%1800<60')->limit(48)->get();
    }

    /**
     * 获取节点 5 分钟内最新的在线人数
     */
    public function getNodeOnlineUserCount(): int
    {
        if (in_array($this->sort, [9])) {
            return -1;
        }
        $log = NodeOnlineLog::where('node_id', $this->id)->where('log_time', '>', time() - 300)->orderBy('id', 'desc')->first();
        if ($log === null) {
            return 0;
        }
        return $log->online_user;
    }

    /**
     * 获取节点在线状态
     *
     * @return int 0 = new node OR -1 = offline OR 1 = online
     */
    public function getNodeOnlineStatus(): int
    {
        // 类型 9 或者心跳为 0
        if ($this->node_heartbeat === 0 || in_array($this->sort, [9])) {
            return 0;
        }
        return $this->node_heartbeat + 300 > time() ? 1 : -1;
    }

    /**
     * 获取节点最新负载
     */
    public function getNodeLatestLoad(): int
    {
        $log = NodeInfoLog::where('node_id', $this->id)->where('log_time', '>', time() - 300)->orderBy('id', 'desc')->first();
        if ($log === null) {
            return -1;
        }
        return ((int) explode(' ', $log->load)[0]) * 100;
    }

    /**
     * 获取节点最新负载文本信息
     */
    public function getNodeLatestLoadText(): string
    {
        $load = $this->getNodeLatestLoad();
        return $load === -1 ? 'N/A' : $load . '%';
    }

    /**
     * 获取节点速率文本信息
     */
    public function getNodeSpeedlimit(): string
    {
        if ($this->node_speedlimit === 0.0) {
            return '0';
        }
        if ($this->node_speedlimit >= 1024.00) {
            return round($this->node_speedlimit / 1024.00, 1) . 'Gbps';
        }
        return $this->node_speedlimit . 'Mbps';
    }

    /**
     * 节点是在线的
     */
    public function isNodeOnline(): ?bool
    {
        if ($this->node_heartbeat === 0) {
            return false;
        }
        return $this->node_heartbeat > time() - 300;
    }

    /**
     * 节点流量已耗尽
     */
    public function isNodeTrafficOut(): bool
    {
        return ! ($this->node_bandwidth_limit === 0 || $this->node_bandwidth < $this->node_bandwidth_limit);
    }

    /**
     * 节点是可用的，即流量未耗尽并且在线
     */
    public function isNodeAccessable(): bool
    {
        return $this->isNodeTrafficOut() === false && $this->isNodeOnline() === true;
    }

    /**
     * 更新节点 IP
     */
    public function changeNodeIp(string $server_name): bool
    {
        if (! Tools::isIp($server_name)) {
            $ip = gethostbyname($server_name);
            if ($ip === '') {
                return false;
            }
        } else {
            $ip = $server_name;
        }
        $this->node_ip = $ip;
        return true;
    }

    /**
     * 获取节点 IP
     */
    public function getNodeIp(): string
    {
        $node_ip_str = $this->node_ip;
        $node_ip_array = explode(',', $node_ip_str);
        return $node_ip_array[0];
    }

    /**
     * 获取出口地址 | 用于节点IP获取的地址
     */
    public function getOutAddress(): string
    {
        return explode(';', $this->server)[0];
    }

    public function getArgs(): array
    {
        return json_decode($this->custom_config, true);
    }

    public function setArgs(string $key, mixed $value): void
    {
        $current = json_decode($this->custom_config);
        $current[$key] = $value;
        $this->custom_config = json_encode($current);
        $this->save();
    }

    /**
     * 获取入口地址
     */
    public function getEntranceAddress(): string
    {
        if ($this->sort === 13) {
            $server = Tools::ssv2Array($this);
            return $server['add'];
        }

        $explode = explode(';', $this->server);
        $values = $this->getArgs();

        if (in_array($this->sort, [0]) && isset($values['server'])) {
            return $values['server'];
        }
        return $explode[0];
    }

    /**
     * 获取偏移后的端口
     *
     * @param mixed $port
     */
    public function getOffsetPort($port)
    {
        return Tools::outPort($this->server, $this->name, $port)['port'];
    }

    /**
     * 获取 SS/SSR 节点
     */
    public function getItem(User $user, int $mu_port = 0, int $is_ss = 0, bool $emoji = false): ?array
    {
        $node_name = $this->name;
        $return_array = [];
        if ($mu_port !== 0) {
            $mu_user = User::where('port', '=', $mu_port)->where('is_multi_user', '<>', 0)->first();
            if ($mu_user === null) {
                return null;
            }
            // 如果混淆和协议均为SS原生且为单端口的，即判断为AEAD单端口类型，密码配置为用户自身密码
            if ($mu_user->obfs === 'plain' && $mu_user->protocol === 'origin') {
                $mu_user->passwd = $user->passwd;
                $mu_user->obfs_param = '';
                $mu_user->protocol_param = '';
            } else {
                $mu_user->obfs_param = $user->getMuMd5();
                $mu_user->protocol_param = $user->id . ':' . $user->passwd;
            }
            $user = $mu_user;
            $node_name .= ($_ENV['disable_sub_mu_port'] ? '' : ' - ' . $mu_port . ' 单端口');
        }
        if ($is_ss) {
            if (! URL::SSCanConnect($user)) {
                return null;
            }
            $user = URL::getSSConnectInfo($user);
            $return_array['type'] = 'ss';
        } else {
            if (! URL::SSRCanConnect($user)) {
                return null;
            }
            $user = URL::getSSRConnectInfo($user);
            $return_array['type'] = 'ssr';
        }
        $return_array['address'] = $this->getEntranceAddress();
        $return_array['port'] = $user->port;
        $return_array['protocol'] = $user->protocol;
        $return_array['protocol_param'] = $user->protocol_param;
        $return_array['obfs'] = $user->obfs;
        $return_array['obfs_param'] = $user->obfs_param;
        if ($mu_port !== 0 && strpos($this->server, ';') !== false) {
            $node_tmp = Tools::outPort($this->server, $this->name, $mu_port);
            $return_array['port'] = $node_tmp['port'];
            $node_name = $node_tmp['name'];
        }
        $return_array['passwd'] = $user->passwd;
        $return_array['method'] = $user->method;
        $return_array['remark'] = ($emoji ? Tools::addEmoji($node_name) : $node_name);
        $return_array['class'] = $this->node_class;
        $return_array['group'] = $_ENV['appName'];
        $return_array['ratio'] = $this->traffic_rate;

        return $return_array;
    }

    /**
     * 获取 V2Ray 节点
     */
    public function getV2RayItem(User $user, int $mu_port = 0, int $is_ss = 0, bool $emoji = false): array
    {
        $item = Tools::v2Array($this);
        $item['type'] = 'vmess';
        $item['remark'] = ($emoji ? Tools::addEmoji($this->name) : $this->name);
        $item['id'] = $user->uuid;
        $item['class'] = $this->node_class;
        return $item;
    }

    /**
     * 获取 V2RayPlugin | obfs 节点
     *
     * @param User $user 用户
     *
     * @return array|null
     */
    public function getV2RayPluginItem(User $user, int $mu_port = 0, int $is_ss = 0, bool $emoji = false): ?array
    {
        $return_array = Tools::ssv2Array($this);
        // 非 AEAD 加密无法使用
        if ($return_array['net'] !== 'obfs' && ! in_array($user->method, Config::getSupportParam('ss_aead_method'))) {
            return null;
        }
        $return_array['remark'] = ($emoji ? Tools::addEmoji($this->name) : $this->name);
        $return_array['address'] = $return_array['add'];
        $return_array['method'] = $user->method;
        $return_array['passwd'] = $user->passwd;
        $return_array['protocol'] = 'origin';
        $return_array['protocol_param'] = '';
        if ($return_array['net'] === 'obfs') {
            $return_array['obfs_param'] = $user->getMuMd5();
        } else {
            $return_array['obfs'] = 'v2ray';
            if ($return_array['tls'] === 'tls' && $return_array['net'] === 'ws') {
                $return_array['obfs_param'] = 'mode=ws;security=tls;path=' . $return_array['path'] .
                    ';host=' . $return_array['host'];
            } else {
                $return_array['obfs_param'] = 'mode=ws;security=none;path=' . $return_array['path'] .
                    ';host=' . $return_array['host'];
            }
            $return_array['path'] .= '?redirect=' . $user->getMuMd5();
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
     */
    public function getTrojanItem(User $user, int $mu_port = 0, int $is_ss = 0, bool $emoji = false): array
    {
        $server = explode(';', $this->server);
        $opt = [];
        $item = [];
        $opt = $this->getArgs();

        $item['remark'] = ($emoji ? Tools::addEmoji($this->name) : $this->name);
        $item['type'] = 'trojan';
        $item['address'] = $server[0];
        $item['port'] = (isset($opt['offset_port_user']) ? (int) $opt['offset_port_user'] : (isset($opt['offset_port_node']) ? (int) $opt['offset_port_node'] : 443));
        $item['passwd'] = $user->uuid ?? '';
        $item['host'] = $item['address'];
        $item['net'] = (isset($opt['grpc']) ? 'grpc' : '');
        $item['servicename'] = ($opt['servicename'] ?? '');
        $item['flow'] = ($opt['flow'] ?? '');
        $xtls = ($opt['enable_xtls'] ?? '');
        if ($xtls === 'true') {
            $item['tls'] = 'xtls';
        } else {
            $item['tls'] = 'tls';
        }
        if (isset($opt['host'])) {
            $item['host'] = $opt['host'];
        }
        return $item;
    }
}
