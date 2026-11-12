<?php

declare(strict_types=1);

/**
 * @group database
 * @group feature
 * @group subscribe
 */

use App\Models\Config;
use App\Models\Node;
use App\Models\User;
use App\Services\Subscribe;

function vmessSubCreateUser(): User
{
    $suffix = bin2hex(random_bytes(6));
    $user = new User();
    $user->email = "vmesssub{$suffix}@example.invalid";
    $user->user_name = "vmesssub{$suffix}";
    $user->pass = password_hash('password', PASSWORD_DEFAULT);
    $user->api_token = bin2hex(random_bytes(32));
    $user->port = random_int(20000, 60000);
    $user->passwd = bin2hex(random_bytes(16));
    $user->uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
    $user->method = 'aes-256-gcm';
    $user->transfer_enable = 1099511627776;
    $user->u = 0;
    $user->d = 0;
    $user->node_iplimit = 0;
    $user->node_speedlimit = 0;
    $user->node_group = 0;
    $user->class = 0;
    $user->is_banned = 0;
    $user->class_expire = date('Y-m-d H:i:s', strtotime('+1 year'));
    $user->save();

    return $user;
}

function vmessSubCreateNode(string $name, string $server, array $customConfig): Node
{
    $node = new Node();
    $node->name = $name;
    $node->server = $server;
    $node->password = bin2hex(random_bytes(32));
    $node->type = 1;
    $node->sort = 11;
    $node->node_class = 0;
    $node->node_group = 0;
    $node->custom_config = json_encode($customConfig);
    $node->save();

    return $node;
}

function vmessSubFind(array $items, string $key, string $value): array
{
    foreach ($items as $item) {
        if (($item[$key] ?? null) === $value) {
            return $item;
        }
    }

    throw new RuntimeException("No item with {$key}={$value} in the rendered subscription.");
}

/** Returns the decoded vmess:// share link JSON named $name. */
function vmessSubLink(string $content, string $name): array
{
    foreach (array_filter(explode(PHP_EOL, $content)) as $line) {
        if (str_starts_with($line, 'vmess://')) {
            $link = json_decode(base64_decode(substr($line, 8)), true);
            if ($link['ps'] === $name) {
                return $link;
            }
        }
    }

    throw new RuntimeException("No vmess:// link named {$name} in the V2Ray subscription.");
}

beforeEach(function () {
    $this->vmessEnvBackup = $_ENV;
    // The subscription templates are not part of the test config, load the shipped example profile.
    require BASE_PATH . '/config/appprofile.example.php';

    $this->vmessNodes = [];
    $this->vmessUser = vmessSubCreateUser();
    $suffix = bin2hex(random_bytes(4));
    $this->vmessWs = 'vmess-ws-' . $suffix;
    $this->vmessWsTls = 'vmess-ws-tls-' . $suffix;
    $this->vmessWsLegacy = 'vmess-ws-legacy-' . $suffix;
    $this->vmessGrpc = 'vmess-grpc-' . $suffix;
    $this->vmessUpgrade = 'vmess-httpupgrade-' . $suffix;
    $this->vmessTcp = 'vmess-tcp-' . $suffix;

    // Plain VMess over WebSocket, the WebSocket Host differs from the address clients dial.
    $this->vmessNodes[] = vmessSubCreateNode($this->vmessWs, 'edge.example.com', [
        'offset_port_node' => '80',
        'security' => 'none',
        'network' => 'ws',
        'host' => 'ws.example.com',
        'path' => '/vmess',
    ]);
    // The documented WebSocket + TLS example: host plus Clash ws-opts.
    $this->vmessNodes[] = vmessSubCreateNode($this->vmessWsTls, 'edge.example.com', [
        'offset_port_node' => '443',
        'security' => 'tls',
        'network' => 'ws',
        'host' => 'cdn.example.com',
        'path' => '/vmess',
        'ws-opts' => ['path' => '/vmess', 'headers' => ['Host' => 'cdn.example.com']],
    ]);
    // Legacy header form, Host and path given only through header.request.
    $this->vmessNodes[] = vmessSubCreateNode($this->vmessWsLegacy, 'legacy.example.com', [
        'offset_port_node' => '443',
        'security' => 'tls',
        'network' => 'ws',
        'encryption' => 'auto',
        'utls' => true,
        'header' => ['type' => 'none', 'request' => ['path' => ['/ws'], 'headers' => ['Host' => ['ws.legacy.example.com']]]],
        'ws_opts' => ['path' => '/ws', 'headers' => ['Host' => 'ws.legacy.example.com']],
        'max_early_data' => '2048',
        'early_data_header_name' => 'Sec-WebSocket-Protocol',
    ]);
    $this->vmessNodes[] = vmessSubCreateNode($this->vmessGrpc, 'grpc.example.com', [
        'offset_port_node' => '443',
        'security' => 'tls',
        'network' => 'grpc',
        'host' => 'grpc.example.com',
        'servicename' => 'vmess-grpc',
    ]);
    $this->vmessNodes[] = vmessSubCreateNode($this->vmessUpgrade, 'edge.example.com', [
        'offset_port_node' => '8080',
        'security' => 'none',
        'network' => 'httpupgrade',
        'host' => 'hu.example.com',
        'path' => '/hu',
    ]);
    $this->vmessNodes[] = vmessSubCreateNode($this->vmessTcp, 'tcp.example.com', [
        'offset_port_node' => '443',
        'security' => 'tls',
        'network' => 'tcp',
        'host' => 'tcp.example.com',
        'encryption' => 'chacha20-poly1305',
        'allow_insecure' => '1',
    ]);

    // V2Ray subscriptions are gated by this switch, make sure the row exists and is on.
    $this->vmessConfigRow = Config::where('item', 'enable_v2_sub')->first();
    $this->vmessConfigCreated = false;
    $this->vmessConfigOriginal = null;
    if ($this->vmessConfigRow === null) {
        $row = new Config();
        $row->item = 'enable_v2_sub';
        $row->value = '1';
        $row->class = 'subscribe';
        $row->is_public = 1;
        $row->type = 'bool';
        $row->default = '1';
        $row->mark = 'V2Ray 订阅开关';
        $row->save();
        $this->vmessConfigRow = $row;
        $this->vmessConfigCreated = true;
    } else {
        $this->vmessConfigOriginal = $this->vmessConfigRow->value;
        $this->vmessConfigRow->value = '1';
        $this->vmessConfigRow->save();
    }
});

