<?php

declare(strict_types=1);

namespace App\Services\Subscribe;

use App\Models\Setting;
use App\Utils\Tools;
use function array_key_exists;
use function json_decode;
use const PHP_EOL;

final class SIP002 extends Base
{
    public function getContent($user): string
    {
        $links = '';
        //判断是否开启SS订阅
        if (! Setting::obtain('enable_ss_sub')) {
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
            if ((int) $node_raw->sort === 0) {
                $plugin = $node_custom_config['plugin'] ?? '';
                $plugin_option = $node_custom_config['plugin_option'] ?? '';

                $links .= $user->method . ':' . $user->passwd . '@' . $server . ':' .
                    $user->port . '/?plugin=' . $plugin . '&' . $plugin_option . '#' .
                    $node_raw->name . PHP_EOL;
            }
        }

        return $links;
    }
}
