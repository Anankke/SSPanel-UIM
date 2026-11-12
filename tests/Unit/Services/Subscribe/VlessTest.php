<?php

declare(strict_types=1);

use App\Controllers\Admin\NodeController;
use App\Services\Subscribe\Vless;

function vlessUnitBase64Url(string $raw): string
{
    return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
}

function vlessUnitPrivateKey(): string
{
    return vlessUnitBase64Url(str_repeat("\x11", 32));
}

function vlessUnitPublicKey(): string
{
    return vlessUnitBase64Url(sodium_crypto_scalarmult_base(str_repeat("\x11", 32)));
}

function vlessUnitRealityConfig(array $override = []): array
{
    return array_replace([
        'enable_vless' => '1',
        'enable_reality' => true,
        'security' => 'reality',
        'network' => 'tcp',
        'flow' => 'xtls-rprx-vision',
        'offset_port_node' => '443',
        'offset_port_user' => '8443',
        'reality-opts' => [
            'dest' => 'cover.example.com:443',
            'server_names' => ['cover.example.com'],
            'private_key' => vlessUnitPrivateKey(),
            'short_ids' => ['0123456789abcdef'],
        ],
    ], $override);
}

function vlessUnitValidate(int $sort, mixed $raw): ?string
{
    $controller = (new ReflectionClass(NodeController::class))->newInstanceWithoutConstructor();

    return (new ReflectionClass($controller))->getMethod('validateCustomConfig')->invoke($controller, $sort, $raw);
}

describe('Vless::enabled', function () {
    it('accepts the node backend contract values', function () {
        expect(Vless::enabled(['enable_vless' => 1]))->toBeTrue()
            ->and(Vless::enabled(['enable_vless' => '1']))->toBeTrue()
            ->and(Vless::enabled(['enable_vless' => true]))->toBeTrue();
    });

    it('rejects every other value', function () {
        foreach ([0, '0', false, null, 'true', 'yes', 2, [], ''] as $value) {
            expect(Vless::enabled(['enable_vless' => $value]))->toBeFalse();
        }
        expect(Vless::enabled([]))->toBeFalse();
    });

    it('is false for a custom_config that is not an array', function () {
        expect(Vless::enabled('abc'))->toBeFalse()
            ->and(Vless::enabled(null))->toBeFalse()
            ->and(Vless::enabled(1))->toBeFalse()
            ->and(Vless::enabled(true))->toBeFalse();
    });
});

