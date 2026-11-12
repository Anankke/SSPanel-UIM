<?php

declare(strict_types=1);

use App\Controllers\Admin\NodeController;
use App\Services\Subscribe\Hysteria2;

it('builds a hysteria2 URI with UUID authentication and port hopping', function () {
    $node = (object) [
        'name' => 'HY2 IPv6',
        'server' => '2001:db8::1',
        'custom_config' => json_encode([
            'offset_port_node' => '443',
            'host' => 'hy.example.com',
            'allow_insecure' => true,
            'hysteria2' => [
                'finalmask' => [
                    'udp' => [[
                        'type' => 'salamander',
                        'settings' => ['password' => 'obfs secret'],
                    ]],
                ],
                'portHopping' => [
                    'enabled' => true,
                    'ports' => '20000-30000',
                ],
            ],
        ]),
    ];
    $user = (object) ['uuid' => '123e4567-e89b-12d3-a456-426614174000'];

    $uri = Hysteria2::buildUri($node, $user);

    expect($uri)
        ->toStartWith('hysteria2://123e4567-e89b-12d3-a456-426614174000@[2001:db8::1]:443/?')
        ->toContain('sni=hy.example.com')
        ->toContain('insecure=1')
        ->toContain('obfs=salamander')
        ->toContain('obfs-password=obfs%20secret')
        ->toContain('mport=20000-30000')
        ->toEndWith('#HY2%20IPv6');
});

function hy2ConfigFor(array $custom, string $server = 'hy.example.com'): array
{
    $custom['hysteria2'] = array_merge(['version' => 2], $custom['hysteria2'] ?? []);

    return Hysteria2::config((object) [
        'name' => 'HY2',
        'server' => $server,
        'custom_config' => json_encode($custom),
    ]);
}

function hy2ValidateCustomConfig(int $sort, string $raw): ?string
{
    $controller = (new ReflectionClass(NodeController::class))->newInstanceWithoutConstructor();

    return (new ReflectionClass($controller))->getMethod('validateCustomConfig')->invoke($controller, $sort, $raw);
}

function hy2ValidConfig(array $override = []): array
{
    return array_replace_recursive([
        'offset_port_node' => '443',
        'offset_port_user' => '443',
        'host' => 'hy.example.com',
        'allow_insecure' => false,
        'hysteria2' => [
            'version' => 2,
            'udpIdleTimeout' => 60,
            'masquerade' => ['type' => '404'],
            'finalmask' => [
                'udp' => [[
                    'type' => 'salamander',
                    'settings' => ['password' => 'obfs-secret'],
                ]],
                'quicParams' => ['congestion' => 'bbr'],
            ],
            'portHopping' => ['enabled' => true, 'autoConfigureFirewall' => false, 'ports' => '20000-30000'],
        ],
    ], $override);
}

it('prefers offset_port_user over offset_port_node and defaults to 443', function () {
    expect(hy2ConfigFor(['offset_port_user' => '8443', 'offset_port_node' => '9443'])['port'])->toBe(8443)
        ->and(hy2ConfigFor(['offset_port_node' => '9443'])['port'])->toBe(9443)
        ->and(hy2ConfigFor([])['port'])->toBe(443);
});

it('falls back to the server address when no SNI host is configured', function () {
    expect(hy2ConfigFor(['host' => 'sni.example.com'])['sni'])->toBe('sni.example.com')
        ->and(hy2ConfigFor([], 'node.example.com')['sni'])->toBe('node.example.com');
});

it('picks the salamander password from finalmask.udp', function () {
    $config = hy2ConfigFor(['hysteria2' => ['finalmask' => ['udp' => [
        ['type' => 'noise', 'settings' => ['password' => 'ignored']],
        ['type' => 'salamander', 'settings' => ['password' => 'obfs-secret']],
    ]]]]);

    expect($config['obfs'])->toBe('salamander')
        ->and($config['obfs_password'])->toBe('obfs-secret')
        ->and(hy2ConfigFor([])['obfs'])->toBe('none')
        ->and(hy2ConfigFor([])['obfs_password'])->toBe('');
});

