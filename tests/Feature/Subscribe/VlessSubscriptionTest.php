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

function vlessSubCreateUser(): User
{
    $suffix = bin2hex(random_bytes(6));
    $user = new User();
    $user->email = "vlesssub{$suffix}@example.invalid";
    $user->user_name = "vlesssub{$suffix}";
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

function vlessSubCreateNode(string $name, string $server, string $customConfig, int $sort = 11): Node
{
    $node = new Node();
    $node->name = $name;
    $node->server = $server;
    $node->password = bin2hex(random_bytes(32));
    $node->type = 1;
    $node->sort = $sort;
    $node->node_class = 0;
    $node->node_group = 0;
    $node->custom_config = $customConfig;
    $node->save();

    return $node;
}

function vlessSubFind(array $items, string $key, string $value): array
{
    foreach ($items as $item) {
        if (($item[$key] ?? null) === $value) {
            return $item;
        }
    }

    throw new RuntimeException("No item with {$key}={$value} in the rendered subscription.");
}

function vlessSubBase64Url(string $raw): string
{
    return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
}

/** Fixed REALITY X25519 private key (32 bytes of 0x11), the public key is derived with libsodium. */
function vlessSubPrivateKey(): string
{
    return vlessSubBase64Url(str_repeat("\x11", 32));
}

function vlessSubPublicKey(): string
{
    return vlessSubBase64Url(sodium_crypto_scalarmult_base(str_repeat("\x11", 32)));
}

function vlessSubRealityOpts(string $shortId): array
{
    return [
        'dest' => 'cover.example.com:443',
        'server_names' => ['cover.example.com'],
        'private_key' => vlessSubPrivateKey(),
        'short_ids' => [$shortId],
    ];
}

function vlessSubRealityTcpConfig(): string
{
    return json_encode([
        'enable_vless' => '1',
        'enable_reality' => true,
        'reality_x25519mlkem768' => true,
        'security' => 'reality',
        'network' => 'tcp',
        'flow' => 'xtls-rprx-vision',
        'offset_port_node' => '443',
        'offset_port_user' => '8443',
        'reality-opts' => vlessSubRealityOpts('0123456789abcdef'),
    ]);
}

function vlessSubWsTlsConfig(): string
{
    return json_encode([
        'enable_vless' => 1,
        'security' => 'tls',
        'network' => 'ws',
        'host' => 'ws.example.com',
        'path' => '/vless-ws',
        'offset_port_node' => '443',
    ]);
}

function vlessSubGrpcTlsConfig(): string
{
    return json_encode([
        'enable_vless' => true,
        'security' => 'tls',
        'network' => 'grpc',
        'host' => 'grpc.example.com',
        'servicename' => 'vless-grpc',
        'allow_insecure' => true,
        'offset_port_node' => '9443',
    ]);
}

function vlessSubXhttpRealityConfig(): string
{
    return json_encode([
        'enable_vless' => '1',
        'enable_reality' => true,
        'network' => 'xhttp',
        'path' => '/vless-xhttp',
        'offset_port_node' => '443',
        'reality-opts' => vlessSubRealityOpts('ab'),
    ]);
}

function vlessSubVmessConfig(): string
{
    return '{"offset_port_node":"443","security":"tls","network":"ws","host":"vmess.example.com","path":"/"}';
}

/** @return array{0: array, 1: array} the parsed share link parts and its query */
function vlessSubParseLink(string $content, string $name): array
{
    foreach (array_filter(explode(PHP_EOL, $content)) as $line) {
        if (str_starts_with($line, 'vless://') && str_ends_with($line, '#' . $name)) {
            $parts = parse_url($line);
            parse_str($parts['query'], $query);

            return [$parts, $query];
        }
    }

    throw new RuntimeException("No vless:// link named {$name} in the V2Ray subscription.");
}

function vlessSubVmessNames(string $content): array
{
    $names = [];
    foreach (array_filter(explode(PHP_EOL, $content)) as $line) {
        if (str_starts_with($line, 'vmess://')) {
            $names[] = json_decode(base64_decode(substr($line, 8)), true)['ps'];
        }
    }

    return $names;
}

beforeEach(function () {
    $this->vlessEnvBackup = $_ENV;
    // The subscription templates are not part of the test config, load the shipped example profile.
    require BASE_PATH . '/config/appprofile.example.php';

    $this->vlessNodes = [];
    $this->vlessUser = vlessSubCreateUser();

    // V2Ray subscriptions are gated by this switch, make sure the row exists and is on.
    $this->vlessConfigRow = Config::where('item', 'enable_v2_sub')->first();
    $this->vlessConfigCreated = false;
    $this->vlessConfigOriginal = null;
    if ($this->vlessConfigRow === null) {
        $row = new Config();
        $row->item = 'enable_v2_sub';
        $row->value = '1';
        $row->class = 'subscribe';
        $row->is_public = 1;
        $row->type = 'bool';
        $row->default = '1';
        $row->mark = 'V2Ray 订阅开关';
        $row->save();
        $this->vlessConfigRow = $row;
        $this->vlessConfigCreated = true;
    } else {
        $this->vlessConfigOriginal = $this->vlessConfigRow->value;
        $this->vlessConfigRow->value = '1';
        $this->vlessConfigRow->save();
    }
});

afterEach(function () {
    foreach ($this->vlessNodes as $node) {
        $node->delete();
    }
    $this->vlessUser->delete();

    if ($this->vlessConfigCreated) {
        $this->vlessConfigRow->delete();
    } else {
        $this->vlessConfigRow->value = $this->vlessConfigOriginal;
        $this->vlessConfigRow->save();
    }

    $_ENV = $this->vlessEnvBackup;
});

describe('VLESS subscription output', function () {
    beforeEach(function () {
        $suffix = bin2hex(random_bytes(4));
        $this->vlessReality = 'vless-reality-' . $suffix;
        $this->vlessWs = 'vless-ws-' . $suffix;
        $this->vlessGrpc = 'vless-grpc-' . $suffix;
        $this->vlessXhttp = 'vless-xhttp-' . $suffix;
        $this->vlessVmess = 'vless-vmess-' . $suffix;

        $this->vlessNodes[] = vlessSubCreateNode($this->vlessReality, 'reality.example.com', vlessSubRealityTcpConfig());
        $this->vlessNodes[] = vlessSubCreateNode($this->vlessWs, 'ws.example.com', vlessSubWsTlsConfig());
        $this->vlessNodes[] = vlessSubCreateNode($this->vlessGrpc, 'grpc.example.com', vlessSubGrpcTlsConfig());
        $this->vlessNodes[] = vlessSubCreateNode($this->vlessXhttp, 'xhttp.example.com', vlessSubXhttpRealityConfig());
        $this->vlessNodes[] = vlessSubCreateNode($this->vlessVmess, 'vmess.example.com', vlessSubVmessConfig());
    });

    it('renders Clash/Mihomo vless proxies', function () {
        $content = Subscribe::getContent($this->vlessUser, 'clash');
        $proxies = yaml_parse($content)['proxies'];
        $uuid = $this->vlessUser->uuid;

        $reality = vlessSubFind($proxies, 'name', $this->vlessReality);
        expect($reality)
            ->toMatchArray([
                'type' => 'vless',
                'server' => 'reality.example.com',
                'port' => 8443,
                'uuid' => $uuid,
                'network' => 'tcp',
                'tls' => true,
                'udp' => true,
                'flow' => 'xtls-rprx-vision',
                'servername' => 'cover.example.com',
                'client-fingerprint' => 'chrome',
                'reality-opts' => [
                    'public-key' => vlessSubPublicKey(),
                    'short-id' => '0123456789abcdef',
                    'support-x25519mlkem768' => true,
                ],
            ])
            ->not->toHaveKey('ws-opts')
            ->not->toHaveKey('skip-cert-verify');

        expect(vlessSubFind($proxies, 'name', $this->vlessWs))
            ->toMatchArray([
                'type' => 'vless',
                'port' => 443,
                'uuid' => $uuid,
                'network' => 'ws',
                'tls' => true,
                'servername' => 'ws.example.com',
                'skip-cert-verify' => false,
                'ws-opts' => ['path' => '/vless-ws', 'headers' => ['Host' => 'ws.example.com']],
            ])
            ->not->toHaveKey('flow')
            ->not->toHaveKey('reality-opts');

        expect(vlessSubFind($proxies, 'name', $this->vlessGrpc))
            ->toMatchArray([
                'type' => 'vless',
                'port' => 9443,
                'network' => 'grpc',
                'servername' => 'grpc.example.com',
                'skip-cert-verify' => true,
                'grpc-opts' => ['grpc-service-name' => 'vless-grpc'],
            ]);

        expect(vlessSubFind($proxies, 'name', $this->vlessXhttp))
            ->toMatchArray([
                'type' => 'vless',
                'port' => 443,
                'network' => 'xhttp',
                'servername' => 'cover.example.com',
                'xhttp-opts' => ['host' => '', 'path' => '/vless-xhttp', 'mode' => 'auto'],
                'reality-opts' => [
                    'public-key' => vlessSubPublicKey(),
                    'short-id' => 'ab',
                ],
            ]);

        expect(vlessSubFind($proxies, 'name', $this->vlessVmess)['type'])->toBe('vmess')
            ->and($content)->not->toContain(vlessSubPrivateKey())
            ->and($content)->not->toContain('private_key');
    });

    it('renders sing-box vless outbounds and omits the xhttp node', function () {
        $content = Subscribe::getContent($this->vlessUser, 'singbox');
        $profile = json_decode($content, true);
        $outbounds = $profile['outbounds'];
        $uuid = $this->vlessUser->uuid;

        $reality = vlessSubFind($outbounds, 'tag', $this->vlessReality);
        expect($reality)
            ->toMatchArray([
                'type' => 'vless',
                'server' => 'reality.example.com',
                'server_port' => 8443,
                'uuid' => $uuid,
                'flow' => 'xtls-rprx-vision',
                'tls' => [
                    'enabled' => true,
                    'server_name' => 'cover.example.com',
                    'utls' => ['enabled' => true, 'fingerprint' => 'chrome'],
                    'reality' => [
                        'enabled' => true,
                        'public_key' => vlessSubPublicKey(),
                        'short_id' => '0123456789abcdef',
                    ],
                ],
            ])
            ->not->toHaveKey('transport');

        expect(vlessSubFind($outbounds, 'tag', $this->vlessWs))
            ->toMatchArray([
                'type' => 'vless',
                'server_port' => 443,
                'uuid' => $uuid,
                'tls' => ['enabled' => true, 'server_name' => 'ws.example.com', 'insecure' => false],
                'transport' => ['type' => 'ws', 'path' => '/vless-ws', 'headers' => ['Host' => 'ws.example.com']],
            ])
            ->not->toHaveKey('flow');

        expect(vlessSubFind($outbounds, 'tag', $this->vlessGrpc))
            ->toMatchArray([
                'type' => 'vless',
                'server_port' => 9443,
                'tls' => ['enabled' => true, 'server_name' => 'grpc.example.com', 'insecure' => true],
                'transport' => ['type' => 'grpc', 'service_name' => 'vless-grpc'],
            ]);

        expect(vlessSubFind($outbounds, 'tag', $this->vlessVmess)['type'])->toBe('vmess')
            ->and($content)->not->toContain($this->vlessXhttp)
            ->and($profile['outbounds'][0]['outbounds'])->toContain($this->vlessReality, $this->vlessWs, $this->vlessGrpc)
            ->and($profile['outbounds'][0]['outbounds'])->not->toContain($this->vlessXhttp)
            ->and($profile['outbounds'][1]['outbounds'])->not->toContain($this->vlessXhttp)
            ->and($content)->not->toContain(vlessSubPrivateKey())
            ->and($content)->not->toContain('private_key');
    });

    it('renders vless share links in the V2Ray subscription', function () {
        $content = Subscribe::getContent($this->vlessUser, 'v2ray');
        $uuid = $this->vlessUser->uuid;

        [$parts, $query] = vlessSubParseLink($content, $this->vlessReality);
        expect($parts['user'])->toBe($uuid)
            ->and($parts['host'])->toBe('reality.example.com')
            ->and($parts['port'])->toBe(8443)
            ->and($query)->toEqual([
                'encryption' => 'none',
                'security' => 'reality',
                'type' => 'tcp',
                'flow' => 'xtls-rprx-vision',
                'sni' => 'cover.example.com',
                'fp' => 'chrome',
                'pbk' => vlessSubPublicKey(),
                'sid' => '0123456789abcdef',
            ]);

        [$parts, $query] = vlessSubParseLink($content, $this->vlessWs);
        expect($parts['host'])->toBe('ws.example.com')
            ->and($parts['port'])->toBe(443)
            ->and($query)->toEqual([
                'encryption' => 'none',
                'security' => 'tls',
                'type' => 'ws',
                'sni' => 'ws.example.com',
                'host' => 'ws.example.com',
                'path' => '/vless-ws',
            ]);

        [$parts, $query] = vlessSubParseLink($content, $this->vlessGrpc);
        expect($parts['port'])->toBe(9443)
            ->and($query)->toEqual([
                'encryption' => 'none',
                'security' => 'tls',
                'type' => 'grpc',
                'sni' => 'grpc.example.com',
                'allowInsecure' => '1',
                'serviceName' => 'vless-grpc',
            ]);

        [, $query] = vlessSubParseLink($content, $this->vlessXhttp);
        expect($query)->toEqual([
            'encryption' => 'none',
            'security' => 'reality',
            'type' => 'xhttp',
            'sni' => 'cover.example.com',
            'fp' => 'chrome',
            'pbk' => vlessSubPublicKey(),
            'sid' => 'ab',
            'host' => '',
            'path' => '/vless-xhttp',
            'mode' => 'auto',
        ]);

        expect(vlessSubVmessNames($content))->toContain($this->vlessVmess)
            ->and($content)->not->toContain(vlessSubPrivateKey())
            ->and($content)->not->toContain('private_key');
    });

    it('does not render a VLESS outbound in the V2RayJson subscription', function () {
        $content = Subscribe::getContent($this->vlessUser, 'v2rayjson');
        $outbounds = json_decode($content, true)['outbounds'];

        $vmess = vlessSubFind($outbounds, 'tag', $this->vlessVmess);
        expect($vmess['protocol'])->toBe('vmess')
            ->and(array_column($outbounds, 'protocol'))->not->toContain('vless')
            ->and($content)->not->toContain($this->vlessReality)
            ->and($content)->not->toContain($this->vlessWs)
            ->and($content)->not->toContain($this->vlessGrpc)
            ->and($content)->not->toContain($this->vlessXhttp)
            ->and($content)->not->toContain(vlessSubPrivateKey());
    });
});

describe('VLESS subscription with unusable nodes', function () {
    beforeEach(function () {
        $suffix = bin2hex(random_bytes(4));
        $this->vlessValid = 'vless-valid-' . $suffix;
        $this->vlessVmess = 'vless-vmess-' . $suffix;
        $this->vlessBroken = ['vless-broken-kcp-' . $suffix, 'vless-broken-reality-' . $suffix];
        $this->vlessBrokenServers = ['broken-kcp.example.com', 'broken-reality.example.com'];

        $this->vlessNodes[] = vlessSubCreateNode($this->vlessValid, 'valid.example.com', vlessSubRealityTcpConfig());
        $this->vlessNodes[] = vlessSubCreateNode(
            $this->vlessBroken[0],
            $this->vlessBrokenServers[0],
            '{"enable_vless":true,"network":"kcp","offset_port_node":"443"}'
        );
        $this->vlessNodes[] = vlessSubCreateNode(
            $this->vlessBroken[1],
            $this->vlessBrokenServers[1],
            '{"enable_vless":true,"enable_reality":true,"network":"tcp","offset_port_node":"443"}'
        );
        $this->vlessNodes[] = vlessSubCreateNode($this->vlessVmess, 'vmess.example.com', vlessSubVmessConfig());

        // Skipped nodes are logged with error_log(), keep that out of the test output and inspect it.
        $this->vlessLogFile = tempnam(sys_get_temp_dir(), 'vlesslog');
        $this->vlessLogOriginal = (string) ini_get('error_log');
        ini_set('error_log', $this->vlessLogFile);
    });

    afterEach(function () {
        ini_set('error_log', $this->vlessLogOriginal);
        unlink($this->vlessLogFile);
    });

    it('skips only the broken VLESS nodes in the Clash subscription', function () {
        $content = Subscribe::getContent($this->vlessUser, 'clash');
        $names = array_column(yaml_parse($content)['proxies'], 'name');

        expect($names)->toContain($this->vlessValid, $this->vlessVmess)
            ->and($content)->not->toContain($this->vlessBroken[0])
            ->and($content)->not->toContain($this->vlessBroken[1]);
    });

    it('skips only the broken VLESS nodes in the sing-box subscription', function () {
        $content = Subscribe::getContent($this->vlessUser, 'singbox');
        $tags = array_column(json_decode($content, true)['outbounds'], 'tag');

        expect($tags)->toContain($this->vlessValid, $this->vlessVmess)
            ->and($content)->not->toContain($this->vlessBroken[0])
            ->and($content)->not->toContain($this->vlessBroken[1]);
    });

    it('skips only the broken VLESS nodes in the V2Ray subscription', function () {
        $content = Subscribe::getContent($this->vlessUser, 'v2ray');

        expect($content)->toContain('#' . $this->vlessValid)
            ->and(vlessSubVmessNames($content))->toContain($this->vlessVmess)
            ->and($content)->not->toContain($this->vlessBroken[0])
            ->and($content)->not->toContain($this->vlessBroken[1])
            ->and($content)->not->toContain($this->vlessBrokenServers[0])
            ->and($content)->not->toContain($this->vlessBrokenServers[1]);
    });

    it('keeps the VMess node and drops every VLESS node in the V2RayJson subscription', function () {
        $content = Subscribe::getContent($this->vlessUser, 'v2rayjson');
        $tags = array_column(json_decode($content, true)['outbounds'], 'tag');

        expect($tags)->toContain($this->vlessVmess)
            ->and($content)->not->toContain($this->vlessValid)
            ->and($content)->not->toContain($this->vlessBroken[0])
            ->and($content)->not->toContain($this->vlessBroken[1]);
    });

    it('logs only the id of a skipped node', function () {
        Subscribe::getContent($this->vlessUser, 'clash');
        $log = (string) file_get_contents($this->vlessLogFile);

        expect($log)
            ->toContain('Skipped invalid VLESS subscription node id=' . $this->vlessNodes[1]->id)
            ->toContain('Skipped invalid VLESS subscription node id=' . $this->vlessNodes[2]->id)
            ->not->toContain($this->vlessBroken[0])
            ->not->toContain($this->vlessBrokenServers[1])
            ->not->toContain(vlessSubPrivateKey());
    });
});
