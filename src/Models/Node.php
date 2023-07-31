<?php

declare(strict_types=1);

namespace App\Models;

use App\Utils\Tools;
use Exception;
use function array_key_exists;
use function count;
use function dns_get_record;
use function time;
use const DNS_A;
use const DNS_AAAA;

final class Node extends Model
{
    protected $connection = 'default';
    protected $table = 'node';

    protected $casts = [
        'traffic_rate' => 'float',
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
            11 => 'V2Ray',
            14 => 'Trojan',
            default => '未知',
        };
    }

    /**
     * 节点最后活跃时间
     */
    public function nodeHeartbeat(): string
    {
        return Tools::toDateTime($this->node_heartbeat);
    }

    /**
     * 获取节点在线状态
     *
     * @return int 0 = new node OR -1 = offline OR 1 = online
     */
    public function getNodeOnlineStatus(): int
    {
        return $this->node_heartbeat === 0 ? 0 : ($this->node_heartbeat + 600 > time() ? 1 : -1);
    }

    /**
     * 获取节点速率文本信息
     */
    public function getNodeSpeedlimit(): string
    {
        return Tools::autoMbps($this->node_speedlimit);
    }

    /**
     * 节点流量已耗尽
     */
    public function isNodeTrafficOut(): bool
    {
        return ! ($this->node_bandwidth_limit === 0 || $this->node_bandwidth < $this->node_bandwidth_limit);
    }

    /**
     * 更新节点 IP
     */
    public function changeNodeIp(string $server_name): void
    {
        try {
            $result = dns_get_record($server_name, DNS_A + DNS_AAAA);
        } catch (Exception $e) {
            $result = false;
        }

        $dns = [];

        if ($result !== false && count($result) > 0) {
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
}
