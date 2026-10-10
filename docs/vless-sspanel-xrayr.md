# SSPanel-UIM → XrayR：VLESS 接入与全流程验证

验证日期：2026-10-01。基于本仓库工作区代码及配套 XrayR，服务端内核为 HoshinoNeko/Xray-core 26.9.9。所有域名、UUID、证书路径和密钥均为示例或占位符，不是生产配置。

## 1. 结论与支持边界

`sort=11` 仍然是 V2Ray 类型，不要新增节点类型，也不要在 XrayR 中将面板 NodeType 改成 Vless。

- `enable_vless` 为 JSON 整数 `1`、字符串 `"1"` 或布尔 `true`：输出 VLESS。
- 缺失、`0`、`"0"`、`false` 或其他值：保持原 VMess 分支。判断为严格匹配，不把任意非空字符串当作 true。
- 用户认证使用 SSPanel 用户的 `uuid`，无需在 `custom_config` 手写用户列表。
- REALITY 公钥由服务端 `private_key` 通过 X25519 推导。客户端订阅只输出公钥，不输出私钥或 `dest`。PHP 必须启用 sodium 扩展。
- 无效 VLESS 节点在订阅中单独跳过，其他正常节点仍下发；日志只记录被跳过的节点 ID。后台保存 sort=11 的 VLESS 配置时校验端口、传输、security、flow 和 REALITY 客户端所需参数。
- network 缺失或为空时前后端均使用 TCP；`raw → tcp`、`websocket → ws`、`splithttp → xhttp`，并统一处理大小写。VMess 分支保持原有解析。

以下不是仅解析配置的结果：测试启动真实服务端与客户端，通过代理请求本地 HTTP 目标并校验响应正文。

| 节点配置 | Xray 26.9.9 JSON | Mihomo 1.19.32 | 官方 sing-box 1.14.2 |
| --- | --- | --- | --- |
| TCP / none | 通过 | 通过 | 通过 |
| TCP / TLS | 通过 | 通过 | 通过 |
| TCP / TLS / Vision | 通过 | 通过 | 通过 |
| TCP / REALITY / Vision | 通过 | 通过，需 MLKEM 开关 | **连接失败：内核握手不兼容** |
| WebSocket / TLS | 通过 | 通过 | 通过 |
| gRPC / TLS | 通过 | 通过 | 通过 |
| HTTPUpgrade / TLS | 通过 | 通过 | 通过 |
| XHTTP / TLS，auto | 通过 | 通过 | 不支持，订阅中省略该节点 |

普通 URI 的协议、UUID、编码、IPv6、端口及传输参数已通过面板测试；没有逐个操作 v2rayN、v2rayNG、iOS 等图形客户端的导入界面。经典 Clash 不支持 VLESS；此处 Clash 格式的连接验证使用 Mihomo，不能推论所有旧 Clash 内核可用。V2RayJson 的 VLESS 输出按当前 Xray schema 验证，不保证旧 V2Ray 内核支持 REALITY/XHTTP。

### REALITY 的重要版本兼容差异

本次依赖中的 REALITY 服务端会拒绝缺少 `X25519MLKEM768` 的 ClientHello。Mihomo 默认会移除该 key share，必须下发：

```yaml
reality-opts:
  public-key: REALITY_PUBLIC_KEY
  short-id: "0123456789abcdef"
  support-x25519mlkem768: true
```

