# Hysteria2 端口跳跃配置优先级

SSPanel `sort=15` 节点的 `custom_config` 与 XrayR 同时升级后，按以下顺序选择
整个端口跳跃配置对象，不合并低优先级的开关和端口：

1. `custom_config.portHopping`
2. `custom_config.hysteria2.portHopping`（原有写法仍支持）
3. `custom_config.hysteria2.finalmask.quicParams.udpHop`

推荐示例：

```json
{
  "offset_port_node": "55444",
  "offset_port_user": "55444",
  "host": "hy.example.com",
  "portHopping": {
    "enabled": true,
    "autoConfigureFirewall": true,
    "ports": "55001-60000",
    "interval": "5-10"
  },
  "hysteria2": {
    "version": 2,
    "udpIdleTimeout": 60,
    "finalmask": {
      "quicParams": {
        "congestion": "force-brutal",
        "brutalUp": "60 mbps",
        "brutalDown": "0"
      }
    }
  }
}
```

URI、Clash/Mihomo、sing-box 和 Xray JSON 的订阅范围都选用 `55001-60000`，
XrayR 自动将该范围转发到节点监听端口 `55444`。`offset_port_user` 仍是客户端
主端口，存在额外中转时仍需按部署设置转发与端口范围。
`interval` 是客户端设置，不控制服务端 NAT；Xray 的范围可以保留 `5-10`，
其他客户端按现有格式转换周期。

顶层 `{"enabled":false}` 或空对象会覆盖下层开启配置，客户端不发布跳端口，
XrayR 不安装相应自动转发。顶层不存在或为 `null` 时才继续查找下层。
旧 `udpHop` 的 `enable` 布尔值也支持；`enabled` 与 `enable` 同时提供时必须一致。
没有开关、仅包含 `ports` 的旧 `udpHop` 保持隐式开启的订阅兼容行为。

自动防火墙必须同时开启 `enabled` 和 `autoConfigureFirewall`，并要求 Linux
上的 NAT 支持和 root/CAP_NET_ADMIN 权限。通配监听处理 IPv4/IPv6 两套规则，
不依赖 UFW。它不会自动开放 INPUT、云安全组或容器映射。

本地 `custom_inbound.json` 同样支持“顶层优先”：
`入站对象.portHopping` > `streamSettings.finalmask.quicParams.udpHop`。
静态文件更改后重启 XrayR；面板节点仍由原有周期轮询更新。
