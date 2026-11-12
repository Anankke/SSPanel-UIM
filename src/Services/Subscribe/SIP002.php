<?php

declare(strict_types=1);

namespace App\Services\Subscribe;

use App\Models\Config;
use App\Services\Subscribe;
use function base64_encode;
use function json_decode;
use function rawurlencode;
use function rtrim;
use function str_contains;
use function str_starts_with;
use function strtr;
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

                $userinfo = rtrim(strtr(base64_encode($user->method . ':' . $user->passwd), '+/', '-_'), '=');
                $host = (string) $node_raw->server;
                if (str_contains($host, ':') && ! str_starts_with($host, '[')) {
                    $host = '[' . $host . ']';
                }
                $plugin_query = $plugin === '' ? '' :
                    '/?plugin=' . rawurlencode($plugin_option === '' ? $plugin : $plugin . ';' . $plugin_option);

                $links .= 'ss://' . $userinfo . '@' . $host . ':' . $user->port . $plugin_query . '#' .
                    rawurlencode($node_raw->name) . PHP_EOL;
            }
        }

        return $links;
    }
}
