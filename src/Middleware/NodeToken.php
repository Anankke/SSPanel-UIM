<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Models\Node;
use App\Services\Config;

final class NodeToken
{
    public function __invoke(\Slim\Http\Request $request, \Slim\Http\Response $response, callable $next): \Slim\Http\Response
    {
        $key = $request->getQueryParam('key');
        if ($key === null) {
            // 未提供 key
            return $response->withjson([
                'ret' => 0,
                'data' => 'Your key is null.',
            ]);
        }

        if (! in_array($key, Config::getMuKey())) {
            // key 不存在
            return $response->withJson([
                'ret' => 0,
                'data' => 'Token is invalid.',
            ]);
        }

        if ($_ENV['WebAPI'] === false) {
            // 主站不提供 WebAPI
            return $response->withJson([
                'ret' => 0,
                'data' => 'WebAPI is disabled.',
            ]);
        }

        if ($_ENV['checkNodeIp'] === true) {
            if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1') {
                $node = Node::where('node_ip', 'LIKE', $_SERVER['REMOTE_ADDR'] . '%')->first();
                if ($node === null) {
                    return $response->withJson([
                        'ret' => 0,
                        'data' => 'IP is invalid. Now, your IP address is ' . $_SERVER['REMOTE_ADDR'],
                    ]);
                }
            }
        }

        return $next($request, $response);
    }
}
