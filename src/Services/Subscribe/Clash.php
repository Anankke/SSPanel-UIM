<?php

declare(strict_types=1);

namespace App\Services\Subscribe;

use App\Utils\Tools;
use Symfony\Component\Yaml\Yaml;
use function array_key_exists;
use function array_merge;
use function in_array;
use function json_decode;

final class Clash extends Base
{
    public function getContent($user): string
    {
        $nodes = [];
        $clash_config = $_ENV['Clash_Config'];
        $clash_group_indexes = $_ENV['Clash_Group_Indexes'];
        $clash_group_config = $_ENV['Clash_Group_Config'];
        $nodes_raw = Tools::getSubNodes($user);

        foreach ($nodes_raw as $node_raw) {
            $node_custom_config = json_decode($node_raw->custom_config, true);
            //檢查是否配置“前端/订阅中下发的服务器地址”
            if (! array_key_exists('server_user', $node_custom_config)) {
                $server = $node_raw->server;
            } else {
                $server = $node_custom_config['server_user'];
            }

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
                        'server' => $server,
                        'port' => (int) $user->port,
                        'password' => $user->passwd,
                        'cipher' => $user->method,
                        'udp' => $udp,
                        'client-fingerprint' => $client_fingerprint,
                        'plugin' => $plugin,
                        'plugin-opts' => $plugin_option,
                    ];

                    break;
                case 11:
                    $v2_port = $node_custom_config['v2_port'] ?? ($node_custom_config['offset_port_user']
                        ?? ($node_custom_config['offset_port_node'] ?? 443));
                    $alter_id = $node_custom_config['alter_id'] ?? '0';
                    $security = $node_custom_config['security'] ?? 'none';
                    $encryption = $node_custom_config['encryption'] ?? 'auto';
                    $network = $node_custom_config['header']['type'] ?? $node_custom_config['network'] ?? '';
                    $host = $node_custom_config['header']['request']['headers']['Host'][0] ??
                        $node_custom_config['host'] ?? '';
                    $allow_insecure = $node_custom_config['allow_insecure'] ?? false;
                    $tls = in_array($security, ['tls', 'xtls']);
                    // Clash 特定配置
                    $udp = $node_custom_config['udp'] ?? true;
                    $ws_opts = $node_custom_config['ws-opts'] ?? $node_custom_config['ws_opts'] ?? null;
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
                        'server' => $server,
                        'port' => (int) $v2_port,
                        'uuid' => $user->uuid,
                        'alterId' => (int) $alter_id,
                        'cipher' => $encryption,
                        'udp' => $udp,
                        'tls' => $tls,
                        'client-fingerprint' => $client_fingerprint,
                        'fingerprint' => $fingerprint,
                        'flow' => $flow,
                        'skip-cert-verify' => $allow_insecure,
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
                    $trojan_port = $node_custom_config['trojan_port'] ?? ($node_custom_config['offset_port_user']
                        ?? ($node_custom_config['offset_port_node'] ?? 443));
                    $network = $node_custom_config['network']
                        ?? (array_key_exists('grpc', $node_custom_config)
                        && $node_custom_config['grpc'] === '1' ? 'grpc' : 'tcp');
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
                        'server' => $server,
                        'sni' => $host,
                        'port' => (int) $trojan_port,
                        'password' => $user->uuid,
                        'network' => $network,
                        'udp' => $udp,
                        'client-fingerprint' => $client_fingerprint,
                        'fingerprint' => $fingerprint,
                        'flow' => $flow,
                        'flow-show' => $flow_show,
                        'skip-cert-verify' => $allow_insecure,
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
