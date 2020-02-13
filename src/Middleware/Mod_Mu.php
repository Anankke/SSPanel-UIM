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
            return $response->withjson([
                'ret' => 0,
                'data' => 'Your key is null.'
            ]);
        }

        $keys = Config::getMuKey();
        $auth = in_array($key, $keys);

        $node = Node::where('node_ip', 'LIKE', $_SERVER['REMOTE_ADDR'] . '%')->first();
        if ($auth === false
            || ($node === null && $_SERVER['REMOTE_ADDR'] != '127.0.0.1')
            ) {
            return $response->withJson([
                'ret' => 0,
                'data' => 'Token or IP is invalid. Now, your IP address is ' . $_SERVER['REMOTE_ADDR']
            ]);
        }

        return $next($request, $response);
    }
}
