<?php

declare(strict_types=1);

namespace App\Services\Subscribe;

use App\Services\Subscribe;
use function json_decode;

final class Json extends Base
{
    public function getContent($user): string
    {
        $nodes = [];
        $nodes_raw = Subscribe::getSubNodes($user);

        foreach ($nodes_raw as $node_raw) {
            $node_custom_config = json_decode($node_raw->custom_config, true);

            switch ((int) $node_raw->sort) {
                case 0:
                    $plugin = $node_custom_config['plugin'] ?? '';
                    $plugin_option = $node_custom_config['plugin_option'] ?? '';
                    $node = [
                        'name' => $node_raw->name,
                        'id' => $node_raw->id,
                        'type' => 'ss',
                        'address' => $node_raw->server,
                        'port' => (int) $user->port,
                        'password' => $user->passwd,
                        'encryption' => $user->method,
                        'plugin' => $plugin,
                        'plugin_option' => $plugin_option,
                        'remark' => $node_raw->info,
                    ];
                    break;
                case 11:
                    $v2_port = $node_custom_config['offset_port_user'] ?? ($node_custom_config['offset_port_node'] ?? 443);
                    $security = $node_custom_config['security'] ?? 'none';
                    $flow = $node_custom_config['flow'] ?? '';
                    $encryption = $node_custom_config['encryption'] ?? '';
                    $network = $node_custom_config['network'] ?? '';
                    $header = $node_custom_config['header'] ?? ['type' => 'none'];
                    $header_type = $header['type'] ?? '';
                    $host = $node_custom_config['header']['request']['headers']['Host'][0] ??
                        $node_custom_config['host'] ?? '';
                    $servicename = $node_custom_config['servicename'] ?? '';
                    $path = $node_custom_config['header']['request']['path'][0] ?? $node_custom_config['path'] ?? '/';
                    $tls = $security === 'tls' ? '1' : '0';
                    $node = [
                        'name' => $node_raw->name,
                        'id' => $node_raw->id,
                        'type' => 'vmess',
                        'address' => $node_raw->server,
                        'port' => (int) $v2_port,
                        'uuid' => $user->uuid,
                        'alterid' => 0,
                        'security' => $security,
                        'flow' => $flow,
                        'encryption' => $encryption,
                        'network' => $network,
                        'header' => $header,
                        'header_type' => $header_type,
                        'host' => $host,
                        'path' => $path,
                        'servicename' => $servicename,
                        'tls' => (int) $tls,
                        'remark' => $node_raw->info,
                    ];
                    break;
                case 14:
                    $trojan_port = $node_custom_config['offset_port_user'] ?? ($node_custom_config['offset_port_node'] ?? 443);
                    $host = $node_custom_config['host'] ?? '';
                    $allow_insecure = $node_custom_config['allow_insecure'] ?? '0';
                    $mux = $node_custom_config['mux'] ?? '';
                    $network = $node_custom_config['network'] ?? 'tcp';
                    $transport_plugin = $node_custom_config['transport_plugin'] ?? '';
                    $transport_method = $node_custom_config['transport_method'] ?? '';
                    $servicename = $node_custom_config['servicename'] ?? '';
                    $path = $node_custom_config['path'] ?? '';
                    $node = [
                        'name' => $node_raw->name,
                        'id' => $node_raw->id,
                        'type' => 'trojan',
                        'address' => $node_raw->server,
                        'host' => $host,
                        'port' => (int) $trojan_port,
                        'uuid' => $user->uuid,
                        'mux' => $mux,
                        'transport' => $network,
                        'transport_plugin' => $transport_plugin,
                        'transport_method' => $transport_method,
                        'allow_insecure' => (int) $allow_insecure,
                        'servicename' => $servicename,
                        'path' => $path,
                        'remark' => $node_raw->info,
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
        }

        return json_encode([
            'version' => 3,
            'sub_name' => $_ENV['appName'],
            'user_email' => $user->email,
            'user_name' => $user->user_name,
            'user_class' => $user->class,
            'user_class_expire_date' => $user->class_expire,
            'user_total_traffic' => $user->transfer_enable,
            'user_used_traffic' => $user->u + $user->d,
            'nodes' => $nodes,
        ]);
    }
}
