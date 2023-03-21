<?php

declare(strict_types=1);

namespace App\Models;

use function array_key_exists;
use function count;
use function time;

final class Node extends Model
{
    protected $connection = 'default';

    protected $table = 'node';

    protected $casts = [
        'traffic_rate' => 'float',
        'mu_only' => 'int',
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
        return match ($this->sort) {
            0 => 'Shadowsocks',
            1 => 'ShadowsocksR',
            11 => 'V2Ray 节点',
            14 => 'Trojan',
            default => '系统保留',
        };
    }

    /**
     * 单端口多用户启用类型
     */
    public function muOnly(): string
    {
        return match ($this->mu_only) {
            -1 => '只启用普通端口',
            0 => '单端口多用户与普通端口并存',
            1 => '只启用单端口多用户',
            default => '错误类型',
        };
    }

    /**
     * 节点最后活跃时间
     */
    public function nodeHeartbeat(): string
    {
        return date('Y-m-d H:i:s', $this->node_heartbeat);
    }

    /**
     * 获取节点在线状态
     *
     * @return int 0 = new node OR -1 = offline OR 1 = online
     */
    public function getNodeOnlineStatus(): int
    {
        // 类型 9 或者心跳为 0
        if ($this->node_heartbeat === 0 || $this->sort === 9) {
            return 0;
        }
        return $this->node_heartbeat + 300 > time() ? 1 : -1;
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
    public function changeNodeIp(string $server_name): void
    {
        $result = dns_get_record($server_name, DNS_A + DNS_AAAA);
        $dns = [];
        if (count($result) > 0) {
            $dns = $result[0];
        }
        if (array_key_exists('ip', $dns)) {
            $ip = $dns['ip'];
        } elseif (array_key_exists('ipv6', $dns)) {
            $ip = $dns['ipv6'];
        } else {
            $ip = $server_name;
        }
        $this->node_ip = $ip;
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
}
