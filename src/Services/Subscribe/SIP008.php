<?php

declare(strict_types=1);

namespace App\Services\Subscribe;

use App\Models\Setting;
use App\Utils\Tools;
use function array_key_exists;
use function json_decode;
use function json_encode;

final class SIP008 extends Base
{
    public function getContent($user): string
    {
        $nodes = [];
        //判断是否开启SS订阅
        if (! Setting::obtain('enable_ss_sub')) {
            return '';
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

            switch ((int) $node_raw->sort) {
                case 0:
                    $plugin = $node_custom_config['plugin'] ?? '';
                    $plugin_option = $node_custom_config['plugin_option'] ?? '';
                    $node = [
                        'id' => $node_raw->id,
                        'remarks' => $node_raw->name,
                        'server' => $server,
                        'server_port' => (int) $user->port,
                        'password' => $user->passwd,
                        'method' => $user->method,
                        'plugin' => $plugin,
                        'plugin_opts' => $plugin_option,
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
            'version' => 1,
            'servers' => $nodes,
            'bytes_used' => $user->u + $user->d,
            'bytes_remaining' => $user->transfer_enable - $user->u - $user->d,
        ]);
    }
}
