<?php

declare(strict_types=1);

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