订阅生成器现已补上此字段，实际连接通过。[Mihomo 官方配置说明](https://wiki.metacubex.one/config/proxies/tls/)。

官方 sing-box 1.14.2 的 REALITY 客户端在握手前无条件移除该 key share，实际测试连接失败；修改 `sni`、`insecure` 或公钥不能解决这个问题。见 [该版本客户端源码](https://github.com/SagerNet/sing-box/blob/v1.14.2/common/tls/reality_client.go)。当前仍生成符合 sing-box schema 的 REALITY 配置，但**不能宣称与此服务端可连接**。需要使用修复了此行为的客户端内核；本次没有降级服务端或修改第三方客户端。

官方 sing-box 没有 XHTTP transport，故不下发无效 outbound。[官方传输类型说明](https://sing-box.sagernet.org/configuration/shared/v2ray-transport/)。

如果用户只有 XHTTP、无效节点或没有可用节点，sing-box 配置保留可加载的选择组，并将首条路由设为 reject。空测速组改为选择组，不进行直连测速；代理流量拒绝转发，不会自动改成直连。

## 2. 原始行为、调用链和修改内容

此前 XrayR 的服务端解析和 SSPanel 的订阅生成属于不同路径。原四个 sort=11 订阅生成器均未读取 `enable_vless`，所以服务端可以运行 VLESS，但客户端仍拿到 VMess。

面板节点下发路径：

```text
Node 数据库 custom_config 原始 JSON
  → Controllers/WebAPI/NodeController::getInfo（解码为对象并输出）
  → XrayR api/sspanel::GetNodeInfo / ParseSSPanelNodeInfo
  → controller.Start / inboundBuilder / 用户安装
```

用户与订阅路径：

```text
Controllers/WebAPI/UserController::index → 用户 uuid → XrayR GetUserList
Controllers/SubController::index
  → Services/Subscribe::getContent → getClient
  → Subscribe/{V2Ray,Clash,SingBox,V2RayJson}::getContent
  → Subscribe::getUserNodes → sort=11 → Vless::enabled / client / uri
```

Node Model 没有统一解析 VLESS 布尔开关的方法；四个生成器现在复用 `src/Services/Subscribe/Vless.php`。它统一判断协议、客户端端口、security、flow、REALITY 公钥及 SNI。原 VMess 分支保留，其他 Node Sort 不进入该覆盖逻辑。

| 文件 | 修改目的 |
| --- | --- |
| `src/Services/Subscribe/Vless.php` | 统一开关与参数解析、标准 URI、X25519 公钥推导与校验 |
| `src/Services/Subscribe/V2Ray.php` | VLESS URI 替代 VMess Base64 JSON |
| `src/Services/Subscribe/Clash.php` | VLESS/Mihomo transport、Vision、REALITY 与 MLKEM 开关 |
| `src/Services/Subscribe/SingBox.php` | VLESS outbound、TLS/REALITY、transport；省略不支持的 XHTTP |
| `src/Services/Subscribe/V2RayJson.php` | 原生 Xray VLESS vnext/users 与 streamSettings |
| `.github/workflows/ci.yml` | 测试环境启用 sodium |
| `tests/Integration/Services/Subscribe/VlessSubscriptionTest.php` | 实际数据库、节点/用户/订阅控制器及四种生成格式测试 |
| XrayR `api/sspanel/model.go`、`sspanel.go` | 接受整数、字符串和布尔 enable_vless，与订阅判断对齐 |
| XrayR `api/sspanel/hysteria2_internal_test.go` | 面板开关输入类型兼容测试 |
| XrayR `service/controller/vless_contract_test.go` | 用面板真实响应启动服务端与实际客户端、验证转发 |
| `src/Services/Subscribe/Base.php` | 统一隔离无效 VLESS 节点，使用仅含节点 ID 的日志 |
| `src/Controllers/Admin/NodeController.php` | 保存 VLESS 节点前校验参数 |
| `config/appprofile.example.php` | 修正完整 Xray 模板的日志、DNS、SOCKS UDP 字段和 HTTP settings 对象 |

专用 SS/SIP002/SIP008/Trojan/Hysteria2 订阅不增加 VLESS；这些格式只处理原有对应协议。通用 JSON 元数据格式不是原生客户端 outbound 配置，不应当作 Xray JSON 使用。

## 3. 面板节点基础设置

创建或编辑节点，设置：

- Sort：`11` / V2Ray。
- 节点地址：`node.example.com`，仅主机名或裸 IPv6，不带 `vless://`、端口、路径。
- 节点启用、节点等级/分组与用户权限相符；用户有效且有可用额度。
- 将下面对应的完整 JSON 写入 `custom_config`。建议每个传输组合单独一个节点，端口不可重复监听。
- 面板开启 V2Ray URI 订阅开关 `enable_v2_sub`；全局 `Subscribe` 与 `subUrl` 正确设置。

`offset_port_node` 是 XrayR 监听端口，`offset_port_user` 是订阅连接端口。客户端端口优先级为 `offset_port_user → offset_port_node → 443`，与原 VMess 一致。监听端口应明确填写；两者不同需要外部中转/端口转发，VLESS 开关不会自动创建转发。

以下示例显式填写 `network`，避免依赖后端默认值。`flow` 只在 TCP + TLS/REALITY 的 Vision 组合填写，WS/gRPC/HTTPUpgrade/XHTTP 不要填写 Vision。

## 4. 各传输组合 custom_config

### 4.1 VLESS + TCP，无 TLS

```json
{
  "enable_vless": 1,
  "network": "tcp",
  "security": "none",
  "offset_port_node": "10001",
  "offset_port_user": "10001"
}
```

使用第 5 节 config.yml，并把 CertMode 改为 `none`。无 TLS 不提供传输加密。当前 Xray 禁止向公网地址连接未加密 VLESS，只有私有 IP/私有域名可用；使用 Xray 客户端时，面板节点地址必须填入符合该限制的地址，例如 `192.168.1.10`。本次无 TLS 转发测试使用本地回环地址。公网节点使用 TLS 或 REALITY。

### 4.2 VLESS + TCP + TLS

```json
{
  "enable_vless": "1",
  "network": "tcp",
  "security": "tls",
  "host": "tls.example.com",
  "offset_port_node": "443",
  "offset_port_user": "443"
}
```

使用第 5 节文件证书配置。证书必须覆盖 `tls.example.com`。

### 4.3 VLESS + TCP + TLS + Vision

```json
{
  "enable_vless": true,
  "network": "tcp",
  "security": "tls",
  "flow": "xtls-rprx-vision",
  "host": "tls.example.com",
  "offset_port_node": "443",
  "offset_port_user": "443"
}
```

### 4.4 VLESS + TCP + REALITY + Vision

```json
{
  "enable_vless": 1,
  "network": "tcp",
  "security": "reality",
  "flow": "xtls-rprx-vision",
  "enable_reality": true,
  "host": "cover.example.com",
  "fingerprint": "chrome",
  "offset_port_node": "443",
  "offset_port_user": "443",
  "reality-opts": {
    "dest": "cover.example.com:443",
    "server_names": ["cover.example.com"],
    "private_key": "REPLACE_WITH_GENERATED_X25519_PRIVATE_KEY",
    "short_ids": ["0123456789abcdef"]
  }
}
```

占位密钥必须替换。用 `XrayR x25519` 生成；不要把输出私钥粘贴到分享链接。`short_ids` 必须是偶数长度的十六进制字符串，最大 16 个字符，并在 JSON 中加引号。

`cover.example.com` 仅是占位名：实际选择服务端能够访问、支持合适 TLS 1.3 握手的伪装目标，SNI 必须在 `server_names` 中。REALITY 无需自己的域名证书，不适合放在普通 CDN 的 TLS 终止后面。使用第 6 节 config.yml，并注意第 1 节 sing-box 限制。去掉 flow 可配置非 Vision REALITY，但本次数据面测试覆盖的是 Vision 组合。

### 4.5 VLESS + WebSocket + TLS

```json
{
  "enable_vless": 1,
  "network": "ws",
  "security": "tls",
  "host": "tls.example.com",
  "path": "/vless-ws",
  "offset_port_node": "443",
  "offset_port_user": "443"
}
```

如经反代/CDN，必须正确转发 WebSocket Upgrade 和同一路径；本次验证是客户端直连 XrayR 的 TLS 入站，不包含外部 CDN。

### 4.6 VLESS + gRPC + TLS

```json
{
  "enable_vless": 1,
  "network": "grpc",
  "security": "tls",
  "host": "tls.example.com",
  "servicename": "vless-grpc",
  "offset_port_node": "443",
  "offset_port_user": "443"
}
```

字段名是 `servicename`，不是 `serviceName`。经反代时须支持 HTTP/2 与 gRPC。当前 core 对 gRPC 发出弃用提示，但本版本实测可用。

### 4.7 VLESS + HTTPUpgrade + TLS

```json
{
  "enable_vless": 1,
  "network": "httpupgrade",
  "security": "tls",
  "host": "tls.example.com",
  "path": "/vless-upgrade",
  "offset_port_node": "443",
  "offset_port_user": "443"
}
```

Mihomo 对应 `network: ws` + `ws-opts.v2ray-http-upgrade: true`；sing-box 对应 `transport.type: httpupgrade` 与独立 `host` 字段。当前 core 同样发出弃用提示。

### 4.8 VLESS + XHTTP + TLS

```json
{
  "enable_vless": 1,
  "network": "xhttp",
  "security": "tls",
  "host": "tls.example.com",
  "path": "/vless-xhttp",
  "offset_port_node": "443",
  "offset_port_user": "443"
}
```

当前面板/后端映射支持基础 `host/path` 与 auto 模式，不应宣称覆盖 XHTTP 所有 advanced extra、downloadSettings、多路分离或 H3 参数。官方 sing-box 订阅会省略此节点。Mihomo 映射见 [官方 transport 文档](https://wiki.metacubex.one/config/proxies/transport/)。

## 5. 普通 TLS 节点 config.yml（完整版与 minimal 均可）

```yaml
Log:
  Level: warning
ConnectionConfig:
  Handshake: 4
  ConnIdle: 30
  UplinkOnly: 2
  DownlinkOnly: 4
  BufferSize: 64
Nodes:
  - PanelType: SSpanel
    ApiConfig:
      ApiHost: "https://panel.example.com"
      ApiKey: "REPLACE_WITH_PANEL_API_KEY"
      NodeID: 123
      NodeType: V2ray
      Timeout: 30
      EnableVless: false
      VlessFlow: ""
      DisableCustomConfig: false
      SpeedLimit: 0
      DeviceLimit: 0
    ControllerConfig:
      ListenIP: "0.0.0.0"
      SendIP: "0.0.0.0"
      UpdatePeriodic: 60
      EnableDNS: false
      DNSType: AsIs
      EnableProxyProtocol: false
      EnableFallback: false
      DisableLocalREALITYConfig: true
      EnableREALITY: false
      DisableUploadTraffic: false
      CertConfig:
        CertMode: file
        CertDomain: "tls.example.com"
        CertFile: "/path/to/your/fullchain.pem"
        KeyFile: "/path/to/your/private-key.pem"
```

PanelType 必须使用项目实际枚举 `SSpanel`。APIHost 是面板根地址，不是订阅 URL；NodeID 替换为该 sort=11 节点 ID。

`EnableVless: false` 与空 VlessFlow 是本地默认值，`DisableCustomConfig: false` 使面板覆盖它们。不要只开启本地 VLESS 却忘记面板开关，否则订阅与服务端仍可能不一致。

多个节点在 Nodes 下重复添加完整项，每项使用自身 NodeID、证书及不冲突的监听端口。minimal/nolego 保留 file 证书功能，不支持内置 ACME 自动签发。外部证书续期后应安排服务重启/重新加载，file 模式不创建内置 ACME cert monitor。

## 6. REALITY 节点 config.yml

```yaml
Log:
  Level: warning
ConnectionConfig:
  Handshake: 4
  ConnIdle: 30
  UplinkOnly: 2
  DownlinkOnly: 4
  BufferSize: 64
Nodes:
  - PanelType: SSpanel
    ApiConfig:
      ApiHost: "https://panel.example.com"
      ApiKey: "REPLACE_WITH_PANEL_API_KEY"
      NodeID: 124
      NodeType: V2ray
      Timeout: 30
      EnableVless: false
      VlessFlow: ""
      DisableCustomConfig: false
      SpeedLimit: 0
      DeviceLimit: 0
    ControllerConfig:
      ListenIP: "0.0.0.0"
      SendIP: "0.0.0.0"
      UpdatePeriodic: 60
      EnableDNS: false
      DNSType: AsIs
      EnableProxyProtocol: false
      EnableFallback: false
      DisableLocalREALITYConfig: true
      EnableREALITY: false
      DisableUploadTraffic: false
      CertConfig:
        CertMode: none
```

`DisableLocalREALITYConfig: true` 表示读取面板的 REALITY 配置，不是禁用 REALITY。私钥只在面板 custom_config 管理，不需要在 config.yml 再写 REALITYConfigs。

## 7. 客户端输出与字段映射

订阅入口使用项目路由 `/sub/TOKEN/v2ray`、`/sub/TOKEN/clash`、`/sub/TOKEN/singbox`、`/sub/TOKEN/v2rayjson`；TOKEN 是用户订阅令牌，不是 API key。以面板实际生成的地址和 subUrl 为准。

普通 TCP URI 示例：

```text
vless://123e4567-e89b-42d3-a456-426614174000@192.168.1.10:10001?encryption=none&security=none&type=tcp#Example
```

REALITY URI 结构（公钥占位，不可直接导入）：

```text
vless://USER_UUID@node.example.com:443?encryption=none&security=reality&type=tcp&flow=xtls-rprx-vision&sni=cover.example.com&fp=chrome&pbk=REALITY_PUBLIC_KEY&sid=0123456789abcdef#Example
```

节点名称使用 RFC3986 URL 编码；IPv6 地址自动加方括号，如 `@[2001:db8::1]:443`。URI 单条链接没有 VMess JSON 的 Base64 包装。

| 面板字段 | URI | Mihomo | sing-box | Xray JSON |
| --- | --- | --- | --- | --- |
| 用户 uuid | authority 用户部分 | uuid | uuid | settings.vnext[].users[].id |
| flow | flow | flow | flow | users[].flow |
| network | type | network / transport opts | transport.type，TCP 省略 | streamSettings.network |
| REALITY server_names / host | sni | servername | tls.server_name | realitySettings.serverName |
| private_key 推导公钥 | pbk | reality-opts.public-key | tls.reality.public_key | realitySettings.publicKey |
| short_ids[0] | sid | reality-opts.short-id | tls.reality.short_id | realitySettings.shortId |
| fingerprint，默认 chrome | fp | client-fingerprint | tls.utls.fingerprint | realitySettings.fingerprint |
| dest / private_key | **不输出** | **不输出** | **不输出** | **不输出** |

SNI 优先选择属于 server_names 的顶层 host，否则选择 server_names 第一项。short id 选择第一项。节点 API 是后端管理接口，会包含服务端私钥；必须保护 API key 与 HTTPS。不能把后端 API 响应当作公开订阅发送。

Mihomo REALITY outbound：

```yaml
name: Example
type: vless
server: node.example.com
port: 443
uuid: USER_UUID
network: tcp
tls: true
udp: true
flow: xtls-rprx-vision
servername: cover.example.com
client-fingerprint: chrome
reality-opts:
  public-key: REALITY_PUBLIC_KEY
  short-id: "0123456789abcdef"
  support-x25519mlkem768: true
```

sing-box REALITY outbound（schema 正确，但 1.14.2 与本服务端握手不兼容）：

```json
{
  "type": "vless", "tag": "Example", "server": "node.example.com", "server_port": 443,
  "uuid": "USER_UUID", "flow": "xtls-rprx-vision",
  "tls": {
    "enabled": true, "server_name": "cover.example.com",
    "utls": {"enabled": true, "fingerprint": "chrome"},
    "reality": {"enabled": true, "public_key": "REALITY_PUBLIC_KEY", "short_id": "0123456789abcdef"}
  }
}
```

Xray REALITY outbound：

```json
{
  "protocol": "vless", "tag": "Example",
  "settings": {"vnext": [{"address": "node.example.com", "port": 443,
    "users": [{"id": "USER_UUID", "encryption": "none", "flow": "xtls-rprx-vision"}]}]},
  "streamSettings": {
    "network": "tcp", "security": "reality",
    "realitySettings": {"serverName": "cover.example.com", "fingerprint": "chrome",
      "publicKey": "REALITY_PUBLIC_KEY", "shortId": "0123456789abcdef"}
  }
}
```

普通 TLS 生产环境使用可信证书，不建议 allow_insecure。当前 Xray 已移除 allowInsecure；Xray JSON 生成器跳过无证书 pin 的跳过验证节点，可使用 `pinnedPeerCertSha256`。该高级选项不是所有 URI 客户端均支持，不宜代替正常证书部署。

完整 Xray JSON 模板现在使用原生日志结构 `loglevel/access`、DNS `servers` 和 SOCKS `udp`；空 HTTP settings 输出 `{}`。已有部署若仍保留旧例模板，包含 VLESS 的 Xray JSON 订阅会转换上述旧字段。VMess outbound 的原有逻辑不变；自定义模板的其他非标准字段仍需管理员检查。

## 8. 测试结果与复现方式

- PHP：28 个测试、242 个断言通过。覆盖 VMess 回退、三种开关输入、UUID、客户端端口优先级、Vision、REALITY 公钥/私钥隔离、名称编码、IPv6，以及真实节点 API、用户 API、订阅控制器的 8 种组合。增加了混合错误节点隔离、后台保存校验、6 种 network 默认值/别名，以及完整模板和无可用节点场景。
- Go：跨项目测试使用上述 PHP 控制器导出的真实结构，不手写另一份近似面板响应。通过 XrayR API 解析、控制器安装节点和用户、运行真实客户端及转发校验。
- Xray：8/8 转发通过；Mihomo：8/8；sing-box：6/6 可兼容组合通过。REALITY 已单独实测失败，常规回归中明确 skip；XHTTP 无 outbound，不计入可用组合。
- 完整配置回归：Xray 默认模板与旧模板转换后的两份完整订阅均经过原生 Config.Build/core.New；Mihomo 的完整默认模板经过 `-t` 校验；sing-box 的普通 TLS、仅 XHTTP 和零节点三份完整配置经过官方 `check` 校验。与单节点转发测试分别记录，不把配置校验等同于生产网络连接验收。
- 仅 XHTTP、零节点两个场景还运行了 sing-box：保留生成的选择组和首条 reject 路由，移除无关远程规则集/DNS，用本地目标验证 SOCKS5 请求被拒绝、目标未收到直连请求。
- 原生客户端转发测试使用 mixed 端口的 SOCKS5 入站。Mihomo 启动时先监听、后切换为 Running，原测试可能在启动期间遇到 EOF/502；已改为等待实际数据转发就绪，再独立执行正式断言。原因见 [官方启动顺序](https://github.com/MetaCubeX/mihomo/blob/v1.19.32/hub/executor/executor.go) 和 [未就绪连接处理](https://github.com/MetaCubeX/mihomo/blob/v1.19.32/tunnel/tunnel.go)。
- 普通 TLS 转发测试使用临时测试证书：Xray 加入证书 pin，Mihomo/sing-box 对该证书跳过校验。生产证书链校验和图形客户端完整导入未覆盖。
- PHP 测试本次在临时依赖环境运行，未在仓库安装 vendor，未声称完整项目 Pest/集成测试全部通过。
- 不包含生产面板认证中间件、真实互联网/CDN、UDP 压力、长连接/并发负载、计费/停用/热重载的全量验收。VLESS 节点下发与订阅路径已测，不能据此扩大为所有运营功能都无问题。

具备项目测试依赖、PHP sodium/yaml 后，导出合同测试数据：

```sh
VLESS_CONTRACT_DIR=/absolute/path/to/contracts vendor/bin/pest tests/Integration/Services/Subscribe/VlessSubscriptionTest.php
```

在 XrayR 目录执行：

```sh
VLESS_CONTRACT_DIR=/absolute/path/to/contracts \
VLESS_MIHOMO_BIN=/absolute/path/to/mihomo \
VLESS_SINGBOX_BIN=/absolute/path/to/sing-box \
go test -tags nolego ./service/controller -run '^TestVless(PanelSubscriptionContract|FullSubscriptionProfiles)$' -count=1 -v
```

不设置两个客户端路径则只运行 Xray 客户端测试。要复现 sing-box REALITY 失败，额外设置 `VLESS_TEST_SINGBOX_REALITY=1`；将来客户端修复后也可用该开关验收。测试只覆盖临时本地端口和临时测试证书，不访问生产节点。

上线前检查：确保运行的是含本次更改的面板和 XrayR；检查节点 UUID、端口、SNI 与订阅一致；REALITY 使用兼容客户端，Mihomo 配置必须包含 MLKEM 开关；下载用户订阅确认未包含私钥；再在生产网络验证连接与实际出口。当前更改不会自动部署到服务器。