it('selects port hopping by top-level, hysteria2 and legacy udpHop priority', function () {
    $top = ['enabled' => true, 'ports' => '30000-31000'];
    $nested = ['enabled' => true, 'ports' => '20000-21000'];
    $legacy = ['enabled' => true, 'ports' => '10000-11000'];

    expect(hy2ConfigFor([
        'portHopping' => $top,
        'hysteria2' => ['portHopping' => $nested, 'finalmask' => ['quicParams' => ['udpHop' => $legacy]]],
    ])['ports'])->toBe('30000-31000')
        ->and(hy2ConfigFor([
            'hysteria2' => ['portHopping' => $nested, 'finalmask' => ['quicParams' => ['udpHop' => $legacy]]],
        ])['ports'])->toBe('20000-21000')
        ->and(hy2ConfigFor([
            'hysteria2' => ['finalmask' => ['quicParams' => ['udpHop' => $legacy]]],
        ])['ports'])->toBe('10000-11000')
        ->and(hy2ConfigFor([])['ports'])->toBe('')
        ->and(hy2ConfigFor([])['hop_enabled'])->toBeFalse();
});

it('does not enable a legacy udpHop that lacks an explicit enabled flag', function () {
    $config = hy2ConfigFor(['hysteria2' => ['finalmask' => ['quicParams' => ['udpHop' => ['ports' => '10000-11000']]]]]);

    expect($config['hop_enabled'])->toBeFalse()
        ->and($config['ports'])->toBe('')
        ->and($config['hop_interval'])->toBeNull();
});

it('enables hopping from a native udphop mask only when no hopping object is selected', function () {
    $native = ['hysteria2' => ['finalmask' => ['udp' => [
        ['type' => 'udphop', 'settings' => ['remotePorts' => '40000-41000', 'interval' => 30]],
    ]]]];

    $config = hy2ConfigFor($native);

    expect($config['hop_enabled'])->toBeTrue()
        ->and($config['ports'])->toBe('40000-41000')
        ->and($config['hop_interval'])->toEqual(30)
        ->and(hy2ConfigFor(['portHopping' => ['enabled' => false]] + $native)['hop_enabled'])->toBeFalse()
        ->and(hy2ConfigFor(['portHopping' => ['enabled' => false]] + $native)['ports'])->toBe('');
});

it('lets an explicit top-level disabled port hopping override lower-level enabled ones', function () {
    $lower = [
        'hysteria2' => [
            'portHopping' => ['enabled' => true, 'ports' => '20000-21000'],
            'finalmask' => ['quicParams' => ['udpHop' => ['enabled' => true, 'ports' => '10000-11000']]],
        ],
    ];

    $config = hy2ConfigFor(['portHopping' => ['enabled' => false, 'ports' => '30000-31000']] + $lower);

    expect($config['hop_enabled'])->toBeFalse()
        ->and($config['ports'])->toBe('')
        ->and(hy2ConfigFor(['portHopping' => ['enabled' => false]] + $lower)['ports'])->toBe('');
});

it('lets an explicit disabled hysteria2.portHopping override an enabled legacy udpHop', function () {
    $config = hy2ConfigFor(['hysteria2' => [
        'portHopping' => ['enabled' => false],
        'finalmask' => ['quicParams' => ['udpHop' => ['enabled' => true, 'ports' => '10000-11000']]],
    ]]);

    expect($config['hop_enabled'])->toBeFalse()
        ->and($config['ports'])->toBe('');
});

it('reads bandwidth hints in the formats of each client', function () {
    $config = hy2ConfigFor(['hysteria2' => ['finalmask' => ['quicParams' => [
        'brutalUp' => '60 mbps',
        'brutalDown' => '1 gbps',
    ]]]]);

    expect($config['up'])->toBe('60 mbps')
        ->and($config['down'])->toBe('1 gbps')
        ->and($config['up_mbps'])->toEqual(60)
        ->and($config['down_mbps'])->toEqual(1024);
});

