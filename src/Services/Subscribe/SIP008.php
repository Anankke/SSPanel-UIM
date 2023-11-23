<?php

declare(strict_types=1);

namespace App\Services\Subscribe;

use App\Models\Config;
use App\Services\Subscribe;
use function json_decode;
use function json_encode;

final class SIP008 extends Base
{
    public function getContent($user): string
    {
        $nodes = [];
        //判断是否开启SS订阅
        if (! Config::obtain('enable_ss_sub')) {
            return '';
        }

        $nodes_raw = Subscribe::getUserSubNodes($user);

        foreach ($nodes_raw as $node_raw) {
            $node_custom_config = json_decode($node_raw->custom_config, true);

            if ((int) $node_raw->sort === 0) {
                $plugin = $node_custom_config['plugin'] ?? '';
                $plugin_option = $node_custom_config['plugin_option'] ?? '';
                $node = [
                    'id' => $node_raw->id,
                    'remarks' => $node_raw->name,
                    'server' => $node_raw->server,
                    'server_port' => (int) $user->port,
                    'password' => $user->passwd,
                    'method' => $user->method,
                    'plugin' => $plugin,
                    'plugin_opts' => $plugin_option,
                ];
            }

            $nodes[] = $node;
        }

        return json_encode([
            'version' => 1,
            'servers' => $nodes,
            'bytes_used' => $user->u + $user->d,
            'bytes_remaining' => $user->transfer_enable - $user->u - $user->d,
        ]);
    }
}