afterEach(function () {
    foreach ($this->vmessNodes as $node) {
        $node->delete();
    }
    $this->vmessUser->delete();

    if ($this->vmessConfigCreated) {
        $this->vmessConfigRow->delete();
    } else {
        $this->vmessConfigRow->value = $this->vmessConfigOriginal;
        $this->vmessConfigRow->save();
    }

    $_ENV = $this->vmessEnvBackup;
});

describe('VMess subscription output', function () {
    it('renders sing-box vmess outbounds from the node security, host, path and service name', function () {
        $outbounds = json_decode(Subscribe::getContent($this->vmessUser, 'singbox'), true)['outbounds'];
        $uuid = $this->vmessUser->uuid;

        expect(vmessSubFind($outbounds, 'tag', $this->vmessWs))
            ->toBe([
                'type' => 'vmess',
                'tag' => $this->vmessWs,
                'server' => 'edge.example.com',
                'server_port' => 80,
                'uuid' => $uuid,
                'security' => 'auto',
                'alter_id' => 0,
                'packet_encoding' => 'xudp',
                'global_padding' => true,
                'authenticated_length' => true,
                'transport' => ['type' => 'ws', 'path' => '/vmess', 'headers' => ['Host' => 'ws.example.com']],
            ]);

        expect(vmessSubFind($outbounds, 'tag', $this->vmessWsTls))
            ->toMatchArray([
                'tls' => ['enabled' => true, 'server_name' => 'cdn.example.com', 'utls' => ['enabled' => false, 'fingerprint' => 'chrome']],
                'transport' => ['type' => 'ws', 'path' => '/vmess', 'headers' => ['Host' => 'cdn.example.com']],
            ]);

        // Nodes that already carried their Host in header.request render exactly as before.
        expect(vmessSubFind($outbounds, 'tag', $this->vmessWsLegacy))
            ->toBe([
                'type' => 'vmess',
                'tag' => $this->vmessWsLegacy,
                'server' => 'legacy.example.com',
                'server_port' => 443,
                'uuid' => $uuid,
                'security' => 'auto',
                'alter_id' => 0,
                'tls' => ['enabled' => true, 'server_name' => 'ws.legacy.example.com', 'utls' => ['enabled' => true, 'fingerprint' => 'chrome']],
                'packet_encoding' => 'xudp',
                'global_padding' => true,
                'authenticated_length' => true,
                'transport' => [
                    'type' => 'ws',
                    'path' => '/ws',
                    'headers' => ['Host' => ['ws.legacy.example.com']],
                    'max_early_data' => 2048,
                    'early_data_header_name' => 'Sec-WebSocket-Protocol',
                ],
            ]);

        expect(vmessSubFind($outbounds, 'tag', $this->vmessGrpc))
            ->toMatchArray([
                'tls' => ['enabled' => true, 'server_name' => 'grpc.example.com', 'utls' => ['enabled' => false, 'fingerprint' => 'chrome']],
                'transport' => ['type' => 'grpc', 'service_name' => 'vmess-grpc'],
            ]);

        expect(vmessSubFind($outbounds, 'tag', $this->vmessUpgrade))
            ->not->toHaveKey('tls')
            ->toMatchArray(['transport' => ['type' => 'httpupgrade', 'host' => 'hu.example.com', 'path' => '/hu']]);

        expect(vmessSubFind($outbounds, 'tag', $this->vmessTcp))
            ->toMatchArray([
                'security' => 'chacha20-poly1305',
                'tls' => [
                    'enabled' => true,
                    'server_name' => 'tcp.example.com',
                    'insecure' => true,
                    'utls' => ['enabled' => false, 'fingerprint' => 'chrome'],
                ],
            ]);
    });

    it('renders Clash/Mihomo vmess ws and grpc options from the node host, path and service name', function () {
        $proxies = yaml_parse(Subscribe::getContent($this->vmessUser, 'clash'))['proxies'];

        expect(vmessSubFind($proxies, 'name', $this->vmessWs))
            ->toMatchArray([
                'type' => 'vmess',
                'server' => 'edge.example.com',
                'port' => 80,
                'tls' => false,
                'network' => 'ws',
                'ws-opts' => ['path' => '/vmess', 'headers' => ['Host' => 'ws.example.com']],
            ]);

        expect(vmessSubFind($proxies, 'name', $this->vmessWsTls))
            ->toMatchArray([
                'tls' => true,
                'servername' => 'cdn.example.com',
                'ws-opts' => ['path' => '/vmess', 'headers' => ['Host' => 'cdn.example.com']],
            ]);

        expect(vmessSubFind($proxies, 'name', $this->vmessWsLegacy)['ws-opts'])
            ->toBe(['path' => '/ws', 'headers' => ['Host' => 'ws.legacy.example.com']]);

        expect(vmessSubFind($proxies, 'name', $this->vmessGrpc))
            ->toMatchArray(['network' => 'grpc', 'grpc-opts' => ['grpc-service-name' => 'vmess-grpc'], 'ws-opts' => null]);

        expect(vmessSubFind($proxies, 'name', $this->vmessUpgrade))
            ->toMatchArray([
                'tls' => false,
                'network' => 'ws',
                'ws-opts' => ['path' => '/hu', 'headers' => ['Host' => 'hu.example.com'], 'v2ray-http-upgrade' => true],
            ]);

        expect(vmessSubFind($proxies, 'name', $this->vmessTcp))
            ->toMatchArray(['tls' => true, 'cipher' => 'chacha20-poly1305', 'ws-opts' => null, 'grpc-opts' => null]);
    });

    it('carries the gRPC service name in the V2Ray share link path', function () {
        $content = Subscribe::getContent($this->vmessUser, 'v2ray');

        expect(vmessSubLink($content, $this->vmessGrpc))->toMatchArray(['net' => 'grpc', 'path' => 'vmess-grpc', 'tls' => 'tls'])
            ->and(vmessSubLink($content, $this->vmessWs))
            ->toMatchArray(['net' => 'ws', 'host' => 'ws.example.com', 'path' => '/vmess', 'tls' => 'none']);
    });

    it('sets the V2RayJson TLS server name only for TLS nodes', function () {
        $outbounds = json_decode(Subscribe::getContent($this->vmessUser, 'v2rayjson'), true)['outbounds'];

        expect(vmessSubFind($outbounds, 'tag', $this->vmessGrpc)['streamSettings'])
            ->toMatchArray(['security' => 'tls', 'securitySettings' => ['tls' => ['server_name' => 'grpc.example.com']]])
            ->and(vmessSubFind($outbounds, 'tag', $this->vmessWs)['streamSettings'])
            ->toMatchArray(['security' => 'none', 'securitySettings' => []]);
    });
});
