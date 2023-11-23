<?php

declare(strict_types=1);

namespace App\Services\Subscribe;

use App\Models\Config;
use App\Services\Subscribe;
use function json_decode;
use const PHP_EOL;

final class Trojan extends Base
{
    public function getContent($user): string
    {
        $links = '';
        //判断是否开启Trojan订阅
        if (! Config::obtain('enable_trojan_sub')) {
            return $links;
        }

        $nodes_raw = Subscribe::getUserSubNodes($user);

        foreach ($nodes_raw as $node_raw) {
            $node_custom_config = json_decode($node_raw->custom_config, true);

            if ((int) $node_raw->sort === 14) {
                $trojan_port = $node_custom_config['offset_port_user'] ?? ($node_custom_config['offset_port_node'] ?? 443);
                $host = $node_custom_config['host'] ?? '';
                $allow_insecure = $node_custom_config['allow_insecure'] ?? '0';
                $security = $node_custom_config['security'] ?? 'tls';
                $mux = $node_custom_config['mux'] ?? '0';
                $network = $node_custom_config['network'] ?? 'tcp';
                $transport_plugin = $node_custom_config['transport_plugin'] ?? '';
                $transport_method = $node_custom_config['transport_method'] ?? '';
                $servicename = $node_custom_config['servicename'] ?? '';
                $path = $node_custom_config['path'] ?? '';

                $links .= 'trojan://' . $user->uuid . '@' . $node_raw->server . ':' . $trojan_port . '?peer=' . $host . '&sni='
                    . $host . '&obfs=' . $transport_plugin . '&path=' . $path . '&mux=' . $mux . '&allowInsecure='
                    . $allow_insecure . '&obfsParam=' . $transport_method . '&type=' . $network . '&security='
                    . $security . '&serviceName=' . $servicename . '#' . $node_raw->name . PHP_EOL;
            }
        }

        return $links;
    }
}
