<?php

declare(strict_types=1);

namespace App\Services\Subscribe;

use App\Utils\Tools;
use function array_key_exists;
use function in_array;
use function json_decode;

final class Json extends Base
{
    public function getContent($user): string
    {
        $nodes = [];
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
                    $plugin_option = $node_custom_config['plugin_option'] ?? '';
                    $node = [
                        'name' => $node_raw->name,
                        'id' => $node_raw->id,
                        'type' => 'ss',
                        'address' => $server,
                        'port' => (int) $user->port,
                        'password' => $user->passwd,
                        'encryption' => $user->method,
                        'plugin' => $plugin,
                        'plugin_option' => $plugin_option,
                        'remark' => $node_raw->info,
                    ];
                    break;
                case 11:
                    $v2_port = $node_custom_config['v2_port'] ?? ($node_custom_config['offset_port_user']
                        ?? ($node_custom_config['offset_port_node'] ?? 443));
                    //默認值有問題的請懂 V2 怎麽用的人來改一改。
                    $alter_id = $node_custom_config['alter_id'] ?? '0';
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
                    $tls = in_array($security, ['tls', 'xtls']) ? '1' : '0';
                    $enable_vless = $node_custom_config['enable_vless'] ?? '0';
                    $node = [
                        'name' => $node_raw->name,
                        'id' => $node_raw->id,
                        'type' => 'vmess',
                        'address' => $server,
                        'port' => (int) $v2_port,
                        'uuid' => $user->uuid,
                        'alterid' => (int) $alter_id,
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
                        'enable_vless' => (int) $enable_vless,
                        'remark' => $node_raw->info,
                    ];
                    break;
                case 14:
                    $trojan_port = $node_custom_config['trojan_port'] ?? ($node_custom_config['offset_port_user']
                        ?? ($node_custom_config['offset_port_node'] ?? 443));
                    $host = $node_custom_config['host'] ?? '';
                    $allow_insecure = $node_custom_config['allow_insecure'] ?? '0';
                    $security = $node_custom_config['security']
                        ?? array_key_exists('enable_xtls', $node_custom_config)
                        && $node_custom_config['enable_xtls'] === '1' ? 'xtls' : 'tls';
                    $mux = $node_custom_config['mux'] ?? '';
                    $transport = $node_custom_config['transport']
                        ?? array_key_exists('grpc', $node_custom_config)
                        && $node_custom_config['grpc'] === '1' ? 'grpc' : 'tcp';

                    $transport_plugin = $node_custom_config['transport_plugin'] ?? '';
                    $transport_method = $node_custom_config['transport_method'] ?? '';
                    $servicename = $node_custom_config['servicename'] ?? '';
                    $path = $node_custom_config['path'] ?? '';
                    $node = [
                        'name' => $node_raw->name,
                        'id' => $node_raw->id,
                        'type' => 'trojan',
                        'address' => $server,
                        'host' => $host,
                        'port' => (int) $trojan_port,
                        'uuid' => $user->uuid,
                        'security' => $security,
                        'mux' => $mux,
                        'transport' => $transport,
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
            'version' => 2,
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
