<?php

declare(strict_types=1);

namespace App\Services\Subscribe;

use App\Services\Subscribe;
use App\Utils\Tools;
use function array_filter;
use function array_merge;
use function json_decode;
use function json_encode;

final class SingBox extends Base
{
    public function getContent($user): string
    {
        $nodes = [];
        $singbox_config = $_ENV['SingBox_Config'];
        $nodes_raw = Subscribe::getUserNodes($user);

        foreach ($nodes_raw as $node_raw) {
            $node_custom_config = json_decode($node_raw->custom_config, true);

            switch ((int) $node_raw->sort) {
                case 0:
                    $node = [
                        'type' => 'shadowsocks',
                        'tag' => $node_raw->name,
                        'server' => $node_raw->server,
                        'server_port' => (int) $user->port,
                        'method' => $user->method,
                        'password' => $user->passwd,
                    ];

                    break;
                case 1:
                    $ss_2022_port = $node_custom_config['offset_port_user'] ??
                        ($node_custom_config['offset_port_node'] ?? 443);
                    $method = $node_custom_config['method'] ?? '2022-blake3-aes-128-gcm';
                    $user_pk = Tools::genSs2022UserPk($user->passwd, $method);
                    $uot = $node_custom_config['uot'] ?? false;

                    if (! $user_pk) {
                        $node = [];
                        break;
                    }

                    $server_key = $node_custom_config['server_key'] ?? '';

                    $node = [
                        'type' => 'shadowsocks',
                        'tag' => $node_raw->name,
                        'server' => $node_raw->server,
                        'server_port' => (int) $ss_2022_port,
                        'method' => $method,
                        'password' => $server_key === '' ? $user_pk : $server_key . ':' .$user_pk,
                        'udp_over_tcp' => (bool) $uot,
                    ];

                    break;
                case 2:
                    $tuic_port = $node_custom_config['offset_port_user'] ??
                        ($node_custom_config['offset_port_node'] ?? 443);
                    $host = $node_custom_config['host'] ?? '';
                    $allow_insecure = $node_custom_config['allow_insecure'] ?? false;
                    $congestion_control = $node_custom_config['congestion_control'] ?? 'bbr';

                    $node = [
                        'type' => 'tuic',
                        'tag' => $node_raw->name,
                        'server' => $node_raw->server,
                        'server_port' => (int) $tuic_port,
                        'uuid' => $user->uuid,
                        'password' => $user->passwd,
                        'congestion_control' => $congestion_control,
                        'zero_rtt_handshake' => true,
                        'tls' => [
                            'enabled' => true,
                            'server_name' => $host,
                            'insecure' => (bool) $allow_insecure,
                        ],
                    ];

                    $node['tls'] = array_filter($node['tls']);

                    break;
                case 11:
                    if (Vless::enabled($node_custom_config ?? [])) {
                        $client = $this->vlessClient($node_raw, $node_custom_config);
                        if ($client === null) {
                            continue 2;
                        }
                        // Official sing-box has no XHTTP transport; omit rather than emit an invalid outbound.
                        if ($client['network'] === 'xhttp') {
                            $node = [];
                            break;
                        }
                        $node = [
                            'type' => 'vless',
                            'tag' => $node_raw->name,
                            'server' => $node_raw->server,
                            'server_port' => $client['port'],
                            'uuid' => $user->uuid,
                        ];
                        if ($client['flow'] !== '') {
                            $node['flow'] = $client['flow'];
                        }
                        if ($client['security'] !== 'none') {
                            $node['tls'] = [
                                'enabled' => true,
                                'server_name' => $client['server_name'],
                            ];
                            if ($client['security'] === 'reality') {
                                $node['tls']['utls'] = ['enabled' => true, 'fingerprint' => $client['fingerprint']];
                                $node['tls']['reality'] = [
                                    'enabled' => true,
                                    'public_key' => $client['public_key'],
                                    'short_id' => $client['short_id'],
                                ];
                            } else {
                                $node['tls']['insecure'] = $client['allow_insecure'];
                            }
                        }
                        if ($client['network'] !== 'tcp') {
                            $node['transport'] = ['type' => $client['network']];
                            if ($client['network'] === 'ws' || $client['network'] === 'httpupgrade') {
                                $node['transport']['path'] = $node_custom_config['path'] ?? '/';
                                if ($client['network'] === 'httpupgrade') {
                                    $node['transport']['host'] = $node_custom_config['host'] ?? '';
                                } else {
                                    $node['transport']['headers'] = ['Host' => $node_custom_config['host'] ?? ''];
                                }
                            } elseif ($client['network'] === 'grpc') {
                                $node['transport']['service_name'] = $node_custom_config['servicename'] ?? '';
                            }
                        }
                        break;
                    }

                    $v2_port = $node_custom_config['offset_port_user'] ??
                        ($node_custom_config['offset_port_node'] ?? 443);
                    $transport = ($node_custom_config['network'] ?? '') === 'tcp' ? '' : $node_custom_config['network'];
                    $host = $node_custom_config['header']['request']['headers']['Host'][0] ??
                        $node_custom_config['host'] ?? '';
                    $path = $node_custom_config['header']['request']['path'][0] ?? $node_custom_config['path'] ?? '';
                    $headers = $node_custom_config['header']['request']['headers'] ?? [];
                    $service_name = $node_custom_config['servicename'] ?? '';
                    $utls = $node_custom_config['utls'] ?? false;
                    $method = $node_custom_config['method'] ?? '';
                    $max_early_data = $node_custom_config['max_early_data'] ?? '';
                    $early_data_header_name = $node_custom_config['early_data_header_name'] ?? '';

                    $node = [
                        'type' => 'vmess',
                        'tag' => $node_raw->name,
                        'server' => $node_raw->server,
                        'server_port' => (int) $v2_port,
                        'uuid' => $user->uuid,
                        'security' => 'auto',
                        'alter_id' => 0,
                        'tls' => [
                            'enabled' => true,
                            'server_name' => $host,
                            'utls' => [
                                'enabled' => $utls,
                                'fingerprint' => 'chrome',
                            ],
                        ],
                        'packet_encoding' => 'xudp',
                        'global_padding' => true,
                        'authenticated_length' => true,
                        'transport' => [
                            'type' => $transport,
                            'path' => $path,
                            'method' => $method,
                            'headers' => $headers,
                            'service_name' => $service_name,
                            'max_early_data' => (int) $max_early_data,
                            'early_data_header_name' => $early_data_header_name,
                        ],
                    ];

                    $node['tls'] = array_filter($node['tls']);
                    $node['transport'] = array_filter($node['transport']);

                    break;
                case 14:
                    $trojan_port = $node_custom_config['offset_port_user'] ??
                        ($node_custom_config['offset_port_node'] ?? 443);
                    $host = $node_custom_config['host'] ?? '';
                    $allow_insecure = $node_custom_config['allow_insecure'] ?? '0';
                    $transport = $node_custom_config['network'] ?? '';
                    $path = $node_custom_config['header']['request']['path'][0] ?? $node_custom_config['path'] ?? '';
                    $headers = $node_custom_config['header']['request']['headers'] ?? [];
                    $service_name = $node_custom_config['servicename'] ?? '';

                    $node = [
                        'type' => 'trojan',
                        'tag' => $node_raw->name,
                        'server' => $node_raw->server,
                        'server_port' => (int) $trojan_port,
                        'password' => $user->uuid,
                        'tls' => [
                            'enabled' => true,
                            'server_name' => $host,
                            'insecure' => (bool) $allow_insecure,
                        ],
                        'transport' => [
                            'type' => $transport,
                            'path' => $path,
                            'headers' => $headers,
                            'service_name' => $service_name,
                        ],
                    ];

                    $node['tls'] = array_filter($node['tls']);
                    $node['transport'] = array_filter($node['transport']);

                    break;
                case 15:
                    $hy2 = Hysteria2::config($node_raw);
                    $node = [
                        'type' => 'hysteria2',
                        'tag' => $node_raw->name,
                        'server' => $hy2['server'],
                        'server_port' => $hy2['port'],
                        'password' => $user->uuid,
                        'tls' => [
                            'enabled' => true,
                            'server_name' => $hy2['sni'],
                            'insecure' => $hy2['insecure'],
                        ],
                    ];

                    if ($hy2['salamander'] !== '') {
                        $node['obfs'] = [
                            'type' => 'salamander',
                            'password' => $hy2['salamander'],
                        ];
                    }
                    if ($hy2['up_mbps'] !== null) {
                        $node['up_mbps'] = $hy2['up_mbps'];
                    }
                    if ($hy2['down_mbps'] !== null) {
                        $node['down_mbps'] = $hy2['down_mbps'];
                    }
                    if ($hy2['ports'] !== '') {
                        unset($node['server_port']);
                        $node['server_ports'] = array_map(
                            static fn (string $range): string => str_replace('-', ':', trim($range)),
                            explode(',', $hy2['ports'])
                        );
                    }
                    if ($hy2['hop_interval'] !== null) {
                        $node['hop_interval'] = $hy2['hop_interval'] . 's';
                    }

                    break;
                default:
                    $node = [];
                    break;
            }

            if ($node === []) {
                continue;
            }

            $nodes[] = $node;
            $singbox_config['outbounds'][0]['outbounds'][] = $node_raw->name;
            $singbox_config['outbounds'][1]['outbounds'][] = $node_raw->name;
        }

        if ($nodes === []) {
            // Keep the profile loadable, but reject traffic when there are no compatible nodes.
            $tags = array_column($singbox_config['outbounds'], 'tag');
            $emptyTag = 'subscription-empty';
            while (in_array($emptyTag, $tags, true)) {
                $emptyTag .= '-';
            }
            $singbox_config['outbounds'][] = ['type' => 'direct', 'tag' => $emptyTag];
            foreach ($singbox_config['outbounds'] as &$outbound) {
                if (in_array($outbound['type'] ?? '', ['selector', 'urltest'], true) && ! ($outbound['outbounds'] ?? [])) {
                    if ($outbound['type'] === 'urltest') {
                        // No background direct connectivity probes for an unavailable profile.
                        $outbound = ['type' => 'selector', 'tag' => $outbound['tag'], 'outbounds' => []];
                    }
                    $outbound['outbounds'] = [$emptyTag];
                    if (isset($outbound['default'])) {
                        $outbound['default'] = $emptyTag;
                    }
                }
            }
            unset($outbound);
            $singbox_config['route']['rules'] = array_merge([['action' => 'reject']], $singbox_config['route']['rules'] ?? []);
        }
        $singbox_config['outbounds'] = array_merge($singbox_config['outbounds'], $nodes);
        $singbox_config['experimental']['cache_file']['cache_id'] = $_ENV['appName'];

        return json_encode($singbox_config);
    }
}