it('returns no config for nodes whose custom_config cannot describe a hysteria2 node', function (string $raw) {
    $node = (object) ['name' => 'Broken', 'server' => 'hy.example.com', 'custom_config' => $raw];

    expect(Hysteria2::config($node))->toBeNull()
        ->and(Hysteria2::buildUri($node, (object) ['uuid' => '123e4567-e89b-12d3-a456-426614174000']))->toBeNull();
})->with([
    'json string' => ['"abc"'],
    'json number' => ['5'],
    'json boolean' => ['true'],
    'empty string' => [''],
    'hysteria2 missing' => ['{"offset_port_node":"443"}'],
    'hysteria2 not an object' => ['{"hysteria2":"abc"}'],
]);

it('accepts a valid sort 15 custom_config', function () {
    expect(hy2ValidateCustomConfig(15, json_encode(hy2ValidConfig())))->toBeNull();
});

it('accepts explicit disabled and native udphop port hopping', function () {
    $disabled = hy2ValidConfig(['portHopping' => ['enabled' => false]]);
    $native = hy2ValidConfig(['hysteria2' => ['finalmask' => ['udp' => [
        1 => ['type' => 'udphop', 'settings' => ['remotePorts' => '40000-41000,42000']],
    ]]]]);

    expect(hy2ValidateCustomConfig(15, json_encode($disabled)))->toBeNull()
        ->and(hy2ValidateCustomConfig(15, json_encode($native)))->toBeNull();
});

it('rejects invalid sort 15 custom_config', function (string $raw) {
    expect(hy2ValidateCustomConfig(15, $raw))->toBeString()->not->toBeEmpty();
})->with([
    'port 0' => [json_encode(hy2ValidConfig(['offset_port_node' => 0]))],
    'short salamander password' => [json_encode(hy2ValidConfig(['hysteria2' => ['finalmask' => ['udp' => [
        ['type' => 'salamander', 'settings' => ['password' => 'abc']],
    ]]]]))],
    'descending hop range' => [json_encode(hy2ValidConfig(['hysteria2' => ['portHopping' => ['ports' => '30000-20000']]]))],
    'hop object without enabled' => [json_encode(hy2ValidConfig(['portHopping' => ['ports' => '20000-30000']]))],
    'hop object with legacy enable key' => [json_encode(hy2ValidConfig([
        'hysteria2' => ['portHopping' => ['enable' => true, 'enabled' => true, 'ports' => '20000-30000']],
    ]))],
    'hop enabled not boolean' => [json_encode(hy2ValidConfig(['hysteria2' => ['portHopping' => ['enabled' => 'true']]]))],
    'legacy udpHop without enabled' => [json_encode(hy2ValidConfig(['hysteria2' => ['finalmask' => ['quicParams' => [
        'udpHop' => ['ports' => '20000-30000'],
    ]]]]))],
    'hysteria2.portHopping without enabled' => [(static function (): string {
        $config = hy2ValidConfig(['portHopping' => ['enabled' => false]]);
        unset($config['hysteria2']['portHopping']['enabled']);

        return json_encode($config);
    })()],
    'udphop mask without remotePorts' => [json_encode(hy2ValidConfig(['hysteria2' => ['finalmask' => ['udp' => [
        1 => ['type' => 'udphop', 'settings' => ['interval' => 30]],
    ]]]]))],
    'udphop mask with invalid remotePorts' => [json_encode(hy2ValidConfig(['hysteria2' => ['finalmask' => ['udp' => [
        1 => ['type' => 'udphop', 'settings' => ['remotePorts' => '30000-20000']],
    ]]]]))],
    'json list' => ['[]'],
    'json string' => ['"abc"'],
    'json number' => ['5'],
    'invalid json' => ['not json'],
    'empty' => [''],
]);

it('keeps the previous save behaviour for other node types', function (int $sort, string $raw) {
    expect(hy2ValidateCustomConfig($sort, $raw))->toBeNull();
})->with([
    'vmess empty' => [11, ''],
    'vmess invalid json' => [11, 'not json'],
    'trojan empty' => [14, ''],
    'trojan invalid json' => [14, 'not json'],
]);
