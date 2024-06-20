<?php

declare(strict_types=1);

namespace App\Services\Subscribe;

use App\Models\Config;
use App\Services\Subscribe;
use function json_decode;
use const PHP_EOL;

final class SIP002 extends Base
{
    public function getContent($user): string
    {
        $links = '';
        //判断是否开启SS订阅
        if (! Config::obtain('enable_ss_sub')) {
            return $links;
        }

        $nodes_raw = Subscribe::getUserNodes($user);

        foreach ($nodes_raw as $node_raw) {
            $node_custom_config = json_decode($node_raw->custom_config, true);

            if ((int) $node_raw->sort === 0) {
                $plugin = $node_custom_config['plugin'] ?? '';
                $plugin_option = $node_custom_config['plugin_option'] ?? '';

                $links .= $user->method . ':' . $user->passwd . '@' . $node_raw->server . ':' .
                    $user->port . '/?plugin=' . $plugin . '&' . $plugin_option . '#' .
                    $node_raw->name . PHP_EOL;
            }
        }

        return $links;
    }
}
