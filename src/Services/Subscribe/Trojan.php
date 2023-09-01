<?php

declare(strict_types=1);

namespace App\Services\Subscribe;

use App\Models\Setting;
use App\Utils\Tools;
use function array_key_exists;
use function json_decode;
use const PHP_EOL;

final class Trojan extends Base
{
    public function getContent($user): string
    {
        $links = '';
        //判断是否开启Trojan订阅
        if (! Setting::obtain('enable_trojan_sub')) {
            return $links;
        }

        $nodes_raw = Tools::getSubNodes($user);

        foreach ($nodes_raw as $node_raw) {
            $node_custom_config = json_decode($node_raw->custom_config, true);
            //檢查是否配置“前端/订阅中下发的服务器地址”
            if (! array_key_exists('server_user', $node_custom_config)) {
                $server = $node_raw->server;
            } else {
                $server = $node_custom_config['server_user'];
            }
            if ((int) $node_raw->sort === 14) {
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

                $links .= 'trojan://' . $user->uuid . '@' . $server . ':' . $trojan_port . '?peer=' . $host . '&sni='
                    . $host . '&obfs=' . $transport_plugin . '&path=' . $path . '&mux=' . $mux . '&allowInsecure='
                    . $allow_insecure . '&obfsParam=' . $transport_method . '&type=' . $transport . '&security='
                    . $security . '&serviceName=' . $servicename . '#' . $node_raw->name . PHP_EOL;
            }
        }

        return $links;
    }
}
