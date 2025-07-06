<?php

declare(strict_types=1);

namespace App\Models;

use App\Utils\Tools;
use Exception;
use Illuminate\Database\Query\Builder;
use function dns_get_record;
use function time;
use const DNS_A;
use const DNS_AAAA;

/**
 * @property int    $id                      节点ID
 * @property string $name                    节点名称
 * @property int    $type                    节点启用
 * @property string $server                  节点地址
 * @property string $custom_config           自定义配置
 * @property int    $sort                    节点类型
 * @property float  $traffic_rate            流量倍率
 * @property int    $is_dynamic_rate         是否启用动态流量倍率
 * @property int    $dynamic_rate_type       动态流量倍率计算方式
 * @property string $dynamic_rate_config     动态流量倍率配置
 * @property int    $node_class              节点等级
 * @property int    $node_speedlimit         节点限速
 * @property int    $node_bandwidth          节点流量
 * @property int    $node_bandwidth_limit    节点流量限制
 * @property int    $bandwidthlimit_resetday 流量重置日
 * @property int    $node_heartbeat          节点心跳
 * @property int    $online_user             节点在线用户
 * @property string $ipv4                    IPv4地址
 * @property string $ipv6                    IPv6地址
 * @property int    $node_group              节点群组
 * @property int    $online                  在线状态
 * @property int    $gfw_block               是否被GFW封锁
 * @property string $password                后端连接密码
 *
 * @mixin Builder
 */
final class Node extends Model
{
    protected $connection = 'default';
    protected $table = 'node';

    protected $casts = [
        'traffic_rate' => 'float',
        'node_heartbeat' => 'int',
    ];

    /**
     * 节点状态颜色
     */
    public function getColorAttribute(): string
    {
        return match ($this->getNodeOnlineStatus()) {
            0 => 'orange',
            1 => 'green',
            default => 'red',
        };
    }

    public function getConnectionTypeAttribute(): int
    {
        // 0 = IPv4, 1 = IPv6, 2 = DualStack
        return $this->ipv6 !== '::1' && $this->ipv4 !== '127.0.0.1' ? 2 : ($this->ipv4 !== '127.0.0.1' ? 0 : 1);
    }

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
            1 => 'Shadowsocks2022',
            2 => 'TUIC',
            3 => 'WireGuard',
            11 => 'Vmess',
            14 => 'Trojan',
            default => '未知',
        };
    }

    public function isDynamicRate(): string
    {
        return $this->is_dynamic_rate ? '是' : '否';
    }

    public function dynamicRateType(): string
    {
        return match ($this->dynamic_rate_type) {
            0 => 'Logistic',
            1 => 'Linear',
            default => '未知',
        };
    }

    /**
     * 获取节点在线状态
     *
     * @return int 0 = new node, -1 = offline, 1 = online
     */
    public function getNodeOnlineStatus(): int
    {
        return $this->node_heartbeat === 0 ? 0 : ($this->node_heartbeat + 600 > time() ? 1 : -1);
    }

    /**
     * 更新节点 IP
     */
    public function updateNodeIp(): void
    {
        if (Tools::isIPv4($this->server)) {
            $this->ipv4 = $this->server;
            $this->ipv6 = '::1';
        } elseif (Tools::isIPv6($this->server)) {
            $this->ipv4 = '127.0.0.1';
            $this->ipv6 = $this->server;
        } else {
            try {
                $result = dns_get_record($this->server, DNS_A + DNS_AAAA);
                $this->ipv4 = $result[0]['ip'] ?? '127.0.0.1';
                $this->ipv6 = $result[1]['ipv6'] ?? '::1';
            } catch (Exception) {
                $this->ipv4 = '127.0.0.1';
                $this->ipv6 = '::1';
            }
        }
    }
}