describe('Vless::client', function () {
    it('defaults to plain TCP on port 443 and the node server name', function () {
        expect(Vless::client(['enable_vless' => 1], 'node.example.com'))->toBe([
            'port' => 443,
            'network' => 'tcp',
            'security' => 'none',
            'flow' => '',
            'server_name' => 'node.example.com',
            'fingerprint' => 'chrome',
            'allow_insecure' => false,
            'public_key' => '',
            'short_id' => '',
            'x25519mlkem768' => false,
        ]);
    });

    it('prefers offset_port_user over offset_port_node', function () {
        expect(Vless::client(['offset_port_user' => '8443', 'offset_port_node' => '9443'], '')['port'])->toBe(8443)
            ->and(Vless::client(['offset_port_node' => '9443'], '')['port'])->toBe(9443);
    });

    it('normalises network aliases', function () {
        foreach ([
            'raw' => 'tcp', '' => 'tcp', 'TCP' => 'tcp', 'websocket' => 'ws', 'WS' => 'ws',
            'splithttp' => 'xhttp', 'xhttp' => 'xhttp', 'grpc' => 'grpc', 'httpupgrade' => 'httpupgrade',
        ] as $alias => $network) {
            expect(Vless::client(['network' => $alias], '')['network'])->toBe($network);
        }
    });

    it('maps the xtls security alias to tls', function () {
        expect(Vless::client(['security' => 'xtls'], '')['security'])->toBe('tls')
            ->and(Vless::client(['security' => 'tls', 'host' => 'tls.example.com'], '')['server_name'])
            ->toBe('tls.example.com');
    });

    it('rejects an unsupported network, flow, security or port', function () {
        foreach ([
            ['network' => 'kcp'],
            ['flow' => 'xtls-rprx-direct'],
            ['security' => 'ssl'],
            ['offset_port_node' => '0'],
            ['offset_port_node' => '65536'],
            ['offset_port_user' => 'abc'],
        ] as $config) {
            expect(fn () => Vless::client($config, ''))->toThrow(InvalidArgumentException::class);
        }
    });

    it('rejects Vision without TCP and TLS or REALITY', function () {
        expect(fn () => Vless::client(['flow' => 'xtls-rprx-vision', 'network' => 'tcp', 'security' => 'none'], ''))
            ->toThrow(InvalidArgumentException::class)
            ->and(fn () => Vless::client(['flow' => 'xtls-rprx-vision', 'network' => 'ws', 'security' => 'tls'], ''))
            ->toThrow(InvalidArgumentException::class)
            ->and(Vless::client(['flow' => 'xtls-rprx-vision', 'network' => 'tcp', 'security' => 'tls'], '')['flow'])
            ->toBe('xtls-rprx-vision');
    });

    it('rejects string fields that are not strings', function () {
        foreach (['network', 'security', 'flow', 'host', 'fingerprint', 'path', 'servicename'] as $field) {
            expect(fn () => Vless::client([$field => ['x']], ''))->toThrow(InvalidArgumentException::class);
        }
    });

    it('requires reality_x25519mlkem768 to be a JSON boolean', function () {
        foreach ([1, '1', 'true'] as $value) {
            expect(fn () => Vless::client(vlessUnitRealityConfig(['reality_x25519mlkem768' => $value]), ''))
                ->toThrow(InvalidArgumentException::class, 'reality_x25519mlkem768 must be a JSON boolean');
        }
        expect(Vless::client(vlessUnitRealityConfig(['reality_x25519mlkem768' => true]), '')['x25519mlkem768'])->toBeTrue()
            ->and(Vless::client(vlessUnitRealityConfig(), '')['x25519mlkem768'])->toBeFalse();
    });

    it('requires enable_reality to be a JSON boolean', function () {
        foreach (['true', 1, '1', 'false', 0] as $value) {
            expect(fn () => Vless::client(vlessUnitRealityConfig(['enable_reality' => $value]), ''))
                ->toThrow(InvalidArgumentException::class, 'enable_reality must be a JSON boolean');
        }
    });

    it('rejects security reality without enable_reality=true', function () {
        $config = vlessUnitRealityConfig();
        unset($config['enable_reality']);

        expect(fn () => Vless::client($config, ''))
            ->toThrow(InvalidArgumentException::class, 'REALITY requires enable_reality=true')
            ->and(fn () => Vless::client(array_replace($config, ['enable_reality' => false]), ''))
            ->toThrow(InvalidArgumentException::class, 'REALITY requires enable_reality=true');
    });

    it('rejects enable_reality=true combined with a different security', function () {
        foreach (['none', 'tls', 'xtls'] as $security) {
            expect(fn () => Vless::client(vlessUnitRealityConfig(['security' => $security]), ''))
                ->toThrow(InvalidArgumentException::class, 'enable_reality=true contradicts');
        }
    });

    it('accepts enable_reality=true with security reality, empty or absent', function () {
        $absent = vlessUnitRealityConfig();
        unset($absent['security']);

        foreach ([vlessUnitRealityConfig(), vlessUnitRealityConfig(['security' => '']), $absent] as $config) {
            expect(Vless::client($config, '')['security'])->toBe('reality');
        }
    });

    it('derives the REALITY public key from the X25519 private key', function () {
        $client = Vless::client(vlessUnitRealityConfig(), 'node.example.com');

        expect($client)
            ->toMatchArray([
                'port' => 8443,
                'network' => 'tcp',
                'security' => 'reality',
                'flow' => 'xtls-rprx-vision',
                'server_name' => 'cover.example.com',
                'short_id' => '0123456789abcdef',
                'public_key' => vlessUnitPublicKey(),
            ])
            ->and(json_encode($client))->not->toContain(vlessUnitPrivateKey());
    });

    it('selects the configured host as REALITY server name only when it is allowed', function () {
        $opts = vlessUnitRealityConfig()['reality-opts'];
        $opts['server_names'] = ['one.example.com', 'two.example.com'];

        expect(Vless::client(vlessUnitRealityConfig(['host' => 'two.example.com', 'reality-opts' => $opts]), '')['server_name'])
            ->toBe('two.example.com')
            ->and(Vless::client(vlessUnitRealityConfig(['host' => 'other.example.com', 'reality-opts' => $opts]), '')['server_name'])
            ->toBe('one.example.com');
    });

    it('rejects incomplete REALITY options without echoing the private key', function () {
        $good = vlessUnitRealityConfig()['reality-opts'];
        $cases = [
            array_diff_key($good, ['private_key' => 1]),
            array_replace($good, ['private_key' => 'bad-secret']),
            array_replace($good, ['private_key' => vlessUnitBase64Url(str_repeat("\x11", 31))]),
            array_replace($good, ['server_names' => []]),
            array_replace($good, ['server_names' => ['']]),
            array_replace($good, ['short_ids' => []]),
            array_replace($good, ['short_ids' => ['abc']]),
            array_replace($good, ['short_ids' => ['0123456789abcdef00']]),
            array_replace($good, ['short_ids' => ['zz']]),
        ];

        foreach ($cases as $opts) {
            try {
                Vless::client(vlessUnitRealityConfig(['reality-opts' => $opts]), '');
                $message = null;
            } catch (InvalidArgumentException $error) {
                $message = $error->getMessage();
            }
            expect($message)->toBeString()->not->toContain('bad-secret');
        }
        expect(Vless::client(vlessUnitRealityConfig(['reality-opts' => array_replace($good, ['short_ids' => ['']])]), '')['short_id'])
            ->toBe('');
    });
});

