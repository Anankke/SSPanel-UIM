<?php

declare(strict_types=1);

namespace App\Services\Subscribe;

use App\Services\Subscribe;
use App\Utils\Tools;
use function array_filter;
use function array_merge;
use function json_decode;
use function json_encode;

final class V2RayJson extends Base
{
    public function getContent($user): string
    {
        $nodes = [];
        $v2rayjson_config = $_ENV['V2RayJson_Config'];
        $nodes_raw = Subscribe::getUserNodes($user);

        foreach ($nodes_raw as $node_raw) {
            $node_custom_config = json_decode($node_raw->custom_config, true);

            switch ((int) $node_raw->sort) {
                case 0:
                    $node = [
                        'protocol' => 'shadowsocks',
                        'settings' => [
                            'address' => $node_raw->server,
                            'port' => (int) $user->port,
                            'method' => $user->method,
                            'password' => $user->passwd,
                        ],
                        'tag' => $node_raw->name,
                    ];

                    break;
                case 1:
                    $ss_2022_port = $node_custom_config['offset_port_user'] ?? ($node_custom_config['offset_port_node'] ?? 443);
                    $method = $node_custom_config['method'] ?? '2022-blake3-aes-128-gcm';
                    $user_pk = Tools::genSs2022UserPk($user->passwd, $method);

                    if (! $user_pk) {
                        $node = [];
                        break;
                    }

                    $server_key = $node_custom_config['server_key'] ?? '';

                    $node = [
                        'protocol' => 'shadowsocks2022',
                        'settings' => [
                            'address' => $node_raw->server,
                            'port' => (int) $ss_2022_port,
                            'method' => $user->method,
                            'psk' => $server_key === '' ? $user_pk : $server_key . ':' .$user_pk,
                        ],
                        'tag' => $node_raw->name,
                    ];

                    break;
                case 11:
                    $v2_port = $node_custom_config['offset_port_user'] ?? ($node_custom_config['offset_port_node'] ?? 443);
                    $security = $node_custom_config['security'] ?? 'none';
                    $transport = $node_custom_config['network'] ?? 'tcp';
                    $host = $node_custom_config['header']['request']['headers']['Host'][0] ??
                        $node_custom_config['host'] ?? $node_raw->server;
                    $path = $node_custom_config['header']['request']['path'][0] ?? $node_custom_config['path'] ?? '';
                    $headers = $node_custom_config['header']['request']['headers'] ?? [];
                    $service_name = $node_custom_config['servicename'] ?? '';
                    $meek_url = $node_custom_config['meek_url'] ?? '';

                    $node = [
                        'protocol' => 'vmess',
                        'settings' => [
                            'address' => $node_raw->server,
                            'port' => (int) $v2_port,
                            'uuid' => $user->uuid,
                        ],
                        'tag' => $node_raw->name,
                        'streamSettings' => [
                            'transport' => $transport,
                            'transportSettings' => [
                                'ws' => [
                                    'path' => $transport === 'ws' ? $path : '',
                                    'header' => $headers,
                                ],
                                'grpc' => [
                                    'host' => $transport === 'grpc' ? $host : '',
                                    'service_name' => $service_name,
                                ],
                                'meek' => [
                                    'url' => $meek_url,
                                ],
                                'httpupgrade' => [
                                    'path' => $transport === 'httpupgrade' ? $path : '',
                                    'host' => $transport === 'httpupgrade' ? $host : '',
                                ],
                            ],
                            'security' => $security,
                            'securitySettings' => [
                                'tls' => [
                                    'server_name' => $security === ('tls' || 'auto') ? $host : '',
                                ],
                            ],
                        ],
                    ];

                    $node['streamSettings']['transportSettings']['ws'] = array_filter($node['streamSettings']['transportSettings']['ws']);
                    $node['streamSettings']['transportSettings']['grpc'] = array_filter($node['streamSettings']['transportSettings']['grpc']);
                    $node['streamSettings']['transportSettings']['meek'] = array_filter($node['streamSettings']['transportSettings']['meek']);
                    $node['streamSettings']['transportSettings']['httpupgrade'] = array_filter($node['streamSettings']['transportSettings']['httpupgrade']);
                    $node['streamSettings']['transportSettings'] = array_filter($node['streamSettings']['transportSettings']);
                    $node['streamSettings']['securitySettings']['tls'] = array_filter($node['streamSettings']['securitySettings']['tls']);
                    $node['streamSettings']['securitySettings'] = array_filter($node['streamSettings']['securitySettings']);

                    break;
                case 14:
                    $trojan_port = $node_custom_config['offset_port_user'] ?? ($node_custom_config['offset_port_node'] ?? 443);
                    $host = $node_custom_config['host'] ?? $node_raw->server;
                    $allow_insecure = $node_custom_config['allow_insecure'] ?? '0';
                    $transport = $node_custom_config['network'] ?? '';
                    $path = $node_custom_config['header']['request']['path'][0] ?? $node_custom_config['path'] ?? '';
                    $headers = $node_custom_config['header']['request']['headers'] ?? [];
                    $service_name = $node_custom_config['servicename'] ?? '';

                    $node = [
                        'protocol' => 'trojan',
                        'settings' => [
                            'address' => $node_raw->server,
                            'port' => (int) $trojan_port,
                            'password' => $user->uuid,
                        ],
                        'tag' => $node_raw->name,
                        'streamSettings' => [
                            'transport' => $transport,
                            'transportSettings' => [
                                'ws' => [
                                    'path' => $transport === 'ws' ? $path : '',
                                    'header' => $headers,
                                ],
                                'grpc' => [
                                    'host' => $transport === 'grpc' ? $host : '',
                                    'service_name' => $service_name,
                                ],
                                'httpupgrade' => [
                                    'path' => $transport === 'httpupgrade' ? $path : '',
                                    'host' => $transport === 'httpupgrade' ? $host : '',
                                ],
                            ],
                            'security' => 'tls',
                            'securitySettings' => [
                                'tls' => [
                                    'allow_insecure' => (bool) $allow_insecure,
                                    'server_name' => $host,
                                ],
                            ],
                        ],
                    ];

                    $node['streamSettings']['transportSettings']['ws'] = array_filter($node['streamSettings']['transportSettings']['ws']);
                    $node['streamSettings']['transportSettings']['grpc'] = array_filter($node['streamSettings']['transportSettings']['grpc']);
                    $node['streamSettings']['transportSettings']['httpupgrade'] = array_filter($node['streamSettings']['transportSettings']['httpupgrade']);
                    $node['streamSettings']['transportSettings'] = array_filter($node['streamSettings']['transportSettings']);
                    $node['streamSettings']['securitySettings'] = array_filter($node['streamSettings']['securitySettings']);

                    break;
                default:
                    $node = [];
                    break;
            }

            if ($node === []) {
                continue;
            }

            $nodes[] = $node;
        }

        $v2rayjson_config['outbounds'] = array_merge($v2rayjson_config['outbounds'], $nodes);

        return json_encode($v2rayjson_config);
    }
}
