<?php

declare(strict_types=1);

namespace App\Services\Subscribe;

use App\Models\Config;
use App\Services\Subscribe;
use function base64_encode;
use const PHP_EOL;

final class SS extends Base
{
    public function getContent($user): string
    {
        $links = '';
        //判断是否开启SS订阅
        if (! Config::obtain('enable_ss_sub')) {
            return $links;
        }

        $nodes_raw = Subscribe::getUserSubNodes($user);

        foreach ($nodes_raw as $node_raw) {
            if ((int) $node_raw->sort === 0) {
                $links .= base64_encode($user->method . ':' . $user->passwd . '@' . $node_raw->server . ':' . $user->port) . '#' .
                    $node_raw->name . PHP_EOL;
            }
        }

        return $links;
    }
}