describe('Vless::uri', function () {
    it('builds a REALITY Vision link with an IPv6 host and an encoded name', function () {
        $node = (object) ['name' => '测试 #1', 'server' => '2001:db8::1'];
        $user = (object) ['uuid' => '123e4567-e89b-42d3-a456-426614174000'];

        $uri = Vless::uri($node, $user, vlessUnitRealityConfig());

        expect($uri)
            ->toStartWith('vless://123e4567-e89b-42d3-a456-426614174000@[2001:db8::1]:8443?')
            ->toContain('encryption=none&security=reality&type=tcp&flow=xtls-rprx-vision')
            ->toContain('&sni=cover.example.com&fp=chrome&pbk=' . vlessUnitPublicKey() . '&sid=0123456789abcdef')
            ->toEndWith('#%E6%B5%8B%E8%AF%95%20%231')
            ->not->toContain(vlessUnitPrivateKey());
    });

    it('builds TLS links per transport', function () {
        $node = (object) ['name' => 'N', 'server' => 'node.example.com'];
        $user = (object) ['uuid' => 'u'];
        $base = ['enable_vless' => 1, 'security' => 'tls', 'host' => 'h.example.com', 'offset_port_node' => '9443'];

        expect(Vless::uri($node, $user, $base + ['network' => 'ws', 'path' => '/ws']))
            ->toBe('vless://u@node.example.com:9443?encryption=none&security=tls&type=ws&sni=h.example.com'
                . '&host=h.example.com&path=%2Fws#N')
            ->and(Vless::uri($node, $user, $base + ['network' => 'grpc', 'servicename' => 'svc', 'allow_insecure' => true]))
            ->toBe('vless://u@node.example.com:9443?encryption=none&security=tls&type=grpc&sni=h.example.com'
                . '&allowInsecure=1&serviceName=svc#N')
            ->and(Vless::uri($node, $user, $base + ['network' => 'xhttp', 'path' => '/x']))
            ->toBe('vless://u@node.example.com:9443?encryption=none&security=tls&type=xhttp&sni=h.example.com'
                . '&host=h.example.com&path=%2Fx&mode=auto#N')
            ->and(Vless::uri($node, $user, ['security' => 'none']))
            ->toBe('vless://u@node.example.com:443?encryption=none&security=none&type=tcp#N');
    });
});

describe('NodeController::validateCustomConfig for VLESS', function () {
    $valid = ['enable_vless' => 1, 'offset_port_node' => '443', 'network' => 'websocket'];

    it('accepts a valid VLESS config', function () use ($valid) {
        expect(vlessUnitValidate(11, json_encode($valid)))->toBeNull()
            ->and(vlessUnitValidate(11, json_encode(vlessUnitRealityConfig())))->toBeNull();
    });

    it('rejects a bad port, flow, network or REALITY key', function () use ($valid) {
        foreach ([
            ['offset_port_node' => '0'],
            ['offset_port_node' => '70000'],
            ['flow' => 'xtls-rprx-vision'],
            ['network' => 'kcp'],
            ['enable_reality' => true, 'security' => 'tls'],
        ] as $override) {
            $error = vlessUnitValidate(11, json_encode(array_replace($valid, $override)));
            expect($error)->toBeString();
        }

        $withoutPort = $valid;
        unset($withoutPort['offset_port_node']);
        $badKey = vlessUnitRealityConfig(['reality-opts' => ['server_names' => ['a.example.com'], 'short_ids' => ['00'], 'private_key' => 'bad-secret']]);
        $error = vlessUnitValidate(11, json_encode($badKey));

        expect(vlessUnitValidate(11, json_encode($withoutPort)))->toBeString()
            ->and($error)->toStartWith('VLESS custom_config 无效：')
            ->and($error)->not->toContain('bad-secret');
    });

    it('keeps the save behaviour of sort 11 nodes without enable_vless', function () {
        expect(vlessUnitValidate(11, 'not json'))->toBeNull()
            ->and(vlessUnitValidate(11, '"abc"'))->toBeNull()
            ->and(vlessUnitValidate(11, ''))->toBeNull()
            ->and(vlessUnitValidate(11, json_encode(['enable_vless' => 0, 'network' => 'kcp'])))->toBeNull()
            ->and(vlessUnitValidate(11, json_encode(['enable_vless' => false, 'offset_port_node' => '0'])))->toBeNull()
            ->and(vlessUnitValidate(11, json_encode(['network' => 'kcp'])))->toBeNull()
            ->and(vlessUnitValidate(11, null))->toBeNull();
    });

    it('does not apply to other node sorts', function () {
        expect(vlessUnitValidate(14, json_encode(['enable_vless' => 1, 'network' => 'kcp'])))->toBeNull();
    });
});
