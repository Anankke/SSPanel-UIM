<?php

namespace App\Middleware;

use App\Services\Config;
use App\Models\Node;

class Mod_Mu
{
    /**
     * @param \Slim\Http\Request    $request
     * @param \Slim\Http\Response   $response
     * @param callable              $next
     *
     * @return \Slim\Http\Response
     */
    public function __invoke($request, $response, $next)
    {
        $key = $request->getQueryParam('key');
        if ($key === null) {
            // 未提供 key
            return $response->withjson([
                'ret'  => 0,
                'data' => 'Your key is null.'
            ]);
        }

        if (!in_array($key, Config::getMuKey())) {
            // key 不存在
            return $response->withJson([
                'ret'  => 0,
                'data' => 'Token is invalid'
            ]);
        }

        if ($_ENV['WebAPI'] === false) {
            // 主站不提供 WebAPI
            return $response->withJson([
                'ret'  => 0,
                'data' => 'We regret this service is temporarily unavailable'
            ]);
        }

        if ($_ENV['checkNodeIp'] === true) {
            if ($_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
                $node = Node::where('node_ip', 'LIKE', $_SERVER['REMOTE_ADDR'] . '%')->first();
                if ($node === null) {
                    return $response->withJson([
                        'ret'  => 0,
                        'data' => 'IP is invalid. Now, your IP address is ' . $_SERVER['REMOTE_ADDR']
                    ]);
                }
            }
        }

        return $next($request, $response);
    }
}
