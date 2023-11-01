<?php

declare(strict_types=1);

namespace App\Services\Subscribe;

use App\Services\Subscribe;
use Symfony\Component\Yaml\Yaml;
use function array_merge;
use function json_decode;

final class Clash extends Base
{
    public function getContent($user): string
    {
        $nodes = [];
        $clash_config = $_ENV['Clash_Config'];
        $clash_group_indexes = $_ENV['Clash_Group_Indexes'];
        $clash_group_config = $_ENV['Clash_Group_Config'];
        $nodes_raw = Subscribe::getSubNodes($user);

        foreach ($nodes_raw as $node_raw) {
            $node_custom_config = json_decode($node_raw->custom_config, true);

            switch ((int) $node_raw->sort) {
                case 0:
                    $plugin = $node_custom_config['plugin'] ?? '';
                    $plugin_option = $node_custom_config['plugin_option'] ?? null;
                    // Clash 特定配置
                    $udp = $node_custom_config['udp'] ?? true;
                    // Clash.Meta
                    $client_fingerprint = $node_custom_config['client_fingerprint'] ?? '';

                    $node = [
                        'name' => $node_raw->name,
                        'type' => 'ss',
                        'server' => $node_raw->server,
                        'port' => (int) $user->port,
                        'password' => $user->passwd,
                        'cipher' => $user->method,
                        'udp' => (bool) $udp,
                        'client-fingerprint' => $client_fingerprint,
                        'plugin' => $plugin,
                        'plugin-opts' => $plugin_option,
                    ];

                    break;
                case 1:
                    $ss_2022_port = $node_custom_config['offset_port_user'] ?? ($node_custom_config['offset_port_node'] ?? 443);
                    $method = $node_custom_config['method'] ?? '2022-blake3-aes-128-gcm';

                    $pk_len = match ($method) {
                        '2022-blake3-aes-128-gcm' => 16,
                        default => 32,
                    };

                    $user_pk = $user->getSs2022Pk($pk_len);
                    // Clash 特定配置
                    $udp = $node_custom_config['udp'] ?? true;

                    $node = [
                        'name' => $node_raw->name,
                        'type' => 'ss',
                        'server' => $node_raw->server,
                        'port' => (int) $ss_2022_port,
                        'password' => $user_pk,
                        'cipher' => $method,
                        'udp' => (bool) $udp,
                    ];

                    break;
                case 2:
                    $tuic_port = $node_custom_config['offset_port_user'] ?? ($node_custom_config['offset_port_node'] ?? 443);
                    $host = $node_custom_config['host'] ?? '';
                    $congestion_control = $node_custom_config['congestion_control'] ?? 'bbr';

                    // Tuic V5 Only
                    $node = [
                        'name' => $node_raw->name,
                        'type' => 'tuic',
                        'server' => $node_raw->server,
                        'port' => (int) $tuic_port,
                        'password' => $user->passwd,
                        'uuid' => $user->uuid,
                        'sni' => $host,
                        'congestion-controller' => $congestion_control,
                        'reduce-rtt' => true,
                    ];

                    break;
                case 11:
                    $v2_port = $node_custom_config['offset_port_user'] ?? ($node_custom_config['offset_port_node'] ?? 443);
                    $security = $node_custom_config['security'] ?? 'none';
                    $encryption = $node_custom_config['encryption'] ?? 'auto';
                    $network = $node_custom_config['header']['type'] ?? $node_custom_config['network'] ?? '';
                    $host = $node_custom_config['header']['request']['headers']['Host'][0] ??
                        $node_custom_config['host'] ?? '';
                    $path = $node_custom_config['header']['request']['path'][0] ?? $node_custom_config['path'] ?? '/';
                    $allow_insecure = $node_custom_config['allow_insecure'] ?? false;
                    $tls = $security === 'tls';
                    
                    // Clash 特定配置
                    $udp = $node_custom_config['udp'] ?? true;
                    $custom_ws_opt = ['path' => $path, 'headers' => ['Host' => $host], ];
                    $ws_opts = $node_custom_config['ws-opts'] ?? $node_custom_config['ws_opts'] ?? $custom_ws_opt ?? null;
                    $h2_opts = $node_custom_config['h2-opts'] ?? $node_custom_config['h2_opts'] ?? null;
                    $http_opts = $node_custom_config['http-opts'] ?? $node_custom_config['http_opts'] ?? null;
                    $grpc_opts = $node_custom_config['grpc-opts'] ?? $node_custom_config['grpc_opts'] ?? null;
                    // Clash.Meta
                    $type = ($node_custom_config['enable_vless'] ?? '0') === '1' ? 'vless' : 'vmess';
                    $client_fingerprint = $node_custom_config['client_fingerprint'] ?? '';
                    $fingerprint = $node_custom_config['fingerprint'] ?? '';
                    $flow = $node_custom_config['flow'] ?? '';
                    $reality_opts = $node_custom_config['reality-opts'] ?? $node_custom_config['reality_opts'] ?? null;

                    $node = [
                        'name' => $node_raw->name,
                        'type' => $type,
                        'server' => $node_raw->server,
                        'port' => (int) $v2_port,
                        'uuid' => $user->uuid,
                        'alterId' => 0,
                        'cipher' => $encryption,
                        'udp' => (bool) $udp,
                        'tls' => $tls,
                        'client-fingerprint' => $client_fingerprint,
                        'fingerprint' => $fingerprint,
                        'flow' => $flow,
                        'skip-cert-verify' => (bool) $allow_insecure,
                        'servername' => $host,
                        'network' => $network,
                        'ws-opts' => $ws_opts,
                        'h2-opts' => $h2_opts,
                        'http-opts' => $http_opts,
                        'grpc-opts' => $grpc_opts,
                        'reality-opts' => $reality_opts,
                    ];

                    break;
                case 14:
                    $trojan_port = $node_custom_config['offset_port_user'] ?? ($node_custom_config['offset_port_node'] ?? 443);
                    $network = $node_custom_config['network'] ?? 'tcp';
                    $host = $node_custom_config['host'] ?? '';
                    $allow_insecure = $node_custom_config['allow_insecure'] ?? false;
                    // Clash 特定配置
                    $udp = $node_custom_config['udp'] ?? true;
                    $ws_opts = $node_custom_config['ws-opts'] ?? $node_custom_config['ws_opts'] ?? null;
                    $grpc_opts = $node_custom_config['grpc-opts'] ?? $node_custom_config['grpc_opts'] ?? null;
                    // Clash.Meta
                    $client_fingerprint = $node_custom_config['client_fingerprint'] ?? '';
                    $fingerprint = $node_custom_config['fingerprint'] ?? '';
                    $flow = $node_custom_config['flow'] ?? '';
                    $flow_show = $node_custom_config['flow_show'] ?? false;

                    $node = [
                        'name' => $node_raw->name,
                        'type' => 'trojan',
                        'server' => $node_raw->server,
                        'sni' => $host,
                        'port' => (int) $trojan_port,
                        'password' => $user->uuid,
                        'network' => $network,
                        'udp' => (bool) $udp,
                        'client-fingerprint' => $client_fingerprint,
                        'fingerprint' => $fingerprint,
                        'flow' => $flow,
                        'flow-show' => (bool) $flow_show,
                        'skip-cert-verify' => (bool) $allow_insecure,
                        'ws-opts' => $ws_opts,
                        'grpc-opts' => $grpc_opts,
                    ];

                    break;
                default:
                    $node = [];
                    break;
            }

            if ($node === []) {
                continue;
            }

            $nodes[] = $node;

            foreach ($clash_group_indexes as $index) {
                $clash_group_config['proxy-groups'][$index]['proxies'][] = $node_raw->name;
            }
        }

        $clash_nodes = [
            'proxies' => $nodes,
        ];

        return Yaml::dump(
            array_merge($clash_config, $clash_nodes, $clash_group_config),
            4,
            1,
            Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE
        );
    }
}
